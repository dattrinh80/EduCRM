<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Application\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomersTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function headings(): array
    {
        return [
            'name',
            'phone',
            'email',
            'dob',
            'gender',
            'address',
            'center_code'
        ];
    }

    public function array(): array
    {
        return [
            [
                'Phụ Huynh Nguyễn Văn B',
                '0908888999',
                'phuhuynh@example.com',
                '1985-05-15',
                'MALE',
                '123 Đường ABC, Quận 1, TP.HCM',
                'CS-01'
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
