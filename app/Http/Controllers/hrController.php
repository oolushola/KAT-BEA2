<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\StaffEducation;
use App\StaffExperiences;
use App\StaffDependants;
use App\StaffExtras;
use App\PrsSession;
use Illuminate\Support\Facades\DB;


class hrController extends Controller
{
    public function dashboard() {
        $staffs = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.prs_starts, prs_ends FROM users a LEFT JOIN tbl_kaya_prs_sessions b ON a.id = b.user_id AND prs_starts = TRUE AND prs_ends = FALSE WHERE status = TRUE AND role_id != 7 AND email != "timi@kayaafrica.co" ORDER BY first_name ASC '
            )
        );
        return view('hr.dashboard', compact('staffs', 'prsSessions'));
    }

    public function displayBiodata(Request $request) {
        $user = User::findOrFail($request->user_id);
        $userEducation = StaffEducation::WHERE('user_id', $user->id)->GET();
        $userExperiences = StaffExperiences::WHERE('user_id', $user->id)->GET();
        $userDependants = StaffDependants::WHERE('user_id', $user->id)->GET();
        $additionalInfos = StaffExtras::WHERE('user_id', $user->id)->GET();

        $response ='<table class="table  table-condensed">
            
            <tbody>
                <tr class="table-success">
                    <td>
                        <span class="font-weight-semibold text-primary">'.$user->current_post_held.'</span>
                        <h3 class="m-0 font-weight-bold">'.ucwords($user->first_name).', '.ucwords($user->last_name).'</h3>
                        <span class="text-danger font-weight-semibold">'.$user->phone_no.', '.$user->email.'</span>
                    </td>
                    <td>
                        <span class="font-weight-semibold text-primary">'.$user->bank_name.'</span>
                        <h3>Account No: '.$user->account_no.'</h3>
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="table  table-bordered">
            <thead class="table-info">
                <th width="50%">School</th>
                <th>Duration</th>
            </thead>
            <tbody>';
                if(count($userEducation)) {
                    foreach($userEducation as $education) {
                        $response.='<tr class="table-success">
                            <td>
                                <span class="font-weight-semibold text-primary">'.$education->qualification_obtained.'</span>
                                <p class="m-0 font-weight-bold font-size-sm">'.ucwords($education->school_name).'</p>
                            </td>
                            <td class="font-size-xs">
                                <span>From: '.date("d/m/Y", strtotime($education->sch_start_from)).'</span>
                                <span>To: '.date("d/m/Y", strtotime($education->sch_end)).'</span>
                            </td>
                        </tr>';
                    }
                }
                else {
                $response.='<tr>
                    <td colspan="3">Education has not been added</td>
                </tr>';
                }
            $response.='</tbody>
        </table>';

        $response.='<table class="table  table-bordered">
            <thead class="table-info">
                <th width="50%">Company Name</th>
                <th>Duration</th>
            </thead>
            <tbody>';
                if(count($userExperiences)) {
                    foreach($userExperiences as $experience) {
                    $response.='<tr class="table-success">
                        <td>
                            <span class="font-weight-semibold text-primary">'.$experience->position_held.'</span>
                            <p class="m-0 font-weight-bold font-size-sm">'.ucwords($experience->company_name).'</p>
                        </td>
                        <td class="font-size-xs">
                            <span>From: '.date("d/m/Y", strtotime($experience->company_from)).'</span>
                            <span>To: '.date("d/m/Y", strtotime($experience->company_to)).'</span>
                        </td>
                    </tr>';
                    }
                }
                else {
                $response.='<tr>
                    <td colspan="3">Education has not been added</td>
                </tr>';
                }
            $response.='</tbody>
        </table>';

        if(count($userDependants)) {
            $response.='<h5 class="font-size-sm font-weight-bold mt-2 mb-2">DEPENDANTS</h5>';
            $response.='<div class="row">';
                foreach($userDependants as $dependant) {
                    $response.='<div class="col-md-4 col-sm-6">
                        <span class="font-weight-semibold text-primary">'.$dependant->dependant_type.'</span>
                        <h3 class="m-0 font-weight-bold">'.ucwords($dependant->dependant_full_name).'</h3>
                        <span class="text-danger font-weight-semibold">Date of Birth: '.$dependant->dependant_dob.'</span>
                    </div>';
                }
            $response.='</div>';

        }
        else {
            $response.='There are no dependants available';
        }       
        
        $response.='<table class="table  table-bordered mt-2">
            <thead class="table-info">
                <th>Guarantor</th>
                <th>Next of Kin</th>
            </thead>
            <tbody>';
            if(count($additionalInfos)) {
                foreach($additionalInfos as $extra) {
                $response.='<tr class="table-success">
                    <td>
                        <span class="font-weight-semibold text-primary">'.$extra->guarantor_phone_no.'</span>
                        <p class="m-0 font-weight-bold font-size-sm">'.ucwords($extra->guarantor_full_name).'</p>
                    </td>
                    <td>
                        <span class="font-weight-semibold text-primary">'.$extra->nok_phone_no.'</span>
                        <p class="m-0 font-weight-bold font-size-sm">'.ucwords($extra->nok_full_name).'</p>
                    </td>
                </tr>';
                }
            }
            else {
                $response.='<tr>
                    <td colspan="3">Additional information has not been added</td>
                </tr>';
            }
            $response.='</tbody>
        </table>';

        return $response;
    }
}
