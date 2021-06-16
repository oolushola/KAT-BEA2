<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpsLoadingSite extends Model
{
    protected $table = 'tbl_kaya_pair_ops_loading_site';
    protected $fillable = [
        'loading_site_id',
        'user_id'
    ];
}
