<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class clientProduct extends Model
{
    protected $table = 'tbl_kaya_client_products';
    protected $fillable = [
        'client_id',
        'product_id'
    ];
}
