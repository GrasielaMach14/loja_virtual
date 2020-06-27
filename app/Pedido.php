<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = [
        'user_id', 
        'status'
    ];
    
    public function pedido_produtos()
    {
        return $this->hasMany(PedidoProduto::class)
                ->select(DB::raw('produto_id, sum(desconto) as descontos, sum(valor) as valores, 
                count(1) as qtd'))
                ->groupBy('produto_id')
                ->orderBy('produto_id', 'desc');
    }

    public function pedido_produtos_item()
    {
        return $this->hasMany(PedidoProduto::class);
    }

    public static function consultaId($where)
    {
        $pedido = self::where($where)->first(['id']);

        return !empty($pedido->id)? $pedido->id : null;
    }
}
