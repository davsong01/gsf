<?php

namespace App\Exports;

use App\Category;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CategoriesExport implements FromCollection, WithHeadings
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
        return Category::all();
		}

		/**
		 * 
		 */
		public function headings(): array {
			return $this->tables;
		}
}
