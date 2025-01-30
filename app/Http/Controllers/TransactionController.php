<?php
// app/Http/Controllers/TransactionController.php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use Illuminate\Http\Request;
use App\Models\Pack;

class TransactionController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function process(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_email' => 'required|email|max:100',
            'product_type' => 'required|in:free,package,custom',
            'pack_id' => 'required_unless:product_type,custom|exists:pack,id',
            'items' => 'required_if:product_type,custom|array',
            'items.*.pack_id' => 'required_if:product_type,custom|exists:pack,id',
            'items.*.quantity' => 'required_if:product_type,custom|integer|min:1'
        ]);

        $result = $this->midtransService->createTransaction($request);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }

        return response()->json($result);
    }

    public function notification(Request $request)
    {
        $notification = new \Midtrans\Notification();

        $transaction = Transaction::where('order_id', $notification->order_id)->first();

        if ($transaction) {
            $transaction->update([
                'status' => $notification->transaction_status,
                'payment_type' => $notification->payment_type,
                'payment_code' => $notification->payment_code ?? null,
                'pdf_url' => $notification->pdf_url ?? null
            ]);
        }

        return response()->json(['success' => true]);
    }
}
