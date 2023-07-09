<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class JobVacancyMaintenance
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

      if($request->is('api/job_vacancy/view')){
        if($user->can('jobvacancies-list')){
          return $next($request);
        }
      }

			if($request->is('api/job_vacancy/store')){
        if($user->can('jobvacancies-create')){
          return $next($request);
        }
      }

			if($request->is('api/job_vacancy/update')){
        if($user->can('jobvacancies-update')){
          return $next($request);
        }
      }

			if($request->is('api/job_vacancy/delete_job_vacancy/*')){
        if($user->can('jobvacancies-delete')){
          return $next($request);
        }
      }

			if($request->is('api/job_vacancy/edit_job_vacancy/*')){
        if($user->can('jobvacancies-edit')){
          return $next($request);
        }
      }

			return abort(401, 'Unauthorized');
    }
}
