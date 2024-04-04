<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Exports\ApplicantsExport;
use App\Applicant;
use App\ApplicantEducAttain;
use App\ApplicantExperience;
use App\ApplicantReference;
use App\ApplicantFamilyMember;
use App\ApplicantDependent;
use App\ApplicantFile;
use App\Position;
use App\Branch;
use App\JobVacancy;
use Validator;
use Excel;
use Mail;
use File;
use DB;
use Auth;
use Carbon\Carbon;

class ApplicantController extends Controller
{
  public function get_applicants_old(){
    $job_applicants = DB::table('applicants')
                        ->select('id', 
                                 DB::raw("CONCAT(lastname, ', ', firstname, ', ', IFNULL(middlename,'')) AS name"),
                                 'address', 
                                 'birthdate', 
                                 'age', 
                                 'contact_no', 
                                 'email', 
                                 'educ_attain', 
                                 'course', 
                                 'school_grad',
                                 'how_learn',
                                 'file',
                                 'status',
                                 'created_at',
                                 'updated_at'
                        )
                        ->orderBy('id', 'DESC')
                        ->get()
                        ->each(function ($row, $index) {
                          $row->cnt_id = $index + 1;
                        });								

    return response()->json(['job_applicants' => $job_applicants], 200);
  }

	public function all_job_applicants()
	{

		$job_applicants = DB::table('job_vacancies')
												->join('applicants', 'applicants.jobvacancy_id', '=', 'job_vacancies.id')
												->join('positions', 'positions.id', '=', 'job_vacancies.position_id')
												->join('branches', 'branches.id', '=', 'applicants.branch_id')
												->leftJoin('branches as branch_complied', 'branch_complied.id', '=', 'applicants.branch_complied')
												->leftJoin('positions as employment_position', 'employment_position.id', '=', 'applicants.employment_position')
												->leftJoin('branches as employment_branch', 'employment_branch.id', '=', 'applicants.employment_branch')
												->select('applicants.id AS id',
																 'applicants.branch_id',
																 DB::raw('branches.name as branch_applied'),
																 DB::raw('DATE_FORMAT(applicants.created_at, "%m/%d/%Y") as date_applied'),
																 'positions.name AS position_name',
																 DB::raw("CONCAT(applicants.lastname, ', ', applicants.firstname, IFNULL(CONCAT(', ', applicants.middlename),'')) AS name"),
																 'applicants.lastname',
																 'applicants.firstname',
																 'applicants.middlename',
														     'applicants.address',
														 		 DB::raw('DATE_FORMAT(applicants.birthdate, "%m/%d/%Y") as birthdate'),
															 	 'applicants.age',
																 'applicants.gender',
														     'applicants.contact_no', 
																 'applicants.civil_status', 
														     'applicants.email',
														     'applicants.educ_attain',
														     'applicants.course',
																 'applicants.school_grad',
														     'applicants.how_learn',
																 DB::raw("DATE_FORMAT(applicants.created_at, '%m/%d/%Y') AS created_at"),
																 'branches.name AS branch_name',
																 'applicants.status',
																 'applicants.position_preference',
																 'applicants.branch_preference',
														 		 DB::raw('applicants.status as screening_status'),
																 'applicants.initial_interview_status',
														 		 DB::raw('DATE_FORMAT(applicants.initial_interview_date, "%m/%d/%Y") as initial_interview_date'),
														     'applicants.position_preference',
																 'applicants.branch_preference',
																 'applicants.iq_status',
																 DB::raw('branch_complied.id as branch_id_complied'),
																 DB::raw('branch_complied.name as branch_complied'),
																 'applicants.bi_status',
																 DB::raw('DATE_FORMAT(applicants.final_interview_date, "%m/%d/%Y") as final_interview_date'),
														 		 'applicants.final_interview_status',
																 'employment_position.name as employment_position',
																 'employment_branch.name as employment_branch',
																 DB::raw('employment_branch.id as employment_branch_id'),
																 'applicants.hiring_officer_position',
																 'applicants.hiring_officer_name',
														 		 DB::raw('DATE_FORMAT(applicants.orientation_date, "%m/%d/%Y") as orientation_date'),
														 		 DB::raw('DATE_FORMAT(applicants.signing_of_contract_date, "%m/%d/%Y") as signing_of_contract_date')
																);	

		return $job_applicants;
	}

  public function get_applicants_new(){

		$job_applicants = $this->all_job_applicants()->where(function($query) {
																										$user = Auth::user();
																										if($user->hasRole('Branch Manager'))
																										{
																											//status 0 = on process, 1 = passed
																											$query->where(function($qry){
																																//where status either on on process or passed; exclude failed status
																																// $qry->where(function($q){
																																// 			$q->whereIn('iq_status', [0, 1])
																																// 				->whereNull('bi_status');
																																// 		})
																																// 		->orWhere(function($q){
																																// 			$q->whereIn('bi_status', [0, 1])
																																// 				->whereNull('final_interview_status');
																																// 		});
																																$qry->whereIn('iq_status', [0, 1])
																																		->orWhereIn('bi_status', [0, 1]);
																																			
																															})
																															->where(function($qry) {
																																$qry->where(function($q){
																																				$user = Auth::user();
																																				// $q->whereNotNull('branch_complied.id')
																																				// 	->where(function($d){
																																				// 		$d->whereNull('final_interview_status')
																																				// 			->orWhere('final_interview_status', 0);
																																				// 	})
																																				$q->where(function($d) use ($user){
																																					$d->where('branch_complied.id', $user->branch_id)
																																						->whereNull('employment_branch');
																																					});
																																				
																																		})
																																		->orWhereNull('branch_complied.id')
																																		->orWhere(function($qry) { // where final interview is passed then get the record where user branch is equal to employment branch
																																			$user = Auth::user();

																																			$qry->where('final_interview_status', [1, 4])
																																					->whereNotNull('final_interview_status')
																																					->where('employment_branch', $user->branch_id);
																																		});
																															});
																										}
																										
																								})
																								->orderBy('applicants.created_at', 'DESC')
																								->get()
																								->each(function ($row, $index) {
																									$row->cnt_id = $index + 1;
																								});
		$applicantsArr = [];
		$user = Auth::user();

		foreach ($job_applicants as $applicant) {
			
			$branch_preference = $applicant->branch_preference;

			if($user->hasRole('Branch Manager'))
			{
				if($branch_preference)
				{
					$branch_ids = explode(',', $branch_preference);

					if(in_array($user->branch_id, $branch_ids))
					{
						$applicantsArr[] = $applicant;
					}
				}
			}
			else
			{
				$applicantsArr[] = $applicant;
			}

		}

		$branches = DB::table('branches')
												->orderBy('id', 'ASC')
												->get();

		$positions = Position::orderBy('name', 'Asc')->get();

		return response()->json(['job_applicants' => $applicantsArr, 'branches' => $branches, 'positions' => $positions], 200);										
	}

