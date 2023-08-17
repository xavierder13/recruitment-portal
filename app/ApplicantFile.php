<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApplicantFile extends Model
{
    protected $fillable = [
        'applicant_id',
        'file_name',
        'file_path',
        'file_type',
        'title',
    ];

    public function applicant()
    {
        return $this->hasOne('App\Applicant', 'id', 'applicant_id');
        //                 ( <Model>, <id_of_specified_Model>, <id_of_this_model> )
    }
}
