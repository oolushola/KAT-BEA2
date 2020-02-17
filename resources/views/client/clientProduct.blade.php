@extends('layout')

@section('title') Kaya ::. Products cargo by {{ucwords($clientName)}} @stop

@section('main')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4>
                <i class="icon-arrow-left52 mr-2"></i> 
                <span class="font-weight-semibold">Home - Clients</span> - {{ucwords($clientName)}}
            </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                <a href="#" class="btn btn-link btn-float text-default" id="closeBtn">
                    <i class="icon-x text-primary"></i> <span>Close</span>
                </a>
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
                <h5 class="card-title">Add Product</h5>
            </div>

            <div class="card-body">
                <form action="" name="frmClientProducts" id="frmClientProducts">
                    @csrf
                    <input type="hidden" name="client_id"  value="{{$client_id}}">
                    <div class="form-group">
                        <label>Product Type</label>
                        <select class="form-control form-control-select2" id="productType" name="product_id">
                            <option value="0">Choose Products</option>
                            @foreach($products as $productName)
                            <option value="{{$productName->id}}">{{$productName->product}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="text-right">
                        <span id="loader"></span>
                        <button type="submit" class="btn btn-primary" id="addClientProduct">Add Product <i class="icon-paperplane ml-2"></i></button>
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
                <h5 class="card-title">Products of {{ucwords($clientName)}}</h5>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-info">
                        <tr style="font-size:10px;">
                            <th>#</th>
                            <th>Product Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $counter = 0; ?>
                    @if(count($clientProducts))
                        @foreach($clientProducts as $specificClientProduct)
                        <?php $counter++; 
                            $counter % 2 == 0 ? $css = '' : $css = 'table-success'
                        ?>
                        <tr class="{{$css}}" style="font-size:10px">
                            <td>{{$counter}}</td>
                            <td>{{$specificClientProduct->product}}</td>
                            <td>
                                <div class="list-icons">
                                    <a href="#" class="list-icons-item text-danger-600">
                                        <i class="icon-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="table-success">You've not added any products for this client</td>
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
<script type="text/javascript" src="{{URL::asset('js/validator/client.js')}}"></script>
@stop