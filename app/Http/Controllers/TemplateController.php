<?php

namespace App\Http\Controllers;

use App\Models\Template;

use App\Http\Requests\TemplateRequest;
use App\Repositories\EmployeeRepository;
use App\Http\Resources\TemplateResource;
use App\Services\Contracts\TemplateServiceInterface;
use App\Services\Contracts\TemplateMessageServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use App\Services\MailService;
use Illuminate\Support\Facades\Mail;
use App\Mail\TemplateAuthorizationMail;


class TemplateController extends Controller
{
    /**
     * @var TemplateServiceInterface
     */
    protected $templateService;

    /**
     * @var TemplateMessageServiceInterface
     */
    
    protected $templateMessageService;
    protected $employeeRepository;
      protected $mailService;

    /**
     * TemplateController constructor.
     *
     * @param TemplateServiceInterface $templateService
     * @param TemplateMessageServiceInterface $templateMessageService
     */
   public function __construct(
        TemplateServiceInterface $templateService,
        TemplateMessageServiceInterface $templateMessageService,
        EmployeeRepository $employeeRepository,
        MailService $mailService
    ) {
        $this->templateService = $templateService;
        $this->templateMessageService = $templateMessageService;
        $this->employeeRepository = $employeeRepository;
        $this->mailService = $mailService;
    }

    /**
     * Display a listing of the templates.
     *
     * @return \Illuminate\View\View
     */
   public function index()
{
    // Get only unique templates grouped by name
    $templates = $this->templateService->getAllTemplates()
        ->groupBy('name')
        ->map(function ($group) {
            return $group->sortByDesc('created_at')->first(); // pick latest per name
        })
        ->values(); // important: reset numeric keys

    return view('template.index', compact('templates'));
}


    /**
     * Show the form for creating a new template.
     *
     * @return \Illuminate\View\View
     */
    // public function create()
    // {
        
    //     $employees = (new EmployeeRepository())->getAuthorizers();

    // return view('template.create', compact('employees'));
        
    //     // return view('template.create');
    // }

    public function create()
{
    $employees = $this->employeeRepository->getAuthorizers();
    return view('template.create', compact('employees'));
}

