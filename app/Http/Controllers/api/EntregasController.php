<?php

namespace App\Http\Controllers\API;

use App\Entrega;
use App\Http\Controllers\Controller;
use App\Http\Requests\EntregaRequest;
use App\PedidoEntrega;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntregasController extends Controller
{

    function list($status)
    {
        try {
            $entregas = DB::select("select ent.id,
                                           ent.placa,
                                           ent.motorista,
                                           ent.kminicial,
                                           ent.kmfinal,
                                           date_format(ent.data_inicio,'%d/%m/%Y') as data_inicio,
                                           ent.observacao,
                                           count(pent.numpedido) as pedidos
                                        from entregas as ent 
                                        left join pedidoentrega as pent on(pent.id_entrega = ent.id)
                                        where ent.status= '$status'
                                        group by ent.id, ent.placa, ent.motorista,ent.kminicial,ent.kmfinal, data_inicio, ent.observacao");
            if (is_null($entregas)) {
                return response()->json(['message' => "Nenhum registro encontrado."]);
            }
            return response()->json($entregas);
        } catch (Exception $e) {
            return response()->json(['message' => "Erro ao realizar consulta.", 'error' => "$e"]);
        }
    }
    public function create(EntregaRequest $request)
    {
        try {
            $entregas = new Entrega;
            $entregas->placa = $request->placa;
            $entregas->motorista = $request->motorista;
            $entregas->kminicial = $request->kminicial;
            $entregas->data_inicio = date('Y-m-d H:i:s');
            $entregas->observacao = $request->observacao;
            $entregas->status = 'A';
            $entregas->save();
            response()->json($entregas);
            return response()->json(['message' => "Entrega criada com sucesso!"]);
        } catch (Exception $e) {
            return response()->json(['message' => "Erro ao criar a entrega.", 'error' => "$e"]);
        }
    }
    public function removeLogic($id_entrega)
    {
        try {
            DB::table('Entregas')
                ->where('id', $id_entrega)
                ->update(array('status' => 'D'));
            return response()->json(['message' => "Entrega $id_entrega excluída com sucesso!"]);
        } catch (Exception $e) {
            return response()->json(['message' => "Erro ao tentar excluir entrega.", 'error' => "$e"]);
        }
    }
    public function finishEntrega(Request $request, $entrega)
    {
        try {
            $entregas = Entrega::find($entrega);
            $entregas->kmfinal = $request->kmfinal;
            $entregas->observacao = $request->observacao;
            $entregas->status = $request->status;
            $entregas->save();

            $pedidos = DB::table('pedidoentrega')
                        ->select('numpedido')
                        ->where('id_entrega', $entrega)
                        ->get();
            foreach($pedidos as $value){
                $pedido = $value->numpedido;
                DB::table('pedidos')
                ->where('numpedido', $pedido)
                ->update(['status'=>'F']);
            }
            return response()->json(['message' => "Entrega número $entrega finalizada com sucesso!"]);
        } catch (Exception $e) {
            return response()->json(['message' => "Erro ao tentar finalizar entrega.", 'error' => "$e"]);
        }
    }
    public function consultEntrega($entrega)
    {
        try {
            $entregas = Entrega::find($entrega);
            if (is_null($entregas)) {
                return response()->json(['message' => "Nenhum registro foi encontrado."]);
            }
            return response()->json($entregas);
        } catch (Exception $e) {
            return response()->json(['message' => "Erro ao buscar a entrega.", 'error' => "$e"]);
        }
    }
    public function consultPedidoEntrega($entrega)
    {
        try {
            $pedidoentrega = DB::select("select PENT.numpedido,
                                                VEN.id_cliente,
                                                format(VEN.total_venda,2,'de_DE') as total_venda,
                                                CLI.nome_cliente,
                                                CLI.bairro,
                                                ENT.placa,
                                                ENT.motorista,
                                                ENT.kminicial,
                                                ENT.kmfinal,
                                                ENT.observacao,
                                                ENT.status
                                            from PEDIDOENTREGA AS PENT
                                            left join VENDAS AS VEN ON(VEN.NUMPEDIDO = PENT.NUMPEDIDO)
                                            left join CLIENTES AS CLI ON(CLI.ID = VEN.ID_CLIENTE)
                                            left join ENTREGAS AS ENT ON(ENT.ID = PENT.ID_ENTREGA)
                                            where PENT.ID_ENTREGA = $entrega");
            if (is_null($pedidoentrega)) {
                return response()->json(["message" => "Nenhum registro encontrado."]);
            }
            return response()->json($pedidoentrega);
        } catch (Exception $e) {
            return response()->json(['message' => "Erro ao buscar a entrega.", 'error' => "$e"]);
        }
    }
    public function totalValor($entrega)
    {
        try {
            $totalEntrega = DB::table('Entregas')
                ->select(DB::raw('format(sum(vendas.total_venda),2,"de_DE") as valor, entregas.id'))
                ->join('Pedidoentrega', 'Entregas.id', '=', 'Pedidoentrega.id_entrega')
                ->join('Vendas', 'Pedidoentrega.numpedido', '=', 'Vendas.numpedido')
                ->where('Entregas.id', '=', $entrega)
                ->groupBy('Entregas.id')
                ->get();
            if (is_null($totalEntrega)) {
                return response()->json(["message" => "Nenhum registro encontrado."]);
            }
            return response()->json($totalEntrega);
        } catch (Exception $e) {
            return response()->json(['message' => "Erro ao buscar a entrega.", 'error' => "$e"]);
        }
    }
    public function hashPedido($numpedido)
    {
        try {
            $infoPedido = DB::table('Pedidoentrega')
                ->join('Entregas', 'Pedidoentrega.id_entrega', '=', 'Entregas.id')
                ->select(
                    'Entregas.id',
                    'Entregas.placa',
                    'Entregas.motorista',
                    'Entregas.kminicial',
                    'Entregas.kmfinal',
                    'Entregas.data_inicio',
                    'Entregas.observacao',
                    'Entregas.status'
                )
                ->where('Pedidoentrega.numpedido', '=', $numpedido)
                ->get();
            if (is_null($infoPedido)) {
                return response()->json(["message" => "Nenhum registro encontrado."]);
            }
            return response()->json($infoPedido);
        } catch (Exception $e) {
            return response()->json(['message' => "Erro ao buscar a entrega.", 'error' => "$e"]);
        }
    }
    public function cadastrarPedidos(Request $request)
    {
        $msg = "";
        $id = $request->Id_entregas;
        // $pedidosArr = $request->Pedidos;
        $pedidosRetirada = [];
        $pedidosEntrega = [];
        foreach ($request->Pedidos as $value) {
            $pedido = DB::table('Pedidos')
                ->where('numpedido', $value)
                ->where('entrega', 1);
            if (is_null($pedido) || $pedido->count() != 0) {
                array_push($pedidosEntrega, "$value");
            } else {
                array_push($pedidosRetirada, "$value");
            }
        }
        $retirada = count($pedidosRetirada);
        if ($retirada != 0) {
            foreach ($pedidosRetirada as $value) {
                $msg .= $value . ",";
            }
            return response()->json(["message" => "Pedidos: $msg não existe(m) ou a opção entrega não foi selecionada.", "status" => "1"]);
        } else {
            foreach ($pedidosEntrega as $value) {
                $pedidoentrega = new Pedidoentrega();
                $pedidoentrega->numpedido = $value;
                $pedidoentrega->id_entrega = $id;
                $pedidoentrega->save();
                $msg .= $value . ",";
            }
            return response()->json(["message" => "Pedidos: $msg cadastrados com sucesso na entrega $id!", "status" => "0"]);
        }
    }
    public function consultarPedidoentrega(Request $request)
    {
        $pedidoExiste = [];
        $msg = '';
        try {
            foreach ($request->pedidos as $numpedido) {
                $pedidoentrega = DB::table('Pedidoentrega')
                    ->where('numpedido', $numpedido)
                    ->get();
                $lenght = count($pedidoentrega);
                if ($lenght != 0) {
                    array_push($pedidoExiste, "$numpedido");
                    $msg .= $numpedido . ", ";
                }
            }
            $lengthExiste = count($pedidoExiste);
            if($lengthExiste != 0){
                return response()->json(["message"=>"Pedido(s): $msg registrado(s) em outra(s) entrega(s)!", "existe"=>"1"]);
            }
            return response()->json(["message"=>"Pedido(s) não assosciado(s)", "existe"=>"0"]);
        } catch (Exception $e) {
            return response()->json(["message" => "Erro ao se conectar com o servidor de Banco de Dados", "erro" => "$e"]);
        }
    }
    public function deletePedidoEntrega($numpedido, $entrega)
    {
        try {
            DB::select("DELETE FROM PEDIDOENTREGA WHERE NUMPEDIDO = $numpedido AND ID_ENTREGA = $entrega");
            return response()->json(["message" => "O pedido nº $numpedido foi removido da entrega com sucesso!"]);
        } catch (Exception $e) {
            return response()->json(['message' => "Erro ao conectar com o servidor de Banco de Dados.", 'error' => "$e"]);
        }
    }
    public function entregasReport($dataInicio, $dataFim, $status){
        try{
            $entregas = DB::select("SELECT  COUNT(PENT.NUMPEDIDO) AS totalPedidos,
                                            ENT.id, 
                                            date_format(ENT.data_inicio,'%d/%m/%Y') as data_inicio,
                                            ENT.kminicial, 
                                            ENT.kmfinal,
                                            ENT.status,
                                            FORMAT(SUM(VEN.total_venda),2,'de_DE') AS total_venda
                                        from PEDIDOENTREGA AS PENT
                                        JOIN ENTREGAS AS ENT ON(ENT.ID = PENT.ID_ENTREGA)
                                        JOIN VENDAS AS VEN ON(VEN.NUMPEDIDO = PENT.NUMPEDIDO)
                                        WHERE ENT.STATUS = '$status'
                                        AND ENT.DATA_INICIO BETWEEN '$dataInicio' AND '$dataFim'
                                        GROUP BY ENT.ID, ENT.data_inicio, ENT.kminicial, ENT.kmfinal, ENT.status");
            if(!empty($entregas)){
                return response()->json($entregas);
            }
            return response()->json(["message"=>"Nenhum Registro Encontrado."]);
        }catch(Exception $e){
            return response()->json(["message"=>"Erro ao conectar com o servidor de Banco de Dados","dados"=>"false", "error"=>"$e"]);
        }
    }
    public function entregasPedidosReport($dataInicio, $dataFim, $status){
        try{
             $pedidos = DB::select("select cli.id as codcli,	
                                            cli.nome_cliente as cliente,
                                            ped.numpedido,
                                            format(ven.total_venda,2,'de_DE') as vltotal,
                                            pent.id_entrega as Entrega
                                        from clientes cli
                                        join pedidos ped on(ped.id_cliente = cli.id)
                                        join vendas ven on(ven.numpedido = ped.numpedido)
                                        join pedidoentrega pent on(pent.numpedido = ped.numpedido)
                                        where ped.status = '$status' and ped.data_pedido between '$dataInicio' and '$dataFim'");
            if(!empty($pedidos)){
                return response()->json($pedidos);
            }
            return response()->json(["message"=>"Nenhum Registro Encontrado."]);
        }catch(Exception $e){
            return response()->json(["message"=>"Erro ao conectar com o servidor de Banco de Dados", "error"=>"$e"]);
        }
    }
}
