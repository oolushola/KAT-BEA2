<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class parentClient extends Model
{
    protected $table = "tbl_kaya_parent_clients";
    protected $fillable = [
        'parent_name'
    ];
}
