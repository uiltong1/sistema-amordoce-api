<?php

namespace App\Http\Controllers\API;

use App\Pedido;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidosController extends Controller
{
    public function list($status)
    {
        try {
            $pedido = DB::select("SELECT PED.numpedido, 
                                         PED.status, 
                                         PED.entrega,
                                         DATE_FORMAT(PED.DATA_PEDIDO, '%d/%m/%Y') as data_pedido, 
                                         DATE_FORMAT(PED.DATA_ENTREGA, '%d/%m/%Y') AS data_entrega,
                                         CLI.nome_cliente,
                                         CLI.bairro
                                    from PEDIDOS AS PED
                                    JOIN CLIENTES AS CLI ON(CLI.ID = PED.ID_CLIENTE)
                                    WHERE PED.STATUS = '$status' ORDER BY PED.DATA_ENTREGA ASC ");
            if (is_null($pedido)) {
                return response()->json(["message" => "Nenhum registro encontrado."]);
            }
            return response()->json($pedido);
        } catch (Exception $e) {
            return response()->json(["message" => "Erro ao buscar pedido.", "error" => "$e"]);
        }
    }
    public function createPedido(Request $request)
    {
        try {
            $pedido = new Pedido();
            $pedido->id_cliente = $request->id_cliente;
            $pedido->status = 'A';
            $pedido->data_pedido = date('Y/m/d');
            $pedido->data_entrega = $request->data_entrega;
            $pedido->entrega = $request->entrega;
            $pedido->save();
            $pedido = DB::table('Pedidos')->max('numpedido');
            return response()->json(["message" => "Pedido número $pedido registrado com sucesso!", "id" => "$pedido"]);
        } catch (Exception $e) {
            return response()->json(["message" => "Erro ao inserir pedido a base de dados.", "error" => "$e"]);
        }
    }
    public function alterarStatus($numpedido, $status)
    {
        try {
            Db::table('Pedidos')
                ->where('numpedido', $numpedido)
                ->update(["status" => "$status"]);
            return response()->json(["message" => "O status do pedido $numpedido foi alterado com sucesso!"]);
        } catch (Exception $e) {
            return response()->json(['message' => "Erro ao cancelar pedido.", "error" => "$e"]);
        }
    }
    public function cancelarPedido($numpedido)
    {
        try {
            Db::table('Pedidos')
                ->where('numpedido', $numpedido)
                ->update(["status" => "C"]);
            return response()->json(["message" => "O pedido $numpedido foi cancelado."]);
        } catch (Exception $e) {
            return response()->json(['message' => "Erro ao cancelar pedido.", "error" => "$e"]);
        }
    }
    public function associarItem(Request $request)
    {
        try {
            foreach ($request->itens as  $value) {
                DB::table('Pedido_itens')
                    ->insert(['numpedido' => $request->pedido_id, 'coditem' => $value]);
            }
            return response()->json(["message" => "Registro realizado com sucesso!"]);
        } catch (Exception $e) {
            return response()->json(["message" => "Erro ao inserir registro ao banco de dados.", "error" => "$e"]);
        }
    }
    public function pedidoVendas(Request $request)
    {
        try {
            DB::table('Vendas')
                ->insert(['id_cliente' => $request->id_cliente, 'numpedido' => $request->pedido, 'total_venda' => $request->valor, 'pagamento' => $request->pagamento]);
            return response()->json(["message" => "ok"]);
        } catch (Exception $e) {
            return response()->json(["message" => "Erro ao conectar a Base de Dados.", "error" => "$e"]);
        }
    }
    public function consultPedidoClient($cliente)
    {
        try {
            $pedidos = DB::select("SELECT ped.numpedido, 
                                          date_format(ped.data_pedido,'%d/%m/%Y') as data_pedido,
                                          date_format(ped.data_entrega,'%d/%m/%Y') as data_entrega,
                                          ped.status,
                                          ven.total_venda
                                        FROM PEDIDOS AS PED
                                        LEFT JOIN VENDAS AS VEN ON(VEN.ID_CLIENTE = PED.ID_CLIENTE)
                                        WHERE PED.ID_CLIENTE = ?", [$cliente]);
            if (is_null($pedidos)) {
                response()->json(["message" => "Nenhum Registro Encontrado."]);
            }
            return response()->json($pedidos);
        } catch (Exception $e) {
            return response()->json(["message" => "Erro ao se conectar com servidor de Banco de Dados.", "error" => "$e"]);
        }
    }
    public function listaProdutos($numpedido)
    {
        try {
            $produtos = DB::select("SELECT PIT.coditem,
                                        IT.descricao,
                                        format(IT.valor,2,'de_DE') AS valor
                                        FROM PEDIDO_ITENS AS PIT 
                                        INNER JOIN ITENS IT ON(IT.ID = PIT.CODITEM)
                                        WHERE PIT.NUMPEDIDO = $numpedido");
            if (is_null($produtos)) {
                response()->json(["message" => "Nenhum registro encontrado."]);
            }
            return response()->json($produtos);
        } catch (Exception $e) {
            return response()->json(["message" => "Erro ao conectar ao servidor de Banco de Dados.", "error" => "$e"]);
        }
    }
    public function listaProdutosSoma($numpedido)
    {
        try {
            $soma = DB::select("SELECT format(SUM(IT.VALOR),2,'de_DE') AS valor_total,
                                       PIT.numpedido
                                    FROM ITENS AS IT
                                    JOIN PEDIDO_ITENS AS PIT ON(IT.ID = PIT.CODITEM)
                                    WHERE PIT.NUMPEDIDO = $numpedido
                                    GROUP BY PIT.NUMPEDIDO");
            if (is_null($soma)) {
                response()->json(["message" => "Nenhum registro encontrado."]);
            }
            return response()->json($soma);
        } catch (Exception $e) {
            return response()->json(["message" => "Erro ao conectar ao servidor de Banco de Dados.", "error" => "$e"]);
        }
    }
    public function consultarPedido($numpedido)
    {
        try {
            $pedido = DB::select("select ped.numpedido, 
                                         cli.nome_cliente,
                                         cli.bairro,
                                         cli.cep,
                                         cli.endereco,
                                         ped.status,
                                         ped.data_entrega as dataAtual,
                                         date_format(ped.data_pedido,'%d/%m/%Y') as data_pedido, 
                                         date_format(ped.data_entrega, '%d/%m/%Y') as data_entrega,
                                         ped.entrega,
                                         ven.pagamento
                                    from pedidos as ped
                                    inner join clientes as cli on(cli.id = ped.id_cliente)
                                    inner join Vendas as Ven on(Ven.numpedido = ped.numpedido)
                                    where  ped.numpedido = $numpedido");
            if (is_null($pedido)) {
                return response()->json(["message" => "Nenhum Registro Encontrado."]);
            }
            return response()->json($pedido);
        } catch (Exception $e) {
            return response()->json(["message" => "Erro ao se conectar com o servidor de Banco de Dados", "erro" => "$e"]);
        }
    }

    public function alterarPedidoDados(Request $request)
    {
        try {
            $pedido = $request->numpedido;
            $cliente = $request->cliente;
            //ALTERAR DADOS DO PEDIDO
            DB::table('Pedidos')
                ->where('numpedido', $pedido)
                ->update(["entrega" => $request->entrega, "data_entrega" => $request->data_entrega]);
            //ALTERA DADOS DE ENTREGAS DO CLIENTE
            DB::select("update clientes
                        set bairro = '$request->bairro', endereco='$request->endereco', cep='$request->cep'
                        where id = 1");
            return response()->json(["message" => "Dados do pedido nº $pedido foram alterados com sucesso!"]);
        } catch (Exception $e) {
            return response()->json(["message" => "Erro ao conectar com o servidor de Banco de Dados.", "erro" => "$e"]);
        }
    }
}
