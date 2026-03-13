<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Commands;

use App\Core\CQRS\CommandHandler;
use App\Core\CQRS\Command;
use Illuminate\Support\Facades\Cache;

class ProcessLeadImportChunkHandler implements CommandHandler
{
    public function __construct(
        private readonly ImportLeadHandler $rowHandler
    ) {}

    public function handle(Command $command): mixed
    {
        /** @var ProcessLeadImportChunkCommand $command */
        $rows = Cache::get('lead_import_' . $command->importId);
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
                
                $rowCommand = new ImportLeadCommand(
                    (string)($normalizedRow['name'] ?? ''),
                    (string)($normalizedRow['phone'] ?? ''),
                    empty($normalizedRow['email']) ? null : (string)$normalizedRow['email'],
                    empty($normalizedRow['center_code']) ? null : (string)$normalizedRow['center_code'],
                    empty($normalizedRow['dob']) ? null : (string)$normalizedRow['dob'],
                    empty($normalizedRow['lead_source_code']) ? null : (string)$normalizedRow['lead_source_code'],
                    empty($normalizedRow['campaign_code']) ? null : (string)$normalizedRow['campaign_code'],
                    empty($normalizedRow['interest_type_code']) ? null : (string)$normalizedRow['interest_type_code']
                );
                
                $this->rowHandler->handle($rowCommand);
                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
                $logs[] = "Dòng [{$currentRow}]: " . $e->getMessage();
            }
        }
        
        $isFinished = ($command->offset + $command->limit >= $total);
        if ($isFinished) {
            Cache::forget('lead_import_' . $command->importId);
        }

        return [
            'success_count' => $successCount,
            'error_count'   => $errorCount,
            'logs'          => $logs,
            'next_offset'   => $command->offset + $command->limit,
            'is_finished'   => $isFinished
        ];
    }
}
