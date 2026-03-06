<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LeadsExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    use Exportable;

    private array $centers;
    private array $users;

    private int $rowNumber = 0;

    public function __construct(
        private Collection $leads,
        array $centers,
        array $users
    ) {
        $this->centers = $centers;
        $this->users = $users;
    }

    public function collection()
    {
        return $this->leads;
    }

    public function headings(): array
    {
        return [
            'STT',
            'Họ và Tên',
            'Số điện thoại',
            'Email',
            'Ngày sinh',
            'Nguồn',
            'Nhu cầu (Dịch vụ)',
            'Trạng thái',
            'Người phụ trách',
            'Cơ sở',
            'Ngày đăng ký',
        ];
    }

    public function map($lead): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $lead->name,
            $lead->phone,
            $lead->email,
            $lead->dob ? \Carbon\Carbon::parse($lead->dob)->format('d/m/Y') : '',
            $lead->leadSource?->name ?? '',
            $lead->interestType?->name ?? '',
            $lead->leadStatus?->name ?? 'N/A',
            $lead->assigned_to ? ($this->users[$lead->assigned_to] ?? 'Chưa giao') : 'Chưa giao',
            $lead->center_id ? ($this->centers[$lead->center_id] ?? '') : '',
            $lead->created_at?->format('d/m/Y H:i:s'),
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
                $sheet->getStyle('A1:K' . $highestRow)->getAlignment()->setWrapText(true);

                // Explicit column widths to ensure it fits A4 Landscape
                $widths = [
                    'A' => 5,   // STT
                    'B' => 18,  // Name
                    'C' => 12,  // Phone
                    'D' => 20,  // Email
                    'E' => 12,  // DOB
                    'F' => 12,  // Source
                    'G' => 15,  // Interest
                    'H' => 12,  // Status
                    'I' => 18,  // Assigned
                    'J' => 20,  // Center
                    'K' => 15,  // Date
                ];

                foreach ($widths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }
            },
        ];
    }
}
