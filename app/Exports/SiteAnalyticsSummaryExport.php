<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SiteAnalyticsSummaryExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct(private readonly Collection $rows) {}

    public function collection(): Collection
    {
        return $this->rows;
    }

    /** @return array<int, string> */
    public function headings(): array
    {
        return ['Type', 'Name', 'Visit Count', 'Last Visit Country', 'Last Visit City', 'Last Visit On'];
    }
}
