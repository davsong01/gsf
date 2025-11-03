<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FoodParticipantExport implements WithHeadings, FromQuery
{
	use Exportable;

	private $data = [];

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	/**
	 * 
	 */
	public function headings(): array
	{
		return ["Family ID","Transaction ID", "Name", "Email", "Phone","Gender", "Chapter","Registration Date","Amount Paid","Level","Foodstand"];
	}
	
	public function query()
	{
		$participants = User::join('transactions', 'transactions.user_id', '=', 'users.id')
		->Join('food', 'food.id', '=', 'transactions.food_id')
		->leftJoin('chapters', 'chapters.id', '=', 'users.chapter_id')
		->where(['food_id'=> $this->data['food_id']])
		->select('users.family_id', 'transactions.transid', 'users.name', 'users.email', 'users.phone', 'users.sex', 'chapters.name as chapter', 'transactions.created_at as registration_date','transactions.amount_paid','transactions.level','food.name as food')
		->orderBy('users.created_at', 'desc');

		return $participants;
	}
}
