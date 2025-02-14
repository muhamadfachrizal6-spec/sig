<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use App\Models\UserAnalyze;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function success(Request $request)
    {

        if ($request->user()->user_type === 'free' && $request->user()->user_type !== 'bundle') {
            UserAnalyze::updateOrCreate(
                ['id' => $request->user()->id],
                ['user_type' => 'custom']
            );
        } else {
            UserAnalyze::updateOrCreate(
                ['id' => $request->user()->id],
                ['user_type' => 'bundle']
            );
        }

        Transactions::updateOrCreate(
            ['user_id' => $request->user()->id],
            ['status' => 'Success'],
        );

        return response()->json([
            'message' => 'Payment success processed'
        ], 200);
    }

    public function pending(Request $request)
    {
        return response()->json([
            'message' => 'Payment pending processed'
        ], 200);
    }
}