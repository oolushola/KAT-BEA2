
    <div class="col-md-2 mb-2">
        <label class="badge badge-primary">Date Range From</label>
        <input type="date" class="form-control" id="weekstartfrom" style="font-size:10px;">
    </div>

    <div class="col-md-2 mb-2">
        <label class="badge badge-primary">Date Range To</label>
        <input type="date" class="form-control" id="weekendto" style="font-size:10px;">
    </div>
    
    <div class="col-md-2 mb-2">
    <label>&nbsp;</label>
        <div id="">
            <select class="form-control" id="weekLoadingSiteId" style="font-size:10px;">
                <option value="0">Loading Site</option>
                @foreach($loadingSites as $siteofloading)
                <option value="{{$siteofloading->id}}">{{$siteofloading->loading_site}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-2 mb-2">
        <label>&nbsp;</label>
        <select class="form-control" id="weekProductId" style="font-size:10px;">
            <option value="0">Products</option>
            @foreach($products as $product)
                <option value="{{$product->id}}">{{$product->product}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2 mb-2">
        <label>&nbsp;</label><br>
        <button class="btn btn-primary" id="filterWeekWise" style="font-size:10px;">Filter</button>
    </div>