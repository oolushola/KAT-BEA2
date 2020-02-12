<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    protected $table = 'tbl_kaya_products';
    protected $fillable = [
        'product_code',
        'product'
    ];
}
