<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Application\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Collection;

class CustomersExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    use Exportable;

    private array $centers;
    private int $rowNumber = 0;

    public function __construct(
        private Collection $customers,
        array $centers
    ) {
        $this->centers = $centers;
    }

    public function collection()
    {
        return $this->customers;
    }

    public function headings(): array
    {
        return [
            'STT',
            'Họ và Tên',
            'Số điện thoại',
            'Email',
            'Ngày sinh',
            'Giới tính',
            'Địa chỉ',
            'Cơ sở',
            'Ngày tạo',
        ];
    }

    public function map($customer): array
    {
        $this->rowNumber++;
        $genderMap = [
            'MALE' => 'Nam',
            'FEMALE' => 'Nữ',
            'OTHER' => 'Khác'
        ];

        return [
            $this->rowNumber,
            $customer->name,
            $customer->phone,
            $customer->email,
            $customer->dob ? \Carbon\Carbon::parse($customer->dob)->format('d/m/Y') : '',
            $genderMap[$customer->gender] ?? $customer->gender,
            $customer->address,
            $customer->center_id ? ($this->centers[$customer->center_id] ?? '') : '',
            $customer->created_at?->format('d/m/Y H:i:s'),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $pageSetup = $sheet->getPageSetup();
                
                // Set Landscape orientation
                $pageSetup->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

                // Fit to 1 page wide
                $pageSetup->setFitToPage(true);
                $pageSetup->setFitToWidth(1);
                $pageSetup->setFitToHeight(0);

                // Set DejaVu Sans font to support Vietnamese UTF-8 characters 
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('DejaVu Sans');

                // Enable text wrapping for all cells
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle('A1:I' . $highestRow)->getAlignment()->setWrapText(true);

                // Explicit column widths to ensure it fits A4 Landscape
                $widths = [
                    'A' => 5,   // STT
                    'B' => 20,  // Name
                    'C' => 15,  // Phone
                    'D' => 25,  // Email
                    'E' => 12,  // DOB
                    'F' => 10,  // Gender
                    'G' => 30,  // Address
                    'H' => 20,  // Center
                    'I' => 15,  // Date
                ];

                foreach ($widths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }
            },
        ];
    }
}
