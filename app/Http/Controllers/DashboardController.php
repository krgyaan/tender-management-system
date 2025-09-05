<?php

namespace App\Http\Controllers;

use App\Models\BidSubmission;
use App\Models\TenderInfo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public $roles = [
        'admin' => 'Admin',
        'coordinator' => 'Team Coordinator',
        'common-coordinator' => 'Common Coordinator',
        'team-leader' => 'Tender Team Leader',
        'tender-executive' => 'Tender Executive',
        'operation-leader' => 'Operations Team Leader',
        'operation-executive' => 'Operations Executive',
        'operation-engineer' => 'Operations Engineer',
        'account-leader' => 'Account Team Leader',
        'account-executive' => 'Account Executive',
        'accountant' => 'Accountant',
        'field' => 'Field',
    ];

    public function index()
    {
        $user = Auth::user();
        if (!array_key_exists($user->role, $this->roles)) {
            return redirect()->back()->with('error', 'Please contact the admin to update your role and permission.');
        }
        $data['role'] = $this->roles[$user->role];
        $data['userCount'] = User::where('role', '!=', 'admin')->where('status', 1)->count();
        $tenderInfoQuery = TenderInfo::query()->where('deleteStatus', '0');

        if ($user->role == 'admin') {
            // Admins can see everything — no query filter needed
        } elseif (in_array($user->role, ['team-leader', 'coordinator'])) {
            $tenderInfoQuery->where('team', $user->team);
        } else {
            $tenderInfoQuery->where('team_member', $user->id);
        }

        // Eager load tq_received
        $data['tender_info'] = $tenderInfoQuery->with('tq_received')->get();

        $data['tenderInfoCount'] = $data['tender_info']->count();
        $data['follow_ups'] = DB::table('follow_ups')
            ->where('assign_initiate', 'Followup Initiated')
            ->when($user->role != 'admin', function ($query) use ($user) {
                $query->where('assigned_to', $user->id);
            })
            ->get();

        $data['bided'] = BidSubmission::with('tenderdue')->where('status', 'Bid Submitted')
            ->when($user->role != 'admin', function ($query) use ($user) {
                $query->whereHas('tenderdue', function ($query) use ($user) {
                    $query->where('team_member', $user->id);
                });
            })
            ->count();

        // Add: Pass all active users (except admin) for admin dropdown
        $activeUsers = [];
        if ($user->role == 'admin') {
            $activeUsers = User::where('role', '!=', 'admin')->where('status', 1)->get();
        } else if (in_array($user->role, ['team-leader', 'coordinator'])) {
            $activeUsers = User::where('role', '!=', 'admin')->where('team', $user->team)->where('status', 1)->get();
        }

        return view('dashboard', compact('user', 'data', 'activeUsers'));
    }
}
