<?php

// use Illuminate\Http\Request;
// use Illuminate\Routing\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/* ROTAS RELACIONADAS A TABELA CLIENTES */
Route::get('clientes/clientesAll', 'API\ClientesController@index');
Route::get('clientes/list', 'API\ClientesController@list');
Route::post('clientes/create', 'API\ClientesController@create');
Route::post('clientes/updateClient', 'API\ClientesController@updateClient');
Route::post('clientes/updateClientEndereco', 'API\ClientesController@updateClientEndereco');
Route::post('clientes/updateDados', 'API\ClientesController@updateDados');
Route::get('clientes/consultclient/{cpf}', 'API\ClientesController@consultClient');

/* ROTAS RELACIONADAS A TABELA ENTREGAS */
Route::get('entregas/list/{status}', 'API\EntregasController@list');
Route::post('entregas/create', 'API\EntregasController@create');
Route::patch('entregas/deleteLogic/{entrega}', 'API\EntregasController@removeLogic');
Route::put('entregas/finishEntrega/{entrega}', 'API\EntregasController@finishEntrega');
Route::get('entregas/consultarEntrega/{entrega}', 'API\EntregasController@consultEntrega');
Route::get('entregas/totalValor/{entrega}', 'API\EntregasController@totalValor');
Route::get('entregas/entregasReport/{inicio}/{fim}/{status}', 'API\EntregasController@entregasReport');
Route::get('/entregas/entregasPedidosReport/{inicio}/{fim}/{status}', 'API\EntregasController@entregasPedidosReport');
/* ROTAS RELACIONADAS A TABELA DE PEDIDOS*/
Route::get('pedidos/list/{status}', 'API\PedidosController@list');
Route::patch('pedidos/alterarStatus/{numpedido}/{status}', 'API\PedidosController@alterarStatus');
Route::patch('pedidos/cancelarPedido/{numpedido}', 'API\PedidosController@cancelarPedido');
Route::post('pedidos/createPedido', 'API\PedidosController@createPedido');
Route::post('pedidos/associarItem', 'API\PedidosController@associarItem');
Route::post('pedidos/pedidoVendas', 'API\PedidosController@pedidoVendas');
Route::get('pedidos/consultPedidoClient/{cliente}', 'API\PedidosController@consultPedidoClient');
Route::get('pedidos/listaProdutos/{numpedido}', 'API\PedidosController@listaProdutos');
Route::get('pedidos/listaProdutosSoma/{numpedido}', 'API\PedidosController@listaProdutosSoma');
Route::get('pedidos/consultarPedido/{numpedido}', 'API\PedidosController@consultarPedido');
Route::put('pedidos/alterarPedidoDados', 'API\PedidosController@alterarPedidoDados');

/* ROTAS RELACIONADAS A TABELA ITENS */
Route::get('itens/list', 'API\ItensController@list');
Route::post('itens/create', 'API\ItensController@create');
Route::post('itens/updateItem', 'API\ItensController@updateItens');
Route::patch('itens/deleteItem/{id}', 'API\ItensController@deleteLogic');
Route::get('itens/buscarItem/{id}', 'API\ItensController@buscarItem');

/* ROTAS RELACIONADAS A TABELA PEDIDO_ITENS */
Route::patch('pedido_itens/removeItemPedido/{numpedido}/{coditem}', 'API\ItensController@removeItemPedido');
Route::post('pedido_itens/addItemPedido/{numpedido}/{coditem}', 'API\ItensController@addItemPedido');

/* ROSTAS RELACIONADAS A TABELA DE VENDAS */
Route::put('vendas/updateVendas', 'API\VendasController@updateVendas');
Route::get('vendas/relatorioVendas/{inicio}/{fim}/{status}', 'API\VendasController@relatorioVendas');

/* ROTAS RELACIONADAS A TABELA PEDIDOENTREGA */
Route::get('entregas/consultarEntregaPedido/{entrega}', 'API\EntregasController@consultPedidoEntrega');
Route::post('entregas/consultarPedidoentrega', 'API\EntregasController@consultarPedidoentrega');
Route::get('entregas/buscarPedido/{pedido}', 'API\EntregasController@hashPedido');
Route::post('entregas/inserePedidos', 'API\EntregasController@cadastrarPedidos');
Route::delete('entregas/deletePedidoEntrega/{numpedido}/{entrega}', 'API\EntregasController@deletePedidoEntrega');

/*ROTA PARA CONSULTAR DADOS MENSAL DASHBOARD*/
Route::get('Vendas/dadosMensal/{mes}', 'API\VendasController@dadosMensal');
Route::get('Vendas/dadosAnuais/{ano}', 'API\VendasController@dadosAnuais');
