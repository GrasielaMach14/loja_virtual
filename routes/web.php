<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', 'HomeController@index')->name('index');
Route::get('/produto/{id}', 'HomeController@produto')->name('produto');
Route::get('/carrinho', 'CarrinhoController@index')->name('carrinho.index');
Route::get('/carrinho/adicionar', function() {
    return redirect()->route('index');
});
//Rotas para Carrinho de compras
Route::post('/carrinho/adicionar', 'CarrinhoController@adicionar')->name('carrinho.adicionar');
Route::delete('/carrinho/remover', 'CarrinhoController@remover')->name('carrinho.remover');
Route::get('/carrinho/compras', 'CarrinhoController@compras')->name('carrinho.compras');
Route::post('/carrinho/concluir', 'CarrinhoController@concluir')->name('carrinho.concluir');
Route::post('/carrinho/cancelar', 'CarrinhoController@cancelar')->name('carrinho.cancelar');
Route::post('/carrinho/desconto', 'CarrinhoController@desconto')->name('carrinho.desconto');

//Rotas do admin
Route::group(['prefix' => 'admin'], function(){
    Route::get('produtos', 'Admin\ProdutoController@index')->name('admin.produtos.index');
    Route::get('produtos/adicionar', 'Admin\ProdutoController@adicionar')
    ->name('admin.produtos.adicionar');
    Route::post('produtos/salvar', 'Admin\ProdutoController@salvar')
    ->name('admin.produtos.salvar');
    Route::get('produtos/editar/{id}', 'Admin\ProdutoController@editar')
    ->name('admin.produtos.editar');
    Route::put('produtos/atualizar/{id}', 'Admin\ProdutoController@atualizar')
    ->name('admin.produtos.atualizar');
    Route::get('produtos/deletar/{id}', 'Admin\ProdutoController@deletar')
    ->name('admin.produtos.deletar');

    Route::get('cupons', 'Admin\CupomDescontoController@index')->name('admin.cupons');
    Route::get('cupons/adicionar', 'Admin\CupomDescontoController@adicionar')
    ->name('admin.cupons.adicionar');
    Route::post('cupons/salvar', 'Admin\CupomDescontoController@salvar')
    ->name('admin.cupons.salvar');
    Route::get('cupons/editar/{id}', 'Admin\CupomDescontoController@editar')
    ->name('admin.cupons.editar');
    Route::put('cupons/atualizar/{id}', 'Admin\CupomDescontoController@atualizar')
    ->name('admin.cupons.atualizar');
    Route::get('cupons/deletar/{id}', 'Admin\CupomDescontoController@deletar')
    ->name('admin.cupons.deletar');
});