    /**
     * Store a newly created template in storage.
     *
     * @param TemplateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(TemplateRequest $request)
    {
        try {
            $validatedData = $request->validated();

            // Create the template
            // $template = $this->templateService->createTemplate(
            //     $validatedData,
            //     $request->hasFile('template_file') ? $request->file('template_file') : null
            // );

            $existingTemplate = $this->templateService->getTemplateByName($validatedData['name']);

if ($existingTemplate) {
    $template = $existingTemplate;
} else {
    // Create the template
    $template = $this->templateService->createTemplate(
        $validatedData,
        $request->hasFile('template_file') ? $request->file('template_file') : null
    );
}

            // Create messages for the template if provided
            if (isset($validatedData['messages']) && is_array($validatedData['messages'])) {
                $this->templateMessageService->createMultipleMessages(
                    $template->id,
                    $validatedData['messages']
                );
            }

            
        // Send authorization email
        $sent = true;
         if (!empty($validatedData['authorizer'])) {
                $this->sendAuthorizationEmail($validatedData['authorizer'], $validatedData['name']);
            }

            return redirect()->route('template.index')
                ->with('success', 'Template created successfully and email sent to the authorizer.');
        } catch (\Throwable $e) {
            Log::error('Template Creation Error', ['error' => $e->getMessage()]);
            return back()
                ->with('error', 'Error creating template: '.$e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified template.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $template = $this->templateService->getTemplateById($id);

        if (!$template) {
            return redirect()->route('template.index')
                ->with('error', 'Template not found.');
        }

        return view('template.edit', compact('template'));
    }

    /**
     * Update the specified template in storage.
     *
     * @param TemplateRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(TemplateRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();

            $template = $this->templateService->updateTemplate($id, $validatedData);

            if (!$template) {
                return redirect()->route('template.index')
                    ->with('error', 'Template not found.');
            }

            // Update messages for the template if provided
            if (isset($validatedData['messages']) && is_array($validatedData['messages'])) {
                // Delete existing messages and create new ones
                $this->templateMessageService->deleteMessagesByTemplateId($template->id);

                $this->templateMessageService->createMultipleMessages(
                    $template->id,
                    $validatedData['messages']
                ); 
            }

            return redirect()->route('template.index')
                ->with('success', 'Template updated successfully.');
        } catch (\Exception $e) {
            Log::error('Template Update Error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Error updating template: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified template from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
public function destroy($id)
{
    try {
        $template = Template::findOrFail($id);
        $template->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Template hidden successfully'
            ]);
        }

        return redirect()->route('template.index')
               ->with('success', 'Template hidden successfully');

    } catch (\Exception $e) {
        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Error hiding template'
            ], 500);
        }

        return redirect()->route('template.index')
               ->with('error', 'Error hiding template');
    }
}

    /**
     * View messages for a specific template.
     *
     * @param string $name
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function viewMessages($name, Request $request)
    {
        $templates = $this->templateService->getTemplatesByName($name);

        if ($templates->isEmpty()) {
            return redirect()->route('template.index')
                ->with('error', 'Template not found.');
        }

        // Get the first template to access its messages
        $template = $templates->first();

        // Get all template messages for this template name
        $templateMessages = collect();
        foreach ($templates as $tmpl) {
            $templateMessages = $templateMessages->merge($tmpl->templateMessages);
        }

        // Determine the back URL based on referrer or passed parameter
        $backUrl = route('template.index'); // Default back URL

        // Check if there's a 'from' parameter in the URL
        if ($request->has('from') && $request->input('from') === 'auth') {
            $backUrl = route('auth.list_template');
        } else {
            // Check referrer URL to determine source page
            $referer = $request->header('referer');
            if ($referer && str_contains($referer, '/auth/auth_list_template')) {
                $backUrl = route('auth.list_template');
            }
        }

        return view('template.view', [
            'templateMessages' => $templateMessages,
            'templateName' => $name,
            'backUrl' => $backUrl,
            'hasFile' => !empty($template->file_path)
        ]);
    }

    /**
     * Show the form for sending SMS.
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
     * Fetch template messages for a specific template.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchTemplateMessages(Request $request)
    {
        $templateName = $request->input('templateName');

        // Get template by name
        $templates = $this->templateService->getTemplatesByName($templateName);
        $template = $templates->where('approval_status', 'approved')->first();

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'No approved template found with this name.',
                'data' => []
            ]);
        }

        // Get messages for the template
        $messages = $this->templateMessageService->getMessagesByTemplateId($template->id);

        return response()->json([
            'success' => true,
            'data' => $messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'title' => $message->title,
                    'message' => $message->message
                ];
            })
        ]);
    }

    /**
     * Show the list of templates for authorization.
     *
     * @return \Illuminate\View\View
     */
    public function authListTemplate()
    {
        $templateStats = $this->templateService->getTemplateStatistics();

        return view('auth.auth_list_template', compact('templateStats'));
    }

    /**
     * Update the status of a template.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateTemplateStatus(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'status' => 'required|in:approved,rejected'
        ]);

        $result = $this->templateService->updateTemplateStatus(
            $request->input('name'),
            $request->input('status')
        );

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Template status updated successfully.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update template status.'
            ]);
        }
    }


private function sendAuthorizationEmail(string $authorizerEmail, string $templateName): bool
{
    try {
        \Log::info('SMTP STREAM', ['stream' => config('mail.mailers.smtp.stream')]);
        \Log::info('MAILER ACTIVE', [
            'default'    => config('mail.default'),
            'host'       => config('mail.mailers.smtp.host'),
            'port'       => config('mail.mailers.smtp.port'),
            'encryption' => config('mail.mailers.smtp.encryption'),
        ]);

        \Mail::mailer('smtp')
            ->to($authorizerEmail)
            ->send(new \App\Mail\TemplateAuthorizationMail($templateName, $authorizerEmail));

        return true;
    } catch (\Throwable $e) {
        \Log::error('TemplateAuthorizationMail failed', [
            'authorizer' => $authorizerEmail,
            'template'   => $templateName,
            'error'      => $e->getMessage(),
        ]);
        return false;
    }
}


    /**
     * Download template data as CSV.
     *
     * @param string $templateName
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function downloadTemplateData($templateName)
    {
        $result = $this->templateService->generateTemplateCSV($templateName);

        if (!$result) {
            return redirect()->back()
                ->with('error', 'Template not found.');
        }

        if ($result['type'] === 'file') {
            return Response::download($result['path'], $result['name']);
        } else {
            return Response::make($result['data'], 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $result['name'] . '"'
            ]);
        }
    }


    

}
