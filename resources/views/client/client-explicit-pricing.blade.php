@extends('layout')

@section('title') Kaya :: Client Rates @stop

@section('main')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home - Clients</span> - {{ucwords($clientName)}}</h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                <a href="{{URL('')}}" class="btn btn-link btn-float text-default"><i class="icon-coins text-primary"></i> <span>View Rate</span></a>
            </div>
            <div class="d-flex justify-content-center">
                <a href="#" class="btn btn-link btn-float text-default" id="closeBtn"><i class="icon-x text-primary"></i> <span>Close</span></a>
            </div>
        </div>
    </div>
</div>

 <!-- Contextual classes -->
 <div class="card">
            <div class="card-header header-elements-inline">
                <h6 class="card-title font-weight-semibold">Fare Rate of {{ucwords($clientName)}}</h6>
                <input type="text" id="myInput" placeholder="Search">
            </div>

            <div class="table-responsive" style="max-height:800px; overflow:auto">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th colspan="3">&nbsp;</th>
                            <th colspan="5" class="text-center table-info">WITHOUT VAT</th>
                        </tr>
                        <tr  class="table-info" style="font-size:9px;">
                            <th>#</th>
                            <th>Destination</th>
                            <th>State on AX</th>
                            <th>15 Tons</th>
                            <th>30 Tons</th>
                            <th>40 Tons</th>
                            <th>45 Tons</th>
                            <th>60 Tons</th>
                            
                        </tr>
                    </thead>
                    <tbody id="myTable">
                        <?php $counter = 0; ?>
                        @if(count($clientRates))
                            @foreach($clientRates as $location)
                            <?php $counter++; 
                            $counter % 2 == 0 ? $css = '' : $css = 'table-success';
                            
                            ?>
                                <tr class="{{$css}}" style="font-size:10px;">
                                    <td>{{$counter}}</td>
                                    <td>{{$location->destination}}</td>
                                    <td>{{$location->state}}</td>
                                    
                                    <td>{!! clientRatepPerTonnage($location, $ratings, '15000') !!}</td>
                                    <td>{!! clientRatepPerTonnage($location, $ratings, '30000') !!}</td>
                                    <td>{!! clientRatepPerTonnage($location, $ratings, '40000') !!}</td>
                                    <td>{!! clientRatepPerTonnage($location, $ratings, '45000') !!}</td>
                                    <td>{!! clientRatepPerTonnage($location, $ratings, '60000') !!}</td>
                            
                                </tr>
                            @endforeach
                        @else                                               
                        <tr>
                            <td class="table-info" colspan="7">You've not added any ratings for this client</td>
                        </tr>
                        @endif

                        <?php
                             function clientRatepPerTonnage($master, $childArray, $tonValue){
                                foreach($childArray as $object){
                                    if($object->destination == $master->destination && $object->destination = $tonValue){
                                        return number_format($object->amount_rate, 2);
                                        break;
                                    }
                                    continue;
                                }
                            }
                        ?>
                    
                        
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /contextual classes -->
@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('js/validator/jquery.form.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/validator/validatefile.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/validator/client.js')}}"></script>
@stop