	public function submit_application(Request $req){
		// return $req;
		try{
			$applicant_lastname 	= $req->get('lastname');
			$applicant_firstname  = $req->get('firstname');
			$applicant_middlename = $req->get('middlename');
			$job 								  = $req->get('jobvacancy_id');

			$check_applicant = DB::table('applicants')
													 ->where('lastname', '=', $applicant_lastname)
													 ->where('firstname', '=', $applicant_firstname)
													 ->where('middlename', '=', $applicant_middlename)
													 ->where('jobvacancy_id', '=', $job)
													 ->get();
			
			if($check_applicant->count() > 0){
				return response()->json(['duplicate' => true, 'message' => 'Duplicate records']);
			}else{
				$rules = [
					'lastname.required' 	  => 'Last name is required.',
					'firstname.required' 	  => 'First name is required.',
					// 'middlename.required'   => 'Middle name is required.',
					'address.required' 		  => 'Address is required.',
					'birth_place.required'  => 'Birth place is required',
					'birthdate.required' 	  => 'Birthday is required.',
					'gender.required' 	   	=> 'Gender is required.',
					'civil_status.required' => 'Civil Status is required.',	
					'contact_no.required'   => 'Contact Number is required.',
					'contact_no.digits'  	  => 'Must be a valid Phone Number.',
	
					// 'email.required' 			  => 'Email is required.',
					'email.email' 			    => 'Must be a valid Email Address.',
					'citizenship.required'  => 'Citizenship is required',
					'religion.required'  		=> 'Religion is required',
					'weight.required'		    => 'Enter a valid value',
          'weight.numeric' 				=> 'Enter a valid value',
          'weight.between'	 			=> 'Enter a valid value',
					'height.required'		    => 'Enter a valid value',
          'height.numeric' 				=> 'Enter a valid value',
          'height.between'	 			=> 'Enter a valid value',
					'educ_attain.required' 	=> 'Educational attainment is required.',
					// 'course.required' 		 => 'Course is required.',
					// 'school_grad.required' => 'School graduated is required.',
					'how_learn.required' 	  => 'This field is required.',
					'file.required' 			  => 'File is required.',
				];

				$valid_fields = [
					'lastname' 								=> 'required',
					'firstname' 							=> 'required',
					// 'middlename' 							=> 'required',
					'address'									=> 'required',
					'birthdate'								=> 'required',
					'birth_place'								=> 'required',
					'gender'		  						=> 'required',
					'civil_status'						=> 'required',
					'gender'     	 						=> 'required',
					'contact_no' 							=> 'required|digits:11',
					'email' 									=> 'nullable|email',
					'educ_attain' 						=> 'required',
					// 'course' 				=> 'required',
					// 'school_grad' 	=> 'required',
					'how_learn' 							=> 'required',
					'file'			  						=> 'required',
					'religion'								=> 'required',
					'citizenship'							=> 'required',
					'weight'			  					=> 'required|numeric|between:0.00000001, 999999.99',
					'height'			 	 					=> 'required|numeric|between:0.00000001, 999999.99',
					'references.*.name' 			=> 'required',
					'references.*.address' 		=> 'required',
					'references.*.contact' 		=> 'required',
				];

				$educ_attain = $req->educ_attain;
				$k_12_highschool = filter_var($req->k_12_highschool, FILTER_VALIDATE_BOOLEAN);
				$post_hs_levels = ['Vocational School', 'College Graduate', 'College Undergraduate', 'Graduate School'];

				if($educ_attain == 'Highschool Graduate' || (!$k_12_highschool && in_array($educ_attain, $post_hs_levels)))
				{
					$valid_fields['highschool.school'] = 'required';
					$valid_fields['highschool.sy_start'] = 'required';
					$valid_fields['highschool.sy_end'] = 'required';
				}

				if($educ_attain == 'Junior Highschool Graduate' || $k_12_highschool)
				{
					$valid_fields['jr_highschool.school'] = 'required';
					$valid_fields['jr_highschool.sy_start'] = 'required';
					$valid_fields['jr_highschool.sy_end'] = 'required';
				}

				if($educ_attain == 'Senior Highschool Graduate' || $k_12_highschool)
				{
					$valid_fields['sr_highschool.school'] = 'required';
					$valid_fields['sr_highschool.sy_start'] = 'required';
					$valid_fields['sr_highschool.sy_end'] = 'required';
				}

				if($educ_attain == 'Vocational School')
				{	
					$valid_fields['vocational_school.school'] = 'required';
					$valid_fields['vocational_school.course'] = 'required';
					$valid_fields['vocational_school.sy_start'] = 'required';
					$valid_fields['vocational_school.sy_end'] = 'required';
				}

				if(in_array($educ_attain, ['College Graduate', 'College Undergraduate', 'Graduate School']))
				{
					$valid_fields['college.school'] = 'required';
					$valid_fields['college.course'] = 'required';
					$valid_fields['college.sy_start'] = 'required';
					$valid_fields['college.sy_end'] = 'required';
				}

				if($educ_attain == 'Graduate School')
				{	
					$valid_fields['graduate_school.school'] = 'required';
					$valid_fields['graduate_school.course'] = 'required';
					$valid_fields['graduate_school.sy_start'] = 'required';
					$valid_fields['graduate_school.sy_start'] = 'required';
				}
				
				$validator = Validator::make($req->all(), $valid_fields, $rules);
				
				if($validator->fails()){
					return response()->json($validator->errors(), 200);
				}
	
				// $resume_file = $req->file('file');

				// $random_string_1 = Str::random(6);
				// $random_string_2 = Str::random(4);

				// $random_string = $random_string_1 . $random_string_2;

				// if(Applicant::where('file', $random_string )->exists()) {
				// 	$random_string_1 = Str::random(4);
				// 	$random_string_2 = Str::random(6);
		
				// 	$random_string = $random_string_1 . $random_string_2;
				// }
	
				// $file_name = $random_string . '.' . $resume_file->getClientOriginalExtension();
	
				// //Move to file directory.
				// $resume_file->move(public_path() . '/wysiwyg' . '/resume_files' . '/', $file_name);		

				$applicant  								= new Applicant();
				$applicant->jobvacancy_id		= $req->get('jobvacancy_id');
				$applicant->branch_id				= $req->get('branch_id');
				$applicant->lastname 				= $req->get('lastname');
				$applicant->firstname 			= $req->get('firstname');
				$applicant->middlename 			= $req->get('middlename');
				$applicant->address 				= $req->get('address');
				$applicant->address2 				= $req->get('address2');
				$applicant->birth_place 		= $req->get('birth_place');
				$applicant->birthdate 			= $req->get('birthdate');
				$applicant->age 						= $req->get('age');
				$applicant->gender 					= $req->get('gender');
				$applicant->civil_status 		= $req->get('civil_status');
				$applicant->contact_no 			= $req->get('contact_no');
				$applicant->email 					= $req->get('email');
				$applicant->citizenship 		= $req->get('citizenship');
				$applicant->religion 				= $req->get('religion');
				$applicant->height 					= $req->get('height');
				$applicant->weight 					= $req->get('weight');
				$applicant->educ_attain 		= $req->get('educ_attain');
				// $applicant->course 					= $req->get('course');
				// $applicant->school_grad 		= $req->get('school_grad');
				$applicant->sss_no 					= $req->get('sss_no');
				$applicant->philhealth_no 	= $req->get('philhealth_no');
				$applicant->pagibig_no 			= $req->get('pagibig_no');
				$applicant->tin_no 					= $req->get('tin_no');
				$applicant->how_learn 			= $req->get('how_learn');
				// $applicant->file 						= $file_name;
				$applicant->status 					= 0;
				$applicant->save();
				
				$file_extension = '';
				if($req->file('file'))
				{   
						$file_extension = $req->file('file')->getClientOriginalExtension();
				}
				$file_date = Carbon::now()->format('Y-m-d');
				$uploadedFile = $req->file('file');
				$file_name = time().$uploadedFile->getClientOriginalName();
				$file_path = '/wysiwyg/applicant_files/' . $file_date;

				$uploadedFile->move(public_path() . $file_path, $file_name);
				
				$applicant_file = new ApplicantFile();
				$applicant_file->applicant_id = $applicant->id;
				$applicant_file->file_name = $file_name;
				$applicant_file->file_path = $file_path;
				$applicant_file->file_type = $file_extension;
				$applicant_file->title = "Resume";
				$applicant_file->save();

				if($educ_attain == 'Highschool Graduate' || !$k_12_highschool)
				{
					$hs = $req->highschool;
					ApplicantEducAttain::create(
						[
							'applicant_id' => $applicant->id,
							'educ_level' => 'HighSchool',
							'school' => $hs['school'],
							'sy_attended' => $hs['sy_start'] . ' to ' . $hs['sy_end'],
						]
					);
				}

				if($educ_attain == 'Junior Highschool Graduate' || $k_12_highschool)
				{
					$hs = $req->jr_highschool;
					ApplicantEducAttain::create(
						[
							'applicant_id' => $applicant->id,
							'educ_level' => 'Junior HighSchool',
							'school' => $hs['school'],
							'sy_attended' => $hs['sy_start'] . ' to ' . $hs['sy_end'],
						]
					);
				}

				if($educ_attain == 'Senior Highschool Graduate' || $k_12_highschool)
				{
					$hs = $req->sr_highschool;
					ApplicantEducAttain::create(
						[
							'applicant_id' => $applicant->id,
							'educ_level' => 'Senior HighSchool',
							'school' => $hs['school'],
							'sy_attended' => $hs['sy_start'] . ' to ' . $hs['sy_end'],
						]
					);
				}

				if($educ_attain == 'Vocational School')
				{	
					$voc = $req->vocational_school;
					ApplicantEducAttain::create(
						[
							'applicant_id' => $applicant->id,
							'educ_level' => 'Vocational School',
							'school' => $voc['school'],
							'course' => $voc['course'],
							'major' => $voc['major'],
							'sy_attended' => $voc['sy_start'] . ' to ' . $voc['sy_end'],
						]
					);
				}

				if(in_array($educ_attain, ['College Graduate', 'College Undergraduate', 'Graduate School']))
				{
					$college = $req->college;
					ApplicantEducAttain::create(
						[
							'applicant_id' => $applicant->id,
							'educ_level' => 'College',
							'school' => $college['school'],
							'course' => $college['course'],
							'major' => $college['major'],
							'sy_attended' => $college['sy_start'] . ' to ' . $college['sy_end'],
						]
					);
				}

				if($educ_attain == 'Graduate School')
				{	
					$grad = $req->graduate_school;
					ApplicantEducAttain::create([
						'applicant_id' => $applicant->id,
						'educ_level' => 'Graduate School',
						'school' => $grad['school'],
						'course' => $grad['course'],
						'major' => $grad['major'],
						'sy_attended' => $grad['sy_start'] . ' to ' . $grad['sy_end'],
					]);
				}

				foreach ($req->experiences as $value) {

					$date_of_service = $value['service_start'] && $value['service_end'] ? $value['service_start'] . ' to ' . $value['service_end'] : '';

					ApplicantExperience::create([
						'applicant_id' => $applicant->id,
						'employer' => $value['company'],
						'position' => $value['position'],
						'salary' => $value['salary'],
						'date_of_service' => $date_of_service,
						'job_description' => $value['job_description'],
					]);
				}

				foreach ($req->references as $value) {
					ApplicantReference::create([
						'applicant_id' => $applicant->id,
						'name' => $value['name'],
						'address' => $value['address'],
						'contact' => $value['contact'],
						'company' => $value['company'],
						'position' => $value['position'],
					]);
				}

				
				foreach ($req->dependents as $value) {
					ApplicantDependent::create([
						'applicant_id' => $applicant->id,
						'name' => $value['name'],
						'relationship' => $value['relationship'],
						'age' => $value['age'],
						'address' => $value['address'],
						'occupation' => $value['occupation'],
					]);
				}

				$fam_members = ['father', 'mother', 'spouse', 'guardian'];

				foreach ($fam_members as $fam) {

					${$fam} = $req[$fam];

					ApplicantFamilyMember::create([
						'applicant_id' => $applicant->id,
						'relationship' => $fam,
						'name' => ${$fam}['name'],
						'age' => ${$fam}['age'],
						'address' => ${$fam}['address'],
						'contact' => ${$fam}['contact'],
						'occupation' => ${$fam}['occupation'],
					]);
				}
	
				$email_name = $req->get('email');
	
				$html = '<html>
									<p>Good day!</p>
									<p>Thank you for your interest to be part of ADDESSA Corporation. 
										Please be informed that your application will be reviewed and evaluated.
										If you are selected for an interview, you can expect a phone call from us.
										We do appreciate the time that you invested in this application. Thank you
										and God bless!
									</p>
									<br>
									<span>Best Regards,</span>
									<br>
									<br>
									<span>ADDESSA Corporation</span>
									<br>
									<span>Human Resource Team</span>
								</html>';
	
				if($email_name)
				{
					Mail::send([], [], function ($message) use ($html, $email_name) {
						$message->to($email_name)
										->subject('Addessa Online Job Application')
										->from('addessa.corporation2013@gmail.com', 'Addessa Corporation')
										->setBody($html, 'text/html');
					});
				}
   
				// Mail::send([], [], function($message) {
				// 	$message->to('jeremiahherreria19@gmail.com', 'Tutorials Point');
				// 	$message->subject('Laravel Basic Testing Mail');
				// 	$message->from('addessa.corporation2013@gmail.com','Jeremiah Herreria');
				// 	$message->setBody("<p>This is a test email.</p>", 'text/html');
				// });
	
				return response()->json(['success' => true, 'resp' => 'Record has successfully added!'], 200);
			}
		} catch (\Exception $e) {
			return response()->json(['success' => false, 'Error form submit', 'error' => $e], 200);
		}
	}

