<?php

use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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
    $total = 0;
    foreach (\Cart::content() as $product) {
        $total += ($product->price + $product->options->variants_total) * $product->qty;
    }
    return $total;
}

/** Get payable total amount */
function getMainCartTotal()
{
    if (Session::has('coupon')) {
        $coupon = Session::get('coupon');
        $subTotal = getCartTotal();
        if ($coupon['discount_type'] === 'amount') {
            $total = $subTotal - $coupon['discount'];
            return $total;
        } elseif ($coupon['discount_type'] === 'percent') {
            $discount = $subTotal - ($subTotal * $coupon['discount'] / 100);
            $total = $subTotal - $discount;
            return $total;
        }
    } else {
        return getCartTotal();
    }
}

/** Get cart discount */
function getCartDiscount()
{
    if (Session::has('coupon')) {
        $coupon = Session::get('coupon');
        $subTotal = getCartTotal();
        if ($coupon['discount_type'] === 'amount') {
            return $coupon['discount'];
        } elseif ($coupon['discount_type'] === 'percent') {
            $discount = $subTotal - ($subTotal * $coupon['discount'] / 100);
            return $discount;
        }
    } else {
        return 0;
    }
}

/** Get selected shipping fee from session */
function getShppingFee()
{
    if (Session::has('shipping_method')) {
        return Session::get('shipping_method')['cost'];
    } else {
        return 0;
    }
}

/** Get payable amount */
function getFinalPayableAmount()
{
    return  getMainCartTotal() + getShppingFee();
}

/** Limit text */
function limitText($text, $limit = 20)
{
    return \Str::limit($text, $limit);
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
    $totalQuantity = \Cart::count();
    $shippingFee = getShppingFee();

    foreach (\Cart::content() as $product) {
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
