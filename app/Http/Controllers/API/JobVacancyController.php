<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\JobVacancy;
use Validator;
use DB;

class JobVacancyController extends Controller
{
	public function get_branches(){
		$branches = db::table('branches')
									->select('id',
													 'name'
									)
									->where('name', '!=', "All branches")
									->orderBy('id', 'ASC');

		$all_branches = $branches->get();
		$paginated_branch = $branches->paginate(12);

		return response()->json(['all_branches' => $all_branches, 'paginated_branch' => $paginated_branch], 200);
	}

  public function store(Request $req){

		$rules = [
			'position_id.required' => 'Position is required.',
			'educ_attain.required' => 'Educational attainment is required.',
			'branch_type.required' 	 => 'Branch type is required.',
		];

		$validator = Validator::make($req->all(),[
			'position_id' => 'required',
			'educ_attain' => 'required',
			'branch_type' 	=> 'required',
		], $rules);

		if($validator->fails()){
			return response()->json($validator->errors(), 200);
		}

		$jobvacancy = new JobVacancy();
		$jobvacancy->position_id = $req->get('position_id');
		$jobvacancy->branch_type = $req->get('branch_type');
		$jobvacancy->educ_attain = $req->get('educ_attain');
		$jobvacancy->status = $req->get('status');
		$jobvacancy->save();

		return response()->json(['success' => true, 'resp' => 'Record has successfully added'], 200);
	}

	public function view(){
		$job_vacancy_lists = DB::table('job_vacancies')
													 ->join('positions', 'positions.id', '=', 'job_vacancies.position_id')
													 ->select(
														 			 'job_vacancies.id',
																	 DB::raw('CASE 
																	 						WHEN job_vacancies.branch_type = 0 
																							 THEN "Branch Only" 
																							ELSE "Admin Only" 
																					 END AS branch_type'
																	 ),
													 				 'positions.name AS position_name',
																	 'job_vacancies.status')
													 ->get();

		return response()->json(['job_vacancy_lists' => $job_vacancy_lists], 200);
	}

	// public function job_vacancy_public(){
	// 	$job_vacancy_lists = DB::table('job_vacancies')
	// 												 ->join('positions', 'positions.id', '=', 'job_vacancies.position_id')
	// 												 ->join('branches', 'branches.id', '=', 'job_vacancies.branch_id')
	// 												 ->where('positions.status', '=', 1)
	// 												 ->where('job_vacancies.status', '=', 1)
	// 												 ->select('job_vacancies.id',
	// 												 					'positions.name AS position_name',
	// 																	'branches.name AS branch_name', 
	// 																	'job_vacancies.status', 
	// 																	'positions.description AS description', 
	// 																	'positions.qualifications AS qualifications',
	// 																	DB::raw('DATEDIFF(NOW(), job_vacancies.updated_at) AS days'),
	// 																	DB::raw('TIMESTAMPDIFF(MINUTE, job_vacancies.updated_at, NOW()) AS minutes'),
	// 																	DB::raw('TIMESTAMPDIFF(HOUR, job_vacancies.updated_at, NOW()) AS hours')
	// 																 )
	// 												 ->orderBy('job_vacancies.id', 'DESC')
	// 												 ->take(8)
	// 												 ->get();

	// 	return response()->json(['job_vacancy_lists' => $job_vacancy_lists], 200);
	// }

	// public function all_job_vacancy_public(){
	// 	$job_vacancy_lists = DB::table('job_vacancies')
	// 												 ->join('positions', 'positions.id', '=', 'job_vacancies.position_id')
	// 												 ->join('branches', 'branches.id', '=', 'job_vacancies.branch_id')
	// 												 ->where('positions.status', '=', 1)
	// 												 ->where('job_vacancies.status', '=', 1)
	// 												 ->select('job_vacancies.id', 
	// 												 					'positions.name AS position_name', 
	// 																	'branches.name AS branch_name', 
	// 																	'job_vacancies.status', 
	// 																	'positions.description AS description', 
	// 																	'positions.qualifications AS qualifications',
	// 																	DB::raw('DATEDIFF(NOW(), job_vacancies.updated_at) AS days'),
	// 																	DB::raw('TIMESTAMPDIFF(MINUTE, job_vacancies.updated_at, NOW()) AS minutes'),
	// 																	DB::raw('TIMESTAMPDIFF(HOUR, job_vacancies.updated_at, NOW()) AS hours')
	// 																	)
	// 													->orderBy('job_vacancies.id', 'DESC')				
	// 												 ->get();

	// 	return response()->json(['job_vacancy_lists' => $job_vacancy_lists], 200);
	// }

	public function search_branches(Request $req){

		$search_textfield = $req->search_textfield;

		$branch = DB::table('branches')
								->orWhere('branches.name', 'LIKE', '%' . $search_textfield . '%')
								->get();

		return response()->json(['branch' => $branch], 200);
	}

	public function get_job_vacancy_public(Request $req){
		$branch_id = $req->branch_id;

		$get_job_vacancy_lists = DB::table('job_vacancies')
													 		 ->join('positions', 'positions.id', '=', 'job_vacancies.position_id')
													 		 ->select('job_vacancies.id', 
																				'job_vacancies.branch_type', 
													 		          'job_vacancies.position_id',
															 					'job_vacancies.educ_attain AS educ_attain',
															 					'positions.name AS position_name', 
															 					'positions.description AS description',
															 					'positions.qualifications AS qualifications'
															 				 )
													 		 ->where(function($query) use ($branch_id){
																	// Admin - 1
																	// Branch - 0
																	if($branch_id == 1){
																		$query->where('branch_type', 1);
																	}else{
																		$query->where('branch_type', 0);
																	}
															 })
													 		 ->where('job_vacancies.status', 1)
													 		 ->get();

		return response()->json(['get_job_vacancy_lists' => $get_job_vacancy_lists], 200);
	}

	public function get_job_details(Request $req){
		$job_vacancy_id = $req->job_vacancy_id;

		$job_details = DB::table('job_vacancies')
															 ->join('positions', 'positions.id', '=', 'job_vacancies.position_id')
															 ->select('job_vacancies.id', 
															          'job_vacancies.position_id',
															 					'job_vacancies.educ_attain AS educ_attain',
															 					'positions.name AS position_name', 
															 					'positions.description AS description',
															 					'positions.qualifications AS qualifications'
															 				 )
															 ->where('job_vacancies.status', 1)
															 ->where('job_vacancies.id', $job_vacancy_id)
															 ->first();

		return response()->json(['job_details' => $job_details], 200);
	}

	public function edit_job_vacancy($id){
		$job_vacancy = JobVacancy::find($id);

		return response()->json(['success' => true, 'resp' => $job_vacancy]);
	}

	public function update(Request $req){

		$id = $req->job_vac_id;
		$job_vacancy = JobVacancy::find($id);

		$job_vacancy->status = $req->status;
		$job_vacancy->save();

		return response()->json(['success' => true, 'resp' => 'Job Vacancy Updated Successfully!']);
	}

	public function delete_job_vacancy($id){
		$job_vacancy = JobVacancy::find($id);

		if($job_vacancy->delete()){
			return response()->json(['success' => true, 'message' => 'Job Vacancy Deleted Successfully!']);
		}else{
			return response()->json(['success' => false, 'message' => 'Job Vacancy Deletion failed!']);
		}
	}
}