	public function delete_applicant($id){

		$applicant = Applicant::find($id);

		// $filename = DB::table('applicants')
		// 							->select('file')
		// 							->whereId($id)
		// 							->get();

		//unlink file in public storage
		// foreach($filename as $data){
		// 	$filename = $data->file;
		// 	$destinationPath = public_path(). '/wysiwyg/resume_files/';
		// 	File::delete($destinationPath.$filename);
		// }

		if(empty($applicant->id))
		{
				return abort(404, 'Not Found');
		}

		$files = ApplicantFile::where('applicant_id', $applicant->id)->get();
    
		foreach ($files as $file) {

			//if record is empty then display error page
			if(empty($file->id))
			{
					return abort(404, 'Not Found');
			}

			ApplicantFile::find($file->id)->delete();

			$file_path = $file->file_path;

			$path = public_path() . $file_path . "/" . $file->file_name;
			unlink($path);
		}
		
		if($applicant->delete()){
			return response()->json(['success' => true, 'message' => 'Applicant Deleted Successfully!']);
		}else{
			return response()->json(['success' => false, 'message' => 'Applicant Deletion failed!']);
		}
	}

  public function view_applicants($id){
    $job_applicants = DB::table('applicants')
							->select('id', 
							 DB::raw("CONCAT(lastname, ', ', firstname, ', ', middlename) AS name"),
									 'address',
							 DB::raw('DATE_FORMAT(birthdate, "%m-%d-%Y") as birthdate'),
									 'age',
									 'gender',
									 'contact_no', 
									 'email',
									 'educ_attain',
									 'course',
									 'school_grad',
									 'how_learn',
									 'file',
									 'status'
							)
							->where('id', $id)					
							->get();			

		return response()->json(['success' => true, 'resp' => $job_applicants]);
  }

