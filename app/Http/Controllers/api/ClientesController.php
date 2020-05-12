<?php

namespace App\Http\Controllers\API;

use App\Clientes;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientesController extends Controller
{
    public function index()
    {
        $clientes = Clientes::all();
        return response()->json($clientes);
    }
    function list() {
        try {
            $clientes = DB::table('Clientes')
                ->get();

            if (is_null($clientes) || $clientes->count() == 0) {
                return response()->json(['message' => 'Nenhum registro encontrado.']);
            }
            return response()->json($clientes);
        } catch (Exception $e) {
            return response()->json(['message' => 'Não foi possível listar os clientes.', 'error' => "$e"]);
        }
    }
    public function create(Request $request)
    {
        try {
            $clientes = new Clientes;
            $clientes->nome_cliente = $request->nome_cliente;
            $clientes->cnpj = $request->cnpj;
            $clientes->cpf = $request->cpf;  
            $clientes->telefone = $request->telefone;
            $clientes->email = $request->email;
            $clientes->endereco = $request->endereco;
            $clientes->bairro = $request->bairro;
            $clientes->cep = $request->cep;
            $clientes->save();
            $cpf = $request->cpf;
            $cliente = DB::select("SELECT id FROM CLIENTES WHERE CPF = '$cpf'");
            $cliente = $cliente[0]->id;
            return response()->json(['message' => 'Dados cadastrados com sucesso!', 'status' => '1', "id_cliente"=> $cliente]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao cadastrar cliente', 'error' => "$e"]);
        }
    }
    public function consultClient($cpf)
    {
        try {
            $cliente = Clientes::where('cpf', $cpf)->first();
            if (is_null($cliente) || $cliente->count() == 0) {
                return response()->json(['message' => 'Cliente não cadastrado.', 'status' => '1']);
            }
            return response($cliente);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao consultar cliente.', 'error' => "$e"]);
        }
    }
    public function updateClientEndereco(Request $request)
    {
        try {
            $cpf = $request->cpf;
            DB::table('clientes')
                ->where('cpf', $cpf)
                ->update(['bairro' => $request->bairro, 'endereco' => $request->endereco, 'cep' => $request->cep]);
            return response()->json(["message" => "Dados do cliente: $cpf foram atualizados com sucesso!"]);
        } catch (Exception $e) {
            return response()->json(["message" => "$e"]);
        }
    }
    public function updateClient(Request $request)
    {
        try {
            $cpf = $request->cpf;
            DB::table('clientes')
                ->where('cpf', $cpf)
                ->update(['nome_cliente' => $request->nome_cliente, 'telefone' => $request->telefone, 'email' => $request->email]);
            return response()->json(["message" => "Os dados do cliente foram atualizados com sucesso!"]);
        } catch (Exception $e) {
            return response()->json(["message" => "$e"]);
        }
    }
    public function updateDados(Request $request){
        try{
            $cpf = $request->cpf;
            $altera = DB::table('Clientes')
            ->where('cpf', $cpf)
            ->update(['nome_cliente' => $request->nome_cliente, 'telefone' => $request->telefone, 'email' => $request->email,
                        'bairro' => $request->bairro, 'endereco' => $request->endereco, 'cep' => $request->cep]);
                        var_dump($altera);
                        return response()->json(["message" => "Os dados do cliente foram atualizados com sucesso!"]);
            
        }catch(Exception $e){
            return response()->json(["message"=>"Erro ao conectar com o servidor de Banco de Dados.","error" => "$e"]);
        }
    }
}
