    <div class="col-md-2 mb-2">
        <select class="form-control" id="yearInview" style="font-size:10px;">
            <option value='0'>Choose Year</option>
            <?php $year = date('Y'); ?>
            @for($y = $year; $y >= 2017; $y--)
                <option>{{$y}}</option>
            @endfor
        </select>
    </div>
    
    <div class="col-md-2 mb-2">
        <select class="form-control" id="yearAndMonth" style="font-size:10px;">
            <option value="0">Choose Month</option>
            <?php
                $currentMonth = date('F');
                for($m=1; $m<=12; $m++) {
                    $month = date('F', mktime(0,0,0,$m, 1, date('Y')));
                    echo "<option value=".$month.">".$month."</option>";
                    
                }
            ?>
        </select>
    </div>
    <div class="col-md-2 mb-2">
        <select class="form-control" id="yearClientId" style="font-size:10px;">
            <option value="0">Client</option>
            @foreach($clients as $client)
                <option value="{{$client->id}}">{{ucwords($client->company_name)}}</option>
            @endforeach
        </select>
    </div>
    
    <div class="col-md-2 mb-2">
        <div id="">
            <select class="form-control" id="yearLoadingSiteId" style="font-size:10px;">
                <option value="0">Loading Site</option>
                @foreach($loadingSites as $siteofloading)
                <option value="{{$siteofloading->id}}">{{$siteofloading->loading_site}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-2 mb-2">
        <select class="form-control" id="YeartransporterId" style="font-size:10px;">
            <option value="0">Transporter</option>
            @foreach($transporters as $transporter)
                <option value="{{$transporter->id}}">{{$transporter->transporter_name}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2 mb-2">
        <select class="form-control" id="yearProductId" style="font-size:10px;">
            <option value="0">Products</option>
            @foreach($products as $product)
                <option value="{{$product->id}}">{{$product->product}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2 mb-2">
        <select class="form-control" id="yearDestination" style="font-size:10px;">
            <option value="0">State on Ax.</option>
            @foreach($states as $state)
                <option value="{{$state->regional_state_id}}">{{$state->state}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2 mb-2">
        <div id="exactLocationPlaceholder">
            <select class="form-control" style="font-size:10px;">
                <option value="0">Exact Location</option>
            </select>
        </div>
    </div>
    <div class="col-md-2 mb-2">
        <label>&nbsp;</label>
        <button class="btn btn-primary" id="filterMaster" style="font-size:10px;">Filter Selection</button>
    </div>