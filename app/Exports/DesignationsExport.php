<?php

namespace App\Exports;

use App\Designation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DesignationsExport implements FromCollection, WithHeadings
{
	use Exportable;

	public function __construct(array $tables)
	{
		$this->tables = $tables;
	}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Designation::all();
		}
		

		/**
		 * 
		 */
		public function headings(): array {
			return $this->tables;
		}
}
