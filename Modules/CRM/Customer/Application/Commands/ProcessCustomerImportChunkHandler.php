<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Application\Commands;

use Illuminate\Support\Facades\Cache;

class ProcessCustomerImportChunkHandler
{
    public function __construct(
        private readonly ImportCustomerHandler $importHandler
    ) {}

    public function handle(ProcessCustomerImportChunkCommand $command): array
    {
        $rows = Cache::get('customer_import_' . $command->importId);
        if (!$rows) {
            throw new \Exception('Tiến trình đã hết hạn hoặc không tìm thấy, vui lòng upload lại file.');
        }
        
        $total = count($rows);
        $chunk = array_slice($rows, $command->offset, $command->limit);
        
        $successCount = 0;
        $errorCount = 0;
        $logs = [];
        
        foreach ($chunk as $index => $row) {
            $currentRow = $command->offset + $index + 2;
            
            try {
                $normalizedRow = [];
                foreach ($row as $k => $v) {
                    $normalizedRow[strtolower(trim((string)$k))] = is_string($v) ? trim($v) : $v;
                }
                
                $importCommand = new ImportCustomerCommand(
                    (string)($normalizedRow['name'] ?? ''),
                    (string)($normalizedRow['phone'] ?? ''),
                    empty($normalizedRow['email']) ? null : (string)$normalizedRow['email'],
                    empty($normalizedRow['center_code']) ? null : (string)$normalizedRow['center_code'],
                    empty($normalizedRow['dob']) ? null : (string)$normalizedRow['dob'],
                    empty($normalizedRow['gender']) ? null : (string)$normalizedRow['gender'],
                    empty($normalizedRow['address']) ? null : (string)$normalizedRow['address']
                );
                
                $this->importHandler->handle($importCommand);
                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
                $logs[] = "Dòng [{$currentRow}]: " . $e->getMessage();
            }
        }
        
        return [
            'success_count' => $successCount,
            'error_count'   => $errorCount,
            'logs'          => $logs,
            'next_offset'   => $command->offset + $command->limit,
            'is_finished'   => ($command->offset + $command->limit >= $total)
        ];
    }
}
