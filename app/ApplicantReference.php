<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApplicantReference extends Model
{
    protected $fillable = [
        'applicant_id',
		'name',
		'address',
		'contact',
		'company',
		'position',	
    ];
}
