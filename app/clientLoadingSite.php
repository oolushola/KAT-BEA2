<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class clientLoadingSite extends Model
{
    protected $table = 'tbl_kaya_client_loading_sites';
    protected $fillable = [
        'client_id',
        'state_id',
        'loading_site_id'
    ];
}
