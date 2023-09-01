<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ApplicantFile;
use Validator;
use Carbon\Carbon;

class ApplicantFileController extends Controller
{
    public function index()
    {

    }

    public function store(Request $request)
    {   
        // return $request;
        try {

            if($request->file('file'))
            {   
                    $file_extension = $request->file('file')->getClientOriginalExtension();
            }

            $file_date = Carbon::now()->format('Y-m-d');
            $uploadedFile = $request->file('file');
            $file_name = time().$uploadedFile->getClientOriginalName();
            $file_path = '/wysiwyg/applicant_files/' . $file_date;

            $uploadedFile->move(public_path() . $file_path, $file_name);
            
            $applicant_file = new ApplicantFile();
            $applicant_file->applicant_id = $request->applicant_id;
            $applicant_file->file_name = $file_name;
            $applicant_file->file_path = $file_path;
            $applicant_file->file_type = $file_extension;
            $applicant_file->title = $request->document_type;
            $applicant_file->save();

            return response()->json(['success' => 'File has been uploaded'], 200);

        } catch (\Exception $e) {
                
            return response()->json(['error' => $e->getMessage()], 200);
        }
    }

    public function delete(Request $request)
    {
        $file_id = $request->get('file_id');
        $file = ApplicantFile::find($file_id);
        
        //if record is empty then display error page
        if(empty($file->id))
        {
            return abort(404, 'Not Found');
        }

        $file->delete();

        $file_path = $file->file_path;

        $path = public_path() . $file_path . "/" . $file->file_name;
        unlink($path);

        return response()->json(['success' => 'Record has been deleted'], 200);
    }
}
