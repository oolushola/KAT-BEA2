<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\IssueType;


class IssueTypeController extends Controller
{
    public function index() {
        $issueTypes = IssueType::ALL();
        return view('transportation.issues.issue-type', compact('issueTypes'));
    }

    public function store(Request $request) {
        $validator = $this->validate($request, [
            'issue_category' => 'required',
            'issue_type' => 'required'
        ]);
        if($validator) {
            $checker = IssueType::WHERE('issue_category', $request->issue_category)->WHERE('issue_type', $request->issue_type)->exists();
            if($checker) {
                return 'exists';
            }
            else {
                IssueType::CREATE($request->all());
                return 'saved';
            }
        }
        else{
            return $validator;
        }
    }

    public function edit($id) {
        $recid = IssueType::findOrFail($id);
        $issueTypes = IssueType::ALL();
        return view('transportation.issues.issue-type', compact('issueTypes', 'recid'));
    }

    public function update(Request $request, $id) {
        $validator = $this->validate($request, [
            'issue_category' => 'required',
            'issue_type' => 'required'
        ]);
        if($validator) {
            $checker = IssueType::WHERE('issue_category', $request->issue_category)->WHERE('issue_type', $request->issue_type)->WHERE('id', '!=', $id)->exists();
            if($checker) {
                return 'exists';
            }
            else {
                $issueType = IssueType::findOrFail($id);
                $issueType->UPDATE($request->all());
                $issueType->save();
                return 'updated';
            }
        }
        else{
            return $validator;
        }
    }

    public function delete() {

    }
}
