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
use Validator;
use Excel;
use Mail;
use File;
use DB;
use Auth;

class ApplicantController extends Controller
{
  public function get_applicants_old(){
    $job_applicants = DB::table('applicants')
                        ->select('id', 
                                 DB::raw("CONCAT(lastname, ', ', firstname, ', ', middlename) AS name"),
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

  public function get_applicants_new(){

		$job_applicants = DB::table('job_vacancies')
												->join('applicants', 'applicants.jobvacancy_id', '=', 'job_vacancies.id')
												->join('positions', 'positions.id', '=', 'job_vacancies.position_id')
												->join('branches', 'branches.id', '=', 'applicants.branch_id')
												->select('applicants.id AS id',
																 'positions.name AS position_name',
																 DB::raw("CONCAT(applicants.lastname, ', ', applicants.firstname, ', ', applicants.middlename) AS name"),
																 DB::raw("DATE_FORMAT(applicants.created_at, '%M %d, %Y') AS created_at"),
																 'branches.name AS branch_name',
																 'applicants.status'
																)
												->where(function($query) {
														$user = Auth::user();
														if($user->hasRole('Branch Manager'))
														{
															$query->where('applicants.status', 1)
																		->where('applicants.branch_id', $user->branch_id);
														}
												})
												->orderBy('applicants.created_at', 'DESC')
												->get()
												->each(function ($row, $index) {
													$row->cnt_id = $index + 1;
												});	

		$branches = DB::table('branches')
												->orderBy('id', 'ASC')
												->get();

		return response()->json(['job_applicants' => $job_applicants, 'branches' => $branches], 200);										
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
					'middlename.required'   => 'Middle name is required.',
					'address.required' 		  => 'Address is required.',
					'birth_place.required'  => 'Birth place is required',
					'birthdate.required' 	  => 'Birthday is required.',
					'gender.required' 	   	=> 'Gender is required.',
					'civil_status.required' => 'Civil Status is required.',	
					'contact_no.required'   => 'Contact Number is required.',
					'contact_no.digits'  	  => 'Must be a valid Phone Number.',
	
					'email.required' 			  => 'Email is required.',
					'email.email' 			    => 'Must be a valid Email Address.',
					'citizenship.required'  => 'Citizenship is required',
					'religion.required'  		=> 'Religion is required',
					'weight.required'		    => 'Enter a valid value',
          'weight.numeric' 				=> 'Enter a valid value',
          'weight.between'	 			=> 'Enter a valid value',
					'height.required'		    => 'Enter a valid value',
          'height.numeric' 				=> 'Enter a valid value',
          'height.between'	 			=> 'Enter a valid value',
					'educ_attain.required' => 'Educational attainment is required.',
					// 'course.required' 		 => 'Course is required.',
					// 'school_grad.required' => 'School graduated is required.',
					'how_learn.required' 	  => 'This field is required.',
					'file.required' 			  => 'File is required.',
				];

				$valid_fields = [
					'lastname' 								=> 'required',
					'firstname' 							=> 'required',
					'middlename' 							=> 'required',
					'address'									=> 'required',
					'birthdate'								=> 'required',
					'gender'		  						=> 'required',
					'civil_status'						=> 'required',
					'gender'     	 						=> 'required',
					'contact_no' 							=> 'required|digits:11',
					'email' 									=> 'required|email',
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
					'references.*.contact' 	=> 'required',
				];

				$educ_attain = $req->educ_attain;
				$k_12_highschool = filter_var($req->k_12_highschool, FILTER_VALIDATE_BOOLEAN);
				$post_hs_levels = ['Vocational School', 'College Graduate', 'College Undergraduate', 'Graduate School'];

				if($educ_attain == 'Highschool Graduate' || (!$k_12_highschool && in_array($educ_attain, $post_hs_levels)))
				{
					$valid_fields['highschool.school'] = 'required';
					$valid_fields['highschool.sy_attended'] = 'required';
				}

				if($educ_attain == 'Junior Highschool Graduate' || $k_12_highschool)
				{
					$valid_fields['jr_highschool.school'] = 'required';
					$valid_fields['jr_highschool.sy_attended'] = 'required';
				}

				if($educ_attain == 'Senior Highschool Graduate' || $k_12_highschool)
				{
					$valid_fields['sr_highschool.school'] = 'required';
					$valid_fields['sr_highschool.sy_attended'] = 'required';
				}

				if($educ_attain == 'Vocational School')
				{	
					$valid_fields['vocational_school.school'] = 'required';
					$valid_fields['vocational_school.course'] = 'required';
					$valid_fields['vocational_school.sy_attended'] = 'required';
				}

				if(in_array($educ_attain, ['College Graduate', 'College Undergraduate', 'Graduate School']))
				{
					$valid_fields['college.school'] = 'required';
					$valid_fields['college.course'] = 'required';
					$valid_fields['college.sy_attended'] = 'required';
				}

				if($educ_attain == 'Graduate School')
				{	
					$valid_fields['graduate_school.school'] = 'required';
					$valid_fields['graduate_school.course'] = 'required';
					$valid_fields['graduate_school.sy_attended'] = 'required';
				}
				
				$validator = Validator::make($req->all(), $valid_fields, $rules);
				
				if($validator->fails()){
					return response()->json($validator->errors(), 200);
				}
	
				$resume_file = $req->file('file');

				$random_string_1 = Str::random(6);
				$random_string_2 = Str::random(4);

				$random_string = $random_string_1 . $random_string_2;

				if(Applicant::where('file', $random_string )->exists()) {
					$random_string_1 = Str::random(4);
					$random_string_2 = Str::random(6);
		
					$random_string = $random_string_1 . $random_string_2;
				}
	
				$file_name = $random_string . '.' . $resume_file->getClientOriginalExtension();
	
				//Move to file directory.
				$resume_file->move(public_path() . '/wysiwyg' . '/resume_files' . '/', $file_name);
				
				$applicant  								= new Applicant();
				$applicant->jobvacancy_id		= $req->get('jobvacancy_id');
				$applicant->branch_id				= $req->get('branch_id');
				$applicant->lastname 				= $req->get('lastname');
				$applicant->firstname 			= $req->get('firstname');
				$applicant->middlename 			= $req->get('middlename');
				$applicant->address 				= $req->get('address');
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
				$applicant->file 						= $file_name;
				$applicant->status 					= 0;
				$applicant->save();

				if($educ_attain == 'Highschool Graduate' || !$k_12_highschool)
				{
					$hs = $req->highschool;
					ApplicantEducAttain::create(
						[
							'applicant_id' => $applicant->id,
							'educ_level' => 'HighSchool',
							'school' => $hs['school'],
							'sy_attended' => $hs['sy_attended'],
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
							'sy_attended' => $hs['sy_attended'],
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
							'sy_attended' => $hs['sy_attended'],
						]
					);
				}

				if($educ_attain == 'Vocational School')
				{	
					$voc = $req->voational_school;
					ApplicantEducAttain::create(
						[
							'applicant_id' => $applicant->id,
							'educ_level' => 'Vocational School',
							'school' => $voc['school'],
							'course' => $voc['course'],
							'major' => $voc['major'],
							'sy_attended' => $voc['sy_attended'],
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
							'sy_attended' => $college['sy_attended'],
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
						'sy_attended' => $grad['sy_attended'],
					]);
				}

				foreach ($req->experiences as $value) {
					ApplicantExperience::create([
						'applicant_id' => $applicant->id,
						'employer' => $value['company'],
						'position' => $value['position'],
						'salary' => $value['salary'],
						'date_of_service' => $value['date_of_service'],
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
	
				Mail::send([], [], function ($message) use ($html, $email_name) {
					$message->to($email_name)
									->subject('Addessa Online Job Application')
									->from('addessa.corporation2013@gmail.com', 'Addessa Corporation')
									->setBody($html, 'text/html');
				});
   
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

		$filename = DB::table('applicants')
									->select('file')
									->whereId($id)
									->get();

		//unlink file in public storage
		foreach($filename as $data){
			$filename = $data->file;
			$destinationPath = public_path(). '/wysiwyg/resume_files/';
			File::delete($destinationPath.$filename);
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
												->select('positions.name AS position_name',
																 'branches.name AS branch_name',
																 'applicants.id', 
												 DB::raw("CONCAT(applicants.lastname, ', ', applicants.firstname, ', ', applicants.middlename) AS name"),
																 'applicants.address',
												 DB::raw('DATE_FORMAT(applicants.birthdate, "%m-%d-%Y") as birthdate'),
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
																 'applicants.status'
																)
												->where('applicants.id', $id)					
												->get()->first();
		$educ_attains = ApplicantEducAttain::where('applicant_id', $id)->get();
		$experiences = ApplicantExperience::where('applicant_id', $id)->get();
		$references = ApplicantReference::where('applicant_id', $id)->get();
		$fam_members = ApplicantFamilyMember::where('applicant_id', $id)->get();
		$dependents = ApplicantDependent::where('applicant_id', $id)->get();
			
		return response()->json([
			'success' => true, 
			'educ_attains' => $educ_attains,
			'applicant' => $applicant,
			'experiences' => $experiences,
			'references' => $references,
			'fam_members' => $fam_members,
			'dependents' => $dependents,
		]);
	}

	public function export_applicants(Request $req){

		$branch_id = $req->branch_id;
		$date_from = $req->date_from;
		$date_to = $req->date_to;

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
												$result->whereDate('applicants.created_at', '>=', $date_from);
												$result->whereDate('applicants.created_at', '<=', $date_to);
											}else{
												$result->where('branches.id', '=', $branch_id);
												$result->whereDate('applicants.created_at', '>=', $date_from);
												$result->whereDate('applicants.created_at', '<=', $date_to);
											}
										})
										->get();
		

		if(!$applicants->isEmpty()){
			return response()->json(['success' => true, 'resp' => $applicants, 'branch_id' => $branch_id]);
		}else{
			return response()->json(['success' => false, 'resp' => "No records found."]);
		}
	}

  public function export_applicants_new(Request $req){

		$branch_id = $req->branch_id;
		$date_from = $req->date_from;
		$date_to = $req->date_to;

		// id: 1000 - Admin
		// else branch - branches

		$applicants = DB::table('job_vacancies')
										->join('applicants', 'applicants.jobvacancy_id', '=', 'job_vacancies.id')
										->join('positions', 'positions.id', '=', 'job_vacancies.position_id')
										->join('branches', 'branches.id', '=', 'applicants.branch_id')
										->select(DB::raw('applicants.id AS cnt_id'),
														 DB::raw('DATE_FORMAT(applicants.created_at, "%m-%d-%Y") as date_applied'),
														 				 'branches.name AS branch_name',			 
														         'positions.name AS position_name',
														 				 'applicants.lastname',
																		 'applicants.firstname',
																		 'applicants.middlename',
														         'applicants.address',
														 DB::raw('DATE_FORMAT(applicants.birthdate, "%m-%d-%Y") as birthdate'),
															 			 'applicants.age',
																		 'applicants.gender',
														         'applicants.contact_no', 
																		 'applicants.civil_status', 
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
												$result->whereDate('applicants.created_at', '>=', $date_from);
												$result->whereDate('applicants.created_at', '<=', $date_to);
											}else{
												$result->where('branches.id', '=', $branch_id);
												$result->whereDate('applicants.created_at', '>=', $date_from);
												$result->whereDate('applicants.created_at', '<=', $date_to);
											}
										})
										->get()
										->each(function ($row, $index) {
											$row->cnt_id = $index + 1;
										});	

		if(!$applicants->isEmpty()){
			return response()->json(['success' => true, 'resp' => $applicants, 'branch_id' => $branch_id]);
		}else{
			return response()->json(['success' => false, 'resp' => "No records found."]);
		}
	}

	public function update_status(Request $req){

		$id = $req->applicant_id;
		$status_value = $req->status_value;

		$result = Applicant::where('id', $id)->update([
														'status' => $status_value,
													]);	

		$applicant = DB::table('job_vacancies')
								   ->join('applicants', 'applicants.jobvacancy_id', '=', 'job_vacancies.id')
								   ->join('positions', 'positions.id', '=', 'job_vacancies.position_id')
								   ->join('branches', 'branches.id', '=', 'applicants.branch_id')
								   ->select(
										//DB::raw('row_number() OVER(ORDER BY applicants.id) AS cnt_id'),
								   				 'applicants.id AS id',
								   				 'positions.name AS position_name',
								   				 DB::raw("CONCAT(applicants.lastname, ', ', applicants.firstname, ', ', applicants.middlename) AS name"),
								   				 DB::raw("DATE_FORMAT(applicants.created_at, '%M %d, %Y') AS created_at"),
								   				 'branches.name AS branch_name',
								   				 'applicants.status'
								   				)
								   ->where('applicants.id', $id)->first();		

		return response()->json(['success' => true, 'resp' => 'Updated status successfully.', 'applicant' => $applicant]);
	}
}