	public function view_applicants_new($id){

		$applicant = DB::table('job_vacancies')
												->join('applicants', 'applicants.jobvacancy_id', '=', 'job_vacancies.id')
												->join('positions', 'positions.id', '=', 'job_vacancies.position_id')
												->join('branches', 'branches.id', '=', 'applicants.branch_id')
												->leftJoin(DB::raw('branches as tbranches'), 'tbranches.id', '=', 'applicants.branch_complied')
												->select(DB::raw('DATE_FORMAT(applicants.created_at, "%m/%d/%Y") as date_submitted'),
																 DB::raw('DATE_FORMAT(applicants.created_at, "%Y-%m-%d") as date_applied'),
																 'positions.id AS position_id',
																 'positions.name AS position_name',
																 'branches.id AS branch_id',
																 'branches.name AS branch_name',
																 'applicants.id', 
																 DB::raw("CONCAT(applicants.lastname, ', ', applicants.firstname, IFNULL(CONCAT(', ', applicants.middlename),'')) AS name"),
																 'applicants.lastname',
																 'applicants.firstname',
																 DB::raw("IFNULL(applicants.middlename, '') as middlename"),
																 'applicants.address',
																 'applicants.address2',
																 'applicants.birth_place',
												 				 DB::raw('DATE_FORMAT(applicants.birthdate, "%m/%d/%Y") as birthdate'),
																 'applicants.age',
																 'applicants.gender',
																 'applicants.civil_status',
																 'applicants.contact_no', 
																 'applicants.email',
																 'applicants.educ_attain',
																 'applicants.course',
																 'applicants.school_grad',
																 'applicants.how_learn',
																 'applicants.file',
																 'applicants.status',
																 'applicants.initial_interview_date',
																 'applicants.initial_interview_status',
																 'applicants.position_preference',
																 'applicants.branch_preference',
																 DB::raw('tbranches.id as branch_id_complied'),
																 DB::raw('tbranches.name as branch_complied'),
																 'applicants.iq_status',
																 'applicants.bi_status',
																 'applicants.final_interview_date',
																 'applicants.final_interview_status',
																 'applicants.employment_position',
																 'applicants.employment_branch',
																 'applicants.hiring_officer_position',
																 'applicants.hiring_officer_name',
																 'applicants.orientation_date',
																 'applicants.signing_of_contract_date',
																)
												->where('applicants.id', $id)					
												->get()->first();
												
		$educ_attains = ApplicantEducAttain::where('applicant_id', $id)->get();
		$experiences = ApplicantExperience::where('applicant_id', $id)->get();
		$references = ApplicantReference::where('applicant_id', $id)->get();
		$fam_members = ApplicantFamilyMember::where('applicant_id', $id)->get();
		$dependents = ApplicantDependent::where('applicant_id', $id)->get();
		$applicant_files = ApplicantFile::where('applicant_id', $id)->get();

		return response()->json([
			'success' => true, 
			'educ_attains' => $educ_attains,
			'applicant' => $applicant,
			'experiences' => $experiences,
			'references' => $references,
			'fam_members' => $fam_members,
			'dependents' => $dependents,
			'applicant_files' => $applicant_files,
		]);
	}

	public function export_applicants(Request $req){

		$branch_id = $req->branch_id;
		$date_from = $req->date_from;
		$date_to = $req->date_to;
		$fields = ['status', 'initial_interview_status', 'iq_status', 'bi_status', 'final_interview_status'];
		$step = $req->step;
		$field = $fields[$step];
		// id: 1000 - Admin
		// else branch - branches

		$applicants = DB::table('applicants')
										->join('branches', 'branches.id', '=', 'applicants.branch_id')
										->select(DB::raw('row_number() OVER(ORDER BY applicants.id) AS cnt_id'),
														 DB::raw('DATE_FORMAT(applicants.created_at, "%m-%d-%Y") as date_applied'),
														 				 'applicants.lastname',
																		 'applicants.firstname',
																		 'applicants.middlename',
														         'applicants.address',
														 DB::raw('DATE_FORMAT(applicants.birthdate, "%m-%d-%Y") as birthdate'),
															 			 'applicants.age',
														         'applicants.contact_no', 
														         'applicants.email',
														         'applicants.educ_attain',
														         'applicants.course',
																		 'applicants.school_grad',
														         'applicants.how_learn',
														DB::raw("(CASE WHEN applicants.status = 0 THEN ('On-Progress') 
																 					 WHEN applicants.status = 1 THEN ('Qualified') 
																					 WHEN applicants.status = 2 THEN ('Not-Qualified')
																					 WHEN applicants.status = 3 THEN ('Hired')
																					 WHEN applicants.status = 4 THEN ('Failed')
																				ELSE 'None' 
																			END) AS status")
														)
										->where(function($result) use ($branch_id, $date_from, $date_to){
											if($branch_id === "1000"){
												$result->whereDate(DB::raw('DATE_FORMAT(applicants.created_at, "%Y-%m-%d")'), '>=', $date_from);
												$result->whereDate(DB::raw('DATE_FORMAT(applicants.created_at, "%Y-%m-%d")'), '<=', $date_to);
											}else{
												$result->where('branches.id', '=', $branch_id);
												$result->whereDate(DB::raw('DATE_FORMAT(applicants.created_at, "%Y-%m-%d")'), '>=', $date_from);
												$result->whereDate(DB::raw('DATE_FORMAT(applicants.created_at, "%Y-%m-%d")'), '<=', $date_to);
											}
										})
										->where(function($result) use ($step, $field) {
											if(isset($step))
											{
												$result->whereIn($field, [0, 2]);
											}
										})
										->get();
		

		if($applicants->isEmpty()){
			return response()->json(['success' => true, 'resp' => $applicants, 'branch_id' => $branch_id]);
		}else{
			return response()->json(['success' => false, 'resp' => "No records found."]);
		}
	}

