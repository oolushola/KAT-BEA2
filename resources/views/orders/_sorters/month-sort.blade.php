    <div class="col-md-2 mb-2">
        <select class="form-control" id="specificMonth">
            <?php
                $currentMonth = date('F');
                for($m=1; $m<=12; $m++) {
                    $month = date('F', mktime(0,0,0,$m, 1, date('Y')));
                    if($currentMonth == $month) {
                    echo "<option value=".$month." selected>".$month."</option>";
                    } else {
                    echo "<option value=".$month.">".$month."</option>";
                    }
                }
            ?>
        </select>
    </div>
    <div class="col-md-2 mb-2">
        <select class="form-control" id="yearClientId">
            <option>Client</option>
            @foreach($clients as $client)
                <option value="{{$client->id}}">{{ucwords($client->company_name)}}</option>
            @endforeach
        </select>
    </div>
    
    <div class="col-md-2 mb-2">
        <div id="">
            <select class="form-control">
                <option value="0">Loading Site</option>
                @foreach($loadingSites as $siteofloading)
                <option value="{{$siteofloading->id}}">{{$siteofloading->loading_site}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-2 mb-2">
        <select class="form-control" id="YeartransporterId">
            <option value="0">Transporter</option>
            @foreach($transporters as $transporter)
                <option value="{{$transporter->id}}">{{$transporter->transporter_name}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2 mb-2">
        <select class="form-control" id="yearProductId">
            <option value="0">Products</option>
            @foreach($transporters as $transporter)
                <option value="{{$transporter->id}}">{{$transporter->transporter_name}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2 mb-2">
        <select class="form-control" id="yearDestination">
            <option value="0">State on Ax.</option>
            @foreach($transporters as $transporter)
                <option value="{{$transporter->id}}">{{$transporter->transporter_name}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2 mb-2">
        <select class="form-control" id="yearDestination">
            <option value="0">Exact Location</option>
            @foreach($transporters as $transporter)
                <option value="{{$transporter->id}}">{{$transporter->transporter_name}}</option>
            @endforeach
        </select>
    </div>