<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\transporterRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\HttpResponse;
use App\transporter;
use App\transporterDocuments;

class transporterController extends Controller
{
    public function index() {
        $transporters = transporter::ORDERBY('transporter_name')->PAGINATE(50);
        return view('transportation.transporter', 
            compact(
                'transporters'
            )
        );
    }

    public function store(Request $request) {
        $check = transporter::WHERE('email', $request->email)->exists();
        if($check) {
            return 'exists';
        }
        else {
            $record = transporter::CREATE($request->all());
            $transporterId = $record->id;

            $documents = $request->file('document');
            $documentDescriptions = $request->description;

            if($request->hasFile('document')) {
                foreach($documents as $key=> $uploadedDocument){
                    if(isset($uploadedDocument) && $documentDescriptions[$key] != ''){
                        $transporterDocuments = transporterDocuments::CREATE(['transporter_id' => $transporterId, 'description' => $documentDescriptions[$key]]);
                        
                        $name = base64_encode($transporterId).'-'.str_slug($documentDescriptions[$key]).'.'.$uploadedDocument->getClientOriginalExtension();
                        $documents_path = public_path('assets/img/transporters/documents');
                        $documentPath = $documents_path.'/'.$name;
                        $uploadedDocument->move($documents_path, $name);
                        $transporterDocuments->document = $name;
                        $transporterDocuments->save();

                    }
                }
            }
            return 'saved';
        } 
    }

    public function edit($id) {
        $transporters = transporter::ORDERBY('transporter_name')->PAGINATE(50);
        $transporterDocuments = transporterDocuments::WHERE('transporter_id', $id)->GET(); 
        $recid = transporter::findOrFail($id);
        return view('transportation.transporter', 
            compact(
                'transporters',
                'recid',
                'transporterDocuments'
            )
        );
    }

    public function update(Request $request, $id) {
        $check = transporter::WHERE('email', $request->email)->WHERE('id', '!=', $id)->exists();
        if($check) {
            return 'exists';
        }
        else {

            $documents = $request->file('document');
            $documentDescriptions = $request->description;


            foreach($documentDescriptions as $key => $descriptions){
                if(isset($descriptions) && $descriptions != ''){
                    $recid = transporterDocuments::firstOrNew(['description' => $descriptions]);
                    $recid->transporter_id = $id;
                    $recid->description = $descriptions;     
                    $recid->save();
                }

                if($request->hasFile('document')) {
                    if(isset($request->document[$key]) && $request->document[$key] != ''){
                        $updateRecord = transporterDocuments::firstOrNew(['description' => $descriptions]);
                        $updateRecord->transporter_id = $id;
                        $name = base64_encode($id).'-'.str_slug($descriptions).'.'.$request->document[$key]->getClientOriginalExtension();
                        $documents_path = public_path('assets/img/transporters/documents');
                        $documentPath = $documents_path.'/'.$name;
                        $request->document[$key]->move($documents_path, $name);
                        $updateRecord->document = $name;
                        $updateRecord->save();
                    }
                }
            }

            return 'updated';
        }
    }

    public function destroy() {

    }

    public function deleteTransporterDocument($id) {
        $documentName = transporterDocuments::findOrFail($id);
        $documentName->destroy($id);
        $path = $_SERVER['DOCUMENT_ROOT'].'/assets/img/transporters/documents/'.$documentName->document;
        unlink($path);
        return 'deleted';
        
    }
}