  public function export_applicants_new(Request $req){
		
		$branch_id = $req->branch_id;
		$date_from = $req->date_from;
		$date_to = $req->date_to;
		$fields = ['status', 'initial_interview_status', 'iq_status', 'bi_status', 'final_interview_status'];
		$step = $req->step;
		// $field = $fields[$step];
		
		// id: 1000 - Admin
		// else branch - branches
		// $applicants = $this->get_initial_interview_list()->toArray();
		

		// START IQ test list
																						
		$user = Auth::user();
		$applicantsData = $this->all_job_applicants()->where(function($result) use ($date_from, $date_to){
														$result->whereDate(DB::raw('DATE_FORMAT(applicants.created_at, "%Y-%m-%d")'), '>=', $date_from);
														$result->whereDate(DB::raw('DATE_FORMAT(applicants.created_at, "%Y-%m-%d")'), '<=', $date_to);																
												});
		$applicants = [];

		// if $step has value then get the data per progress
		if(isset($step))
		{
			if($step == 2)//IQ Test
			{
				$applicantsData->whereIn('applicants.iq_status', [0, 2, 3])
											 ->orderBy('applicants.created_at', 'DESC');

				$applicantsArr = [];
				
				foreach ($applicantsData->get() as $applicant) {

					$branch_preference = $applicant->branch_preference;

					if($branch_preference)
					{
						$branch_ids = explode(',', $branch_preference);

						if($branch_id != 1000 && in_array($branch_id, $branch_ids)) //$branch_id with value 1000 means 'ALL'
						{
							$applicantsArr[] = $applicant;
						}
						
						if($branch_id == 1000) //$branch_id with value 1000 means 'ALL'
						{
							$applicantsArr[] = $applicant;
						}
					}
				
				}
				
				$applicants = $applicantsArr;
				// END IQ test list
			}
			else
			{
				$applicantsData->where(function($result) use ($step, $fields) {
											if(isset($step))
											{
												$result->whereNotNull('applicants.'.$fields[$step])
															 ->whereIn('applicants.'.$fields[$step], [0, 2, 3]); //where status is on process or failed
											}
										})
										->where(function($query) use ($user, $branch_id, $step) {

											//status 0 = on process, 1 = passed
											$query->where(function($qry) use ($user, $branch_id, $step) {
																if($branch_id != 1000)
																{
																	if($step == 0 || $step == 1) // Screening or Initial Interview
																	{	
																		$qry->where('branch_id', $branch_id);
																	}
																	else if($step == 3 || $step == 4 ) // BI or Final Interview
																	{
																		$qry->where('branch_complied.id', $branch_id);
																	}
																}
															});
									})
									->orderBy('applicants.created_at', 'DESC')
									->get()
									->each(function ($row, $index) {
										$row->cnt_id = $index + 1;
									})
									->toArray();	

				$applicants = $applicantsData->get();
			}
		}
		else
		{
			$applicantsArr = [];
			
			foreach ($applicantsData->get() as $key => $applicant) {
				if($branch_id != 1000)
				{
					if(!$applicant->branch_preference && $applicant->branch_id == $branch_id) // Screening or Initial Interview status
					{
						$applicantsArr[] = $applicant;

					}
					else if($applicant->branch_preference && !$applicant->branch_complied) // IQ Test status
					{
						
						$branch_ids = explode(',', $applicant->branch_preference);

						if($branch_id != 1000 && in_array($branch_id, $branch_ids)) //$branch_id with value 1000 means 'ALL'
						{
							$applicantsArr[] = $applicant;
						}

					}
					else if($applicant->branch_complied && ($applicant->final_interview_status == null || $applicant->final_interview_status == 0)) // BI Status
					{	

						if($applicant->branch_id_complied == $branch_id)
						{
							$applicantsArr[] = $applicant;
						}

					}
					else if($applicant->employment_branch) // final interview
					{

						if($applicant->employment_branch_id == $branch_id)
						{
							$applicantsArr[] = $applicant;
						}
					}
				}
				else
				{
					$applicantsArr[] = $applicant;
				}	

			}

			$applicants = $applicantsArr;
	
		}

	
		$branches = Branch::get();
		$positions = Position::get();
		
		$arrApplicants = [];
		$status = ['On Process', 'Passed', 'Failed', 'Did Not Comply', 'Reserved'];
					
		foreach ($applicants as $key => $applicant) {
			
			$applicant_files = ApplicantFile::where('applicant_id', $applicant->id)->where('title', '!=', 'Resume')->pluck('title')->toArray();
			$arrApplicants[$key] = [];
			$fields = array_keys((array)$applicant);

			foreach ($fields as $field) {
				$arrApplicants[$key][$field] = $applicants[$key]->{$field};

				$position_preference_id = $applicant->position_preference;
				$position_preference = null;

				$branch_preference_id = $applicant->branch_preference;
				$branch_preference = null;
				
				if($position_preference_id)
				{
					$exploded_position_preference_id  = explode(',', $position_preference_id);
					$position_preferences = $positions->whereIn('id', $exploded_position_preference_id)->pluck('name')->toArray();
					$position_preference = count($position_preferences) ? implode(", ", $position_preferences) : null;
				}

				if($branch_preference_id)
				{
					$exploded_branch_preference_id  = explode(',', $branch_preference_id);
					$branch_preferences = $branches->whereIn('id', $exploded_branch_preference_id)->pluck('name')->toArray();
					$branch_preference = count($branch_preferences) ? implode(", ", $branch_preferences) : null;
				}

				$screening_status = $applicant->screening_status;
				$initial_interview_status = $applicant->initial_interview_status;
				$iq_status = $applicant->iq_status;
				$bi_status = $applicant->bi_status;
				$final_interview_status = $applicant->final_interview_status;

				$arrApplicants[$key]['screening_status'] = !is_null($screening_status) ? $status[$screening_status] : null;
				$arrApplicants[$key]['initial_interview_status'] = !is_null($initial_interview_status) ? $status[$initial_interview_status] : null;
				$arrApplicants[$key]['iq_status'] = !is_null($iq_status) ? $status[$iq_status] : null;
				$arrApplicants[$key]['bi_status'] = !is_null($bi_status) ? $status[$bi_status] : null;
				$arrApplicants[$key]['final_interview_status'] = !is_null($final_interview_status) ? $status[$final_interview_status] : null;
				$arrApplicants[$key]['requirements'] = implode(", ", $applicant_files);
				$arrApplicants[$key]['position_preference'] = $position_preference;
				$arrApplicants[$key]['branch_preference'] = $branch_preference;
				
			}
			
		}		
		

		// if($applicants->count()){
		if(count($arrApplicants)){	
			return response()->json(['success' => true, 'resp' => $arrApplicants, 'branch_id' => $branch_id]);
		}else{
			return response()->json(['success' => false, 'resp' => "No records found."]);
		}
	}

