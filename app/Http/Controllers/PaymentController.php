<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use Illuminate\Http\Request;
use Midtrans\Notification;

class PaymentController extends Controller
{
    public function notification(Request $request)
    {
        try {
            $notification = new Notification();

            $orderId = substr($notification->order_id, 6); // Remove 'ORDER-' prefix
            $transaction = Transactions::find($orderId);

            if ($transaction) {
                $transactionStatus = $notification->transaction_status;
                $type = $notification->payment_type;
                $fraudStatus = $notification->fraud_status;

                $status = 'pending';

                if ($transactionStatus == 'capture') {
                    if ($type == 'credit_card') {
                        $status = ($fraudStatus == 'challenge') ? 'pending' : 'success';
                    }
                } else if ($transactionStatus == 'settlement') {
                    $status = 'success';
                } else if ($transactionStatus == 'pending') {
                    $status = 'pending';
                } else if ($transactionStatus == 'deny') {
                    $status = 'failed';
                } else if ($transactionStatus == 'expire') {
                    $status = 'expired';
                } else if ($transactionStatus == 'cancel') {
                    $status = 'cancelled';
                }

                $transaction->update(['status' => $status]);
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
