<?php

use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Cart;
use App\Models\UserCart;
use Gloudemans\Shoppingcart\Facades\Cart as PackageCart;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/** Set Sidebar item active */
function setActive(array $route)
{
    if (is_array($route)) {
        foreach ($route as $r) {
            if (request()->routeIs($r)) {
                return 'active';
            }
        }
    }
}

/** Check if product have discount */
function checkDiscount($product)
{
    $currentDate = date('Y-m-d');

    if ($product->offer_price > 0 && $currentDate >= $product->offer_start_date && $currentDate <= $product->offer_end_date) {
        return true;
    }

    return false;
}

/** Calculate discount percent */
function calculateDiscountPercent($originalPrice, $discountPrice)
{
    $discountAmount = $originalPrice - $discountPrice;
    $discountPercent = ($discountAmount / $originalPrice) * 100;

    return round($discountPercent);
}


/** Check the product type */
function productType($type)
{
    switch ($type) {
        case 'new_arrival':
            return 'New';
            break;
        case 'featured_product':
            return 'Featured';
            break;
        case 'top_product':
            return 'Top';
            break;

        case 'best_product':
            return 'Best';
            break;

        default:
            return '';
            break;
    }
}

/** Get total cart amount */
function getCartTotal()
{
    try {
        if (Auth::check()) {
            $cartItems = Cart::where('user_id', Auth::id())->where('checked', 'yes')->get();

            // Ensure subtotal values are cast to float before summing
            $cartTotal = $cartItems->sum(function ($item) {
                return (float)$item->subtotal;
            });

            return number_format($cartTotal, 2);
        } else {
            $total = 0;
            foreach (PackageCart::content() as $product) {
                $total += ($product->price + $product->options->variants_total) * $product->qty;
            }
            return number_format($total, 2);
        }
    } catch (Exception $e) {
        Log::info($e->getMessage());
        return '0.00'; // Return a fallback value in case of an exception
    }
}


/** Get payable total amount */
function getMainCartTotal()
{
    if (Session::has('coupon')) {
        $coupon = Session::get('coupon');

        // Ensure getCartTotal() returns a numeric value for calculations
        $subTotal = (float) str_replace(',', '', getCartTotal());

        if ($coupon['discount_type'] === 'amount') {
            $total = $subTotal - $coupon['discount'];
            return number_format(max($total, 0), 2); // Ensure no negative total
        } elseif ($coupon['discount_type'] === 'percent') {
            $discount = ($subTotal * $coupon['discount'] / 100);
            $total = $subTotal - $discount;
            return number_format(max($total, 0), 2); // Ensure no negative total
        }
    } else {
        // Ensure getCartTotal() is returned as a formatted string
        return number_format((float) str_replace(',', '', getCartTotal()), 2);
    }
}


/** Get cart discount */
function getCartDiscount()
{
    if (Session::has('coupon')) {
        $coupon = Session::get('coupon');
        $subTotal = getCartTotal();
        if ($coupon['discount_type'] === 'amount') {
            return number_format($coupon['discount'], 2);
        } elseif ($coupon['discount_type'] === 'percent') {
            $discount = ($subTotal * $coupon['discount'] / 100);
            return number_format($discount, 2);
        }
    } else {
        return number_format(0, 2);
    }
}

/** Get selected shipping fee from session */
function getShppingFee()
{
    if (Session::has('shipping_method')) {
        $shippingCost = Session::get('shipping_method')['cost'];
        return number_format($shippingCost, 2);
    } else {
        return number_format(0, 2);
    }
}

/** Get payable amount */
function getFinalPayableAmount()
{
    return  number_format(getMainCartTotal() + getShppingFee(), 2);
}

/** Limit text */
function limitText($text, $limit = 20)
{
    return Str::limit($text, $limit);
}

function getCurrencyIcon()
{
    $icon = GeneralSetting::first();

    return $icon->currency_icon;
}

/** Create line_items for PayMongo checkout */
function createLineItems()
{
    $lineItems = [];
    $totalQuantity = PackageCart::count();
    $shippingFee = getShppingFee();

    foreach (PackageCart::content() as $product) {
        $shippingPerItem = $totalQuantity > 0 ? ($shippingFee / $totalQuantity) : 0;

        $lineItems[] = [
            'amount' => (int)(($product->price + $shippingPerItem) * 100),
            'currency' => 'PHP',
            'description' => 'Order placed by ' . Auth::user()->name,
            'quantity' => (int)$product->qty,
            'name' => $product->name
        ];
    }

    return $lineItems;
}
