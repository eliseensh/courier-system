<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\IncomingLetter;
use App\Models\OutgoingLetter;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user && $user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Status counts
        $incomingStats = [
            'pending'     => IncomingLetter::where('status', 'pending')->count(),
            'in-progress' => IncomingLetter::where('status', 'in-progress')->count(),
            'viewed'      => IncomingLetter::where('status', 'viewed')->count(),
            'responded'   => IncomingLetter::where('status', 'responded')->count(),
            'done'        => IncomingLetter::where('status', 'done')->count(),
            'archived'    => IncomingLetter::where('status', 'archived')->count(),
        ];

        $outgoingStats = [
            'draft'    => OutgoingLetter::where('status', 'draft')->count(),
            'sent'     => OutgoingLetter::where('status', 'sent')->count(),
            'archived' => OutgoingLetter::where('status', 'archived')->count(),
        ];

        // Monthly data for chart (last 12 months)
        $months = collect();
        $incomingMonthly = collect();
        $outgoingMonthly = collect();

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months->push($month->format('M Y'));

            $incomingMonthly->push(
                IncomingLetter::whereYear('date', $month->year)
                              ->whereMonth('date', $month->month)
                              ->count()
            );

            $outgoingMonthly->push(
                OutgoingLetter::whereYear('date', $month->year)
                              ->whereMonth('date', $month->month)
                              ->count()
            );
        }

        return view('dashboard', compact(
            'incomingStats', 
            'outgoingStats', 
            'months', 
            'incomingMonthly', 
            'outgoingMonthly'
        ));
    }
}
