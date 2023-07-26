<?php

namespace App\Exports;

use App\User;
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
		return ["Family ID","Transaction ID", "Name", "Email", "Phone", "Chapter","Registration Date","Amount Paid","Level","Foodstand"];
	}
	
	public function query()
	{
		$participants = User::join('payments', 'payments.user_id', '=', 'users.id')
		->Join('food', 'food.id', '=', 'payments.food_id')
		->leftJoin('chapters', 'chapters.id', '=', 'users.chapter_id')
		->where(['food_id'=> $this->data['food_id']])
		->select('users.family_id', 'payments.transid', 'users.name', 'users.email', 'users.phone', 'chapters.name as chapter', 'payments.created_at as registration_date','payments.amount_paid','payments.level','food.name as food')
		->orderBy('users.created_at', 'desc');

		return $participants;
	}
}
