<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PrsSession;

class PrsSessionController extends Controller
{
    public function startPrs(Request $request) {
        $userId = $request->user_id;
        $position = $request->position;
        $startPrs = PrsSession::firstOrNew([
            'user_id' => $userId, 
            'position_held' => $position, 
        ]);
        $startPrs->save();
        return 'started';
    }

    public function jobDescription($user) {
        return view('hr.jd.create');
    }

    public function performanceReview($user) {
        return view('hr.review.create');
    }

    public function hrUserJd(Request $request) {
        $answer = '<div class="row">
                    <div class="col-md-12 table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <th class="table-success text-center" colspan="6">Job Description: Finance Graduate Trainee</th>
                                <tr style="background:#000; color:#fff">
                                    <th>Ref</th>
                                    <th>Main Duties</th>
                                    <th>Frequency</th>
                                    <th>Description</th>
                                    <th>Objective</th>
                                    <th>Standard Output</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>S1.0</td>
                                    <td>Bookkeeping</td>
                                    <td>Weekly</td>
                                    <td>Capture day to day financial transactions - verify, classify, compute, post and record accounts receivables data.</td>
                                    <td>Capture day to day financial transactions - verify, classify, compute, post and record accounts receivables data.</td>
                                    <td>All financial data captured accurately and timely</td>
                                </tr>
                                <tr>
                                    <td>S2.0</td>
                                    <td>Daily, weekly & monthly</td>
                                    <td>Weekly</td>
                                    <td>Capture day to day financial transactions - verify, classify, compute, post and record accounts receivables data.</td>
                                    <td>Capture day to day financial transactions - verify, classify, compute, post and record accounts receivables data.</td>
                                    <td>All financial data captured accurately and timely</td>
                                </tr>
                                <tr>
                                    <td>S3.0</td>
                                    <td>Client management</td>
                                    <td>Daily</td>
                                    <td>Capture day to day financial transactions - verify, classify, compute, post and record accounts receivables data.</td>
                                    <td>Capture day to day financial transactions - verify, classify, compute, post and record accounts receivables data.</td>
                                    <td>All financial data captured accurately and timely</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
        </div>';

        return $answer;
    }

    public function hrUserReview(Request $request) {
        $review = '<div class="row">
        <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th colspan="11" class="font-size-xs text-center table-success">
                            PERFORMANCE MEASUREMENTS <button class="btn btn-success font-weight-bold font-size-xs">UPDATE RATINGS</button>
                        </th>
                    </tr>
                    <tr class="text-center font-size-xs" style="background:#000; color:#fff">
                        <th>REF</th>
                        <th>DUTIES</th>
                        <th>WGHT(%)</th>
                        <th>PERFORMANCE INDICATOR</th>
                        <th>EXCEEDS EXPECTATIONS</th>
                        <th>MEET EXPECTATION</th>
                        <th>NEEDS IMPROVEMENT</th>
                        <th>UNSATISFACTORY</th>
                        <th>RATING <br> (SELF)</th>
                        <th>RATING<br> (REVR) <i class="icon-pencil text-danger pointer"></i></th>
                        <th>WEIGHTED AVG.</th>
                    </tr>
                </thead>
                <tbody class="text-center font-size-xs">
                    <tr>
                        <td>1</td>
                        <td>Product, features & Idea translation</td>
                        <td></td>
                        <td>Software Product: modules & component specific to company\'s requirement</td>
                        <td>Accurately assigns points to tasks. Inform stakeholders of project progress/blockers in a timely manner, adhere to global best practices of software development.</td>
                        <td>Breaks down each module into smaller tasks and classifies them. Constantly updates tool with progress or lack of it</td>
                        <td>Broke down the application into smaller chunks but cannot tell the difference between chores, bugs and features</td>
                        <td>Fails to break down modules into smaller, manageable tasks. </td>
                        <td>3</td>
                        <td><input type="hidden" style="width:30px; height:20px; outline: none" /></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Product, features & Idea translation</td>
                        <td></td>
                        <td>Software Product: modules & component specific to company\'s requirement</td>
                        <td>Accurately assigns points to tasks. Inform stakeholders of project progress/blockers in a timely manner, adhere to global best practices of software development.</td>
                        <td>Breaks down each module into smaller tasks and classifies them. Constantly updates tool with progress or lack of it</td>
                        <td>Broke down the application into smaller chunks but cannot tell the difference between chores, bugs and features</td>
                        <td>Fails to break down modules into smaller, manageable tasks. </td>
                        <td>3</td>
                        <td><input type="hidden" style="width:30px; height:20px; outline: none" /></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Product, features & Idea translation</td>
                        <td></td>
                        <td>Software Product: modules & component specific to company\'s requirement</td>
                        <td>Accurately assigns points to tasks. Inform stakeholders of project progress/blockers in a timely manner, adhere to global best practices of software development.</td>
                        <td>Breaks down each module into smaller tasks and classifies them. Constantly updates tool with progress or lack of it</td>
                        <td>Broke down the application into smaller chunks but cannot tell the difference between chores, bugs and features</td>
                        <td>Fails to break down modules into smaller, manageable tasks. </td>
                        <td>2</td>
                        <td><input type="hidden" style="width:30px; height:20px; outline: none" /></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="p-3 font-weight-bold">Comment on Review</td>
                        <td colspan="7"></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="p-3 font-weight-bold">RECOMMENDATIONS FOR ACTION BY SUPERVISOR (REVIEWER)</td>
                        <td colspan="7"></td>
                    </tr>
                    <tr>
                        <td colspan="11" class="p-3 font-weight-bold">ACKNOWLEDGEMENT: I have reviewed this document and discussed the contents with the reviewer and have been advised of my performance status. </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>';

    return $review;
    }
}
