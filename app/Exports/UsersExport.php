<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
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
		return User::where('users.level', '!=', 'Admin')
			->foreign(
				['food_id', 'hostel_id', 'chapter'],
				['food', 'hostels', 'chapters'],
				['id', 'id', 'id'],
				['name', 'name', 'name']
			)->get();
	}


	/**
	 * 
	 */
	public function headings(): array
	{
		return $this->tables;
	}
}
