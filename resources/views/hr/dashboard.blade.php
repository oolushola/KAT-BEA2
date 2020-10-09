@extends('layout')
@section('title') Human Resources Dashboard for Performance Review @stop
@section('main')
<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title font-weight-bold">People <input type="text" placeholder="Search People" /></h6>
    </div>
    <div class="row">
        @if(count($staffs))
        @foreach($staffs as $key=> $user)
        <?php 
            if($key % 2 == 0) { $cssStyle = 'table-success'; } else { $cssStyle = ''; }
        ?>
        <div class="col-md-4  mt-2 mb-2 p-3 {{ $cssStyle }}">
            <span class="d-block font-size-xs font-weight-semibold">Post Held: {{ ucwords($user->current_post_held ) }}
                <span style="display:inline-table; float:right" id="loader{{$user->id}}"></span>
            </span>
            <h4 class="font-weight-bold">{{ ucwords($user->first_name) }}, {{ ucwords($user->last_name) }}</h4>
            <div>
                <?php 
                if($user->prs_starts == TRUE) {
                    $prsText = 'END PRS <i class="icon-stop  font-size-xs"></i>';
                    $prsClasses = 'endPrs badge-danger';
                    $prsPreview = ' href="#previewPR" data-toggle="modal" ';
                } 
                else {
                    $prsText = 'START PRS <i class="icon-play3 font-size-xs"></i>';
                    $prsClasses = 'startPrs badge-primary';
                    $prsPreview = 'disabled';   
                }
                ?>
                
                <span class="pointer badge {{ $prsClasses }}" name="{{ $user->current_post_held }}" id="{{ $user->id }}">
                    {!! $prsText !!}
                </span>
                <span class="viewBiodata badge badge-success pointer" href="#bioDataPopup" data-toggle="modal" id="{{ $user->id }}" data-fullName="{{ ucwords($user->first_name) }}, {{ ucwords($user->last_name) }}">
                    PERSONAL PROFILE <i class="icon-eye font-size-xs"></i>
                </span>    
                <span class="badge badge-info font-weight-bold font-size-xs pointer hrPrReview" {!! $prsPreview !!} id="preview{{$user->id}}"  data-fullName="{{ ucwords($user->first_name) }}, {{ ucwords($user->last_name) }}" value="{{ $user->id }}">
                    PREVIEW PR<i class="icon-display ml-1 font-size-xs"></i>
                </span>
            </div>
            
        </div>
        @endforeach
        @else
            <h5>There are no people</h5>
        @endif
    </div>
</div>

@include('hr._partials.biodata-preview')
@include('hr._partials.performance-review')

@stop

@section('script')
<script type="text/javascript">
    $(function() {
        $('.viewBiodata').click(function() {
            $userId = $(this).attr('id');
            $fullName = $(this).attr("data-fullName")
            $('#selectUser').html($fullName)
            $('#userBiodataResponse').html('<i class="spinner icon-spinner2"></i>Please wait...')
            $.get('/hr-biodata-preview', { user_id: $userId }, function(data) {
                $('#userBiodataResponse').html(data)
            })
        })

        $('.startPrs').click(function() {
            $user = $(this).attr('id')
            $postHeld = $(this).attr('name')
            if($postHeld === '') {
                $('#loader'+$user).html('Operation Aborted. First, assign a role.').addClass('text-danger').fadeIn(2000).delay(5000).fadeOut(3000)
                return false;
            }
            else{
                $e = $(this);
                $('#loader'+$user).html('<i class="spinner icon-spinner3"></i> Starting PRS')
                $.get('/hr-start-prs-session', { user_id: $user, position: $postHeld }, function(data) {
                    if(data === 'started') {
                        $e.html('END PRS <i class="icon-stop font-size-xs"></i>').addClass('badge-danger')
                        $('#loader'+$user).html('')
                        $('#preview'+$user).removeAttr('disabled', 'disabled')
                    }
                    else {
                        return false
                    }
                })
            }
        })

        $('.hrPrReview').click(function() {
            $user = $(this).attr('id')
            $name = $(this).attr('data-fullName')
            $('#selectUserPr').html($name+' Performance Review Session.');
        })

        $('#jobDescription').click(function() {
            $userId = $(this).attr('data-user')
            $('#prMasterPlaceholder').html('<i class="icon-spinner spinner"></i>Please wait, fetching job description...')
            $.get('/hr-user-job-description', { id: $userId }, function(data) {
                $('#prMasterPlaceholder').html(data)
            })
        })

        $('#reviewPr').click(function() {
            $userId = $(this).attr('data-user')
            $('#prMasterPlaceholder').html('<i class="icon-spinner spinner"></i>Please wait, gathering their reviews...')
            $.get('/hr-user-review', { id: $userId }, function(data) {
                $('#prMasterPlaceholder').html(data)
            })
        })
    })
</script>
@stop


