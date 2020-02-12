<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class loadingSite extends Model
{
    protected $table = 'tbl_kaya_loading_sites';
    protected $fillable = [
        'state_domiciled',
        'loading_site_code',
        'loading_site',
        'address'
    ];
}
