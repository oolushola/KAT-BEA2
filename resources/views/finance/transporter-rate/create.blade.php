@extends('layout')

@section('title')Kaya :: Transporter Rate @stop

@section('main')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home - Finance</span> - Transporter Rate</h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                <a href="#" class="btn btn-link btn-float text-default" id="closeBtn"><i class="icon-x text-primary"></i> <span>Close</span></a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-5">
    &nbsp;

        <div class="card">
            <div class="card-header header-elements-inline">
                <h6 class="card-title font-weight-semibold">Transporter Rate</h6>
                <i class="icon-stack text-danger-600" id="bulkUpload"></i>
                <i class="icon-stack text-primary-600" id="singleUpload" style="display:none"></i>
            </div>
            

            <div class="card-body">
                <form method="POST" name="frmTransporterRate" id="frmTransporterRate" action="{{URL('transporter-rate')}}" enctype="multipart/form-data">
                    @csrf
                    @if(isset($recid))
                    <input type="text" name="id" id="id" value="{{$recid->id}}">
                    {!! method_field('PATCH') !!}
                    @endif


                    <div id="bulkUploadForm" style="display:none">
                        <div class="form-group">
                            <label>Upload File</label>
                            <input type="file" name="file" id="file" title="Upload only CSV File" />
                            <input type="hidden" name="filecheck" id="filecheck" value="0" /> 
                            <input type="hidden" name="ftype" id="ftype" value="csv" />

                        </div>
                        <span id="loader1"></span>
                        <button type="submit" class="btn btn-primary" id="uploadBulkRating">Upload Bulk Rates 
                            <i class="icon-paperplane ml-2"></i>
                        </button>
                    </div>                    

                    <div id="singleEntryForm">
                        <div class="form-group">
                            <label>From</label>
                            <select class="form-control form-control-select2" name="transporter_from_state_id" id="fromClientId">
                                <option value="0">Choose Starting Point</option>
                                @foreach($superClients as $client)
                                @if(isset($recid) && ($recid->transporter_from_state_id == $client->id))
                                    <option value="{{$client->id}}" selected>{{$client->parent_name}}</option>
                                @else
                                    <option value="{{$client->id}}">{{$client->parent_name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>To: State on AX</label>
                            <select class="form-control form-control-select2" id="destinationStateId" name="transporter_to_state_id">
                                <option value="0">Select Destination State</option>
                                @foreach($states as $state)
                                @if(isset($recid) && ($recid->transporter_to_state_id == $state->regional_state_id))
                                    <option value="{{$state->regional_state_id}}" selected>{{$state->state}}</option>
                                @else
                                    <option value="{{$state->regional_state_id}}">{{$state->state}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" id="exactLocationHolder">
                            <label>Destination</label>
                            <input type="text" name="transporter_destination" class="form-control" id="destination" value="<?php if(isset($recid)){ echo $recid->transporter_destination; }?>" />
                        </div>

                        <div class="form-group">
                            <label>Tonnage</label>
                            <input type="number" class="form-control" placeholder="Tonnage(Kg)" name="transporter_tonnage" id="tonnage" value="<?php if(isset($recid)){ echo $recid->transporter_tonnage; } else { echo 30000; }?>">
                        </div>

                        <div class="form-group">
                            <label>Rate in (&#x20a6;)</label>
                            <input type="number" class="form-control" placeholder="300,000.00" name="transporter_amount_rate" id="amountRate" value="<?php if(isset($recid)){ echo $recid->transporter_amount_rate; }?>">
                        </div>

                        <div class="text-right">
                            <span id="loader"></span>
                            @if(isset($recid))
                            <button type="submit" class="btn btn-primary" id="updateTransporterRate">Update Fare Rate 
                            @else
                            <button type="submit" class="btn btn-primary" id="addTransporterRate">Add Fare Rate 
                            @endif
                                <i class="icon-paperplane ml-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <div class="col-md-7">
    &nbsp;

        <!-- Contextual classes -->
        <div class="card">
            <div class="card-header header-elements-inline">
                <h6 class="card-title font-weight-semibold">Kaya Transporter Rate</h6>
                <input type="text" id="myInput" placeholder="Search">
            </div>

            <div class="table-responsive" style="max-height:800px; overflow:auto">
                <table class="table table-bordered" id="myTable">
                    <thead class="table-info">
                        <tr style="font-size:9px;">
                            <th>#</th>
                            <th>From</th>
                            <th>Destination</th>
                            <th>State on AX</th>
                            <th>Tons</th>
                            <th>Rate (&#x20a6;)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $counter = 0; ?>
                    @if(count($transporterRates))
                        @foreach($transporterRates as $transporterrate)
                            <?php $counter++;
                                $counter % 2 == 0 ? $css = '' : $css = 'table-success';
                            ?>
                            <tr class="{{$css}}" style="font-size:8px;">
                                <td>{{$counter}}</td>
                                <td>{{ $transporterrate->parent_name }}</td>
                                <td>{{$transporterrate->transporter_destination}}</td>
                                <td>{{$transporterrate->state}}</td>
                                <td>{{$transporterrate->transporter_tonnage}}</td>
                                <td>{{number_format($transporterrate->transporter_amount_rate, 2)}}</td>
                                <td>
                                    <div class="list-icons">
                                        <a href="{{URL('transporter-rate/'.$transporterrate->id.'/edit')}}" class="list-icons-item text-primary-600">
                                        <i class="icon-pencil7"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="table-info" colspan="7">You've not added any ratings for this client</td>
                        </tr> 
                    @endif                      
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('js/validator/jquery.form.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/validator/validatefile.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/validator/transporter-rate.js')}}"></script>
@stop