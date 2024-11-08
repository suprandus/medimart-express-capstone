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
use App\Models\PaymongoSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Luigel\Paymongo\Facades\Paymongo;
use Stripe\Charge;
use Stripe\Stripe;
use Razorpay\Api\Api;

class PaymentController extends Controller
{
    public function index()
    {
        $paymongoSetting = PaymongoSetting::first();
        $codSetting = CodSetting::first();

        if (!Session::has('address')) {
            return redirect()->route('user.checkout');
        }
        return view('frontend.pages.payment', compact('paymongoSetting', 'codSetting'));
    }

    public function paymentSuccess()
    {
        return view('frontend.pages.payment-success');
    }

    public function storeOrder($paymentMethod, $paymentStatus, $transactionId, $paidAmount, $paidCurrencyName)
    {
        $setting = GeneralSetting::first();

        $order = new Order();
        $order->invocie_id = rand(1, 999999);
        $order->user_id = Auth::user()->id;
        $order->sub_total = getCartTotal();
        $order->amount =  getFinalPayableAmount();
        $order->currency_name = $setting->currency_name;
        $order->currency_icon = $setting->currency_icon;
        $order->product_qty = \Cart::content()->count();
        $order->payment_method = $paymentMethod;
        $order->payment_status = $paymentStatus;
        $order->order_address = json_encode(Session::get('address'));
        $order->shpping_method = json_encode(Session::get('shipping_method'));
        $order->coupon = json_encode(Session::get('coupon'));
        $order->order_status = 'pending';
        $order->save();

        // store order products
        foreach (\Cart::content() as $item) {
            $product = Product::find($item->id);
            $orderProduct = new OrderProduct();
            $orderProduct->order_id = $order->id;
            $orderProduct->product_id = $product->id;
            $orderProduct->vendor_id = $product->vendor_id;
            $orderProduct->product_name = $product->name;
            $orderProduct->variants = json_encode($item->options->variants);
            $orderProduct->variant_total = $item->options->variants_total;
            $orderProduct->unit_price = $item->price;
            $orderProduct->qty = $item->qty;
            $orderProduct->save();

            // update product quantity
            $updatedQty = ($product->qty - $item->qty);
            $product->qty = $updatedQty;
            $product->save();
        }

        // store transaction details
        $transaction = new Transaction();
        $transaction->order_id = $order->id;
        $transaction->transaction_id = $transactionId;
        $transaction->payment_method = $paymentMethod;
        $transaction->amount = getFinalPayableAmount();
        $transaction->amount_real_currency = $paidAmount;
        $transaction->amount_real_currency_name = $paidCurrencyName;
        $transaction->save();
    }

    public function clearSession()
    {
        \Cart::destroy();
        Session::forget('address');
        Session::forget('shipping_method');
        Session::forget('coupon');
        Session::forget('checkoutId');
    }