	public function export_total_number_of_applicants (Request $request)
	{

		$date_from = $request->date_from;
		$date_to = $request->date_to;
		$asOfDate = $request->asOfDate;
		$branch_id = $request->branch_id;

		// $firstDayofPreviousMonth = Carbon::now()->startOfMonth()->subMonthsNoOverflow()->toDateString();
		// $lastDayofPreviousMonth = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();

		$lastDayofPreviousMonth = Carbon::parse($asOfDate)->subMonthsNoOverflow()->endOfMonth()->toDateString(); // last day last month
		$sixty_days_diff = Carbon::parse($asOfDate)->addDay(-60)->format('Y-m-d');

		$arrApplicants = [];
		$positions = Position::with('job_vacancies')
												 ->whereHas('job_vacancies', function($query) {
													$query->where('branch_type', 0); // branch_type = 0 -> position for branch, branch_type = 1 -> positions for admin
												 })
												 ->get();

		$branches = Branch::where( function($query) use ($branch_id) {
													if($branch_id <> 1000) // not 'ALL BRANCHES'
													{
														$query->where('id', $branch_id);
													}
											})
											->where('name', '<>', 'ALL BRANHES')
											->get();

		foreach ($branches as $branch) {

			// get data from current month
			$applicants = $this->all_job_applicants()->whereDate(DB::raw('DATE_FORMAT(applicants.created_at, "%Y-%m-%d")'), '<=', $asOfDate)																
																							 ->where('branch_id', $branch->id);
			$total_applicants = $this->all_job_applicants()
															 ->whereDate(DB::raw('DATE_FORMAT(applicants.created_at, "%Y-%m-%d")'), '<=', $asOfDate)																
			 											   ->where('branch_id', $branch->id)
															 ->count();
			$initial_interview_passed = $this->all_job_applicants()
																			 ->whereDate(DB::raw('DATE_FORMAT(applicants.created_at, "%Y-%m-%d")'), '<=', $asOfDate)																
																		   ->where('branch_id', $branch->id)
																			 ->where('initial_interview_status', 1);

			// failed in screening and initial interview																 
			$screening_initial_failed = $this->all_job_applicants()
																			 ->whereDate(DB::raw('DATE_FORMAT(applicants.created_at, "%Y-%m-%d")'), '<=', $asOfDate)																
																			 ->where('branch_id', $branch->id)
																			 ->where(function($query) {
																					$query->whereIn('initial_interview_status', [2, 3]) //initial interview
																								->orWhereIn('applicants.status', [2, 3]); //screening
																			 });

			$arrApplicants[$branch->name] = [
				'total_count' => [
														'total_applicants' => $total_applicants,
														'total_initial_passed' => $initial_interview_passed->count(),
														'total_screening_initial_failed' => $screening_initial_failed->count(),
													]
			];

			foreach ($positions as $position) {
				
				// get data from previous month
				$total_initial_passed_per_position_last_month = $this->all_job_applicants()
																														->whereDate(DB::raw('DATE_FORMAT(applicants.created_at, "%Y-%m-%d")'), '<=', $lastDayofPreviousMonth)																
																														->where('branch_id', $branch->id)
																														->where('positions.name', $position->name)
																														->where('initial_interview_status', 1)
																														->count();

				$applicants = $this->all_job_applicants()->whereDate(DB::raw('DATE_FORMAT(applicants.created_at, "%Y-%m-%d")'), '<=', $asOfDate)																
																								 ->where('employment_branch.id', $branch->id)
																								 ->where('employment_position.name', $position->name);
				$qualified_per_position = $applicants->whereIn('final_interview_status', [1, 4])->count(); // qualified status(Final Interview Passed and Reserved)
				$hired_per_position = $this->all_job_applicants()->whereDate('signing_of_contract_date', '<=', $asOfDate)// Hired if signing of contract has value		
																	 ->where('employment_branch.id', $branch->id)
																	 ->where('employment_position.name', $position->name)
																	 ->count();

				// expired means not deployed within 0 days
				$expired_per_position = $this->all_job_applicants()->whereDate('initial_interview_date', '<', $sixty_days_diff)		
																		->whereNull('signing_of_contract_date')
																		->where(function($query) {
																				//where status values are neither 'failed' nor 'did not comply'
																				$query->whereNotIn('initial_interview_status', [0, 2, 3])
																							->orWhere(function($q) {
																								$q->whereNotIn('iq_status', [2, 3]);
																							})
																							->orWhere(function($q) {
																								$q->whereNotIn('bi_status', [2, 3]);
																							})
																							->orWhere(function($q) {
																								$q->whereNotIn('final_interview_status', [2, 3]);
																							});
																			
																		})
																		->where('positions.name', $position->name)
																		->where(DB::raw("IFNULL(IFNULL(employment_branch.id, branch_complied.id), branch_id)"), $branch->id)
																		->count();

				$end_bal_per_position = $qualified_per_position - $hired_per_position;

				$arrApplicants[$branch->name][$position->name] = [
																														'beg_bal' => $total_initial_passed_per_position_last_month,
																														'qualified' => $qualified_per_position,
																														'hired' => $hired_per_position,
																														'expired' => $expired_per_position,
																														'end_bal' => $end_bal_per_position,
																														// 'end_bal' => $applicants->count(), 
																													];
				
			}
		}
		if(count($arrApplicants)){	
			return response()->json(['success' => true, 'resp' => $arrApplicants]);
		}else{
			return response()->json(['success' => false, 'resp' => "No records found."]);
		}
	}

