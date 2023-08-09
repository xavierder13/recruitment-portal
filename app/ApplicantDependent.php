<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApplicantDependent extends Model
{
    protected $fillable = [
        'applicant_id',
		'name',
		'relationship',
		'age',
		'address',
		'contact',
		'occupation',
    ];
}
