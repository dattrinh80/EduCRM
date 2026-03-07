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

        return view('lead::conversion', compact('lead'));
    }

    public function convert(Request $request, string $id)
    {
        $lead = $this->leadRepository->findById($id);
        if (!$lead) {
            return redirect()->route('admin.leads.index')->with('error', 'Lead không tồn tại.');
        }

        $validated = $request->validate([
            'guardian.name' => 'required|string|max:255',
            'guardian.phone' => 'nullable|string|max:20',
            'guardian.email' => 'nullable|email|max:255',
            'guardian.dob' => 'nullable|date',
            'guardian.gender' => 'nullable|string|in:MALE,FEMALE,OTHER',
            'guardian.address' => 'nullable|string',
            'students' => 'required|array|min:1',
            'students.*.name' => 'required|string|max:255',
            'students.*.dob' => 'nullable|date',
            'students.*.gender' => 'nullable|string|in:MALE,FEMALE,OTHER',
            'students.*.relationship' => 'nullable|string|max:50',
        ]);

        try {
            $command = new ConvertLeadToStudentCommand(
                leadId: $id,
                guardianData: $validated['guardian'],
                students: $validated['students'],
                convertedBy: auth()->id()
            );

            $this->conversionHandler->handle($command);

            return redirect()->route('admin.leads.index')->with('success', 'Chuyển đổi Lead thành công!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Lỗi chuyển đổi: ' . $e->getMessage());
        }
    }
}
