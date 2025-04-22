<?php

namespace App\Http\Controllers;

use App\Models\Pack;
use App\Models\Transactions as Transactions_Users;
use App\Models\UserAnalyze;
use Midtrans\Transaction;

class MyOrderController extends Controller
{
    public function index()
    {
        $order = Transactions_Users::where('user_id', auth()->user()->id)
            ->where('status', '!=', 'success')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$order) {
            $order = Transactions_Users::where('user_id', auth()->user()->id)->first();
        }
        $packOrder = Transactions_Users::with('pack')->where('user_id', auth()->user()->id)->first();
        $namePack = ($packOrder && $packOrder->pack) ? $packOrder->pack->name_pack : 'Pack not found';

        $trxDetails = $this->checkPaymentStatus();
    
        // Jika ada trxDetails, kirim ke view
        return view('analyze.myOrder.myOrder', compact('order', 'trxDetails', 'namePack'));
    }

    public function checkPaymentStatus()
    {
        $order = Transactions_Users::where('user_id', auth()->user()->id)
            ->latest('created_at')
            ->first();

        if (is_null($order) || is_null($order->order_id)) {
            // Jika order atau order_id adalah null, tidak perlu melanjutkan apa-apa
            return redirect()->route('myOrderIndex')->withErrors('Order tidak ditemukan atau order_id tidak tersedia.');
        }

        $orderId = $order->order_id;

        try {
            $status = Transaction::status($orderId);
            // dd($status);
            $trxDetails = [
                'orderId' => $status->order_id,
                'trxStatus' => $status->transaction_status,
                'trxId' => $status->transaction_id,
                'trxPrice' => $status->gross_amount,
                'paymentType' => $status->payment_type,
                'trxTimeFormatted' => \Carbon\Carbon::parse($status->transaction_time)->format('d M Y H:i:s'),
                'trxVANumber' => isset($status->va_numbers[0]) ? $status->va_numbers[0]->va_number : (isset($status->permata_va_number) ? $status->permata_va_number : (isset($status->bill_key) ? $status->bill_key : 'N/A')),
                'trxCompanyCode' => isset($status->biller_code) ? $status->biller_code : 'N/A',
                'trxVANumberCStore' => isset($status->payment_code) ? $status->payment_code : 'N/A',
                'trxStore' => isset($status->store) ? $status->store : 'N/A',
            ];
            // dd($trxDetails['trxVANumber']);

            $packIdTransaction = $order->pack_id;
            $packName = Pack::where('id', $packIdTransaction)->first()->name_pack;

            $companyListPending = Transactions_Users::where('user_id', auth()->user()->id)->where('status', 'pending')->where('pack_id', $packIdTransaction)->get()->pluck('selected_emiten')->toArray();

            $companyListExist = Transactions_Users::where('user_id', auth()->user()->id)->where('status', 'Success')->where('pack_id', $packIdTransaction)->get()->pluck('selected_emiten')->toArray();

            $allCompanies = array_merge($companyListPending, $companyListExist);
            $companyList = collect($allCompanies)
                ->flatMap(function ($item) {
                    return array_map('trim', explode(',', $item));
                })
                ->unique()
                ->sort()
                ->values()
                ->implode(', ');

            $userId = auth()->user()->id;

            // Update status based on the transaction details
            if ($packName == 'bundle' && $trxDetails['trxStatus'] == 'settlement') {
                UserAnalyze::updateOrCreate(['id' => $userId], ['user_type' => 'bundle']);
                Transactions_Users::where('user_id', $userId)->update(['status' => 'Success']);

                $transactions = Transactions_Users::where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->get();

                if ($transactions->count() > 1) {
                    $transactionsToDelete = $transactions->slice(1);
                    $idsToDelete = $transactionsToDelete->pluck('id')->toArray();

                    Transactions_Users::whereIn('id', $idsToDelete)->delete();
                }
            } else if ($packName == 'custom' && $trxDetails['trxStatus'] == 'settlement') {
                UserAnalyze::updateOrCreate(['id' => $userId], ['user_type' => 'custom']);
                Transactions_Users::where('user_id', $userId)->update(['status' => 'Success']);
                Transactions_Users::where('user_id', $userId)->update(['selected_emiten' => $companyList]);

                $transactions = Transactions_Users::where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->get();

                if ($transactions->count() > 1) {
                    $transactionsToDelete = $transactions->slice(1);
                    $idsToDelete = $transactionsToDelete->pluck('id')->toArray();

                    Transactions_Users::whereIn('id', $idsToDelete)->delete();
                }
            } else if ($trxDetails['trxStatus'] == 'expire') {
                Transactions_Users::where('user_id', $userId)->update(['status' => 'Expired']);
            }

            return $trxDetails;
        } catch (\Exception $e) {
            // Tangani kesalahan jika terjadi error pada pengecekan transaksi
            return redirect()->route('myOrderIndex')->with($e->getMessage());
        }
    }
}