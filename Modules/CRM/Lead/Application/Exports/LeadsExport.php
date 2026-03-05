<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LeadsExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    private array $centers;
    private array $users;

    private int $rowNumber = 0;

    public function __construct(private Collection $leads)
    {
        $this->centers = DB::table('centers')->pluck('name', 'id')->toArray();
        $this->users = DB::table('users')->pluck('name', 'id')->toArray();
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
            $lead->source?->name ?? '',
            $lead->interestType?->name ?? '',
            strtoupper($lead->status),
            $lead->assigned_to ? ($this->users[$lead->assigned_to] ?? 'Chưa giao') : 'Chưa giao',
            $lead->center_id ? ($this->centers[$lead->center_id] ?? '') : '',
            $lead->created_at?->format('d/m/Y H:i:s'),
        ];
    }
}
