@extends('layout')

@section('title')Kaya ::. Transporter @stop

@section('css')
<style type="text/css">
th {
    white-space: nowrap;
}
td{
    white-space: nowrap;
}
</style>

@section('main')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home - Transportation</span> - Transporter</h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calculator text-primary"></i> <span>View Transporters</span></a>
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calendar5 text-primary"></i> <span>Transporter History</span></a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-5">
    &nbsp;

        <!-- Basic layout-->
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">@if(isset($recid)) Update @else Add @endif New Transporter</h5>
                <i class="icon-stack text-danger-600" id="bulkUpload"></i>
                <i class="icon-stack text-primary-600" id="singleUpload" style="display:none"></i>
            </div>

            <ul style="margin:0; padding:0; margin-left:20px;">
                <li class="tabs tabs-default" id="basciInfoTab">Basic Information</li>
                <li class="tabs" id="bankTab">Bank Details</li>
                <li class="tabs" id="guarantorTab">Guarantor </li>
                <li class="tabs" id="nextofkinTab">Next of Kin</li>
            </ul>

            <div class="card-body">
                @if(isset($recid))
                <form name="frmTransporter" id="frmTransporter" enctype="multipart/form-data" method="post" action="{{URL('transporters', $recid->id)}}">
                @else
                <form name="frmTransporter" id="frmTransporter" enctype="multipart/form-data" method="post" action="{{URL('transporters')}}">
                @endif
                    @csrf
                    @if(isset($recid))
                    <input type="hidden" name="id" id="id" value="{{$recid->id}}" />
                    {!! method_field('PATCH') !!}
                    @endif

                    <div id="bulkUploadForm" style="display:none">
                        <div class="form-group">
                            <label>Upload File</label>
                            <input type="file" name="uploadBulkDrivers" title="Upload only CSV File"  />

                        </div>
                        <span id="loader1"></span>
                        <button type="submit" class="btn btn-primary" id="uploadBulkRating">Upload Bulk Rates 
                                <i class="icon-paperplane ml-2"></i>
                        </button>
                    </div>

                    

                    <div id="singleEntryForm">

                        <div class="basicInfoDetails show">
                            <div class="form-group">
                                <label>Transport Name</label>
                                <input type="text" class="form-control" placeholder="Sinotrucks" name="transporter_name" id="transporterName" value="<?php if(isset($recid)){ echo $recid->transporter_name; }  ?>" >
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" class="form-control" placeholder="johndoe@kayaafrica.co" name="email" id="email" value="<?php if(isset($recid)) { echo $recid->email;} ?>">
                            </div>

                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="number" class="form-control" placeholder="+234-***-***-****" name="phone_no" id="phoneNumber" value="<?php if(isset($recid)) { echo $recid->phone_no; } ?>">
                            </div>

                            <div class="form-group">
                                <label>Address</label>
                                <textarea class="form-control" placeholder="23 Babatunde Jose, Victoria Island, Lagos." name="address" id="address"><?php if(isset($recid)) { echo $recid->address; } ?></textarea>
                            </div>

                            <legend class="font-weight-semibold"><i class="icon-stack mr-2"></i>Document Uploads</legend>

                            <div id="moreDocuments">
                                <p style="font-size:10px; cursor:pointer" id="addmore" class="text-danger-400">Add More Documents</p>
                                @if(isset($recid))
                                    @foreach($transporterDocuments as $uploadedDocuments)
                                    <div class="form-group row">
                                        <input type="file" disabled name="document[]" class="col-md-5 documents" style="font-size:10px; top:5px;">
                                        <input type="text" name="description[]" value="{{$uploadedDocuments->description}}" class="description" disabled placeholder="Description">&nbsp; 
                                            <span class="icon-lock5" id="changeDocument" title="change this document"></span>

                                                <span class="icon-trash text-danger-400 deleteDocument" id="{{$uploadedDocuments->id}}" title="Delete this document" name="{{$uploadedDocuments->description}}"></span>
                                        

                                        <input type="hidden" value="{{$uploadedDocuments->id}}" name="document_id[]">
                                    </div>
                                    @endforeach
                                @else
                                <div class="form-group row">
                                    <input type="file" name="document[]" class="col-md-5" style="font-size:10px; top:5px;">
                                    <input type="text" name="description[]" class="" placeholder="Description"> 
                                </div>
                                @endif
                            </div>

                        </div>

                        <div class="bankDetails hidden">
                            <div class="form-group">
                                <label>Bank Name</label>
                                <input type="text" class="form-control" placeholder="Sterling Bank Nig. Plc" name="bank_name" id="bankName" value="<?php if(isset($recid)){ echo $recid->bank_name; }  ?>" >
                            </div>

                            <div class="form-group">
                                <label>Account Name</label>
                                <input type="text" class="form-control" placeholder="+234-***-***-****" name="account_name" id="accountName" value="<?php if(isset($recid)) { echo $recid->account_name; } ?>">
                            </div>

                            <div class="form-group">
                                <label>Account No.</label>
                                <input type="text" class="form-control" placeholder="0099009900" name="account_number" id="accountNumber" value="<?php if(isset($recid)) { echo $recid->account_number;} ?>">
                            </div>
                        </div>

                        <div class="guarantorDetails hidden">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" class="form-control" placeholder="" name="guarantor_name" id="guarantorName" value="<?php if(isset($recid)){ echo $recid->guarantor_name; }  ?>" >
                            </div>

                            <div class="form-group">
                                <label>Guarantor Phone No.</label>
                                <input type="number" class="form-control" placeholder="+234-***-***-****" name="guarantor_phone_no" id="guarantorPhoneNumber" value="<?php if(isset($recid)) { echo $recid->guarantor_phone_no; } ?>">
                            </div>

                            <div class="form-group">
                                <label>Address</label>
                                <textarea class="form-control" name="guarantor_address" id="guarantorAddress"><?php if(isset($recid)) { echo $recid->guarantor_address;} ?></textarea>
                            </div>
                        </div>

                        <div class="nextOfKinDetails hidden">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" class="form-control" placeholder="John Doe" name="next_of_kin_name" id="nextOfKinName" value="<?php if(isset($recid)){ echo $recid->next_of_kin_name; }  ?>" >
                            </div>

                            <div class="form-group">
                                <label>Phone No.</label>
                                <input type="number" class="form-control" placeholder="+234-***-***-****" name="next_of_kin_phone_no" id="nextOfKinAddress" value="<?php if(isset($recid)) { echo $recid->next_of_kin_address; } ?>">
                            </div>

                            <div class="form-group">
                                <label>Relationship</label>
                                <input type="text" class="form-control" placeholder="" name="next_of_kin_relationship" id="nextOfKinRelationship" value="<?php if(isset($recid)) { echo $recid->next_of_kin_relationship; } ?>">
                            </div>

                            <div class="form-group">
                                <label>Address</label>
                                <textarea class="form-control" name="next_of_kin_address" id="nextOfKinAddress"><?php if(isset($recid)) { echo $recid->next_of_kin_address;} ?></textarea>
                            </div>
                        </div>
                        

                        <div class="text-right">
                            <span id="loader"></span>
                            @if(isset($recid))
                            <button type="submit" class="btn btn-primary" id="updateTransporter">Update Transporter
                            @else
                            <button type="submit" class="btn btn-primary" id="addTransporter">Save Transporter
                            @endif
                                <i class="icon-paperplane ml-2"></i>
                            </button>
                        </div>
                    </div>
                    
                </form>
            </div>
        </div>
        <!-- /basic layout -->

    </div>

    <div class="col-md-7">
    &nbsp;

        <!-- Contextual classes -->
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">Preview Pane of Transporter</h5>
                {{$transporters->links()}}
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-info">
                        <tr style="font-size:11px;">
                            <th>#</th>
                            <th>Name</th>
                            <th>Phone Number</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $counter = 0; ?>
                    @if(count($transporters))
                        @foreach($transporters as $transporter)
                    <?php 
                        $counter++;
                        $counter % 2 == 0 ? $css = '' : $css = 'table-success';
                    ?>
                            <tr class="{{$css}}" style="font-size:10px">
                                <td>{{$counter}}</td>
                                <td>{{$transporter->transporter_name}}</td>
                                <td>{{$transporter->phone_no}}</td>
                                <td>
                                    <div class="list-icons">
                                        <a href="{{URL('transporters/'.$transporter->id.'/edit')}}" class="list-icons-item text-primary-600">
                                            <i class="icon-pencil7"></i>
                                        </a>
                                        <a href="#" class="list-icons-item text-danger-600">
                                            <i class="icon-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="table-success">You've not added any transporter for kaya.</td>
                        </tr>
                    @endif
                        
                        
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /contextual classes -->


    </div> 
</div>

@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('js/validator/jquery.form.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/validator/transporter.js')}}"></script>
@stop