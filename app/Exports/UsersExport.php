<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Usergenderport implements WithHeadings, FromQuery
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
		return ["Family ID","Transaction ID", "Registration Status", "Name", "Moderator","Email", "Phone", "Chapter","Registration Date","Amount Paid","Level","Hostel","Foodstand","Purpose","Location", "Gender"];
	}
	
	public function query()
	{
		$participants = User::join('transactions', 'transactions.user_id', '=', 'users.id')
			->leftJoin('hostels', 'hostels.id', '=', 'transactions.hostel_id')
			->leftJoin('food', 'food.id', '=', 'transactions.food_id')
			->leftJoin('chapters', 'chapters.id', '=', 'users.chapter_id')
			->leftJoin('users as uploaded_by_users', 'uploaded_by_users.id', '=', 'transactions.uploaded_by')
			->where('transactions.conference_edition_id', $this->data['edition_id'])
			->where('users.role', '!=', 1)
			->select(
				'users.family_id',
				'transactions.transid',
				'transactions.registration_status',
				'users.name',
				'uploaded_by_users.name as uploaded_by_name',
				'users.email',
				'users.phone',
				'chapters.name as chapter',
				'transactions.created_at as registration_date',
				'transactions.amount_paid',
				'transactions.level',
				'hostels.name as hostel',
				'food.name as foodstand',
				'transactions.purpose',
				'transactions.location',
				'users.gender'
			)
			->orderBy('uploaded_by_users.name')
			->orderBy('users.created_at', 'asc');

		return $participants;
	}
}
