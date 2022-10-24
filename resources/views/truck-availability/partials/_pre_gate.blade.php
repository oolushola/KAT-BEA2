<div id="pregateHandler" class="modal fade">
    @csrf
    <form method="POST" id="frmConfirmPregateIn">
        @csrf {!! method_field('PATCH') !!}
        
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-semibold" id="textDescriptor">TRUCK AVAILABILITY: PRE GATE<span></span></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                
                <div class="modal-body">
                    <span class="d-block" id="loader4"></span>
                    <div class="row mr-3" id="pregateInstructions">
                      <div class="col-md-6 col-sm-6 bg-success-300">
                        <div class="form-group">
                          <label>Gross Transaction Value (GTV)</label>
                          <input type="hidden" id="clientRate" />
                          <h4 id="gtvLabel"></h4>
                        </div>
                      </div>
                      <div class="col-md-6 col-sm-6 bg-info-300">
                        <div class="form-group">
                          <label>Pay Out</label>
                          <input type="hidden" id="transporterRate" />
                          <h4 id="trLabel"></h4>
                        </div>
                      </div>

                      <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                          <label class="mt-3">Agreed a diff. payout?
                            <input type="checkbox" class="ml-2" id="differentDeal" />
                          </label>
                        </div>
                      </div>
                      <div class="col-md-8 col-sm-6">
                        <div class="form-group">
                          <input type="number" class="form-control mt-1 d-none" id="differentDealVal" style="font-size:18px"   />
                        </div>
                      </div>
                      
                      <div class="col-md-12 d-block p-2 font-weight-bold text-danger">
                        <p class="d-none"  id="authorizationLabel"`>Please contact your account officer for payment agreement and authorization before gate in.</p>
                      </div>

                      <div class="text-left ml-2">
                        <a href="" id="gateInTruck" role="button" class="btn btn-large btn-primary">GATE IN<i class="icon-paperplane"></i></a>
                      </div>
                      <div class="col-md-12" id="loader2"></div>
                    </div>
                </div>

                
            </div>
        </div>  
    </form>
</div>