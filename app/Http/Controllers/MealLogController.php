<?php
namespace App\Http\Controllers;

use App\Models\DietProgressPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MealLogController extends Controller
{
    // Store image
    public function storeImage(Request $request)
    {
        $request->validate([
            'dietplan_id' => 'required|exists:diet_plans,dietplan_id',
            'photoDate'   => 'required|date',
            'photoFile'   => 'required|image|max:5120',
            'photoNote'   => 'nullable|string|max:500',
        ]);

        try {
            $path = $request->file('photoFile')->store('progress_photos', 'public');

            $photo              = new DietProgressPhoto();
            $photo->dietplan_id = $request->dietplan_id;
            $photo->user_id     = Auth::id();
            $photo->photo_date  = $request->photoDate;
            $photo->photo_path  = $path;
            $photo->note        = $request->photoNote;
            $photo->save();

            return redirect()->back()->with('success', 'Progress photo uploaded successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to upload photo. Please try again.');
        }
    }

    // Store meal log
    public function storeMealLog(Request $request)
    {

    }

    // Store weight log
    public function storeWeightLog(Request $request)
    {

    }
}
