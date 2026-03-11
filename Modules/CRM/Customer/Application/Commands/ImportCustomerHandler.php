<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Application\Commands;

use Modules\Core\Center\Infrastructure\ReadModels\CenterReadModel;
use Modules\CRM\Customer\Domain\Customer;

class ImportCustomerHandler
{
    public function __construct(
        private CreateCustomerHandler $createCustomerHandler
    ) {}

    public function handle(ImportCustomerCommand $command): Customer
    {
        if (empty($command->name) || empty($command->phone)) {
            throw new \InvalidArgumentException("Thiếu cột bắt buộc (name, phone)");
        }

        $centerId = null;
        if (!empty($command->centerCode)) {
            $center = CenterReadModel::where('code', $command->centerCode)->first();
            if (!$center) {
                throw new \InvalidArgumentException("Mã cơ sở ({$command->centerCode}) không tồn tại.");
            }
            $centerId = $center->id;
        }

        $dob = null;
        if (!empty($command->dob)) {
            $dobStr = (string)$command->dob;
            if (strtotime($dobStr)) {
                $dob = date('Y-m-d', strtotime($dobStr));
            } elseif (is_numeric($dobStr)) {
                $dob = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dobStr)->format('Y-m-d');
            }
        }

        $gender = null;
        if (!empty($command->gender)) {
            $genderUpper = strtoupper(trim($command->gender));
            if (in_array($genderUpper, ['MALE', 'FEMALE', 'OTHER'])) {
                $gender = $genderUpper;
            } else {
                // Try Vietnamese
                if (str_contains($genderUpper, 'NAM')) $gender = 'MALE';
                elseif (str_contains($genderUpper, 'NỮ')) $gender = 'FEMALE';
                else $gender = 'OTHER';
            }
        }

        $createCommand = new CreateCustomerCommand(
            $command->name,
            $command->phone,
            $command->email,
            $dob,
            $gender,
            $command->address,
            $centerId,
            [] // Tags empty for now during import
        );

        return $this->createCustomerHandler->handle($createCommand);
    }
}
