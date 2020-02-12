<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class transporterDocuments extends Model
{
    protected $table = 'tbl_kaya_transporter_documents';
    protected $fillable = [
        'transporter_id',
        'document',
        'description'
    ];
}