    /** PayMongo config */
    function paymongoConfig()
    {
        $paymongoSetting = PaymongoSetting::first();

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

    /** PayMongo redirect */
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

            // dd(session()->all());

            return redirect()->route('user.payment.success');
        }
    }

    public function paymongoCancel()
    {
        toastr('Someting went wrong try again later!', 'error', 'Error');
        return redirect()->route('user.payment');
    }

    /** Paypal config */
    public function paypalConfig()
    {

        $paypalSetting = PaypalSetting::first();
        $config = [
            'mode'    => $paypalSetting->mode === 1 ? 'live' : 'sandbox',
            'sandbox' => [
                'client_id'         => $paypalSetting->client_id,
                'client_secret'     => $paypalSetting->secret_key,
                'app_id'            => 'APP-80W284485P519543T',
            ],
            'live' => [
                'client_id'         => $paypalSetting->client_id,
                'client_secret'     => $paypalSetting->secret_key,
                'app_id'            => '',
            ],

            'payment_action' => 'Sale',
            'currency'       => $paypalSetting->currency_name,
            'notify_url'     => '',
            'locale'         => 'en_US',
            'validate_ssl'   =>  true,
        ];
        return $config;
    }

    /** Paypal redirect */
    public function payWithPaypal()
    {
        $config = $this->paypalConfig();
        $paypalSetting = PaypalSetting::first();

        $provider = new PayPalClient($config);
        $provider->getAccessToken();


        // calculate payable amount depending on currency rate
        $total = getFinalPayableAmount();
        $payableAmount = round($total * $paypalSetting->currency_rate, 2);


        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('user.paypal.success'),
                "cancel_url" => route('user.paypal.cancel'),
            ],
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => $config['currency'],
                        "value" => $payableAmount
                    ]
                ]
            ]
        ]);

        if (isset($response['id']) && $response['id'] != null) {
            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return redirect()->away($link['href']);
                }
            }
        } else {
            return redirect()->route('user.paypal.cancel');
        }
    }

    public function paypalSuccess(Request $request)
    {
        $config = $this->paypalConfig();
        $provider = new PayPalClient($config);
        $provider->getAccessToken();

        $response = $provider->capturePaymentOrder($request->token);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {

            // calculate payable amount depending on currency rate
            $paypalSetting = PaypalSetting::first();
            $total = getFinalPayableAmount();
            $paidAmount = round($total * $paypalSetting->currency_rate, 2);

            $this->storeOrder('paypal', 1, $response['id'], $paidAmount, $paypalSetting->currency_name);

            // clear session
            $this->clearSession();

            return redirect()->route('user.payment.success');
        }

        return redirect()->route('user.paypal.cancel');
    }

    public function paypalCancel()
    {
        toastr('Someting went wrong try again later!', 'error', 'Error');
        return redirect()->route('user.payment');
    }

    /** Stripe Payment */
    public function payWithStripe(Request $request)
    {

        // calculate payable amount depending on currency rate
        $stripeSetting = StripeSetting::first();
        $total = getFinalPayableAmount();
        $payableAmount = round($total * $stripeSetting->currency_rate, 2);

        Stripe::setApiKey($stripeSetting->secret_key);
        $response = Charge::create([
            "amount" => $payableAmount * 100,
            "currency" => $stripeSetting->currency_name,
            "source" => $request->stripe_token,
            "description" => "product purchase!"
        ]);

        if ($response->status === 'succeeded') {
            $this->storeOrder('stripe', 1, $response->id, $payableAmount, $stripeSetting->currency_name);
            // clear session
            $this->clearSession();

            return redirect()->route('user.payment.success');
        } else {
            toastr('Someting went wrong try again later!', 'error', 'Error');
            return redirect()->route('user.payment');
        }
    }

    /** Razorpay payment */
    public function payWithRazorPay(Request $request)
    {
        $razorPaySetting = RazorpaySetting::first();
        $api = new Api($razorPaySetting->razorpay_key, $razorPaySetting->razorpay_secret_key);

        // amount calculation
        $total = getFinalPayableAmount();
        $payableAmount = round($total * $razorPaySetting->currency_rate, 2);
        $payableAmountInPaisa = $payableAmount * 100;

        if ($request->has('razorpay_payment_id') && $request->filled('razorpay_payment_id')) {
            try {
                $response = $api->payment->fetch($request->razorpay_payment_id)
                    ->capture(['amount' => $payableAmountInPaisa]);
            } catch (\Exception $e) {
                toastr($e->getMessage(), 'error', 'Error');
                return redirect()->back();
            }


            if ($response['status'] == 'captured') {
                $this->storeOrder('razorpay', 1, $response['id'], $payableAmount, $razorPaySetting->currency_name);
                // clear session
                $this->clearSession();

                return redirect()->route('user.payment.success');
            }
        }
    }

    /** pay with cod */
    public function payWithCod(Request $request)
    {
        $codPaySetting = CodSetting::first();
        $setting = GeneralSetting::first();
        if ($codPaySetting->status == 0) {
            return redirect()->back();
        }

        // amount calculation
        $total = getFinalPayableAmount();
        $payableAmount = round($total, 2);


        $this->storeOrder('COD', 'pending', \Str::random(10), $payableAmount, $setting->currency_name);
        // clear session
        $this->clearSession();

        return redirect()->route('user.payment.success');
    }
}
