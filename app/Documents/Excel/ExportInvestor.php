<?php

namespace App\Documents\Excel;

use App\Helpers\Collections\Investors\InvestorCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class ExportInvestor extends StringValueBinder implements FromView,ShouldAutoSize
{

    /* @var InvestorCollection */
    protected $data = [];

    public function setData($data)
    {
        $this->data = $data->map(function($data) {
            return new InvestorCollection($data);
        });
    }

    public function view(): View
    {
        return view('exports.investor', [
            'data' => $this->data,
        ]);
    }
}
