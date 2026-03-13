<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Application\Commands;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class InitiateCustomerImportHandler
{
    public function handle(InitiateCustomerImportCommand $command): array
    {
        $array = Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray, \Maatwebsite\Excel\Concerns\WithHeadingRow {
            public function array(array $array) {}
        }, $command->file);
        
        $rows = $array[0] ?? []; 
        if (empty($rows)) {
            throw new \Exception('File rỗng hoặc không có dữ liệu (Kiểm tra lại Sheet 1).');
        }
        
        $importId = (string) Str::uuid();
        Cache::put('customer_import_' . $importId, $rows, now()->addHours(1));
        
        return [
            'import_id' => $importId,
            'total' => count($rows)
        ];
    }
}
