<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\itens;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItensController extends Controller
{
    public function create(Request $request)
    {
        try {
            $itens = new Itens;
            $itens->descricao = $request->descricao;
            $itens->valor = $request->valor;
            $itens->status = 'A';
            $itens->save();
            return response()->json(["message" => "Item cadastrado com sucesso!"]);
        } catch (Exception $e) {
            return response()->json(["message" => "Erro ao cadastrar item.", "error" => "$e"]);
        }
    }
    public function list(){
        try{
            $itens = DB::select("select format(valor,2,'de_DE') as valor, id, descricao from itens where status = 'A'");

            if(is_null($itens)){
                return response()->json(["message"=> "Nenhum registro encontrado."]);
            }
            return response()->json($itens);
        }catch(Exception $e){
            return response()->json(["message"=>"Erro ao consulatar dados.", "error"=>"$e"]);
        }
    }
    public function updateItens(Request $request)
    {
        try{
            $id = $request->id;
            DB::table('itens')
                ->where('id', $id)
                ->update(["descricao" => $request->descricao,"valor"=>$request->valor]);
            return response()->json(["message" => "Ítem alterado com suceso!"]);
        }catch(Exception $e) {
            return response()->json(["message" => "Erro ao atualizar ítem.", "error" => "$e"]);
        }
    }
    public function deleteLogic($id){
        try{
            DB::table('itens')
            ->where('id', $id)
            ->update(['status'=>$id]);
            return response()->json(["message"=>"Item excluído com sucesso."]);
        }catch(Exception $e){
            return response()->json(["message"=>"Erro na tentativa de excluir o item.", "error"=>"$e"]);
        }
    }
    public function buscarItem($id){
        try {
            $item = DB::select("select format(valor,2,'de_DE') as valor, id, descricao from itens where id = $id");          
            return response($item);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao consultar item.', 'error' => "$e"]);
        }
    }
    public function removeItemPedido($numpedido, $coditem){
        try{
        DB::select("DELETE FROM PEDIDO_ITENS WHERE NUMPEDIDO = $numpedido AND CODITEM = $coditem");
        return response()->json(["message"=>"Item $coditem foi removido com sucesso!"]);
        }catch (Exception $e) {
            return response()->json(['message' => 'Erro ao consultar item.', 'error' => "$e"]);
        }
    }
    public function addItemPedido($numpedido, $coditem){
        try{
        DB::table("Pedido_itens")
        ->insert(["numpedido"=>$numpedido, "coditem"=>$coditem]);
        return response()->json(["message"=>"Item $coditem foi adicionado ao pedido $numpedido com sucesso!"]);
        }catch (Exception $e) {
            return response()->json(['message' => 'Erro ao consultar item.', 'error' => "$e"]);
        }
    }
}
