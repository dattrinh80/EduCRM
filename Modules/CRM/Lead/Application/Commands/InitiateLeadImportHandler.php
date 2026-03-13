<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class InitiateLeadImportHandler implements CommandHandler
{
    public function handle(Command $command): mixed
    {
        /** @var InitiateLeadImportCommand $command */
        $array = Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray, \Maatwebsite\Excel\Concerns\WithHeadingRow {
            public function array(array $array) {}
        }, $command->file);
        
        $rows = $array[0] ?? []; 
        if (empty($rows)) {
            throw new \Exception('File rỗng hoặc không có dữ liệu (Kiểm tra lại Sheet 1).');
        }
        
        $importId = (string) Str::uuid();
        Cache::put('lead_import_' . $importId, $rows, now()->addHours(2));
        
        return [
            'import_id' => $importId,
            'total' => count($rows)
        ];
    }
}
