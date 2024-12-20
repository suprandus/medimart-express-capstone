<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\VendorOrderDataTable;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Luigel\Paymongo\Facades\Paymongo;
use App\Models\PayMongoSetting;

class VendorOrderController extends Controller
{
    /** PayMongo config */
    function paymongoConfig()
    {
        $paymongoSetting = PayMongoSetting::first();

        if ($paymongoSetting) {
            // Update the config in the application environment
            config(['paymongo.livemode' => $paymongoSetting->live_mode == 1 ? 'true' : 'false']);
            config(['paymongo.secret_key' => $paymongoSetting->secret_key]);
            config(['paymongo.public_key' => $paymongoSetting->public_key]);
        }

        // Return the config array
        return [
            'secret_key' => config('paymongo.secret_key', env('PAYMONGO_SECRET_KEY')),
            'public_key' => config('paymongo.public_key', env('PAYMONGO_PUBLIC_KEY')),
            'livemode' => config('paymongo.livemode', false),
        ];
    }

    public function index(VendorOrderDataTable $dataTable)
    {
        return $dataTable->render('vendor.order.index');
    }

    public function show(string $id)
    {
        $order = Order::with(['orderProducts'])->findOrFail($id);
        return view('vendor.order.show', compact('order'));
    }

    public function orderStatus(Request $request, string $id)
    {
        $this->paymongoConfig();

        $order = Order::with('transaction')->findOrFail($id);
        $transactionId = $order->transaction->transaction_id;
        $paymentMethod = $order->transaction->payment_method;

        if ($order->order_status === 'cancelled') {
            return back()->with('error', 'Cannot update cancelled orders');
        }

        if ($paymentMethod === 'COD') {
            $order->order_status = $request->status;
            $order->payment_status = 'cancelled';

            // Add quantity back to products
            foreach ($order->orderProducts as $orderProduct) {
                $product = Product::find($orderProduct->product_id);
                $product->qty += $orderProduct->qty;
                $product->save();
            }

            $order->save();
            toastr('Updated order status', 'success', 'Success');
        } else if ($paymentMethod === 'paymongo') {
            $response = Paymongo::checkout()->find($transactionId);
            $paymentId = $response->payments[0]['id'];
            $amount = $response->payments[0]['attributes']['amount'] / 100;

            // Initiates the refund
            $refund = Paymongo::refund()->create([
                'amount' => $amount,
                'notes' => 'Cancelled order',
                'payment_id' => $paymentId,
                'reason' => \Luigel\Paymongo\Models\Refund::REASON_REQUESTED_BY_CUSTOMER,
            ]);

            $order->order_status = $request->status;
            $order->payment_status = 'refunded';

            // Add quantity back to products
            foreach ($order->orderProducts as $orderProduct) {
                $product = Product::find($orderProduct->product_id);
                $product->qty += $orderProduct->qty;
                $product->save();
            }

            $order->save();
            toastr('Updated order status', 'success', 'Success');
        }

        return redirect()->back();
    }
}
