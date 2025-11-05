<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Models\IncomingLetter;
use App\Models\LetterAnnex;
use App\Models\Notification;
use App\Mail\SendLetter;
use App\Events\LetterActivityEvent;

class IncomingLetterController extends Controller
{
    /** 📋 Display all incoming letters */
    public function index(Request $request)
    {
        $query = IncomingLetter::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $letters = $query->orderBy('number', 'asc')->get();

        $years = IncomingLetter::selectRaw("strftime('%Y', date) as year")
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('incoming_letters.index', compact('letters', 'years'));
    }

    /** 📨 Show create form */
    public function create()
    {
        return view('incoming_letters.create');
    }

    /** 📨 Store new incoming letter + annexes + broadcast notification */
    public function store(Request $request)
    {
        $data = $this->validateLetter($request);

        $nextNumber = str_pad((IncomingLetter::max('number') ?? 0) + 1, 3, '0', STR_PAD_LEFT);
        $data['number'] = $nextNumber;

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('attachments', 'public');
        }

        $letter = IncomingLetter::create($data);

        if ($request->hasFile('annexes')) {
            foreach ($request->file('annexes') as $file) {
                $path = $file->store('annexes', 'public');
                $letter->annexes()->create([
                    'filename' => $file->getClientOriginalName(),
                    'file_path' => $path,
                ]);
            }
        }

        // ✅ Notification
        $notification = Notification::create([
            'user_id' => auth()->id(), // Added for live count
            'type' => 'incoming',
            'action' => 'created',
            'message' => "📥 New incoming letter from {$data['company']} (" . now()->format('H:i') . ")",
        ]);

        event(new LetterActivityEvent($notification));

        return redirect()->route('incoming-letters.index')
            ->with('success', 'Letter and annexes added successfully.');
    }

    /** 🖊 Edit form */
    public function edit($id)
    {
        $incomingLetter = IncomingLetter::findOrFail($id);
        $annexes = $incomingLetter->annexes;
        return view('incoming_letters.edit', compact('incomingLetter', 'annexes'));
    }

    /** 📝 Update letter + broadcast notification */
    public function update(Request $request, $id)
    {
        $letter = IncomingLetter::findOrFail($id);
        $data = $this->validateLetter($request, $letter->id);

        if ($request->hasFile('attachment')) {
            if ($letter->attachment && Storage::disk('public')->exists($letter->attachment)) {
                Storage::disk('public')->delete($letter->attachment);
            }
            $data['attachment'] = $request->file('attachment')->store('attachments', 'public');
        }

        $letter->update($data);

        if ($request->filled('remove_annexes')) {
            foreach ($request->remove_annexes as $annexId) {
                $annex = $letter->annexes()->find($annexId);
                if ($annex && Storage::disk('public')->exists($annex->file_path)) {
                    Storage::disk('public')->delete($annex->file_path);
                    $annex->delete();
                }
            }
        }

        if ($request->hasFile('annexes')) {
            foreach ($request->file('annexes') as $file) {
                $path = $file->store('annexes', 'public');
                $letter->annexes()->create([
                    'filename' => $file->getClientOriginalName(),
                    'file_path' => $path,
                ]);
            }
        }

        // ✅ Notification
        $notification = Notification::create([
            'user_id' => auth()->id(),
            'type' => 'incoming',
            'action' => 'updated',
            'message' => "📄 Incoming letter #{$letter->number} updated (" . now()->format('H:i') . ")",
        ]);

        event(new LetterActivityEvent($notification));

        return redirect()->route('incoming-letters.index')
            ->with('success', 'Letter updated successfully.');
    }

    /** ❌ Delete letter + annexes + broadcast notification */
    public function destroy($id)
    {
        $letter = IncomingLetter::findOrFail($id);

        if ($letter->attachment && Storage::disk('public')->exists($letter->attachment)) {
            Storage::disk('public')->delete($letter->attachment);
        }

        foreach ($letter->annexes as $annex) {
            if (Storage::disk('public')->exists($annex->file_path)) {
                Storage::disk('public')->delete($annex->file_path);
            }
            $annex->delete();
        }

        $letter->delete();

        $notification = Notification::create([
            'user_id' => auth()->id(),
            'type' => 'incoming',
            'action' => 'deleted',
            'message' => "❌ Incoming letter #{$id} deleted (" . now()->format('H:i') . ")",
        ]);

        event(new LetterActivityEvent($notification));

        return redirect()->route('incoming-letters.index')
            ->with('success', 'Letter and annexes deleted successfully.');
    }

    /** 📄 Show a letter */
    public function show($id)
    {
        $incomingLetter = IncomingLetter::findOrFail($id);
        $annexes = $incomingLetter->annexes;
        return view('incoming_letters.show', compact('incomingLetter', 'annexes'));
    }

    /** 🖨 Print version */
    public function print($id)
    {
        $incomingLetter = IncomingLetter::findOrFail($id);
        $annexes = $incomingLetter->annexes;
        return view('incoming_letters.print', compact('incomingLetter', 'annexes'));
    }

    /** ✉ Show email form */
    public function emailForm($id)
    {
        $incomingLetter = IncomingLetter::findOrFail($id);
        return view('incoming_letters.email_form', compact('incomingLetter'));
    }

    /** 📧 Send letter via email */
    public function sendEmail(Request $request, $id)
    {
        $incomingLetter = IncomingLetter::findOrFail($id);

        $validated = $request->validate([
            'to' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
        ]);

        if ($request->hasFile('attachment')) {
            $incomingLetter->attachment = $request->file('attachment')->store('attachments', 'public');
        }

        Mail::to($validated['to'])->send(new SendLetter(
            $incomingLetter,
            $validated['message'],
            $validated['subject']
        ));

        return redirect()->route('incoming-letters.index')
            ->with('success', 'Email sent successfully!');
    }

    /** 🕓 Letter history filter */
    public function history($year = null, $month = null, $day = null)
    {
        $query = IncomingLetter::query();

        if ($year) $query->whereYear('date', $year);
        if ($month) $query->whereMonth('date', $month);
        if ($day) $query->whereDay('date', $day);

        $letters = $query->orderBy('number', 'asc')->get();

        $years = IncomingLetter::selectRaw("strftime('%Y', date) as year")
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('incoming_letters.history', compact('letters', 'years'));
    }

    /** 🧾 Validation rules */
    private function validateLetter(Request $request, $id = null)
    {
        return $request->validate([
            'date' => 'required|date',
            'reference' => 'nullable|string|max:255',
            'company' => 'required|string|max:255',
            'addressed_to' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'status' => 'required|in:pending,in-progress,viewed,responded,done',
            'observation' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
            'annexes.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:4096',
        ]);
    }
}
