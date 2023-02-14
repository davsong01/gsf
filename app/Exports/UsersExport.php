<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements WithHeadings, FromQuery
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
		return ["Family ID","Transaction ID", "Registration Status", "Name", "Email", "Phone", "Chapter","Registration Date","Amount Paid","Level","Hostel","Foodstand","Purpose"];
	}
	
	public function query()
	{
		$participants = User::join('payments', 'payments.user_id', '=', 'users.id')
		->leftJoin('hostels', 'hostels.id', '=', 'payments.hostel_id')
		->leftJoin('food', 'food.id', '=', 'payments.food_id')
		->leftJoin('chapters', 'chapters.id', '=', 'users.chapter_id')
		->where(['payments.conference_edition_id'=> $this->data['edition_id']])->where('role','!=',1)
			->select('users.family_id', 'payments.transid', 'payments.registration_status', 'users.name', 'users.email', 'users.phone', 'chapters.name as chapter', 'payments.created_at as registration_date','payments.amount_paid', 'payments.level','hostels.name as hostel','food.name as foodstand', 'payments.purpose')
			->orderBy('users.created_at', 'desc');

		return $participants;
	}
}