	public function update_status(Request $req){
		// return $req;
		$req->initial_interview_date;
		$id = $req->applicant_id;

		$applicant = Applicant::find($id);	
					
		$step = $req->step;
		$user = Auth::user();

		if($step == 0) //screening
		{

			$applicant->status = $req->status;
			$applicant->initial_interview_date = $req->initial_interview_date;
			$applicant->initial_interview_status = 0;
			
			if(in_array($req->status, [0, 2]))
			{
				$applicant->initial_interview_date = null;
				$applicant->initial_interview_status = null;
			}
			
		}
		else if($step == 1) //initial interview
		{

			$applicant->initial_interview_status = $req->initial_interview_status;
			$applicant->position_preference = $req->position_preference;
			$applicant->branch_preference = $req->branch_preference;
			$applicant->iq_status = 0; //iq test on process

			if(in_array($req->initial_interview_status, [0, 2, 3]))
			{
				$applicant->iq_status = null; // iq test on process
				$applicant->position_preference = null;
				$applicant->branch_preference = null;
			}
		}
		else if($step == 2) //iq test
		{

			// if user is branch manager and status is not On Process then return warning message
			if($applicant->iq_status > 0 && $user->hasRole('Branch Manager'))
			{
				return response()->json(['warning' => 'Status is already updated'], 200);
			}

			$applicant->iq_status = $req->iq_status;
			$applicant->branch_complied = $req->branch_id_complied;
			$applicant->bi_status = 0;
			
			if(in_array($req->iq_status, [0, 2, 3]))
			{
				$applicant->bi_status = null; //background investiation on process
			}
			
		}
		else if($step == 3) //background investigation
		{
			// if user is branch manager and status is not On Process then return warning message
			if($applicant->bi_status > 0 && $user->hasRole('Branch Manager'))
			{
				return response()->json(['warning' => 'Status is already updated'], 200);
			}
			
			$applicant->bi_status = $req->bi_status;
			$applicant->final_interview_status = 0;
			if(in_array($req->bi_status, [0, 2, 3]))
			{
				$applicant->final_interview_status = null; // iq test on process
				$applicant->final_interview_date = null;
				$applicant->final_interview_status = null;
				$applicant->employment_position = null;
				$applicant->employment_branch = null;
				$applicant->hiring_officer_position = null;
				$applicant->hiring_officer_name = null;
				$applicant->orientation_date = null;
				$applicant->signing_of_contract_date = null;
			}
		}
		else if($step == 4) //final interview
		{
			$applicant->final_interview_date = $req->final_interview_date;
			$applicant->final_interview_status = $req->final_interview_status;
			$applicant->employment_position = $req->employment_position;
			$applicant->employment_branch = $req->employment_branch;
			$applicant->hiring_officer_position = $req->hiring_officer_position;
			$applicant->hiring_officer_name = $req->hiring_officer_name;
			$applicant->orientation_date = $req->orientation_date;
			$applicant->signing_of_contract_date = $req->signing_of_contract_date;

			if(in_array($req->final_interview_status, [0, 2, 3]))
			{
				$applicant->employment_position = null;
				$applicant->employment_branch = null;
				$applicant->hiring_officer_position = null;
				$applicant->hiring_officer_name = null;
				$applicant->orientation_date = null;
				$applicant->signing_of_contract_date = null;
			}
		}

		$applicant->save();

		$applicant = DB::table('job_vacancies')
								   ->join('applicants', 'applicants.jobvacancy_id', '=', 'job_vacancies.id')
								   ->join('positions', 'positions.id', '=', 'job_vacancies.position_id')
								   ->join('branches', 'branches.id', '=', 'applicants.branch_id')
								   ->select(
										//DB::raw('row_number() OVER(ORDER BY applicants.id) AS cnt_id'),
								   				 'applicants.id AS id',
								   				 'positions.name AS position_name',
								   				 DB::raw("CONCAT(applicants.lastname, ', ', applicants.firstname, IFNULL(CONCAT(', ', applicants.middlename),'')) AS name"),
								   				 DB::raw("DATE_FORMAT(applicants.created_at, '%M %d, %Y') AS created_at"),
								   				 'branches.name AS branch_name',
								   				 'applicants.status'
								   				)
								   ->where('applicants.id', $id)->first();		

		return response()->json(['success' => true, 'resp' => 'Updated status successfully.', 'applicant' => $applicant]);
	}

	public function download_file(Request $req)
	{
		try {

				$file = ApplicantFile::find($req->file_id);

				$title = $file->title; 
				$file_path = $file->file_path;    
				$file_name = $file->file_name;
				$file_type = $file->file_type;

				$file = public_path() . $file_path . "/" . $file_name;
				$headers = array('Content-Type: application/' . $file_type,);

				return response()->download($file, $title . '.' . $file_type, $headers);

		} catch (\Exception $e) {
				
				return response()->json(['error' => $e->getMessage()], 200);
		}
	}

	public function get_all_status_count()
	{

		$screening_ctr = count($this->get_screening_list());
		$initial_interview_ctr = count($this->get_initial_interview_list());
		$iq_test_ctr = count($this->get_iq_test_list());
		$bi_ctr = count($this->get_bi_list());
		$final_interview_ctr = count($this->get_final_interview_list());
		$orientation_ctr = count($this->get_orientation_list());

		return response()->json([
			'screening_ctr' => $screening_ctr,
			'initial_interview_ctr' => $initial_interview_ctr,
			'iq_test_ctr' => $iq_test_ctr,
			'bi_ctr' => $bi_ctr,
			'final_interview_ctr' => $final_interview_ctr,
			'orientation_ctr' => $orientation_ctr,
		]);
	}

	public function get_applicants_today_list() 
	{
		$applicants = Applicant::where('created_at', now()->day)
											     ->where('status', 0)
												   ->get();
		
		return response()->json(['applicants' => $applicants], 200);
	}

	public function get_screening_list() 
	{
		$job_applicants = $this->all_job_applicants()->where('applicants.status', 0)
																								// ->whereIn('applicants.status', [0, 2])// where status 0 or 2 (on process or failed)
																								->orderBy('applicants.created_at', 'DESC')
																								->get()
																								->each(function ($row, $index) {
																										$row->cnt_id = $index + 1;
																								}); 
		return $job_applicants;
	}

	public function get_initial_interview_list() 
	{
		$job_applicants = $this->all_job_applicants()->where('applicants.initial_interview_status', 0)
																									// ->whereIn('initial_interview_status', [0, 2, 3]) // where status 0, 2 or 3 (on process or failed or did not comply)
																									->orderBy('applicants.initial_interview_date', 'DESC')
																									->get()
																									->each(function ($row, $index) {
																											$row->cnt_id = $index + 1;
																									});

		return $job_applicants;
	}

	public function get_iq_test_list() 
	{
		$applicants = $this->all_job_applicants()->where('applicants.iq_status', 0)
																					  // ->whereIn('iq_status', [0, 2, 3]) // where status 0, 2 or 3 (on process or failed or did not comply)
																						->orderBy('applicants.created_at', 'DESC')
																						->get()
																						->each(function ($row, $index) {
																								$row->cnt_id = $index + 1;
																						});
		$applicantsArr = [];
		$user = Auth::user();

		foreach ($applicants as $applicant) {
			
			$branch_preference = $applicant->branch_preference;

			if($user->hasRole('Branch Manager'))
			{
				if($branch_preference)
				{
					$branch_ids = explode(',', $branch_preference);

					if(in_array($user->branch_id, $branch_ids))
					{
						$applicantsArr[] = $applicant;
					}
				}
			}
			else
			{
				$applicantsArr[] = $applicant;
			}

		}

		return $applicantsArr;
	}

