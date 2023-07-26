<?php

namespace App\Exports;

use App\StakeholderPayment;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportPop implements FromCollection, WithHeadings
{
    use Exportable;
    
    public $campus;
    public $year;
    public $month;

    public function __construct($campus = null, $year = null, $month = null){
        $this->campus = $campus;
        $this->year = $year;
        $this->month = $month;
    }
    /**
    * @return \Illuminate\Support\Collection
    */

    public function collection()
    {
        $payments = StakeholderPayment::select(['report_id', 'created_at', 'amount'])->with('report');

        if($this->campus != 'all'){
            $payments = $payments->where('chapter_id', $this->campus);
        }

        if($this->year != 'all'){
            $payments = $payments->where('year', $this->year);
        }
        if($this->month != 'all'){
            $payments = $payments->where('month', $this->month);
        }
        $payments = $payments->get();

        foreach($payments as $payment){
            $payment->Campus = $payment->report->chapter->name;
            $payment->ReportDate = date("F", mktime(0, 0, 0, $payment->report->month, 10)) . ', ' . $payment->report->year;  
            $payment->Amount = $payment->amount;
            $payment->Date = $payment->created_at;

            unset($payment->report_id);
            unset($payment->created_at);
            unset($payment->amount);
        }

        return $payments;
    }

    public function headings(): array {
        return ["Campus", "Date", "Amount Paid", "Date Uploaded"];
    }
}
