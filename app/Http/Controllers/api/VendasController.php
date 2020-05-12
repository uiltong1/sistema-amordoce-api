<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class VendasController extends Controller
{
    public function list($status) {
        try {
            $pedido = DB::table('Pedido')
                ->where('status_pedido', $status)
                ->get();
            if (is_null($pedido) || $pedido->count() == 0) {
                return response()->json(["message" => "Nenhum registro encontrado."]);
            }
            return response()->json($pedido);
        } catch (Exception $e) {
            return response()->json(["message" => "Erro ao buscar pedido.", "error" => "$e"]);
        }
    }
    public function alterarStatus($numpedido, $status){
        try{
            Db::table('Pedido')
            ->where('numpedido', $numpedido)
            ->update(["status_pedido"=>"$status"]);
            return response()->json(["message"=>"O status do pedido $numpedido foi alterado com sucesso!"]);
        }catch(Exception $e){
            return response()->json(['message'=>"Erro ao cancelar pedido.", "error"=>"$e"]);
        }
    }
    public function cancelarVenda($numpedido){
        try{
            Db::table('Pedido')
            ->where('numpedido', $numpedido)
            ->update(["status_pedido"=>"C"]);
            return response()->json(["message"=>"O pedido $numpedido foi cancelado."]);
        }catch(Exception $e){
            return response()->json(['message'=>"Erro ao cancelar pedido.", "error"=>"$e"]);
        }
    }
    public function updateVendas(Request $request){
        try{
            $numpedido = $request->numpedido;
            DB::table("Vendas")
            ->update(["pagamento"=>$request->pagamento, "total_venda"=>$request->total_venda]);
            return response()->json(["message"=>"Registro de Vendas do pedido nº $numpedido atualizado com sucesso!"]);
        }catch(Exception $e){
            return response()->json(['message'=>"Erro ao cancelar pedido.", "error"=>"$e"]);
        }
    }
    public function dadosMensal($mes){
        try{
            $dadosMes = DB::select("SELECT Pedidos, CANCELADOS, ENTREGAS, FATURAMENTO FROM
            (SELECT count(*) AS PEDIDOS FROM PEDIDOS WHERE MONTH(DATA_PEDIDO) = $mes AND STATUS = 'F')AS PEDIDOS,
            (SELECT count(*) AS CANCELADOS FROM PEDIDOS WHERE MONTH(DATA_PEDIDO) = $mes AND STATUS = 'C')AS CANCELADOS,
            (SELECT COUNT(*) AS ENTREGAS FROM ENTREGAS WHERE MONTH(DATA_INICIO) = 3 AND STATUS = 'F') AS ENTREGAS,
             (SELECT FORMAT(SUM(TOTAL_VENDA),2,'de_DE') AS FATURAMENTO FROM VENDAS AS VEN
             INNER JOIN PEDIDOS PED ON(PED.NUMPEDIDO = VEN.NUMPEDIDO)
             WHERE MONTH(DATA_PEDIDO) = $mes AND PED.STATUS = 'F') AS VENDAS");

             if(is_null($dadosMes)){
                 return response()->json(["message"=>"Nenhum Registro Encontrado."]);
                }
            return response()->json($dadosMes);
        }catch(Exception $e){
            return response()->json(['message'=>"Erro ao cancelar pedido.", "error"=>"$e"]);
        }
    }
    public function dadosAnuais($ano){
        try{
            $dadosAnuais = DB::select("
            SELECT  MONTH(PED.DATA_PEDIDO) AS month,
                    MONTHNAME(PED.DATA_PEDIDO) AS mes,
                   format(SUM(VEN.TOTAL_VENDA),2,'de_DE') AS total
            FROM PEDIDOS PED
            INNER JOIN VENDAS VEN ON (VEN.NUMPEDIDO = PED.NUMPEDIDO)
            WHERE YEAR(PED.DATA_PEDIDO) = '$ano' AND PED.STATUS = 'F'
            GROUP BY mes, month
            ORDER BY month
        ");
            if(is_null($dadosAnuais)){
                return response()->json(["message"=>"Nenhum registro encontrado"]);
            }
            return response()->json($dadosAnuais);
        }catch(Exception $e){
            return response()->json(["message"=>"Erro ao conectar com a base de dados.", "error"=>"$e"]);
        }
    }
    public function relatorioVendas($inicio, $fim, $status){
        try{
            $vendas = DB::select("SELECT ven.id,
                                         format(ven.total_venda,2,'de_DE') as valor,
                                         date_format(ped.data_pedido, '%d/%m/%Y') as data_pedido,
                                         ped.id_cliente,
                                         cli.nome_cliente as cliente,
                                         count(coditem) as itens
                                    from vendas ven
                                    join pedidos ped on(ped.numpedido = ven.numpedido)
                                    join clientes cli on(cli.id = ped.id_cliente)
                                    join pedido_itens pit on(pit.numpedido = ped.numpedido)
                                    where ped.status = '$status' and data_pedido between '$inicio' and '$fim'
                                    group by ven.id, valor, data_pedido, ped.id_cliente, cliente");
                if(!empty($vendas)){
                    return response()->json($vendas);
                }
                return response()->json(["message"=>"Nenhum Registro Encontrado."]);
        }catch(Exception $e){
            return response()->json(["message"=>"Erro ao conectar ao servirdor de Banco de Dados", "error"=>"$e"]);
        }
    }
}
