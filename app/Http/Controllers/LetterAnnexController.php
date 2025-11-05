<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LetterAnnex;
use Illuminate\Support\Facades\Storage;

class LetterAnnexController extends Controller
{
    /**
     * Delete an annex file
     */
    public function destroy($id)
    {
        $annex = LetterAnnex::findOrFail($id);

        // Delete file from storage
        if (Storage::disk('public')->exists($annex->file_path)) {
            Storage::disk('public')->delete($annex->file_path);
        }

        // Delete record from database
        $annex->delete();

        return redirect()->back()->with('success', 'Annex deleted successfully.');
    }
}
