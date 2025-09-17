<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SmsHistory;

class SmsApprovalController extends Controller
{
    //
     public function approve($id)
    {
        $sms = SmsHistory::findOrFail($id);
        $sms->status = 'approved';
        $sms->save();

        return redirect()->back()->with('success', 'SMS approved successfully.');
    }

    public function reject($id)
    {
        $sms = SmsHistory::findOrFail($id);
        $sms->status = 'rejected';
        $sms->save();

        return redirect()->back()->with('success', 'SMS rejected successfully.');
    }
}
