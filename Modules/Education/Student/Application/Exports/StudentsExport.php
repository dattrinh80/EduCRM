<?php

declare(strict_types=1);

namespace Modules\Education\Student\Application\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Collection;

class StudentsExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    use Exportable;

    private int $rowNumber = 0;

    public function __construct(
        private Collection $students
    ) {}

    public function collection()
    {
        return $this->students;
    }

    public function headings(): array
    {
        return [
            'STT',
            'Mã Học Viên',
            'Họ và Tên',
            'Số điện thoại',
            'Email',
            'Ngày sinh',
            'Giới tính',
            'Trạng thái',
            'Trung tâm',
            'Ngày tạo',
        ];
    }

    public function map($student): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $student->student_code,
            $student->customer?->name,
            $student->customer?->phone,
            $student->customer?->email,
            $student->customer?->dob ? \Carbon\Carbon::parse($student->customer->dob)->format('d/m/Y') : '',
            $student->customer?->gender === 'MALE' ? 'Nam' : (($student->customer?->gender === 'FEMALE') ? 'Nữ' : 'Khác'),
            $student->status,
            $student->customer?->center?->name ?? 'N/A',
            $student->created_at?->format('d/m/Y H:i:s'),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('DejaVu Sans');
                
                // Style headings
                $sheet->getStyle('A1:J1')->getFont()->setBold(true);
                
                // Auto size columns
                foreach (range('A', 'J') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
