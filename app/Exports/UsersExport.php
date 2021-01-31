<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements WithHeadings, FromQuery
{
	use Exportable;

	private $level = null;

	public function __construct(array $tables)
	{
		$this->tables = $tables;
	}

	/**
	 * 
	 */
	public function headings(): array
	{
		return $this->tables;
	}

	public function level($level)
	{
		$this->level = $level;
		return $this;
	}

	public function query()
	{
		$query = User::where('users.level', '!=', 'Admin')->foreign(
			['food_id', 'hostel_id', 'chapter'],
			['food', 'hostels', 'chapters'],
			['id', 'id', 'id'],
			['name', 'name', 'name']
		);
		if ($this->level) $query = User::query()->where('users.level', '!=', 'Admin')
			->where('users.level', $this->level)
			->foreign(
				['food_id', 'hostel_id', 'chapter'],
				['food', 'hostels', 'chapters'],
				['id', 'id', 'id'],
				['name', 'name', 'name']
			);
		return  $query;
	}
}
