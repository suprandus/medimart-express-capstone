<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\canceledOrderDataTable;
use App\DataTables\deliveredOrderDataTable;
use App\DataTables\droppedOffOrderDataTable;
use App\DataTables\OrderDataTable;
use App\DataTables\outForDeliveryOrderDataTable;
use App\DataTables\PendingOrderDataTable;
use App\DataTables\processedOrderDataTable;
use App\DataTables\shippedOrderDataTable;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Luigel\Paymongo\Facades\Paymongo;
use App\Models\PayMongoSetting;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(OrderDataTable $dataTable)
    {
        return $dataTable->render('admin.order.index');
    }

    public function pendingOrders(PendingOrderDataTable $dataTable)
    {
        return $dataTable->render('admin.order.pending-order');
    }

    public function processedOrders(processedOrderDataTable $dataTable)
    {
        return $dataTable->render('admin.order.pending-order');
    }

    public function droppedOfOrders(droppedOffOrderDataTable $dataTable)
    {
        return $dataTable->render('admin.order.dropped-off-order');
    }

    public function shippedOrders(shippedOrderDataTable $dataTable)
    {
        return $dataTable->render('admin.order.shipped-order');
    }

    public function outForDeliveryOrders(outForDeliveryOrderDataTable $dataTable)
    {
        return $dataTable->render('admin.order.out-for-delivery-order');
    }

    public function deliveredOrders(deliveredOrderDataTable $dataTable)
    {
        return $dataTable->render('admin.order.delivered-order');
    }

    public function canceledOrders(canceledOrderDataTable $dataTable)
    {
        return $dataTable->render('admin.order.canceled-order');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::select()->with(['transaction'])->findOrFail($id);
        $paymentMethod = $order->transaction->payment_method;

        return view('admin.order.show', compact('order', 'paymentMethod'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::findOrFail($id);

        // delete order products
        $order->orderProducts()->delete();
        // delete transaction
        $order->transaction()->delete();

        $order->delete();

        return response(['status' => 'success', 'message' => 'Deleted successfully!']);
    }

    public function changeOrderStatus(Request $request)
    {
        $order = Order::findOrFail($request->id);

        if ($order->order_status === 'cancelled') {
            return response([
                'status' => 'error',
                'message' => 'Cancelled orders cannot be updated'
            ], 422);
        }

        $order->order_status = $request->status;

        // Add quantity back to products
        foreach ($order->orderProducts as $orderProduct) {
            $product = Product::find($orderProduct->product_id);
            $product->qty += $orderProduct->qty;
            $product->save();
        }

        $order->save();

        return response([
            'status' => 'success',
            'message' => 'Updated order status'
        ]);
    }

    public function changePaymentStatus(Request $request)
    {
        $paymentStatus = Order::findOrFail($request->id);
        $paymentStatus->payment_status = $request->status;
        $paymentStatus->save();

        return response(['status' => 'success', 'message' => 'Updated payment status successfully']);
    }


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

    public function orderPaymongoStatus(Request $request, string $id)
    {
        $this->paymongoConfig();

        $order = Order::with('transaction')->findOrFail($id);
        $transactionId = $order->transaction->transaction_id;
        $paymentMethod = $order->transaction->payment_method;

        if ($order->order_status === 'cancelled') {
            return back()->with('error', 'Cannot update cancelled orders');
        }

        if ($paymentMethod === 'paymongo') {
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

            $order->order_status = 'cancelled';
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
