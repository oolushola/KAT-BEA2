<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrsSession extends Model
{
    protected $table = 'tbl_kaya_prs_sessions';
    protected $fillable = [
        'user_id',
        'position_held',
        'prs_starts',
        'prs_ends',
        'promoted_to'
    ];
}
