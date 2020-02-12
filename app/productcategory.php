<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class productcategory extends Model
{
    protected $table = 'tbl_kaya_product_categories';
    protected $fillable =[
        'product_category_code',
        'product_category'
    ];
}
