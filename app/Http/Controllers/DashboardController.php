<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::where('status', 'active')->orderBy('id', 'desc')->get();
        return view('index', compact('campaigns'));
    }

    public function kampanye()
    {
        $campaigns = Campaign::where('status', 'active')->orderBy('id', 'desc')->get();
        return view('kampanye', compact('campaigns'));
    }

    public function dampak()
    {
        return view('dampak');
    }

    public function donasi()
    {
        $campaigns = Campaign::where('status', 'active')->orderBy('id', 'desc')->get();
        return view('donasi', compact('campaigns'));
    }

    public function donaturDashboard()
    {
        $user = Auth::user();
        
        $totalDonation = Donation::where('user_id', $user->id)
            ->where('status', 'success')
            ->sum('amount');
            
        $supportedCount = Donation::where('user_id', $user->id)
            ->where('status', 'success')
            ->distinct('campaign_id')
            ->count('campaign_id');
            
        $lastDonation = Donation::where('user_id', $user->id)
            ->where('status', 'success')
            ->latest()
            ->first();

        $donations = Donation::where('user_id', $user->id)
            ->with('campaign')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get unique campaigns supported
        $supportedCampaigns = Campaign::whereIn('id', function($query) use ($user) {
            $query->select('campaign_id')
                ->from('donations')
                ->where('user_id', $user->id)
                ->where('status', 'success');
        })->get();

        return view('donatur', compact('totalDonation', 'supportedCount', 'lastDonation', 'donations', 'supportedCampaigns'));
    }

    public function fundraiserDashboard()
    {
        $user = Auth::user();

        $activeCount = Campaign::where('created_by', $user->id)
            ->where('status', 'active')
            ->count();

        $totalCollected = Campaign::where('created_by', $user->id)
            ->where('status', 'active')
            ->sum('collected');

        $campaigns = Campaign::where('created_by', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Recent donations to this fundraiser's campaigns
        $recentDonations = Donation::whereIn('campaign_id', function($query) use ($user) {
            $query->select('id')
                ->from('campaigns')
                ->where('created_by', $user->id);
        })
        ->with(['user', 'campaign'])
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

        return view('fundraiser', compact('activeCount', 'totalCollected', 'campaigns', 'recentDonations'));
    }

    public function adminDashboard()
    {
        $campaigns = Campaign::orderBy('created_at', 'desc')->get();
        
        // Load users and count their campaigns
        $users = User::orderBy('id', 'desc')->get()->map(function($u) {
            $u->campaigns_count = Campaign::where('created_by', $u->id)->count();
            return $u;
        });

        return view('admin', compact('campaigns', 'users'));
    }

    public function switchRole(Request $request)
    {
        $request->validate([
            'role' => 'required|in:donatur,fundraiser',
        ]);

        $user = Auth::user();
        $user->role = $request->role;
        $user->save();

        return response()->json([
            'ok' => true,
            'role' => $user->role,
        ]);
    }
}
