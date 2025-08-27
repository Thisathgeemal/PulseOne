<?php
namespace App\Http\Controllers;

use App\Models\DietProgressPhoto;
use App\Models\MealCompliance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \App\Models\WeightLog;

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
        $request->validate([
            'dietplan_id' => 'required|exists:diet_plans,dietplan_id',
            'meals'       => 'required|array',
        ]);

        $userId         = Auth::id();
        $dietPlanId     = $request->input('dietplan_id');
        $mealsCompleted = $request->input('meals');

        MealCompliance::updateOrCreate(
            [
                'member_id'   => $userId,
                'dietplan_id' => $dietPlanId,
                'log_date'    => now()->toDateString(),
            ],
            [
                'meals_completed' => $mealsCompleted,
            ]
        );

        return redirect()->back()->with('success', 'Daily meal view updated successfully!');
    }

    // Store weight log
    public function storeWeightLog(Request $request)
    {
        $request->validate([
            'dietplan_id' => 'required|exists:diet_plans,dietplan_id',
            'weight'      => 'required|numeric|min:0',
            'weightNote'  => 'nullable|string|max:255',
        ]);

        $weightLog = WeightLog::create([
            'member_id'   => Auth::id(),
            'dietplan_id' => $request->dietplan_id,
            'weight'      => $request->weight,
            'log_date'    => now()->toDateString(),
            'notes'       => $request->weightNote,
        ]);

        return redirect()->back()->with('success', 'Weight log added successfully!');
    }
}
