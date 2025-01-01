<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\VendorOrderDataTable;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Luigel\Paymongo\Facades\Paymongo;
use App\Models\PayMongoSetting;
use App\Models\NotificationsUser;
use App\Models\NotificationsPharmacy;

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
        // USER NOTIFICATION
        $notificationUser = new NotificationsUser();
        $notificationUser->user_id = $order->user_id;
        $notificationUser->user_role = 'user';
        $notificationUser->order_id = $order->id;
        switch($order->order_status) {
            case 'pending':
                $notificationUser->text = 'Your order has been placed successfully!';
                break;
            case 'processed_and_ready_to_ship':
                $notificationUser->text = 'Your order has been processed and is ready to ship!';
                break;
            case 'dropped_off':
                $notificationUser->text = 'Your order has been dropped off!';
                break;
            case 'shipped':
                $notificationUser->text = 'Your order has been shipped!';
                break;
            case 'out_for_delivery':
                $notificationUser->text = 'Your order is out for delivery!';
                break;
            case 'delivered':
                $notificationUser->text = 'Your order has been delivered!';
                break;
            case 'cancelled':
                $notificationUser->text = 'Your order has been cancelled!';
                break;
            default:
                $notificationUser->text = 'Your order status has been updated!';
                break;
        }
        $notificationUser->created_at = now();
        $notificationUser->updated_at = now();
        $notificationUser->save();

        // VENDOR NOTIFICATION
        $notificationPharmacy = new NotificationsPharmacy();
        if ($order->orderProducts->isNotEmpty()) {
            $notificationPharmacy->vendor_id = $order->orderProducts->first()->vendor_id;
        } else {
            $notificationPharmacy->vendor_id = null;
        }
        $notificationPharmacy->user_role = 'vendor';
        $notificationPharmacy->order_id = $order->id;

        switch($order->order_status) {
            case 'pending':
                $notificationPharmacy->text = 'A customer has placed a new order!';
                break;
            case 'processed_and_ready_to_ship':
                $notificationPharmacy->text = 'You accepted and prepared to ship an order!';
                break;
            case 'dropped_off':
                $notificationPharmacy->text = 'You have dropped off the order!';
                break;
            case 'shipped':
                $notificationPharmacy->text = 'The order has been shipped!';
                break;
            case 'out_for_delivery':
                $notificationPharmacy->text = 'The order is out for delivery!';
                break;
            case 'delivered':
                $notificationPharmacy->text = 'The order has been delivered!';
                break;
            case 'cancelled':
                $notificationPharmacy->text = 'The order has been cancelled!';
                break;
            default:
                $notificationPharmacy->text = 'Order status updated!';
                break;
        }
        $notificationPharmacy->created_at = now();
        $notificationPharmacy->updated_at = now();
        $notificationPharmacy->save();

        return redirect()->back();
    }
}
