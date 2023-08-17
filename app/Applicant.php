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

  public function applicant_files()
  {
      return $this->hasMany('App\ApplicantFile', 'applicant_id', 'id');
      //                 ( <Model>, <id_of_specified_Model>, <id_of_this_model> )
  }

  public function applicant()
  {
      return $this->hasOne('App\Branch', 'id', 'applicant_id');
      //                 ( <Model>, <id_of_specified_Model>, <id_of_this_model> )
  }

}
