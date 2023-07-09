<?php

namespace App\Exports;

use App\Applicant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class ApplicantsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */

		protected $branch_id;
		protected $date_from;
		protected $date_to;


		public function __construct($branch_id, $date_from, $date_to)
    {
      $this->branch_id = $branch_id;
			$this->date_from = $date_from;
			$this->date_to = $date_to;
    }

    public function collection()
    {

			$branch_id = $this->branch_id;
			$date_from = $this->date_from;
			$date_to = $this->date_to;
	
			$applicants = DB::table('job_vacancies')
											->join('applicants', 'applicants.jobvacancy_id', '=', 'job_vacancies.id')
											->join('positions', 'positions.id', '=', 'job_vacancies.position_id')
											->join('branches', 'branches.id', '=', 'job_vacancies.branch_id')
											->select('positions.name AS position_name',
															 'applicants.name', 
															 'applicants.address',
															 'applicants.contact_no', 
															 'applicants.email',
															 'applicants.educ_attain',
															 'applicants.course',
															 'applicants.how_learn'
															)
											->where('branches.id', '=', $branch_id)				
											->whereDate('applicants.created_at', '=', $date_from)
											->OrwhereBetween('applicants.created_at', [$date_from, $date_to])
											->get();

      return $applicants;
    }

		public function headings(): array
    {
      return [
        'Position Applied',
        'Name',
        'Complete Address',
        'Contact No.',
				'Email Address',
				'Educational Attainment',
				'Course',
				'How did you learn about job vacancy?'
      ];
    }
}
