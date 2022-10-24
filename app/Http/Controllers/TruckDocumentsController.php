<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\TruckDocuments;
use App\Trucks;
use App\transporter;
use App\truckType;
use Auth;
use App\drivers;

class TruckDocumentsController extends Controller
{
    public function verifyTruckDocuments(Request $request) {
        $phoneNo =  $request->phoneNo;
        $truckNo = $request->truckNo;
        $checkTruck = Trucks::WHERE('truck_no', $truckNo)->FIRST();
        $driversInfo = drivers::WHERE('driver_phone_number', $phoneNo)->FIRST();
        if(isset($driversInfo) && $driversInfo->licence_no == "") {
            return $this->truckDocumentChecklist($truckNo, $phoneNo, $checkTruck, $driversInfo, 'uploadDocuments');
        }
        else if(!isset($driversInfo)) {
            return $this->truckDocumentChecklist($truckNo, $phoneNo, $checkTruck, $driversInfo, 'notFound');
        }
    
        if($checkTruck) {
            $truckDocument = TruckDocuments::WHERE('truck_id', $checkTruck->id)->exists();
            if($truckDocument) {
                return $this->truckDocumentChecklist($truckNo, $phoneNo, $checkTruck, $driversInfo, 'allSet');
            }
            else{
                return $this->truckDocumentChecklist($truckNo, $phoneNo, $checkTruck, $driversInfo, 'uploadDocuments');
            }
        }
        else {
            return $this->truckDocumentChecklist($truckNo, $phoneNo, $checkTruck, $driversInfo, 'notFound');
        }
    }

    public function uploadTruckDocuments(Request $request) {
        $transporter_id = $request->transporter;
        $driverName = $request->driverName;
        $truckNo = strtoupper($request->truckNo);
        $phoneNo = $request->driverPhoneNo;
        $truckInfo = Trucks::WHERE('truck_no', $request->truckNo)->FIRST();
        $driverInfo = drivers::WHERE('driver_phone_number', $phoneNo)->FIRST();
        $driversLicence = $request->file("driversLicence");
        $driversLicenseExpiry = $request->driversLicenseExpiry;
        $truck_type_id = $request->truckType;
        
        if(!$truckInfo) {
            //create the new truckInstance and save the
            $newTruck = trucks::CREATE([
                'truck_no' => $truckNo,
                'truck_type_id' => $truck_type_id,
                'transporter_id' => $transporter_id,
            ]);
            $truckId = $newTruck->id;
            $truckInfo = $newTruck;
        }
        if(!isset($driverInfo)) {
            //create new instance of the driver
            $newDriver = drivers::CREATE([
                'driver_first_name' => $driverName,
                'license_expiry' => $driversLicenseExpiry,
                'driver_phone_number' => $phoneNo,
            ]);
            $driverLicense = $this->imageUploader($driversLicence, "drivers-licence-".$newDriver->id, "licence-no");
            $newDriver->licence_no = $driverLicense;
            $newDriver->save();
            $driverInfo = $newDriver;

        }
        else {
            if(isset($driverInfo)) {
                $driverLicense = $this->imageUploader($driversLicence, "drivers-licence-".$driverInfo->id, "licence-no");
                $driverInfo->licence_no = $driverLicense;
                $driverInfo->license_expiry = $driversLicenseExpiry;
                $driverInfo->save();
            }
        }
        
        $documents = $request->file('documents');
        if(isset($documents)) {
            $vehicleLicence = $this->imageUploader($documents[0], "vehicle-licence-".$truckId, "vehicle-licence");
            $roadworthiness = $this->imageUploader($documents[1], "roadworthiness-".$truckId, "roadworthiness");
            $insurance = $this->imageUploader($documents[2], "vehicle-insurance-".$truckId, "vehicle-insurance");
            $proofOfOwnership = $this->imageUploader($documents[3], "proof-of-ownership-".$truckId, "proof-of-ownership");

            $truckDoc = TruckDocuments::firstOrNew(['truck_id' => $truckId]);
            $truckDoc->uploaded_by = Auth::user()->id;
            $truckDoc->vehicle_licence = $vehicleLicence;
            $truckDoc->roadworthiness = $roadworthiness;
            $truckDoc->insurance = $insurance;
            $truckDoc->proof_of_ownership = $proofOfOwnership;

            $truckDoc->vehicle_licence_expiry = $request->vehicle_licence_expiry;
            $truckDoc->roadworthiness_expiry = $request->roadworthiness_expiry;
            $truckDoc->insurance_expiry = $request->insurance_expiry;
            $truckDoc->poo_expiry = $request->poo_expiry;
            $truckDoc->save();
        }
        return $this->truckDocumentChecklist($truckNo, $phoneNo, $truckInfo, $driverInfo, 'allSet');
    }

    public function imageUploader($file, $pathName, $folder) {
        if(isset($file) && $file != '') {
            $name = $pathName.'.'.$file->getClientOriginalExtension();
            $destination_path = public_path('assets/img/truckdocuments/'.$folder.'');
            $waybillPath = $destination_path."/".$name;
            $file->move($destination_path, $name);
            return $name;
        }
    }