	public function get_bi_list() 
	{
		$job_applicants = $this->all_job_applicants()->where('applicants.bi_status', 0)
																						// ->whereIn('bi_status', [0, 2, 3]) // where status 0, 2 or 3 (on process or failed or did not comply)
																						->where(function($query) {
																							$user = Auth::user();
																							if($user->hasRole('Branch Manager'))
																							{
																								$query->where('applicants.branch_complied', $user->branch_id);
																							}
																						})
																						->orderBy('applicants.created_at', 'DESC')
																						->get()
																						->each(function ($row, $index) {
																								$row->cnt_id = $index + 1;
																						});

		return $job_applicants;
	}

	public function get_final_interview_list() 
	{
		$job_applicants = $this->all_job_applicants()->where('applicants.final_interview_status', 0)
																				    // ->whereIn('final_interview_status', [0, 2, 3]) // where status 0, 2 or 3 (on process or failed or did not comply)
																						->where(function($query) {
																							$user = Auth::user();
																							if($user->hasRole('Branch Manager'))
																							{
																								$query->where('branch_complied.id', $user->branch_id);
																							}
																						})
																						->orderBy('applicants.final_interview_date', 'DESC')
																						->get()
																						->each(function ($row, $index) {
																								$row->cnt_id = $index + 1;
																						});

		return $job_applicants;
	}

	public function get_orientation_list() 
	{
		$job_applicants = $this->all_job_applicants()->where('applicants.final_interview_status', 1)
																				    // ->whereIn('final_interview_status', [0, 2, 3]) // where status 0, 2 or 3 (on process or failed or did not comply)
																						->where(function($query) {
																							$user = Auth::user();
																							if($user->hasRole('Branch Manager'))
																							{
																								$query->where('applicants.employment_branch', $user->branch_id);
																							}
																						})
																						->whereDate('applicants.orientation_date', '>=', Carbon::now()->format('Y-m-d'))
																						->orderBy('applicants.orientation_date', 'DESC')
																						->get()
																						->each(function ($row, $index) {
																								$row->cnt_id = $index + 1;
																						});

		return $job_applicants;
	}


	public function screening_list() 
	{
		$job_applicants = $this->get_screening_list(); 

		$branches = DB::table('branches')
												->orderBy('id', 'ASC')
												->get();

		$positions = Position::orderBy('name', 'Asc')->get();

		return response()->json(['job_applicants' => $job_applicants, 'branches' => $branches, 'positions' => $positions], 200);
	}

	public function initial_interview_list() 
	{
		$job_applicants = $this->get_initial_interview_list();

		$branches = DB::table('branches')
												->orderBy('id', 'ASC')
												->get();

		$positions = Position::orderBy('name', 'Asc')->get();

		return response()->json(['job_applicants' => $job_applicants, 'branches' => $branches, 'positions' => $positions], 200);
	}

	public function iq_test_list() 
	{
		$job_applicants = $this->get_iq_test_list();
		
		$branches = DB::table('branches')
		->orderBy('id', 'ASC')
		->get();

		$positions = Position::orderBy('name', 'Asc')->get();

		return response()->json(['job_applicants' => $job_applicants, 'branches' => $branches, 'positions' => $positions], 200);
	}

	public function bi_list() 
	{
		$job_applicants = $this->get_bi_list();

		$branches = DB::table('branches')
		->orderBy('id', 'ASC')
		->get();

		$positions = Position::orderBy('name', 'Asc')->get();

		return response()->json(['job_applicants' => $job_applicants, 'branches' => $branches, 'positions' => $positions], 200);
	}

	public function final_interview_list() 
	{
		$job_applicants = $this->get_final_interview_list();

		$branches = DB::table('branches')
		->orderBy('id', 'ASC')
		->get();

		$positions = Position::orderBy('name', 'Asc')->get();

		return response()->json(['job_applicants' => $job_applicants, 'branches' => $branches, 'positions' => $positions], 200);
	}

	public function orientation_list() 
	{
		$job_applicants = $this->get_orientation_list();

		$branches = DB::table('branches')
		->orderBy('id', 'ASC')
		->get();

		$positions = Position::orderBy('name', 'Asc')->get();

		return response()->json(['job_applicants' => $job_applicants, 'branches' => $branches, 'positions' => $positions], 200);
	}

	public function cancel_status(Request $req) 
	{
		$applicant = Applicant::find($req->applicant_id);

		$step = $req->step;

		if($step == 0) //screening
		{
			$applicant->status = 0;
			$applicant->initial_interview_date = null;
			$applicant->initial_interview_status = null;
			
			if(in_array($req->status, [0, 2]))
			{
				$applicant->initial_interview_status = 0; // initial interview on process
				$applicant->initial_interview_date = null;			}
			
		}
		else if($step == 1) //inital interview
		{
			$applicant->initial_interview_status = $req->initial_interview_status;
			$applicant->position_preference = $req->position_preference;
			$applicant->branch_preference = $req->branch_preference;
			$applicant->iq_status = $req->iq_status ? $req->iq_status : 0; //iq test on process

			if(in_array($req->initial_interview_status, [0, 2]))
			{
				$applicant->iq_status = 0; // iq test on process
			}
		}
		else if($step == 2) //iq test
		{
			$applicant->iq_status = $req->iq_status;

			if(in_array($req->bi_status, [0, 2]))
			{
				$applicant->bi_status = 0; //background investiation on process
			}
			
		}
		else if($step == 3) //background investigation
		{
			$applicant->bi_status = $req->bi_status;

			if(in_array($req->bi_status, [0, 2]))
			{
				$applicant->final_interview_status = 0; // iq test on process
				$applicant->final_interview_date = null;
				$applicant->final_interview_status = null;
				$applicant->employment_position = null;
				$applicant->employment_branch = null;
				$applicant->orientation_date = null;
				$applicant->signing_of_contract_date = null;
			}
		}
		else if($step == 4) //final interview
		{
			$applicant->final_interview_date = $req->final_interview_date;
			$applicant->final_interview_status = $req->final_interview_status;
			$applicant->employment_position = $req->employment_position;
			$applicant->employment_branch = $req->employment_branch;
			$applicant->orientation_date = $req->orientation_date;
			$applicant->signing_of_contract_date = $req->signing_of_contract_date;

			if(in_array($req->final_interview_status, [0, 2]))
			{
				$applicant->employment_position = null;
				$applicant->employment_branch = null;
				$applicant->orientation_date = null;
				$applicant->signing_of_contract_date = null;
			}
		}

		$applicant->save();
	}

	public function delete_applicants_old() {

		$six_months_old = (Carbon::now())->addDays(-180);
												
		Applicant::whereDate(DB::raw('DATE_FORMAT(applicants.created_at, "%Y-%m-%d")'), '<=', $six_months_old)
							->where(function($query){
								$query->where('final_interview_status', '<>', 1)
											->orWhereNull('final_interview_status');
							})
							->delete();

		// foreach ($applicants as $key => $applicant) {
		// 	DB::table('consolidations')->insert(
		// 		['applicant_id' => $applicant->id]
		// 	);
		// }	

		// $logs = DB::table('consolidations')->get();

		return response()->json(['success' => 'Records has been deleted'], 200);
	}
	
}
