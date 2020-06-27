<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Produto;

class ProdutoController extends Controller
{
    public function index()    
    {
        $produtos = Produto::all();
        
        return view('admin.produtos.index', compact('produtos'));
    }

    public function adicionar()
    {
        return view('admin.produtos.adicionar');
    }

    public function editar($id)
    {
        $registro = Produto::find($id);

        if(empty($registro->id))
        {
            return redirect()->route('admin.produtos.index');
        }

        return view('admin.produtos.editar', compact('registro'));
    }

    public function salvar(Request $request)
    {
        $dados = $request->all();
        
        Produto::create($dados);

        $request->session()
                ->flash('admin-mensagem-sucesso',
                'Produto cadastrado com sucesso');

        return redirect()->route('admin.produtos.index');
    }

    public function atualizar(Request $request, $id)
    {
        $dados = $request->all();

        Produto::find($id)->update($dados);
        $request->session()
            ->flash('admin-mensagem-sucesso',
            'Produto atualizado com sucesso');

        return redirect()->route('admin.produtos.index');             
    }

    public function deletar(Request $request, $id)
    {
        Produto::find($id)->delete();
        $request->session()
            ->flash('admin-mensagem-sucesso',
            'Produto atualizado com sucesso');

        return redirect()->route('admin.produtos.index');             
    }
}
