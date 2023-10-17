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
                         'address2', 
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
                         'initial_interview_date',
                         'initial_interview_status',
                         'position_preference',
                         'branch_preference',
                         'iq_status',
                         'bi_status',
                         'final_interview_date',
                         'final_interview_status',
                         'employment_position',
                         'employment_branch',
                         'orientation_date',
                         'signing_of_contract_date',
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

  public function employment_position()
  {
      return $this->hasOne('App\Position', 'id', 'employment_position');
      //                 ( <Model>, <id_of_specified_Model>, <id_of_this_model> )
  }

  public function employment_branch()
  {
      return $this->hasOne('App\Branch', 'id', 'employment_branch');
      //                 ( <Model>, <id_of_specified_Model>, <id_of_this_model> )
  }

}
