@extends('layout')

@section('title')Kaya ::. Products @stop
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
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home - Global Operation</span> - Products</h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calculator text-primary"></i> <span>View Products</span></a>
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calendar5 text-primary"></i> <span>Product Categories</span></a>
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
                <h5 class="card-title">Add New Product</h5>
            </div>

            <div class="card-body">
                <form id="frmProducts" action="">
                    @csrf 
                    @if(isset($recid)) {!! method_field('PATCH') !!} <input type="hidden" name="id" id="id" value="{{$recid->id}}"> @endif
                    <div class="form-group">
                        <label>Product Code</label>
                        <input type="text" class="form-control" placeholder="sug" name="product_code" id="productCode" value="<?php if(isset($recid)){ echo $recid-> product_code; }  ?>" />
                    </div>

                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" class="form-control" placeholder="Sugar" name="product" id="product" value="<?php if(isset($recid)){ echo $recid-> product; }  ?>">
                    </div>

                    <div class="text-right">
                        <span id="loader"></span>
                        @if(isset($recid))
                            <button type="submit" class="btn btn-primary" id="updateProduct">Update Product 
                        @else
                            <button type="submit" class="btn btn-primary" id="addProduct" >Save Product 
                        @endif
                            <i class="icon-paperplane ml-2"></i>
                        </button>
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
                <h5 class="card-title">Preview Pane of Products</h5>
                <div>{{$products->links()}}</div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-info">
                        <tr style="font-size:11px;">
                            <th>#</th>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $counter = 0; ?>
                    @if(count($products))
                        @foreach($products as $product)
                        <?php $counter++; 
                            $counter % 2 == 0 ? $css= ' ' : $css = 'table-secondary'; 
                        ?>
                            <tr class="{!! $css !!}" style="font-size:10px">
                                <td>{{$counter}}</td>
                                <td>{{$product->product_code}}</td>
                                <td>{{$product->product}}</td>
                                <td>
                                    <div class="list-icons">
                                        <a href="{{URL('products/'.$product->id.'/edit')}}" class="list-icons-item text-primary-600"><i class="icon-pencil7"></i></a>
                                        <a href="#" class="list-icons-item text-danger-600">
                                            <i class="icon-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="5">Total number of products: {{$counter}}</td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="5">You've not added any products</td>
                        </tr>
                    @endif
`                    </tbody>
                </table>
            </div>
        </div>
        <!-- /contextual classes -->


    </div>

    
</div>

@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('js/validator/product.js')}}"></script>
@stop
