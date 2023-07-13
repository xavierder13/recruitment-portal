<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
  protected $fillable = ['jobvacancy_id', 
                         'branch_id',
                         'lastname', 
                         'firstname',
                         'middlename',
                         'address', 
                         'birthdate',
                         'age',
                         'gender',
                         'civil_status',
                         'contact_no', 
                         'email', 
                         'educ_attain', 
                         'course', 
                         'school_grad',
                         'how_learn', 
                         'file',
                         'status',
                         'birth_place',
                         'citizenship',
                         'religion',
                         'weight',
                         'height',
                         'sss_no',
                         'philhealth_no',
                         'pagibig_no',
                         'tin_no',
                        ];
}
