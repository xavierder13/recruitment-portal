<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobVacancy extends Model
{
    protected $fillable = ['position_id', 'branch_type', 'educ_attain', 'status'];

		public function branches(){
			return $this->hasMany('App\Branch', 'id', 'branch_id');
			//                 ( <Model>, <id_of_specified_Model>, <id_of_this_model> )
		}

		public function position(){
			return $this->hasOne('App\Position', 'id', 'position	_id');
			//                 ( <Model>, <id_of_specified_Model>, <id_of_this_model> )
		}
}
