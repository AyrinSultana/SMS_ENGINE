<?php

namespace App\Http\Controllers;

use App\Repositories\EmployeeRepository;
use App\Http\Requests\SendSmsRequest;
use App\Http\Requests\UpdateSmsStatusRequest;
use App\Http\Resources\PendingListResource;
use App\Services\Contracts\SmsServiceInterface;
use App\Services\Contracts\TemplateServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\PendingList;
use App\Models\SmsHistory;
use App\Models\SmsQueue;
use Illuminate\Support\Facades\DB;
use App\Models\Excel;
use App\Mail\SMSAuthorizationMail;
use Illuminate\Support\Facades\Mail;

class SmsController extends Controller
{
    /**
     * @var SmsServiceInterface
     */
    protected $smsService;
      protected $employeeRepository;
    /**
     * @var TemplateServiceInterface
     */
    protected $templateService;
    
    /**
     * SmsController constructor.
     *
     * @param SmsServiceInterface $smsService
     * @param TemplateServiceInterface $templateService
     */
    public function __construct(
        SmsServiceInterface $smsService,
        TemplateServiceInterface $templateService,
        EmployeeRepository $employeeRepository
    ) {
        $this->smsService = $smsService;
        $this->templateService = $templateService;
        $this->employeeRepository = $employeeRepository;
    }

    /**
     * Display SMS form.
     *
     * @return \Illuminate\View\View
     */
    public function showSmsForm()
    {
        $templates = $this->templateService->getApprovedTemplates();
        $templateNames = $this->templateService->getDistinctTemplateNames('approved');
        $employees = $this->employeeRepository->getAuthorizers();

        return view('template.sms_form', compact('templates', 'templateNames', 'employees'));
    }

    /**
     * Display SMS authorization status.
     *
     * @return \Illuminate\View\View
     */
    public function showSmsStatus()
    {
        return view('template.sms_form_auth');
    }

    /**
     * Display authorization list.
     *
     * @return \Illuminate\View\View
     */
    public function authStatus()
    {
        // Use the paginated version to get ALL records, not just pending ones
        $makerStatus = $this->smsService->getPaginatedAllRecords(10);

        return view('auth.auth_list', compact('makerStatus'));
    }

    /**
     * Send SMS based on the selected method.
     *
     * @param SendSmsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendSms(SendSmsRequest $request)
    {
        try {
            // Get the validated data
            $validatedData = $request->validated();
             $authorizerName = $validatedData['authorizer'];
  
            // Get the selected SMS method and message
            $smsMethod = $validatedData['sms_method'];
            $message = $validatedData['messageDropdown'];
            $templateName = $validatedData['templateDropdown'];

            // Handle the sending based on the selected method
            switch ($smsMethod) {
                case 'all_users':
                    $this->smsService->sendToAllUsers($message, $templateName,$authorizerName);
                    break;

                case 'comma_separated':
                    $numbers = $validatedData['numbers'];
                    $this->smsService->sendToCommaSeparatedNumbers($numbers, $message, $templateName,$authorizerName);
                    break;

                case 'upload_excel':
                    $file = $request->file('excel_file');
                        if ($file) {
                                $excelName = $file->getClientOriginalName();

                                Excel::create([
                                    'name' => $excelName,
                                ]);
                            }
                 $this->smsService->sendFromExcel($file, $message, $templateName,$authorizerName);
                    break;

                default:
                    return redirect()->back()->with('error', 'Invalid SMS method selected.');
            }

            return redirect()->route('waiting_list')->with('success', 'SMS queued successfully.');
        } catch (\Exception $e) {
            // Log the error
            Log::error('SMS Sending Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error sending SMS: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the waiting list.
     *
     * @return \Illuminate\View\View
     */
    public function waiting_list()
    {
        // Use the paginated version instead
        $makerStatus = $this->smsService->getPaginatedWaitingList(10);

        return view('template.waiting_list', [
            'makerStatus' => $makerStatus
        ]);
    }

    /**
     * Update the status of a pending SMS.
     *
     * @param UpdateSmsStatusRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function updateStatus(UpdateSmsStatusRequest $request)
    {
        $validatedData = $request->validated();
        $authorizer = $validatedData['authorizer'] ?? 'Unknown Authorizer';
        $result = $this->smsService->updateSmsStatus(
            $validatedData['id'],
            $validatedData['status'],
           $authorizer
        );

        // Check if this is an AJAX request
        if ($request->ajax()) {
            if ($result) {
                return response()->json(['success' => true, 'message' => 'SMS status updated successfully.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to update SMS status.'], 400);
            }
        }

        // Non-AJAX response (fallback)
        if ($result) {
            return redirect()->back()->with('success', 'SMS status updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to update SMS status.');
        }
    }


    /**
     * Queue an SMS for sending (API method).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function queueSms(Request $request)
    {
        // Validate request
        $request->validate([
            'mobile' => 'required|string',
            'message' => 'required|string',
            'template_name' => 'nullable|string',
            'personal_data' => 'nullable|array',
        ]);

        $result = $this->smsService->queueSms(
            $request->input('mobile'),
            $request->input('message'),
            $request->input('template_name'),
            $request->input('personal_data')
        );

        if ($result) {
            return response()->json(['success' => true, 'message' => 'SMS queued successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to queue SMS'], 400);
        }
    }


    private function sendAuthorizationEmail(string $authorizerEmail, string $smsTemplateName): bool
{
    try {
        \Log::info('Sending SMS Authorization Mail', [
            'authorizer' => $authorizerEmail,
            'smsTemplate' => $smsTemplateName,
        ]);

        \Mail::mailer('smtp')
            ->to($authorizerEmail)
            ->send(new \App\Mail\SMSAuthorizationMail($smsTemplateName, $authorizerEmail));

        return true;
    } catch (\Throwable $e) {
        \Log::error('SMSAuthorizationMail failed', [
            'authorizer' => $authorizerEmail,
            'smsTemplate' => $smsTemplateName,
            'error' => $e->getMessage(),
        ]);
        return false;
    }
}

}
