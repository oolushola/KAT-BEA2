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
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home - Operations</span> - Transporter Log</h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <!-- Contextual classes -->
        <div class="card">
            <div class="table-responsive" style="max-height:1300px;">
                <table class="table table-bordered">
                    <thead class="table-info" style="font-size:11px">
                        <tr>
                            <th>&nbsp;</th>
                            <th>
                                <select id="allTransporter" style="border:1px solid #ccc; padding:5px; border-radius: 5px; width:120px; outline:none">
                                    <option value="0">Choose User</option>
                                    @foreach($users as $staff)
                                    <option value="{{$staff->first_name}} {{$staff->last_name}}">
                                        {{$staff->first_name}} {{$staff->last_name}}
                                    </option>
                                    @endforeach
                                </select>
                                <input type="text" id="searchBox" placeholder="SEARCH TRANSPORTER" style="border:1px solid #ccc; padding:5px; border-radius: 5px; width:150px; outline:none " />
                            </th>
                            <th colspan="5">

                            </th>
                        </tr>
                        <tr>
                            <th>SN</th>
                            <th>BASIC INFORMATION</th>
                            <th>ACCOUNT INFORMATION</th>
                            <th class="text-center">DOCUMENTS </th>
                            <th>OTHERS</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $counter = 0; ?>
                    @if(count($transporters))
                        @foreach($transporters as $key => $transporter)
                        <?php 
                            $counter++;
                            $counter % 2 == 0 ? $css = '' : $css = 'table-success';
                        ?>
                            <tr>
                                <td class="font-size-sm font-weight-bold">{{$counter}}</td>
                                <td>
                                    <span class="badge badge-danger" style="font-size:10px">
                                        <a href="{{URL(str_slug($transporter->transporter_name).'/trip/log/'.$transporter->id)}}">Trips Completed ({{$transporterTripCount[$key]->transporterTrips}})</a>
                                    </span> 
                                    
                                        
                                        @if($transporter->transporter_status == TRUE)
                                        <span class="badge transporterStatus" id="{{$transporter->id}}" style="font-size:10px; background:#000; color:#fff; cursor:pointer">Blacklist<i class="icon-x"></i><i class="ml-1" id="status{{$transporter->id}}"></i></span> 
                                        @else
                                        <span class="badge transporterStatus" id="{{$transporter->id}}" style="font-size:10px; background:#999; color:#fff; cursor:pointer">Activate <i class="icon-checkmark2"></i> <i class="ml-1" id="status{{$transporter->id}}"></i></span> 
                                        @endif
                                        
                                    <h5 class="m-0 text-primary font-weight-semibold">{{ucwords($transporter->transporter_name)}}</h5>
                                    <span class="font-size-sm"><strong>Telephone</strong>: {{$transporter->phone_no}}</span><br>
                                    <span class="font-size-sm text-info mt-1"><strong>Address</strong>: {{$transporter->address}}</span><br>
                                    <span class="" style="font-size:10px">Managed by: {{ $transporter->first_name }} {{ $transporter->last_name }}</span>

                                </td>
                                <td>
                                    <span class="font-size-sm font-weight-semibold">{{ucwords($transporter->bank_name)}}</span>
                                    <h5 class="m-0 text-primary font-weight-bold">{{$transporter->account_number}}</h5>
                                    <span class="font-size-sm font-weight-semibold">{{ $transporter->account_name }}</span>
                                </td>
                                <td class="text-center">
                                    @foreach($transporterVerification as $document)
                                        @if($document->transporter_id == $transporter->id)
                                            <span class="d-block mb-1">
                                                <span class="badge badge-primary"  style="font-size:10px"> 
                                                    <a href="{{URL::asset('assets/img/transporters/documents/'.$document->document)}}" target="_blank" style="color:#fff">{{ $document->description }}</a>
                                                </span>
                                            </span>
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    @if($transporter->next_of_kin_name)
                                    <h5 class="m-0 text-primary font-weight-semibold">{{$transporter->next_of_kin_name}}</h5>
                                    <span class="font-size-sm"><strong>Telephone</strong>: {{$transporter->next_of_kin_phone_no}}</span><br>
                                    <span class="font-size-sm text-info mt-1"><strong>Address</strong>: {{$transporter->next_of_kin_address}}</span>

                                    <span class="mt-2 font-weight-bold d-block"><u>Guarantor</u></span>

                                    <h5 class="m-0 text-primary font-weight-semibold">{{$transporter->guarantor_name}}</h5>
                                    <span class="font-size-sm"><strong>Telephone</strong>: {{$transporter->guarantor_phone_no}}</span><br>
                                    <span class="font-size-sm text-info mt-1"><strong>Address</strong>: {{$transporter->guarantor_address}}</span>
                                    @endif
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