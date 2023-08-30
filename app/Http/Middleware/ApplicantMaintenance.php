<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class ApplicantMaintenance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      $user = Auth::user();

			if($request->is('api/job_applicant/get_applicants_old')){
				if($user->can('jobapplicants-list')){
					return $next($request);
				}
			}

      if($request->is('api/job_applicant/get_applicants_new')){
				if($user->can('jobapplicants-list')){
					return $next($request);
				}
			}

			if($request->is('api/job_applicant/view_applicants/*')){
				if($user->can('jobapplicants-view')){
					return $next($request);
				}
			}	

      if($request->is('api/job_applicant/view_applicants_new/*')){
				if($user->can('jobapplicants-view')){
					return $next($request);
				}
			}	
			
			if($request->is('api/job_applicant/delete_applicant/*')){
				if($user->can('jobapplicants-delete')){
					return $next($request);
				}
			}	

			if($request->is('api/job_applicant/export_applicants')){
				if($user->can('jobapplicants-export')){
					return $next($request);
				}
			}

      if($request->is('api/job_applicant/export_applicants_new')){
				if($user->can('jobapplicants-export')){
					return $next($request);
				}
			}

			if($request->is('api/job_applicant/update_status')){
				if($user->can('jobapplicants-change-status')){
					return $next($request);
				}
			}

			if($request->is('api/job_applicant/screening_list')){
				if($user->can('jobapplicants-screening-list')){
					return $next($request);
				}
			}

			if($request->is('api/job_applicant/initial_interview_list')){
				if($user->can('jobapplicants-initial-interview-list')){
					return $next($request);
				}
			}

			if($request->is('api/job_applicant/iq_test_list')){
				if($user->can('jobapplicants-iq-test-list')){
					return $next($request);
				}
			}

			if($request->is('api/job_applicant/bi_list')){
				if($user->can('jobapplicants-bi-list')){
					return $next($request);
				}
			}

			if($request->is('api/job_applicant/final_interview_list')){
				if($user->can('jobapplicants-final-interview-list')){
					return $next($request);
				}
			}

			if($request->is('api/job_applicant/get_all_status_count')){
				if($user->can('jobapplicants-list')){
					return $next($request);
				}
			}

			if($request->is('api/job_applicant/file/download')){
				if($user->can('jobapplicants-file-download')){
					return $next($request);
				}
			}

      return abort(401, 'Unauthorized');
    }
}
