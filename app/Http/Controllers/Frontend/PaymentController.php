<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CodSetting;
use App\Models\GeneralSetting;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\PaypalSetting;
use App\Models\Product;
use App\Models\RazorpaySetting;
use App\Models\StripeSetting;
use App\Models\Transaction;
use App\Models\PayMongoSetting;
use App\Models\SalesAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Luigel\Paymongo\Facades\Paymongo;
use Gloudemans\Shoppingcart\Facades\Cart as PackageCart;
use App\Models\UserCart;
use App\Models\Cart as ModelCart;
use App\Models\NotificationsUser;
use App\Models\NotificationsPharmacy;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function index()
    {
        $cartItems = UserCart::where('user_id',Auth::user()->id)
            ->where('checked', 'yes')
            ->get();
        $paymongoSetting = PayMongoSetting::first();
        $codSetting = CodSetting::first();

        if (!Session::has('address')) {
            return redirect()->route('user.checkout');
        }
        return view('frontend.pages.payment', compact('cartItems', 'paymongoSetting', 'codSetting'));
    }

    public function paymentSuccess()
    {
        return view('frontend.pages.payment-success');
    }

    public function storeOrder($paymentMethod, $paymentStatus, $transactionId, $paidAmount, $paidCurrencyName)
    {
        $setting = GeneralSetting::first();

        $cartItems = UserCart::where('user_id', Auth::user()->id)
            ->where('checked', 'yes')
            ->get();
        $totalOrderQuantity = $cartItems->sum('cart_product_count');

        $order = new Order();
        $order->invocie_id = rand(1, 999999);
        $order->user_id = Auth::id();
        $order->sub_total = getCartTotal();
        $order->amount =  getFinalPayableAmount();
        $order->currency_name = $setting->currency_name;
        $order->currency_icon = $setting->currency_icon;
        $order->product_qty = $totalOrderQuantity;
        $order->payment_method = $paymentMethod;
        $order->payment_status = $paymentStatus;
        $order->order_address = json_encode(Session::get('address'));
        $order->shpping_method = json_encode(Session::get('shipping_method'));
        $order->coupon = json_encode(Session::get('coupon'));
        $order->order_status = 'pending';
        $order->save();

        // store order products
        foreach ($cartItems as $item) {
            Log::info(json_encode($item));

            $product = Product::find($item->product_id);
            $orderProduct = new OrderProduct();
            $orderProduct->order_id = $order->id;
            $orderProduct->product_id = $item->product_id;
            $orderProduct->vendor_id = $product->vendor_id;
            $orderProduct->product_name = $product->name;
            $orderProduct->variants = json_encode($item->brand_id);
            $orderProduct->variant_total = $item->count();
            $orderProduct->unit_price = $item->product_price;
            $orderProduct->qty = $item->cart_product_count;
            $orderProduct->save();

            Log::info("Product '$product->id' Product Stock Before Purchase: ". $product->qty);
            $updatedQty = ($product->qty - $item->qty);
            $product->qty = $updatedQty;
            $product->save();
            Log::info("Product '$product->id' Stock After Purchase: ". $product->qty);


            $salesAdmin = new SalesAdmin();
            $salesAdmin->vendor_id = $product->vendor_id;
            $salesAdmin->product_id = $product->id;
            $salesAdmin->product_name = $product->name;
            $salesAdmin->product_brand_id = $product->brand_id;
            $salesAdmin->product_category_id = $product->category_id;
            $salesAdmin->product_sub_category_id = $product->sub_category_id;
            $salesAdmin->product_child_category_id = $product->child_category_id;
            $salesAdmin->product_price = $item->price;
            $salesAdmin->product_order_quantity = $item->qty;
            $salesAdmin->order_cost = getFinalPayableAmount();
            $salesAdmin->sales = ($item->price * $item->qty) * 0.1; // 10% commission revenue of medimart
            $salesAdmin->created_at = now();
            $salesAdmin->save();

            // clear cart
            $cartItem = ModelCart::where('product_id', $item->product_id)
                ->first();
            $cartItem->delete();
        }

        $transaction = new Transaction();
        $transaction->order_id = $order->id;
        $transaction->transaction_id = $transactionId;
        $transaction->payment_method = $paymentMethod;
        $transaction->amount = getFinalPayableAmount();
        $transaction->amount_real_currency = $paidAmount;
        $transaction->amount_real_currency_name = $paidCurrencyName;
        $transaction->save();

        $vendor_id =  OrderProduct::select('vendor_id')->where('order_id', $order->id)->first();

        //USER NOTIFICATION
        $notificationUser = new NotificationsUser();
        $notificationUser->user_id = Auth::id();
        $notificationUser->user_role = Auth::user()->role;
        $notificationUser->order_id = $order->id;
        $notificationUser->text = 'Your order has been placed successfully!';
        $notificationUser->created_at = now();
        $notificationUser->updated_at = now();
        $notificationUser->save();

        //VENDOR NOTIFICATION
        $notificationPharmacy = new NotificationsPharmacy();
        $notificationPharmacy->vendor_id = $vendor_id->vendor_id;
        $notificationPharmacy->user_role = 'vendor';
        $notificationPharmacy->order_id = $order->id;
        $notificationPharmacy->text = 'A customer has placed a new order!';
        $notificationPharmacy->created_at = now();
        $notificationPharmacy->updated_at = now();
        $notificationPharmacy->save();
    }

    public function clearSession()
    {
        PackageCart::destroy();
        Session::forget('address');
        Session::forget('shipping_method');
        Session::forget('coupon');
        Session::forget('checkoutId');
    }
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
    public function payWithPayMongo()
    {
        $this->paymongoConfig();

        $lineItems = createLineItems();

        $checkout = Paymongo::checkout()->create([
            'cancel_url' => route('user.paymongo.cancel'),
            'billing' => [
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ],
            'line_items' => $lineItems,
            'payment_method_types' => [
                'gcash',
                'paymaya',
                'grab_pay'
            ],
            'success_url' => route('user.paymongo.success'),
            'customer_email' => Auth::user()->email,
            'send_email_receipt' => true,
        ]);

        Session::put('checkoutId', $checkout->id);

        // dd(session('checkoutId'));

        return redirect()->away($checkout->checkout_url);
    }
    public function paymongoSuccess()
    {
        $this->paymongoConfig();
        $checkout = Paymongo::checkout()->find(Session::get('checkoutId'));

        $paymentIntentStatus = $checkout->payment_intent['attributes']['status'];

        if ($paymentIntentStatus === 'succeeded') {
            $this->storeOrder('paymongo', 'completed', $checkout->id, $checkout->payment_intent['attributes']['amount'] / 100, $checkout->payment_intent['attributes']['currency']);

            $this->clearSession();

            return redirect()->route('user.payment.success');
        }
    }
    public function paymongoCancel()
    {
        toastr('Someting went wrong try again later!', 'error', 'Error');
        return redirect()->route('user.payment');
    }
    public function payWithCod(Request $request)
    {
        $codPaySetting = CodSetting::first();
        $setting = GeneralSetting::first();
        if ($codPaySetting->status == 0) {
            return redirect()->back();
        }

        $total = getFinalPayableAmount();
        $payableAmount = round($total, 2);


        $this->storeOrder('COD', 'pending', Str::random(10), $payableAmount, $setting->currency_name);
        $this->clearSession();

        return redirect()->route('user.payment.success');
    }
}
