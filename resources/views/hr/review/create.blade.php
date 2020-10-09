@extends('layout')
@section('title')Performance Review Activity @stop

@section('css')
<style type="text/css">
    .table td, .table th {
        padding:3px;
    }
</style>
@stop

@section('main')


    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">{{'Performance Review'}}</span></h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>

            <div class="header-elements d-none">
                <div class="d-flex justify-content-center">
                    <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calculator text-primary"></i> <span>JD</span></a>
                    <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calendar5 text-primary"></i> <span>ECDP</span></a>
                </div>
            </div>
        </div>
    </div>
    <p class="m-2 font-weight-sm font-weight-semibold d-block text-center">Instructions: Using the ratings indicated below, carefully evaluate and score performance against the assessment criteria </p>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <table class="table table-condensed">
                        <thead class="font-size-xs">
                            <tr>
                                <td>Name: Odejobi Olushola </td>
                                <td>Reviewer: Timi Koleolu</td>
                            </tr>
                            <tr>
                                <td>Job Title: Field Ops</td>
                                <td>Reviewers Job Title: C.E.O</td>
                            </tr>
                            <tr>
                                <td>Time in Position: 1 Year, 6 Months.</td>
                                <td>Review Period: Maiden Year</td>
                            </tr>
                            <tr>
                                <td>Previous Review Dates: NA</td>
                                <td>previous Review Score: NA</td>
                            </tr>
                            <tr>
                                <td>Time in Organization: 1 Year, 2 Weeks</td>
                                <td>Promotions: NA</td>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div> 
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered font-size-xs">
                        <thead>
                            <tr>
                                <th colspan="4" class="text-center font-weight-bold p-2">RATINGS GUIDE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                <strong>(4) Exceeds Requirements</strong><br>
                                Consistently exceeds established standards in most areas of responsibility. All requirements were accomplished and objectives were achieved above the established standards.</td>
                                <td>
                                <strong>(3) Meets Requirements</strong><br>
                                All job requirements and planned objectives were accomplished within established standards. There were no critical areas where accomplishments were less than planned.</td>
                                <td>
                                <strong>(2) Needs improvement</strong><br>
                                Not all job requirements and planned objectives were accomplished within the established standards.  More than one critical area was below established standards</td>
                                <td>
                                <strong>(1) Unsatisfactory</strong><br>
                                Job requirements and planned objectives have not been accomplished within established standards.  Consistently performs below standards in most areas </td>
                            </tr>
                            
                        </tbody>
                    </table>
                </div>
            </div> 
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th colspan="11" class="font-size-xs text-center table-success">PERFORMANCE MEASUREMENTS</th>
                    </tr>
                    <tr class="text-center font-size-xs" style="background:#000; color:#fff">
                        <th>REF</th>
                        <th>DUTIES</th>
                        <th>WGHT(%)</th>
                        <th>EXCEEDS EXPECTATIONS</th>
                        <th>MEET EXPECTATION</th>
                        <th>NEEDS IMPROVEMENT</th>
                        <th>UNSATISFACTORY</th>
                        <th>RATING <br> (SELF)</th>
                        <th>RATING<br> (REVR)</th>
                        <th>WEIGHTED AVG.</th>
                    </tr>
                </thead>
                <tbody class="text-center font-size-xs">
                    <tr>
                        <td>1</td>
                        <td>Product, features & Idea translation</td>
                        <td>Software Product: modules & component specific to company's requirement</td>
                        <td>Accurately assigns points to tasks. Inform stakeholders of project progress/blockers in a timely manner, adhere to global best practices of software development.</td>
                        <td>Breaks down each module into smaller tasks and classifies them. Constantly updates tool with progress or lack of it</td>
                        <td>Broke down the application into smaller chunks but cannot tell the difference between chores, bugs and features</td>
                        <td>Fails to break down modules into smaller, manageable tasks. </td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Product, features & Idea translation</td>
                        <td>Software Product: modules & component specific to company's requirement</td>
                        <td>Accurately assigns points to tasks. Inform stakeholders of project progress/blockers in a timely manner, adhere to global best practices of software development.</td>
                        <td>Breaks down each module into smaller tasks and classifies them. Constantly updates tool with progress or lack of it</td>
                        <td>Broke down the application into smaller chunks but cannot tell the difference between chores, bugs and features</td>
                        <td>Fails to break down modules into smaller, manageable tasks. </td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Product, features & Idea translation</td>
                        <td>Software Product: modules & component specific to company's requirement</td>
                        <td>Accurately assigns points to tasks. Inform stakeholders of project progress/blockers in a timely manner, adhere to global best practices of software development.</td>
                        <td>Breaks down each module into smaller tasks and classifies them. Constantly updates tool with progress or lack of it</td>
                        <td>Broke down the application into smaller chunks but cannot tell the difference between chores, bugs and features</td>
                        <td>Fails to break down modules into smaller, manageable tasks. </td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="p-3 font-weight-bold">Comment on Review</td>
                        <td colspan="7"></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="p-3 font-weight-bold">RECOMMENDATIONS FOR ACTION BY SUPERVISOR (REVIEWER)</td>
                        <td colspan="7"></td>
                    </tr>
                    <tr>
                        <td colspan="10" class="p-3 font-weight-bold">ACKNOWLEDGEMENT: I have reviewed this document and discussed the contents with the reviewer and have been advised of my performance status. </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>


@stop

@section('script')

@stop


