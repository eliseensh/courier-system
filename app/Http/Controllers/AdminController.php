<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IncomingLetter;
use App\Models\OutgoingLetter;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Fetch incoming/outgoing letters
        $incomingLetters = IncomingLetter::orderBy('date', 'desc')->get();
        $outgoingLetters = OutgoingLetter::orderBy('date', 'desc')->get();

        // Incoming stats
        $totalIncoming = $incomingLetters->count();
        $pendingIncoming = $incomingLetters->where('status', 'pending')->count();
        $respondedIncoming = $incomingLetters->where('status', 'responded')->count();
        $doneIncoming = $incomingLetters->where('status', 'done')->count();

        // Outgoing stats
        $totalOutgoing = $outgoingLetters->count();
        $draftOutgoing = $outgoingLetters->where('status', 'draft')->count();
        $sentOutgoing = $outgoingLetters->where('status', 'sent')->count();
        $archivedOutgoing = $outgoingLetters->where('status', 'archived')->count();

        return view('admin.dashboard', compact(
            'incomingLetters',
            'outgoingLetters',
            'totalIncoming',
            'pendingIncoming',
            'respondedIncoming',
            'doneIncoming',
            'totalOutgoing',
            'draftOutgoing',
            'sentOutgoing',
            'archivedOutgoing'
        ));
    }
}
