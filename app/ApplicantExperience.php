<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApplicantExperience extends Model
{
    protected $fillable = [
        'applicant_id',
		'employer',
		'position',
		'salary',
		'date_of_service',
		'job_description',				
    ];
}
