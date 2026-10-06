<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SiteAnalyticsDetailExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct(private readonly Collection $rows) {}

    public function collection(): Collection
    {
        return $this->rows;
    }

    /** @return array<int, string> */
    public function headings(): array
    {
        return ['Visitor Type', 'Country', 'City', 'Region', 'Device', 'Browser', 'Browser Version', 'OS', 'OS Version', 'Duration Seconds', 'Time Spent', 'Started At'];
    }
}
