<?php

namespace App\Http\Controllers;
//namespace App\Providers;

use App\Http\Resources\SmsHistoryResource;
use App\Services\Contracts\HistoryServiceInterface;
use App\Services\Contracts\TemplateServiceInterface;
use App\Repositories\Contracts\SmsHistoryRepositoryInterface;
use Illuminate\Http\Request;

class SmsHistoryController extends Controller
{

     protected $smsHistoryRepo;
     

    /**
     * @var HistoryServiceInterface
     */
    protected $historyService;

    /**
     * @var TemplateServiceInterface
     */
    protected $templateService;

    /**
     * SmsHistoryController constructor.
     *
     * @param HistoryServiceInterface $historyService
     * @param TemplateServiceInterface $templateService
     */
    public function __construct(
        HistoryServiceInterface $historyService,
        TemplateServiceInterface $templateService,
        SmsHistoryRepositoryInterface $smsHistoryRepo
    ) {
        $this->historyService = $historyService;
        $this->templateService = $templateService;
        $this->smsHistoryRepo = $smsHistoryRepo;
    }

    /**
     * Display a listing of SMS history.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 15);

        //$history = $this->historyService->getSmsHistory($search, $perPage);
          $historyData = $this->smsHistoryRepo->getGroupedHistory($search, $perPage);
           //$groupedHistory = $history->groupBy('template_id');

        //$stats = $this->historyService->getHistoryStatistics();
         $stats = $this->smsHistoryRepo->getStatistics();
        $templateNames = $this->templateService->getDistinctTemplateNames();

        return view('sms.history', [
            //'history' => SmsHistoryResource::collection($history),
          'groupedHistory' => $historyData['grouped'],
            'messagesByTemplate' => $historyData['messages'],
            'stats' => $stats,
            //'templateNames' => $templateNames,
            'search' => $search
        ]);
    }

    /**
     * Display SMS history for a specific template.
     *
     * @param Request $request
     * @param int $templateId
     * @return \Illuminate\View\View
     */
    public function templateHistory(Request $request, $templateId)
    {
        $perPage = $request->input('per_page', 15);
          $history = $this->smsHistoryRepo->getByTemplateId($templateId, $perPage);
           $templateSample = $history->first();

        $template = $this->templateService->getTemplateById($templateId);

        if (!$template) {
            return redirect()->route('sms.history')
                ->with('error', 'Template not found.');
        }

        
        $history = $this->historyService->getHistoryByTemplate($templateId, $perPage);


        return view('sms.history', [
            'history' => SmsHistoryResource::collection($history),
            //'template' => $template
            'template' => $templateSample ? $templateSample->template : null,

        ]);
    }

    /**
     * Get SMS history statistics.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistics()
    {
        //$stats = $this->historyService->getHistoryStatistics();
         $stats = $this->smsHistoryRepo->getStatistics();

        return response()->json($stats);
    }
}
