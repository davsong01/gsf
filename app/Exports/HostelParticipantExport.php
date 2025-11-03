<?php

namespace App\Exports;

use App\Models\User;
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
		return ["Family ID","Transaction ID", "Name", "Email", "Phone", "Gender","Chapter","Registration Date","Amount Paid","Level","Hostel"];
	}
	
	public function query()
	{
		$participants = User::join('transactions', 'transactions.user_id', '=', 'users.id')
		->Join('hostels', 'hostels.id', '=', 'transactions.hostel_id')
		->where(['hostels.id'=> $this->data['hostel_id']])
		->leftJoin('chapters', 'chapters.id', '=', 'users.chapter_id')
		->select('users.family_id', 'transactions.transid', 'users.name', 'users.email', 'users.phone', 'users.sex', 'chapters.name as chapter', 'transactions.created_at as registration_date','transactions.amount_paid','transactions.level','hostels.name as hostel')
		->orderBy('users.created_at', 'desc');
	
		return $participants;
	}
}