    public function truckDocumentChecklist($truckNo, $dno, $truckInfo, $driverInfo, $status) {
        $currentDate = Date("Y-m-d");
        $truckTypes = truckType::SELECT('id', 'truck_type', 'tonnage')->ORDERBY('truck_type', 'ASC')->GET();
        $transporters = transporter::SELECT('id', 'transporter_name')->WHERE('transporter_status', TRUE)->ORDERBY('transporter_name', 'ASC')->GET();
            $tuckDocInfo = '';
            $truckId = '';
            $vehicleLicenceExpiry = '';
            $roadworthinessExpiry = '';
            $proofOfOwnershipExpiry = '';
            $insuranceExpiry = '';
            $truckId = $truckInfo ? $truckInfo->id : '' ;
            if(isset($truckInfo)) {
                $truckDocInfo = TruckDocuments::WHERE('truck_id', $truckInfo->id)->FIRST();
                if(isset($truckDocInfo)) {
                    $vehicleLicenceExpiry = $truckDocInfo->vehicle_licence_expiry;
                    $roadworthinessExpiry = $truckDocInfo->roadworthiness_expiry;
                    $insuranceExpiry = $truckDocInfo->insurance_expiry;
                    $proofOfOwnershipExpiry = $truckDocInfo->poo_expiry;
                }
            }
            
            

        if(isset($driverInfo->driver_first_name)){ 
            $driverName_ = $driverInfo->driver_first_name.' '.$driverInfo->driver_last_name;
        } else {
            $driverName_ = '';
        }

        if($status == "allSet") {
            $truckTypeInfo = truckType::findOrFail($truckInfo->truck_type_id);
            $response ='
                <input type="hidden" id="cTonnage" value="'.$truckTypeInfo->tonnage.'" />
                <input type="hidden" id="cDriverName" value="'.$driverInfo->driver_first_name.'" />
                <input type="hidden" id="cTruckType" value="'.$truckTypeInfo->truck_type.'" />
                <input type="hidden" id="cTransporterId" value="'.$truckInfo->transporter_id.'" />
                <input type="hidden" id="cDriverId" value="'.$driverInfo->id.'" />
            ';
        }
        else {
            $response = '';
        }

        $response.= '
        <input type="hidden" name="truckNo" id="ctruckNo" value="'.strtoupper($truckNo).'" />
        <input type="hidden" name="driverPhoneNo" id="cdriverNo" value="'.$dno.'" />
        <input type="hidden" name="truckId" value="'.$truckId.'" id="ctruckId" />
        <input type="hidden" id="cStatus" value="'.$status.'" />

        <div class="row" id="mainContent">';
            $response.='
            <div class="col-md-12 mb-2">';
                if($status == "allSet") {
                    $response.='<span id="proceedToAvailability" class="btn btn-success mt-3 font-weight-bold">ALL SET. PROCEED TO AVAILABILITY</span>';
                }
            $response.='</div>';

            $response.='
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h5 class="card-title">Truck Info</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Transporter</label>
                            <select type="text" class="form-control" name="transporter" id="transporter">
                                <option value="">Choose Transporter</option>';
                                foreach($transporters as $transporter) {
                                    if(isset($truckInfo) && $truckInfo->transporter_id == $transporter->id) {
                                        $response.='<option value="'.$truckInfo->transporter_id.'" selected>'.$transporter->transporter_name.'</option>';
                                    }
                                    else{
                                    $response.='<option value="'.$transporter->id.'">'.$transporter->transporter_name.'</option>';
                                    }
                                }
                            $response.='</select>
                        </div>

                        <div class="form-group">
                            <label>Truck Type *</label>
                            <select type="text" class="form-control" name="truckType" id="truckType">
                                <option value="">Choose Truck Type</option>';
                                foreach($truckTypes as $truckType) {
                                    if(isset($truckInfo) && $truckInfo->truck_type_id == $truckType->id) {
                                        $response.='<option data-tt="'. $truckType->truck_type.' ('.intval($truckType->tonnage)/1000 .'T)" value="'.$truckType->id.'" selected>'. $truckType->truck_type.' ('.intval($truckType->tonnage)/1000 .'T)</option>';
                                    }
                                    else {
                                        $response.='<option data-tt="'. $truckType->truck_type.' ('.intval($truckType->tonnage)/1000 .'T)" value="'.$truckType->id.'">'. $truckType->truck_type.' ('.intval($truckType->tonnage)/1000 .'T)</option>';
                                    }
                                }
                            $response.='</select>
                        </div>
                        <h5 class="card-title">Driver\'s Info</h5>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>Driver\'s Name</label>
                                <input type="text" class="form-control" id="driverName" name="driverName" value="'.$driverName_.'" />
                            </div>
                            
                            <div class="form-group col-md-6">
                                <label>Driver\'s License</label>';
                                 if(isset($driverInfo->licence_no) && $this->countDownExpiry($driverInfo->license_expiry) >= 0) {
                                    $response.='<a href="assets/img/truckdocuments/licence-no/'.$driverInfo->licence_no.'" target="_blank" class="d-block">
                                        <i class="icon-attachment2 mr-2"></i>Preview Drivers license
                                    </a>';
                                }
                                else{
                                 $response.='<input type="file" name="driversLicence" id="driverLicense" />';
                                }

                                if(isset($driverInfo)) {
                                   $licenceExpiry = $driverInfo->license_expiry;
                                }
                                else {
                                    $licenceExpiry = '';
                                }

                            $response.='</div>
                            
                            <div class="form-group col-md-6">
                                <label>Driver\'s license Expiry Date</label>
                                <input type="date" min="'.$currentDate.'" class="form-control" name="driversLicenseExpiry" id="licenseExpiry" value="'.$licenceExpiry.'" />
                            </div>
                        </div>';
                    $response.='
                    </div>
                </div>
            </div>';


            $response.='
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h5 class="card-title">Truck Documents</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group  col-md-6 col-xs-6" >
                                <label>Vehicle licence *</label>';
                                if(isset($truckDocInfo->vehicle_licence_expiry) && $this->countDownExpiry($truckDocInfo->vehicle_licence_expiry) >= 0) {
                                    $response.='<a href="assets/img/truckdocuments/vehicle-licence/'.$truckDocInfo->vehicle_licence.'" target="_blank" class="d-block">
                                        <i class="icon-attachment2 mr-2"></i>Preview Vehicle License
                                    </a>';
                                }
                                else{
                                    $response.='<input type="file" id="vehicleLicence" name="documents[]">';
                                }
                            $response.='</div>
                            <div class="form-group  col-md-6 col-xs-6">
                                <label>Date of Expiry *</label>
                                <input type="date" min="'.$currentDate.'" class="form-control" name="vehicle_licence_expiry" id="vehicleLicenceExpiry" value="'.$vehicleLicenceExpiry.'">
                            </div>

                            <div class="form-group col-md-6">
                                <label>Roadworthiness *</label>';
                                if(isset($truckDocInfo->roadworthiness_expiry) && $this->countDownExpiry($truckDocInfo->roadworthiness_expiry) >= 0) {
                                    $response.='<a href="assets/img/truckdocuments/roadworthiness/'.$truckDocInfo->roadworthiness.'" target="_blank" class="d-block">
                                        <i class="icon-attachment2 mr-2"></i>Preview Roadworthiness
                                    </a>';
                                }
                                else{
                                    $response.='<input type="file" id="roadworthiness" name="documents[]">';
                                }
                            $response.='</div>

                            <div class="form-group  col-md-6 col-xs-6">
                                <label>Date of Expiry *</label>
                                <input type="date" min="'.$currentDate.'" class="form-control" name="roadworthiness_expiry" id="roadworthinessExpiry"  value="'.$roadworthinessExpiry.'">
                            </div>

                            <div class="form-group col-md-6">
                                <label>Insurance *</label>';
                                if(isset($truckDocInfo->insurance_expiry) && $this->countDownExpiry($truckDocInfo->insurance_expiry) >= 0) {
                                    $response.='<a href="assets/img/truckdocuments/vehicle-insurance/'.$truckDocInfo->insurance.'" target="_blank" class="d-block">
                                        <i class="icon-attachment2 mr-2"></i>Preview Vehicle Insurance
                                    </a>';
                                }
                                else{
                                    $response.='<input type="file" id="insurance" name="documents[]">';
                                }
                            $response.='</div>

                            <div class="form-group  col-md-6 col-xs-6">
                                <label>Date of Expiry *</label>
                                <input type="date" min="'.$currentDate.'" class="form-control" name="insurance_expiry" id="insuranceExpiry"  value="'.$insuranceExpiry.'">
                            </div>

                            <div class="form-group col-md-12">
                                <label>Proof of Ownership *</label>';
                                if(isset($truckDocInfo->proof_of_ownership)) {
                                    $response.='<a href="assets/img/truckdocuments/proof-of-ownership/'.$truckDocInfo->proof_of_ownership.'" target="_blank" class="d-block">
                                        <i class="icon-attachment2 mr-2"></i>Preview Proof of Ownership.
                                    </a>';
                                }
                                else{
                                    $response.='<input type="file" id="proofOfOwnership" name="documents[]">';
                                }
                                
                            $response.='</div>
                            
                            <div class="text-right">
                                <span id="loader"></span>';
                                    if($status != "allSet") {
                                        $response.='
                                        <button type="submit" class="btn btn-primary" id="uploadTruckDocuments" >Update Documents 
                                            <i class="icon-paperplane ml-2"></i>
                                        </button>';
                                    }
                            $response.='
                            </div>
                        </div>
                    </div>
                </div>
            </div>';

        $response.='</div>';

        return $response;

    }

    public function countDownExpiry($expectedDateOfExpiry) {
        $now = time(); 
        $your_date = strtotime($expectedDateOfExpiry);
        $datediff = $now - $your_date;
        $noOfDays = round($datediff / (60 * 60 * 24)) * -1;
        return $noOfDays;
    }
}
