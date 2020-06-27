<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\CupomDesconto;

class CupomDescontoController extends Controller
{
    public function index()
    {
        $cupons = CupomDesconto::all();

        return view('admin.cupom_desconto.index', compact('cupons'));
    }

    public function adicionar()
    {
        return view('admin.cupom_desconto.adicionar');
    }

    public function editar($id)
    {
        $registro = CupomDesconto::find($id);
        
        if( empty($registro->id) ) {
        
            return redirect()->route('admin.cupons');
        }
        
        return view('admin.cupom_desconto.editar', compact('registro'));
    }

    public function salvar(Request $request)
    {
        $dados = $request->all();

        CupomDesconto::create($dados);

        $request->session()
                ->flash(
                    'admin-mensagem-sucesso', 'Cupom de desconto criado com sucesso!'
                );

        return redirect()->route('admin.cupons');
    }

    public function atualizar(Request $request, $id)
    {
        $dados = $request->all();

        CupomDesconto::find($id)->update($dados);

        $request->session()
                    ->flash(
                        'admin-mensagem-sucesso', 'Cupom de desconto atualizado com sucesso!'
                    );

        return redirect()->route('admin.cupons');
    }

    public function deletar(Request $request, $id)
    {

        CupomDesconto::find($id)->delete();

        $request->session()
                ->flash(
                    'admin-mensagem-sucesso', 'Cupom de desconto deletado com sucesso!'
                );

        return redirect()->route('admin.cupons');
    }
}
