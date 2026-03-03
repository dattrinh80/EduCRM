<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Queries;

use Maatwebsite\Excel\Facades\Excel;
use Modules\CRM\Lead\Application\Exports\LeadsTemplateExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadLeadTemplateHandler
{
    public function handle(DownloadLeadTemplateQuery $query): BinaryFileResponse
    {
        return Excel::download(new LeadsTemplateExport(), 'leads_import_template.xlsx');
    }
}
