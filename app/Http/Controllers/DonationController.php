<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DonationController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'campaign_id' => 'required|exists:campaigns,id',
            'amount' => 'required|numeric|min:5000',
            'payment_method' => 'required|string',
            'message' => 'nullable|string',
            'is_anonymous' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'msg' => $validator->errors()->first(),
            ]);
        }

        $campaign = Campaign::findOrFail($request->campaign_id);

        $donation = Donation::create([
            'user_id' => Auth::id(),
            'campaign_id' => $campaign->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'message' => $request->message,
            'is_anonymous' => $request->is_anonymous,
            'status' => 'success',
        ]);

        // Update campaign collected funds
        $campaign->collected += $request->amount;
        $campaign->save();

        return response()->json([
            'ok' => true,
            'donation' => $donation,
        ]);
    }
}
