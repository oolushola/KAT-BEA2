@extends('layout')
@section('title') Biodata update @stop
@section('main')
<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title">Bio-data: {{ ucwords(Auth::user()->first_name) }} {{ ucwords(Auth::user()->last_name) }} </h6>
        <div class="header-elements">
            <div class="list-icons">
                <a class="list-icons-item" data-action="collapse"></a>
                <a class="list-icons-item" data-action="reload"></a>
                <a class="list-icons-item" data-action="remove"></a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-7">
            <form class="wizard-form steps-basic wizard clearfix"" role="form" id="frmBioData">
                @csrf
                <input type="text" value="{{ Auth::user()->id }}" name="user_id" />
                <div class="steps clearfix">
                    <ul role="tablist">
                        <li role="tab" class="pointer @if($user->date_of_birth) done @endif"  id="personalDataTab">
                            <a>
                                <span class="current-info audible">current step: </span>
                                <span class="number">1</span> Personal Data
                            </a>
                        </li>
                        <li role="tab" class="pointer" id="educationTab">
                            <a>
                                <span class="number">2</span> Education
                            </a>
                        </li>
                        <li role="tab"  class="pointer" id="experienceTab">
                            <a>
                                <span class="number">3</span> Experience
                            </a>
                        </li>
                        <li role="tab" class="pointer" id="dependantsTab">
                            <a>
                                <span class="number">4</span> Dependants
                            </a>
                        </li>
                        <li role="tab" class="pointer" id="additionalInfoTab">
                            <a>
                                <span class="number">5</span> Additional info
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="content clearfix">
                    <h6 id="personalData" tabindex="-1" class="title current">Personal data</h6>
                    <fieldset id="personalDataFrm" role="tabpanel" aria-labelledby="steps-uid-0-h-0" class="body d-none
                    " aria-hidden="true">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Position Held</label>
                                    <input type="text" class="form-control" value="{{ $user->current_post_held }}" disabled />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date of Appointment</label>
                                    <input type="date" class="form-control" value="{{ $user->date_of_appointment }}" disabled />
                                </div>
                            </div>
                        
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input type="text" class="form-control" value="{{ ucfirst($user->last_name).' '.ucfirst($user->first_name) }}" disabled>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" value="{{ $user->email }}"  disabled>
                                </div>
                            </div>
                        
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone No.</label>
                                    <input type="text" class="form-control" value="{{ $user->phone_no }}" disabled>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date of birth:</label>
                                    <input type="date"  class="form-control" name="dateOfBirth" id="dateOfBirth" value="{{ $user->date_of_birth }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Gender</label>
                                    <select class="form-control" name="gender" id="gender" >
                                        <option value="">Choose Sex</option>
                                        <option value="Male" <?php if($user->gender == 'Male'){ ?> selected  <?php } ?> >Male</option>
                                        <option value="Female" <?php if($user->gender == 'Female'){ ?> selected  <?php } ?>>Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Address</label>
                                    <input type="text"  class="form-control" name="address" id="address" value="{{ $user->address }}" />
                                </div>
                            </div>
                            <legend class="font-weight-semibold ml-2 mr-2"><i class="icon-coins mr-2"></i>Account Details</legend>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Bank Name</label>
                                    <select  class="form-control" name="bankName" id="bankName">
                                        <option value="">Choose a Bank</option>
                                        @foreach($banks as $bank)
                                        <option value="{{ $bank }}" @if($user->bank_name == $bank) selected @endif>{{ $bank }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Account No.</label>
                                    <input type="text"  class="form-control" name="accountNo" id="accountNo" value="{{ $user->account_no }}" />
                                </div>
                            </div>

                            <button class="btn btn-primary font-weight-bold font-size-xs mb-2 ml-2" id="addPersonalData">
                                UPDATE BIODATA <i class="icon-paperplane"></i>
                            </button>

                        </div>
                    </fieldset>

                    <h6 id="education" tabindex="-1" class="title">Education</h6>

                    <fieldset id="educationFrm" role="tabpanel" aria-labelledby="steps-uid-0-h-1" class="body d-none">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>School Name</label>
                                    <input type="text" class="form-control" name="school_name" id="schoolName" />
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>From</label>
                                    <input type="date" class="form-control" name="sch_start_from" id="schoolFrom" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>To</label>
                                    <input type="date" class="form-control" name="sch_end" id="schoolEnd" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Qualification Obtained</label>
                                    <input type="text" name="qualification_obtained" placeholder="Bachelor, Master etc." class="form-control" id="qualificationObtained">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Specialization:</label>
                                    <input type="text" name="specialization" placeholder="Design, Development etc." class="form-control" >
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Address:</label>
                                    <input type="text" name="school_address" placeholder="Design, Development etc." class="form-control" >
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary font-weight-bold font-size-xs mb-2" id="addEducation">
                            ADD EDUCATION <i class="icon-paperplane"></i>
                        </button>

                    </fieldset>

                    <h6 id="experience" tabindex="-1" class="title">Experience</h6>
                    <fieldset id="experienceFrm" role="tabpanel" aria-labelledby="steps-uid-0-h-2" class="body d-none">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Company</label>
                                    <input type="text" name="company_name" placeholder="Company name" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Post Held</label>
                                    <input type="text" name="position_held" placeholder="Company name" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>From</label>
                                    <input type="date" name="company_from" placeholder="Company name" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>To</label>
                                    <input type="date" name="company_to" placeholder="Company name" class="form-control">
                                </div>
                            </div>
                            <button class="btn btn-primary font-weight-bold font-size-xs mb-2 ml-2" id="addExperience">
                                ADD EXPERIENCE <i class="icon-paperplane"></i>
                            </button>

                            
                        </div>
                    </fieldset>

                    <h6 id="dependants" tabindex="-1" class="title">Dependants</h6>
                    <fieldset id="dependantsFrm" role="tabpanel" aria-labelledby="steps-uid-0-h-3" class="body d-none">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input type="text" name="dependant_full_name" placeholder="John Doe" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Dependant Type</label>
                                    <input type="text" name="dependant_type" placeholder="Daughter" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date of Birth</label>
                                    <input type="date" name="dependant_dob" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Address</label>
                                    <input type="text" name="dependant_address" class="form-control" placeholder="23, Babatunde Jose, VI, Lagos." />
                                </div>
                            </div>
                            @if($user->has_dependant == TRUE)
                            <button class="btn btn-primary font-weight-bold font-size-xs mb-2 ml-2" id="addDependants">
                                ADD DEPENDANTS <i class="icon-paperplane"></i>
                            </button>
                            @endif
                        </div>
                    </fieldset>

                    <h6 id="additionalInfo" tabindex="-1" class="title">Additional info</h6>
                    <fieldset id="additionalInfoFrm" role="tabpanel" aria-labelledby="steps-uid-0-h-3" class="body d-none">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Guarantor Full Name</label>
                                    <input type="text" name="guarantor_full_name" placeholder="John Doe" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Guarantor Phone Number</label>
                                    <input type="text" name="guarantor_phone_no" placeholder="0802*******" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Guarantor Address</label>
                                    <textarea class="form-control" name="guarantor_address"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Next of Kin Full Name</label>
                                    <input type="text" name="nok_full_name" placeholder="Company name" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Next of Kin Phone Number</label>
                                    <input type="text" name="nok_phone_no" placeholder="Company name" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Next of Kin Address</label>
                                    <textarea class="form-control" name="nok_address"></textarea>
                                </div>
                            </div>
                            
                            <div>
                                <button class="btn btn-primary font-weight-bold font-size-xs mb-2 ml-2" id="addAdditionalInfo">
                                    ADD <i class="icon-paperplane"></i>
                                </button>
                                <button class="btn btn-primary font-weight-bold font-size-xs mb-2 ml-2 d-none" id="submitProfile">
                                    COMPLETED <i class="icon-paperplane"></i>
                                </button>
                            </div>
                            <p class="ml-2">Accept Terms & Conditions 
                                <input type="checkbox" id="confirmSubmission" />
                                <input type="hidden" value="" id="termsAndCondition">
                            </p>
                        </div>
                    </fieldset>
                    <div id="loader"></div>
                </div>
                
            </form>
        </div>
        <div class="col-md-5">
            <div class="table-responsive">
                <table class="table  table-condensed">
                    <thead class="table-info">
                        <th>Profile</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        <tr class="table-success">
                            <td>
                                <span class="font-weight-semibold text-primary">{{ $user->current_post_held }}</span>
                                <h3 class="m-0 font-weight-bold">{{ ucwords($user->first_name) }}, {{ ucwords($user->last_name) }}</h3>
                                <span class="text-danger font-weight-semibold">{{ $user->phone_no }}, {{ $user->email }}</span>
                            </td>
                            <td>
                                <i class="icon-pencil"></i>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table  table-bordered">
                    <thead class="table-info">
                        <th width="50%">School</th>
                        <th>Duration</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        @if(count($userEducation))
                        @foreach($userEducation as $education)
                        <tr class="table-success">
                            <td>
                                <span class="font-weight-semibold text-primary">{{ $education->qualification_obtained }}</span>
                                <p class="m-0 font-weight-bold font-size-sm">{{ ucwords($education->school_name) }}</p>
                            </td>
                            <td class="font-size-xs">
                                <span class="d-block">From: {{ date('d/m/Y', strtotime($education->sch_start_from)) }}</span>
                                <span class="d-block">To: {{ date('d/m/Y', strtotime($education->sch_end)) }}</span>
                            </td>
                            <td>
                                <i class="icon-pencil"></i>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="3">Education has not been added</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                <table class="table  table-bordered">
                    <thead class="table-info">
                        <th width="50%">Company Name</th>
                        <th>Duration</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        @if(count($userExperiences))
                        @foreach($userExperiences as $experience)
                        <tr class="table-success">
                            <td>
                                <span class="font-weight-semibold text-primary">{{ $experience->position_held }}</span>
                                <p class="m-0 font-weight-bold font-size-sm">{{ ucwords($experience->company_name) }}</p>
                            </td>
                            <td class="font-size-xs">
                                <span class="d-block">From: {{ date('d/m/Y', strtotime($experience->company_from)) }}</span>
                                <span class="d-block">To: {{ date('d/m/Y', strtotime($experience->company_to)) }}</span>
                            </td>
                            <td>
                                <i class="icon-pencil"></i>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="3">Education has not been added</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                
                <div class="d-none">
                    <table class="table  table-condensed">
                        <thead class="table-info">
                            <th>Dependants</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @if(count($userDependants)) 
                            @foreach($userDependants as $dependant)
                            <tr class="table-success">
                                <td>
                                    <span class="font-weight-semibold text-primary">{{ $dependant->dependant_type }}</span>
                                    <h3 class="m-0 font-weight-bold">{{ ucwords($dependant->dependant_full_name) }}</h3>
                                    <span class="text-danger font-weight-semibold">Date of Birth: {{ $dependant->dependant_dob }}</span>
                                </td>
                                <td>
                                    <i class="icon-pencil"></i>
                                </td>
                            </tr>
                            @endforeach
                            @else
                                <tr>
                                    <td colspan="2">You do not have any dependant yet.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <table class="table  table-bordered">
                        <thead class="table-info">
                            <th>Guarantor</th>
                            <th>Next of Kin</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @if(count($additionalInfos))
                            @foreach($additionalInfos as $extra)
                            <tr class="table-success">
                                <td>
                                    <span class="font-weight-semibold text-primary">{{ $extra->guarantor_phone_no }}</span>
                                    <p class="m-0 font-weight-bold font-size-sm">{{ ucwords($extra->guarantor_full_name) }}</p>
                                </td>
                                <td>
                                    <span class="font-weight-semibold text-primary">{{ $extra->nok_phone_no }}</span>
                                    <p class="m-0 font-weight-bold font-size-sm">{{ ucwords($extra->nok_full_name) }}</p>
                                </td>
                                <td>
                                    <i class="icon-pencil"></i>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="3">Additional information has not been added</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <p class="font-size-xs font-weight-semibold mt-2 ml-2 text-danger pointer">Show All</p>
            </div>
        </div>
    </div>
</div>
@stop

@section('script')
<script type="text/javascript">
    $(function() {
        $('#personalDataTab').addClass('current')
        $('#personalDataFrm').removeClass('d-none')
    })
    $('#personalDataTab').click(function() {
        $(this).addClass('current') 
        $('#educationTab').removeClass('current')
        $('#experienceTab').removeClass('current')
        $('#dependantsTab').removeClass('current')
        $('#additionalInfoTab').removeClass('current')

        $('#personalDataFrm').removeClass('d-none')
        $('#educationFrm').addClass('d-none')
        $('#experienceFrm').addClass('d-none')
        $('#dependantsFrm').addClass('d-none')
        $('#additionalInfoFrm').addClass('d-none')
    })

    $('#educationTab').click(function() {
        $(this).addClass('current')
        $('#personalDataTab').removeClass('current')
        $('#experienceTab').removeClass('current')
        $('#dependantsTab').removeClass('current')
        $('#additionalInfoTab').removeClass('current')

        $('#personalDataFrm').addClass('d-none')
        $('#educationFrm').removeClass('d-none')
        $('#experienceFrm').addClass('d-none')
        $('#dependantsFrm').addClass('d-none')
        $('#additionalInfoFrm').addClass('d-none')
    })

    $('#experienceTab').click(function() {
        $(this).addClass('current')
        $('#personalDataTab').removeClass('current')
        $('#educationTab').removeClass('current')
        $('#dependantsTab').removeClass('current')
        $('#additionalInfoTab').removeClass('current')

        $('#personalDataFrm').addClass('d-none')
        $('#educationFrm').addClass('d-none')
        $('#experienceFrm').removeClass('d-none')
        $('#dependantsFrm').addClass('d-none')
        $('#additionalInfoFrm').addClass('d-none')
    })

    $('#dependantsTab').click(function() {
        $(this).addClass('current')
        $('#personalDataTab').removeClass('current')
        $('#educationTab').removeClass('current')
        $('#experienceTab').removeClass('current')
        $('#additionalInfoTab').removeClass('current')

        $('#personalDataFrm').addClass('d-none')
        $('#educationFrm').addClass('d-none')
        $('#experienceFrm').addClass('d-none')
        $('#dependantsFrm').removeClass('d-none')
        $('#additionalInfoFrm').addClass('d-none')
    })

    $('#additionalInfoTab').click(function() {
        $(this).addClass('current')
        $('#personalDataTab').removeClass('current')
        $('#educationTab').removeClass('current')
        $('#experienceTab').removeClass('current')
        $('#dependantsTab').removeClass('current')

        $('#personalDataFrm').addClass('d-none')
        $('#educationFrm').addClass('d-none')
        $('#experienceFrm').addClass('d-none')
        $('#dependantsFrm').addClass('d-none')
        $('#additionalInfoFrm').removeClass('d-none')
    })

    $('#confirmSubmission').click(function() {
        $clickedStatus = $(this).is(':checked')
        if($clickedStatus) {
            $('#termsAndCondition').val(1)
            $('#addAdditionalInfo').addClass('d-none')
            $('#submitProfile').removeClass('d-none')
        }
        else{
            $('#termsAndCondition').val('')
            $('#addAdditionalInfo').addClass('d-none')
            $('#submitProfile').removeClass('d-none')
        }
    })


    $('#addPersonalData').click(function($e) {
        $e.preventDefault();
        $dateOfBirth = $('#dateOfBirth').val();
        if($dateOfBirth === "") {
            notification('#loader', '<i class="icon-x"></i>Date of birth is required')
            return false
        }
        $gender = $('#gender').val();
        if($gender === "") {
            notification('#loader', '<i class="icon-x"></i>Gender is required')
            return false
        }
        $address = $('#address').val();
        if($address === "") {
            notification('#loader', '<i class="icon-x"></i>Your personal address is required')
            return false
        }
        $bankName = $('#bankName').val();
        if($bankName === "") {
            notification('#loader', '<i class="icon-x"></i>Bank name is required')
            return false
        }
        $accountNo = $('#accountNo').val();
        if($accountNo === "") {
            notification('#loader', '<i class="icon-x"></i>Account number is required')
            return false
        }
        submitBioData('/store-bio-data', '#frmBioData', 'Profile')
    })

    const notification = (id, message) => {
        $(id).html(message).addClass('text-danger ml-3 mb-2 font-weight-bold').fadeIn(3000).delay(1000).fadeOut(2000)
    }

    $('#addEducation').click(function($e) {
        $e.preventDefault();
        submitBioData('/store-user-education', '#frmBioData', ' Education ')
    })

    $('#addExperience').click(function($e) {
        $e.preventDefault()
        submitBioData('/store-user-experience', '#frmBioData', ' Past Experiences ')
    })

    $('#addDependants').click(function($e) {
        $e.preventDefault()
        submitBioData('/store-user-dependants', '#frmBioData', ' Dependants ')
    })

    $('#addAdditionalInfo').click(function($e) {
        $e.preventDefault()
        submitBioData('/store-user-extras', '#frmBioData', ' Additional Info ')
    })

    const submitBioData = (url, frmName, section) => {
        $('#loader').html('<i class="icon-spinner3 spinner"></i>Please wait...').addClass('text-danger ml-3 mb-2 font-weight-bold')
        $.post(url, $(frmName).serialize(), (data) => {
            if(data === 'notAllowed') {
                notification('#loader', '<i class="icon-x"></i>Your position has not been verified by the HR.')
                return false
            }
            else{
                if(data == 'saved' || data == 'updated') {
                    notification('#loader', '<i class="icon-checkmark2"></i>'+section+' '+data+' successfully.')
                    window.location.href = '';
                }
            }
        })
    }



   

</script>
@stop
