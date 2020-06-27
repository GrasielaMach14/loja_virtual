<?php

namespace App\Http\Controllers;

use App\CupomDesconto;
use App\Pedido;
use App\PedidoProduto;
use App\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarrinhoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $pedidos = Pedido::where([
            'status' => 'RE',
            'user_id' => Auth::id()
        ])->get();

        // dd([
        //     $pedidos,
        //     $pedidos[0]->pedido_produtos,
        //     $pedidos[0]->pedido_produtos[0]->produto
        // ]);

        return view('carrinho.index', compact('pedidos'));
    }

    public function adicionar()
    {
        $this->middleware('VerifyCsrfToken');

        $request = Request();        
        $idproduto = $request->input('id');
        
        $produto = Produto::find($idproduto);

        if(empty($produto->id))
        {
            $request->session()
                ->flash('mensagem-falha',
                'Produto não encontrado na loja.');

            return redirect()->route('carrinho.index');
        }

        $idusuario = Auth::id();

        $idpedido = Pedido::consultaId([
            'user_id' => $idusuario,
            'status' => 'RE'
        ]);

        if(empty($idpedido))
        {
            $pedido_novo = Pedido::create([
                'user_id' => $idusuario,
                'status' => 'RE'
            ]);

            $idpedido = $pedido_novo->id;
        }

        PedidoProduto::create([
            'pedido_id' => $idpedido,
            'produto_id' => $idproduto,
            'valor' => $produto->valor,
            'status' => 'RE'
        ]);

        $request->session()
                ->flash('mensagem-sucesso',
                'Produto adicionado ao carrinho com sucesso.');

        return redirect()->route('carrinho.index');
            
    }

    public function remover()
    {
        $this->middleware('VerifyCsrfToken');

        $request = Request();        
        $idpedido = $request->input('pedido_id');
        $idproduto = $request->input('produto_id');
        $remove_apenas_item = (boolean)$request->input('item'); #Recebe como boolean e verifica se input tem apenas um item(true) senão removera todos os itens vinculados a este produto
        $idusuario = Auth::id();

        $idpedido = Pedido::consultaId([
            'id' => $idpedido,
            'user_id' => $idusuario,
            'status' => 'RE'
        ]);

        if(empty($idpedido))
        {
            $request->session()
                ->flash('mensagem-falha',
                'Pedido não encontrado.');

            return redirect()->route('carrinho.index');
        }
        #Este objeto vai receber como arrays o pedido com dados do usuário e produto
        $where_produto = [
            'pedido_id' => $idpedido,
            'produto_id' => $idproduto
        ];
        #Através do método where vai ordenar os IDs em decrescente pra remomer o último add na lista
        $produto = PedidoProduto::where($where_produto)
                                ->orderBy('id', 'desc')
                                ->first();

        if(empty($produto->id))
        {
            $request->session()
                ->flash('mensagem-falha',
                'Produto não encontrado no carrinho.');

            return redirect()->route('carrinho.index');
        }                                
        #Se há algum item removerá o primeiro produto a partir do ID
        if($remove_apenas_item) 
        {
            $where_produto['id'] = $produto->id;
        }

        PedidoProduto::where($where_produto)->delete();
        #O objeto abaixo verifica se existe mais algum outro item vinculado a este pedido
        $check_pedido = PedidoProduto::where([ 
            'pedido_id' => $produto->pedido_id
        ])->exists();
        #Na condição abaixo será removido também o pedido vinculado ao carrinho de compras
        if(!$check_pedido)
        {
            Pedido::where([
                'id' => $produto->pedido_id
            ])->delete();
        }

        $request->session()
                ->flash('mensagem-sucesso',
                'Produto removido do carrinho com sucesso.');

        return redirect()->route('carrinho.index');
    }

    public function concluir()
    {
        $this->middleware('VerifyCsrfToken');

        $request = Request();        
        $idpedido = $request->input('pedido_id');
        $idusuario = Auth::id();

        $check_pedido = Pedido::where([
            'id' => $idpedido,
            'user_id' => $idusuario,
            'status' => 'RE' 
        ])->exists();

        if(!$check_pedido)
        {
            $request->session()
                ->flash('mensagem-falha',
                'Pedido não encontrado.');

            return redirect()->route('carrinho.index');
        }

        $check_produtos = PedidoProduto::where([
            'pedido_id' => $idpedido
        ])->exists();

        if(!$check_produtos)
        {
            $request->session()
                ->flash('mensagem-falha',
                'Produtos do pedido não encontrados.');

            return redirect()->route('carrinho.index');
        }

        PedidoProduto::where([
            'pedido_id' => $idpedido
            ])->update([
                'status' => 'PA'
            ]);
        Pedido::where([
            'id' => $idpedido
            ])->update([
                'status' => 'PA'
            ]);                

        $request->session()
        ->flash('mensagem-sucesso',
        'Compra concluída com sucesso.');

        return redirect()->route('carrinho.compras');
    }

    public function compras()
    {
        $compras = Pedido::where([
            'status' => 'PA',
            'user_id' => Auth::id()
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $cancelados = Pedido::where([
            'status' => 'CA',
            'user_id' => Auth::id()
            ])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('carrinho.compras', compact('compras', 'cancelados'));             
    }

    public function cancelar()
    {
        $this->middleware('VerifyCsrfToken');

        $request = Request();
        $idpedido       = $request->input('pedido_id');
        $idspedido_prod = $request->input('id');
        $idusuario      = Auth::id();

        if( empty($idspedido_prod) ) {
            $request->session()->flash('mensagem-falha', 'Nenhum item selecionado para cancelamento!');
            return redirect()->route('carrinho.compras');
        }

        $check_pedido = Pedido::where([
            'id'      => $idpedido,
            'user_id' => $idusuario,
            'status'  => 'PA' // Pago
            ])->exists();

        if( !$check_pedido ) {
            $request->session()->flash('mensagem-falha', 'Pedido não encontrado para cancelamento!');
            return redirect()->route('carrinho.compras');
        }

        $check_produtos = PedidoProduto::where([
                'pedido_id' => $idpedido,
                'status'    => 'PA'
            ])->whereIn('id', $idspedido_prod)->exists();

        if( !$check_produtos ) {
            $request->session()->flash('mensagem-falha', 'Produtos do pedido não encontrados!');
            return redirect()->route('carrinho.compras');
        }

        PedidoProduto::where([
                'pedido_id' => $idpedido,
                'status'    => 'PA'
            ])->whereIn('id', $idspedido_prod)->update([
                'status' => 'CA'
            ]);

        $check_pedido_cancel = PedidoProduto::where([
                'pedido_id' => $idpedido,
                'status'    => 'PA'
            ])->exists();

        if( !$check_pedido_cancel ) {
            Pedido::where([
                'id' => $idpedido
            ])->update([
                'status' => 'CA'
            ]);

            $request->session()->flash('mensagem-sucesso', 'Compra cancelada com sucesso!');

        } else {
            $request->session()->flash('mensagem-sucesso', 'Item(ns) da compra cancelado(s) com sucesso!');
        }

        return redirect()->route('carrinho.compras');
    }

    public function desconto()
    {       

        $this->middleware('VerifyCsrfToken');

        $request = Request();
        $idpedido  = $request->input('pedido_id');
        $cupom     = $request->input('cupom');
        $idusuario = Auth::id();

        if( empty($cupom) ) {
            $request->session()
                    ->flash(
                        'mensagem-falha', 'Cupom inválido!'
                    );

            return redirect()->route('carrinho.index');
        }

        $cupom = CupomDesconto::where([
            'localizador' => $cupom,
            'ativo'       => 'S'
            ])->where(
                'dthr_validade', '>', date('Y-m-d H:i:s')
            )->first();

        if( empty($cupom->id) ) {
            $request->session()
                    ->flash('mensagem-falha', 'Cupom de desconto não encontrado!'
                );

            return redirect()->route('carrinho.index');
        }

        $check_pedido = Pedido::where([
            'id'      => $idpedido,
            'user_id' => $idusuario,
            'status'  => 'RE' // Reservado
            ])->exists();

        if( !$check_pedido ) {
            $request->session()
            ->flash(
                'mensagem-falha', 'Pedido não encontrado para validação!'
            );

            return redirect()->route('carrinho.index');
        }

        $pedido_produtos = PedidoProduto::where([
                'pedido_id' => $idpedido,
                'status'    => 'RE'
            ])->get();

        if( empty($pedido_produtos) ) {
            $request->session()
                    ->flash(
                    'mensagem-falha', 'Produtos do pedido não encontrados!'
                );

            return redirect()->route('carrinho.index');
        }

        $aplicou_desconto = false;
        foreach ($pedido_produtos as $pedido_produto) {

            switch ($cupom->modo_desconto) {
                case 'porc':
                    $valor_desconto = ( $pedido_produto->valor * $cupom->desconto ) / 100;
                    break;

                default:
                    $valor_desconto = $cupom->desconto;
                    break;
            }

            $valor_desconto = ($valor_desconto > $pedido_produto->valor) ? $pedido_produto->valor : number_format($valor_desconto, 2);

            switch ($cupom->modo_limite) {
                case 'qtd':
                    $qtd_pedido = PedidoProduto::whereIn(
                                'status', ['PA', 'RE']
                                )->where([
                                    'cupom_desconto_id' => $cupom->id
                                ])->count();

                    if( $qtd_pedido >= $cupom->limite ) {
                        continue;
                    }
                    break;

                default:
                    $valor_ckc_descontos = PedidoProduto::whereIn(
                                        'status', ['PA', 'RE']
                                        )->where([
                                            'cupom_desconto_id' => $cupom->id
                                        ])->sum('desconto');

                    if( ($valor_ckc_descontos+$valor_desconto) > $cupom->limite ) {
                        continue;
                    }
                    break;
            }

            $pedido_produto->cupom_desconto_id = $cupom->id;
            $pedido_produto->desconto          = $valor_desconto;
            $pedido_produto->update();

            $aplicou_desconto = true;

        }

        if( $aplicou_desconto ) {
            $request->session()
                    ->flash(
                        'mensagem-sucesso', 'Cupom aplicado com sucesso!'
                    );
        } else {
            $request->session()
                        ->flash(
                        'mensagem-falha', 'Cupom esgotado!'
                    );
        }
        
        return redirect()->route('carrinho.index');

    }
}
