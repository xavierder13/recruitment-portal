<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApplicantFamilyMember extends Model
{
    protected $fillable = [
        'applicant_id',
		'relationship',
		'name',
	    'age',
		'address',
		'contact',
		'occupation',			
    ];
}
