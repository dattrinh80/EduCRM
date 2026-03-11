<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Application\Queries;

use Maatwebsite\Excel\Facades\Excel;
use Modules\CRM\Customer\Application\Exports\CustomersTemplateExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadCustomerTemplateHandler
{
    public function handle(DownloadCustomerTemplateQuery $query): BinaryFileResponse
    {
        return Excel::download(new CustomersTemplateExport(), 'customers_import_template.xlsx');
    }
}
