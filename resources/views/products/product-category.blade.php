@extends('layout')

@section('title')Kaya ::. Product Category @stop
@section('css')
<link rel="stylesheet" href="{{URL::asset('css/custom.css')}}" type="text/css" />
@stop

@section('main')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home - Global Operation</span> - Product Category</h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calculator text-primary"></i> <span>View Categories</span></a>
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-cart text-primary"></i> <span>Products</span></a>
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
                <h5 class="card-title"> @if(isset($recid)) Update  @else  Add @endif Product Category</h5>
            </div>

            <div class="card-body">
                <form id="frmProductCategory">
                    @csrf 
                    @if(isset($recid)) {!! method_field('PATCH') !!} 
                        <input type="hidden" name="id" id="id" value="{{$recid->id}}" />
                    @endif
                    <div class="form-group">
                        <label>Product Category Code</label>
                        <input type="text" class="form-control" id="productCategoryCode" name="product_category_code" placeholder="TLV" maxlength="3" value="<?php if(isset($recid)){echo $recid->product_category_code; } else {echo '';} ?>">
                    </div>

                    <div class="form-group">
                        <label>Product Category</label>
                        <input type="text" class="form-control" name="product_category" id="productCategory" placeholder="Groceries" value="<?php if(isset($recid)){echo $recid->product_category; } else {echo '';} ?>" >
                    </div>

                    <div id="loader2"></div>

                    <div class="text-right">
                        <span style="float:left" id="loader"></span>
                        @if(isset($recid))
                            <button type="submit" class="btn btn-primary" id="updateProductCategory">Update Product Category
                        @else
                            <button type="submit" class="btn btn-primary" id="addProductCategory">Save Product Category
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
            <h5 class="card-title">Preview Pane of Product Categories</h5>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-info">
                    <tr style="font-size:11px;">
                        <th>#</th>
                        <th>Product Category Code</th>
                        <th>Product Category</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $counter = 0; ?>
                    @if(count($product_categories))
                        @foreach($product_categories as $productCategory)

                        <?php $counter++;
                            $counter % 2 == 0 ? $css = '' : $css='table-success';
                        ?>

                        <tr class="{!! $css !!}" style="font-size:10px">
                            <td>{!! $counter !!}</td>
                            <td>{!! strtoupper($productCategory->product_category_code) !!}</td>
                            <td>{!! $productCategory->product_category !!}</td>
                            <td>
                                <div class="list-icons">
                                    <a href="{{URL('/product-category/'.$productCategory->id.'/edit')}}" class="list-icons-item text-primary-600">
                                        <i class="icon-pencil7"></i>
                                    </a>
                                    <a href="#" class="list-icons-item text-danger-600">
                                        <i class="icon-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        <tr class="table-info">
                            <td colspan="5">Total number of product categories: {{$counter}}</td>
                        </tr>
                    @else
                        <tr class="table-success">
                            <td colspan="5">You've not added any product categories</td>
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
<script type="text/javascript" src="{{URL::asset('js/validator/jquery-validator.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/validator/product-category.js')}}"></script>
@stop
