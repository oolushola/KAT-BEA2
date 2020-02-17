@extends('layout')

@section('title')Kaya :: Bulk Payment @stop

@section('css')
<style type="text/css">
th {
    white-space: nowrap;
}
td{
    white-space: nowrap;
}
</style>
@stop

@section('main')
<?php 

?>
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home - Finance</span> - Bulk Payment</h4>
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
                <h6 class="card-title font-weight-semibold">Chunk Rate</h6>
            </div>
            <div class="card-body">
                <form method="POST" name="frmBulkPayment" id="frmBulkPayment">
                    @csrf
                    @if(isset($recid))
                    {!! method_field('PATCH') !!}
                    <input type="text" name="id" id="id" value="{{$recid->id}}" />
                    @endif
                    <div class="form-group" id="exactLocationHolder">
                        <label>Transpoter</label>
                       <select class="form-control" id="transporterId" name="transporter_id">
                           <option value="0">Choose a transporter</option>
                           @foreach($transporters as $transporter)
                                @if(isset($recid) && $recid->transporter_id == $transporter->id)
                                <option value="{{$transporter->id}}" selected>{{$transporter->transporter_name}}</option>
                                @else
                                <option value="{{$transporter->id}}">{{$transporter->transporter_name}}</option>
                                @endif
                           @endforeach
                       </select>
                    </div>

                    <div class="form-group">
                        <label>Amount in (&#x20a6;)</label>
                        <input type="number" class="form-control" placeholder="300,000.00" name="amount_credited" id="amountCredited" value="<?php if(isset($recid)){ echo $recid->amount_credited; }?>">
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" name="remark" placeholder="Optional">@if(isset($recid)){{$recid->remark}}@endif</textarea>
                    </div>

                    <div class="text-right">
                        <span id="loader"></span>
                        @if(isset($recid))
                        <button type="submit" class="btn btn-primary" id="updateBulkPayment">Update Chunk Rate 
                        @else
                        <button type="submit" class="btn btn-primary" id="addBulkPayment">Add Chunk Rate 
                        @endif
                            <i class="icon-paperplane ml-2"></i>
                        </button>
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
                <h6 class="card-title font-weight-semibold">Chunk Rate Log</h6>
            </div>

            <div class="table-responsive" style="max-height:800px; overflow:auto">
                <table class="table table-bordered" id="myTable">
                    <thead class="table-info">
                        <tr style="font-size:9px;">
                            <th>S/N</th>
                            <th>Transporter</th>
                            <th>Balance (&#x20a6;)</th>
                            <th>Amount (&#x20a6;)</th>
                            <th class="text-center">Status</th>
                            <th>Uploaded</th>
                            <th>Approved</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <form method="POST" id="frmApproveChunkRate" action="{{URL('approvebulkpayment')}}">
                            @csrf
                        <?php $counter = 0; ?>
                        @if(count($bulkpayments))
                            @foreach($bulkpayments as $chunkpayment)
                            <?php 
                                $counter++;
                                $counter % 2 == 0 ? $css = '' : $css = 'table-success';

                                if($chunkpayment->approval_status == 0){
                                    $aprovalStatus = "<input type='checkbox' name='approvePayment[]' value='$chunkpayment->id'>";
                                    $edit = '<a href="/bulk-payment/'.$chunkpayment->id.'/edit">
                                                <i class=\'icon-pencil3\'></i>
                                        </a>';
                                    
                                }
                                else{
                                    $aprovalStatus = '<i class="text-success icon-checkmark4"></i>';
                                    $edit = "<i class='icon-cancel-circle2'></i>";
                                }

                            ?>
                                <tr class="{{$css}}" style="font-size:10px;">
                                    <td>{{$counter}}</td>
                                    <td>{{$chunkpayment->transporter_name}}</td>
                                    <td>{{number_format($chunkpayment->balance,2)}}</td>
                                    <td>{{number_format($chunkpayment->amount_credited, 2)}}</td>
                                    <td class="text-center">{!! $aprovalStatus !!}</td>
                                    <td>{{$chunkpayment->date_uploaded}}</td>
                                    <td>{{$chunkpayment->date_approved}}</td>
                                    <td class="text-center">{!! $edit !!}</td>
                                </tr>
                            @endforeach
                                <tr class="table-secondary">
                                    <td colspan="4"></td>
                                    <td><button class="btn btn-success" id="approveBulkPayment" type="submit" >Approve</button></td>
                                    <td colspan="4"></td>
                                </tr>
                        @else
                        <tr>
                            <td class="table-info" colspan="7">No bulk payment has been added.</td>
                        </tr>
                        @endif
                        </form>
                                     
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('js/validator/bulkpayment.js')}}"></script>
@stop