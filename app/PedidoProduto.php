<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PedidoProduto extends Model
{
    protected $fillable = [
        'pedido_id',
        'produto_id',
        'status',
        'valor'
    ];
    
    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id', 'id');
    }
}
