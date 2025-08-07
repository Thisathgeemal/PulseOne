<?php
namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // display payment details
    public function getPaymentData(Request $request)
    {
        $search = $request->input('search');
        $date   = $request->input('date');

        $payments = Payment::with(['user', 'membershipType'])
            ->when($date, function ($query) use ($date) {
                $query->whereDate('payment_date', $date);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($q2) use ($search) {
                        $q2->where('first_name', 'like', "%$search%")
                            ->orWhere('last_name', 'like', "%$search%");
                    })
                        ->orWhere('payment_method', 'like', "%$search%")
                        ->orWhereHas('membershipType', function ($q3) use ($search) {
                            $q3->where('type_name', 'like', "%$search%");
                        });
                });
            })
            ->paginate(10);

        return view('adminDashboard.payment', compact('payments'));
    }

}
