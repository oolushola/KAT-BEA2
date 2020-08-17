<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IssueType extends Model
{
    protected $table = 'tbl_kaya_issue_types';
    protected $fillable = [
        'issue_category', 'issue_type', 'description'
    ];
}
