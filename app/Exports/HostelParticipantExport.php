<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HostelParticipantExport implements WithHeadings, FromQuery
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
		return ["Family ID","Transaction ID", "Name", "Email", "Phone", "Chapter","Registration Date","Amount Paid","Level","Hostel"];
	}
	
	public function query()
	{
		$participants = User::join('payments', 'payments.user_id', '=', 'users.id')
		->Join('hostels', 'hostels.id', '=', 'payments.hostel_id')
		->where(['hostels.id'=> $this->data['hostel_id']])
		->leftJoin('chapters', 'chapters.id', '=', 'users.chapter_id')
		->select('users.family_id', 'payments.transid', 'users.name', 'users.email', 'users.phone', 'chapters.name as chapter', 'payments.created_at as registration_date','payments.amount_paid','payments.level','hostels.name as hostel')
		->orderBy('users.created_at', 'desc');
	
		return $participants;
	}
}
