<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TruckDocuments extends Model
{
    protected $table = 'tbl_kaya_truck_documents';
    protected $fillable = [
        "uploaded_by",
        "truck_id",
        "vehicle_licence",
        "vehicle_licence_expiry",
        "roadworthiness",
        "roadworthiness_expiry",
        "insurance",
        "insurance_expiry",
        "proof_of_ownership",
        "poo_expiry",
        "others"
    ];
}
