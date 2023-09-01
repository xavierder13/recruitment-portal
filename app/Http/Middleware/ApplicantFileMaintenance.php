<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class ApplicantFileMaintenance
{

    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        //Applicant Record
        if($request->is('api/jobapplicant_file/get_applicant_files/*')){
            if($user->can('jobapplicants-file-list')){
                return $next($request); 
            }
        }

        //Applicant Create
        if($request->is('api/jobapplicant_file/store')){
            if($user->can('jobapplicants-file-create')){
                return $next($request); 
            }
        }

        //Applicant Delete
        if($request->is('api/jobapplicant_file/delete')){
            if($user->can('jobapplicants-file-delete')){
                return $next($request); 
            }
        }

        return abort(401, 'Unauthorized');
    }
}
