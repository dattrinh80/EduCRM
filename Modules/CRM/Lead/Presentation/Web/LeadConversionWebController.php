<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Presentation\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\Lead\Application\Commands\ConvertLeadToStudentCommand;
use Modules\CRM\Lead\Application\Commands\ConvertLeadToStudentHandler;
use Modules\CRM\Lead\Domain\LeadRepositoryInterface;

class LeadConversionWebController extends Controller
{
    public function __construct(
        private LeadRepositoryInterface $leadRepository,
        private ConvertLeadToStudentHandler $conversionHandler
    ) {}

    public function show(string $id)
    {
        $lead = $this->leadRepository->findById($id);
        if (!$lead) {
            return redirect()->route('admin.leads.index')->with('error', 'Lead không tồn tại.');
        }

        // Load lead source name and campaign name for display
        $leadSourceName = null;
        if ($lead->leadSourceId) {
            $source = \Modules\Marketing\LeadSource\Infrastructure\ReadModels\LeadSourceReadModel::find($lead->leadSourceId);
            $leadSourceName = $source?->name;
        }

        $campaignName = null;
        if ($lead->campaignId) {
            $campaign = \Modules\Marketing\Campaign\Infrastructure\ReadModels\CampaignReadModel::find($lead->campaignId);
            $campaignName = $campaign?->name;
        }

        $centerName = null;
        if ($lead->centerId) {
            $center = \Modules\Core\Center\Infrastructure\ReadModels\CenterReadModel::find($lead->centerId);
            $centerName = $center ? ('[' . ($center->code ?? '') . '] ' . $center->name) : null;
        }

        $assignedToName = null;
        if ($lead->assignedTo) {
            $user = \Modules\Core\User\Infrastructure\ReadModels\UserReadModel::find($lead->assignedTo);
            $assignedToName = $user?->name;
        }

        return view('lead::conversion', compact(
            'lead',
            'leadSourceName',
            'campaignName',
            'centerName',
            'assignedToName'
        ));
    }

    public function convert(Request $request, string $id)
    {
        $lead = $this->leadRepository->findById($id);
        if (!$lead) {
            return redirect()->route('admin.leads.index')->with('error', 'Lead không tồn tại.');
        }

        $validated = $request->validate([
            'students' => 'required|array|min:1',
            'students.*.name' => 'required|string|max:255',
            'students.*.dob' => 'nullable|date',
            'students.*.gender' => 'nullable|string|in:MALE,FEMALE,OTHER',
            'students.*.school' => 'nullable|string|max:255',
            'students.*.grade' => 'nullable|string|max:50',
            'students.*.guardians' => 'required|array|min:1',
            'students.*.guardians.*.name' => 'required|string|max:255',
            'students.*.guardians.*.phone' => 'required|string|max:20',
            'students.*.guardians.*.email' => 'nullable|email|max:255',
            'students.*.guardians.*.relationship' => 'required|string|max:50',
            'students.*.guardians.*.is_primary' => 'nullable',
        ]);

        try {
            $command = new ConvertLeadToStudentCommand(
                leadId: $id,
                students: $validated['students'],
                convertedBy: (string) auth()->id()
            );

            $this->conversionHandler->handle($command);

            return redirect()->route('admin.leads.index')->with('success', 'Chuyển đổi Lead thành công! Đã tạo ' . count($validated['students']) . ' học viên.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Lỗi chuyển đổi: ' . $e->getMessage());
        }
    }
}
