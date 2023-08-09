<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApplicantEducAttain extends Model
{
    protected $fillable = [
        'applicant_id',
		'educ_level',
		'school',
		'course',
		'major',
		'sy_attended',
    ];
}
