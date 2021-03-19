@extends('layout')

@section('title')Kaya ::. Drivers @stop

@section('main')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Account Manager Target</span></h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                <a href="#assignAccountManager" data-toggle="modal" class="btn btn-link btn-float text-default font-weight-semibold"><i class="icon-truck"></i> <span>Assign Account Manager</span></a>
                
                <a href="{{URL('kaya-target')}}" class="btn btn-link btn-float text-default font-weight-semibold"><i class="icon-calendar2"></i> <span>Targets</span></a>
                
                <a href="{{URL('buh-target')}}" class="btn btn-link btn-float text-default font-weight-semibold"><i class="icon-pointer"></i> <span>Target Margin</span></a>
                

                
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
    &nbsp;

        <!-- Basic layout-->
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title font-size-sm font-weight-bold"> Account Manager Target for {{ date('F, Y')}}</h5>
            </div>

            <div class="card-body">
                <form method="POST" name="frmClientTarget" id="frmClientTarget" enctype="multipart/form-data">
                    @csrf
                    @if(isset($recid))
                        <input type="hidden" name="id" value="{{$recid->id}}" id="id" />
                        {!! method_field('PATCH') !!}
                    @endif
                    <div class="row">
                        @foreach($clients as $key => $client)
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="card">
                                    <div class="card-body">
                                        <p class="text-success font-weight-bold font-size-xs">{{ strtoupper($client->company_name) }}</p>
                                        <input type="number" name="target[]" value="{{ $client->target }}" style="border:1px solid #ccc; padding: 5px; width:70px; margin-right: 5px; font-size: 10px; outline: none" placeholder="Client Target">
                                        <input type="number" name="percentage[]" value="{{ $client->percentage }}" style="border:1px solid #ccc; padding: 5px; width:70px; margin-right: 5px; font-size: 10px; outline: none" placeholder="Percentage">
                                        
                                        <input type="hidden" name="client_id[]" value="{{ $client->id }}" />
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-right">
                        <span id="loader"></span>
                        @if(isset($recid))
                        <button type="submit" class="btn btn-primary mt-2" id="updateAccountTarget">Update Account Target
                        @else
                        <button type="submit" class="btn btn-primary mt-2" id="addAccountTarget">Save Account Target 
                        @endif
                        <i class="icon-paperplane ml-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /basic layout -->
    </div>

    <div class="col-md-6"></div>

</div>

@include('transportation.camt.clientaccountofficer')

@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('js/validator/camt.js')}}"></script>
@stop
