<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LeadsTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function headings(): array
    {
        return [
            'name',
            'phone',
            'email',
            'dob',
            'center_code',
            'source_code',
            'campaign_code',
            'interest_type_code'
        ];
    }

    public function array(): array
    {
        return [
            [
                'Nguyễn Văn A',
                '0901234567',
                'nguyenvana@example.com',
                '1995-12-31',
                'CS-01',
                'FB',
                'CMP-01',
                'IELTS'
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }
}
