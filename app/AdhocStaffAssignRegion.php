<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdhocStaffAssignRegion extends Model
{
    protected $table = 'tbl_kaya_adhoc_staff_assign_regions';
    protected $fillable = [
        'user_id',
        'regional_state_id',
        'exact_location'
    ];
}
