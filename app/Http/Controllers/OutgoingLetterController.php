<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\OutgoingLetter;
use App\Models\Notification;
use App\Events\LetterActivityEvent;

class OutgoingLetterController extends Controller
{
    /** 📋 Display all outgoing letters */
    public function index(Request $request)
    {
        $query = OutgoingLetter::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $outgoings = $query->orderBy('number', 'asc')->get();

        $years = $outgoings->pluck('date')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->year)
            ->unique()
            ->sortDesc();

        return view('outgoing.index', compact('outgoings', 'years'));
    }

    /** 📨 Show the create form */
    public function create()
    {
        return view('outgoing.create');
    }

    /** 📨 Store a new outgoing letter and broadcast event */
    public function store(Request $request)
    {
        $validated = $this->validateLetter($request);

        // Generate next letter number
        $nextNumber = str_pad((OutgoingLetter::max('number') ?? 0) + 1, 3, '0', STR_PAD_LEFT);
        $validated['number'] = $nextNumber;

        // Save attachment
        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('outgoing_attachments', 'public');
        }

        $letter = OutgoingLetter::create($validated);

        // ✅ Create and broadcast notification
        $notification = Notification::create([
            'user_id' => auth()->id(), // Added for live count
            'type' => 'outgoing',
            'action' => 'created',
            'message' => "📤 New outgoing letter to {$validated['recipient']} (" . now()->format('H:i') . ")",
        ]);

        event(new LetterActivityEvent($notification));

        return redirect()->route('outgoing-letters.index')
            ->with('success', 'Outgoing letter added successfully.');
    }

    /** ✏️ Show edit form */
    public function edit($id)
    {
        $letter = OutgoingLetter::findOrFail($id);
        return view('outgoing.edit', compact('letter'));
    }

    /** 📝 Update outgoing letter and broadcast event */
    public function update(Request $request, $id)
    {
        $letter = OutgoingLetter::findOrFail($id);
        $validated = $this->validateLetter($request, $id);

        // Handle attachment update
        if ($request->hasFile('attachment')) {
            if ($letter->attachment && Storage::disk('public')->exists($letter->attachment)) {
                Storage::disk('public')->delete($letter->attachment);
            }

            $validated['attachment'] = $request->file('attachment')->store('outgoing_attachments', 'public');
        }

        $letter->update($validated);

        // ✅ Create and broadcast notification
        $notification = Notification::create([
            'user_id' => auth()->id(),
            'type' => 'outgoing',
            'action' => 'updated',
            'message' => "✏️ Outgoing letter #{$letter->number} updated (" . now()->format('H:i') . ")",
        ]);

        event(new LetterActivityEvent($notification));

        return redirect()->route('outgoing-letters.index')
            ->with('success', 'Outgoing letter updated successfully.');
    }

    /** ❌ Delete outgoing letter and broadcast event */
    public function destroy($id)
    {
        $letter = OutgoingLetter::findOrFail($id);

        if ($letter->attachment && Storage::disk('public')->exists($letter->attachment)) {
            Storage::disk('public')->delete($letter->attachment);
        }

        $letter->delete();

        // ✅ Create and broadcast notification
        $notification = Notification::create([
            'user_id' => auth()->id(),
            'type' => 'outgoing',
            'action' => 'deleted',
            'message' => "🗑️ Outgoing letter #{$id} deleted (" . now()->format('H:i') . ")",
        ]);

        event(new LetterActivityEvent($notification));

        return redirect()->route('outgoing-letters.index')
            ->with('success', 'Outgoing letter deleted successfully.');
    }

    /** 🕓 Show outgoing letter history by date filters */
    public function history($year = null, $month = null, $day = null)
    {
        $query = OutgoingLetter::query();

        if ($year) $query->whereYear('date', $year);
        if ($month) $query->whereMonth('date', $month);
        if ($day) $query->whereDay('date', $day);

        $outgoings = $query->orderBy('number', 'asc')->get();

        $years = $outgoings->pluck('date')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->year)
            ->unique()
            ->sortDesc();

        return view('outgoing.history', compact('outgoings', 'years', 'year', 'month', 'day'));
    }

    /** 🧾 Validation rules */
    private function validateLetter(Request $request, $id = null)
    {
        return $request->validate([
            'date' => 'required|date',
            'reference' => 'nullable|string|max:255',
            'recipient' => 'required|string|max:255',
            'addressed_to' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'observation' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
            'status' => 'required|in:draft,sent,archived',
        ]);
    }
}
