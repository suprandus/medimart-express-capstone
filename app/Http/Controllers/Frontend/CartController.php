<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Adverisement;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariantItem;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart as PackageCart;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\UserCart;
use App\Models\Cart as ModelCart;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        try{
            if (Auth::check()){
                if(Auth::user()->role === 'vendor' || Auth::user()->role === 'admin') {
                    return response(['status' => 'error', 'message' => 'You are not allowed to add items to the cart.']);
                }
            }
    
            $product = Product::findOrFail($request->product_id);
    
            if ($product->qty === 0) {
                return response(['status' => 'error', 'message' => 'Product stock out']);
            } elseif ($product->qty < $request->qty) {
                return response(['status' => 'error', 'message' => 'Quantity not available in our stock']);
            }
    
            $variants = [];
            $variantTotalAmount = 0;
    
            if ($request->has('variants_items')) {
                foreach ($request->variants_items as $item_id) {
                    $variantItem = ProductVariantItem::find($item_id);
                    $variants[$variantItem->productVariant->name]['name'] = $variantItem->name;
                    $variants[$variantItem->productVariant->name]['price'] = $variantItem->price;
                    $variantTotalAmount += $variantItem->price;
                }
            }
    
            $productPrice = 0;
    
            if (checkDiscount($product)) {
                $productPrice = $product->offer_price;
            } else {
                $productPrice = $product->price;
            }
            if(Auth::check()){
                $productId = $request->input('product_id');
        
                $cartItem = ModelCart::where('user_id', Auth::id())
                                ->where('product_id', $productId)
                                ->first();
                                
                if ($cartItem) {
                    $cartItem->quantity += 1;
                    $cartItem->subtotal = $cartItem->quantity * $cartItem->product_price;
                    $cartItem->checked = 'yes';
                    $cartItem->created_at = now();
                    $cartItem->updated_at = now();
                    $cartItem->save();
                    return response(['status' => 'success', 'message' => 'Product Quantity updated!']);

                } else{
                    ModelCart::create([
                        'user_id' => Auth::id(),
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_price' => $productPrice,
                        'quantity' => $request->qty,
                        'subtotal' => $productPrice * $request->qty,
                        'product_image' => $product->thumb_image,
                        'prescription_approved' => 'yes',
                        'checked' => 'yes',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    return response(['status' => 'success', 'message' => 'Added to cart successfully!']);
                }
            }
            else{
                $cartData = [];
                $cartData['id'] = $product->id;
                $cartData['name'] = $product->name;
                $cartData['qty'] = $request->qty;
                $cartData['price'] = $productPrice;
                $cartData['weight'] = 10;
                $cartData['options']['variants'] = $variants;
                $cartData['options']['variants_total'] = $variantTotalAmount;
                $cartData['options']['image'] = $product->thumb_image;
                $cartData['options']['slug'] = $product->slug;
        
                PackageCart::add($cartData);
                Log::info('Cart Data: ' . json_encode($cartData));
                return response(['status' => 'success', 'message' => 'Added to cart successfully!']);
            }
        }
        catch(\Exception $e){
            Log::error($e);
            return response(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    
    public function updateProductQty(Request $request)
    {
        $productId = $request->product_id;
        $quantity = $request->quantity;

        if (Auth::check()) {
            $cartItem = ModelCart::where('user_id', Auth::id())->where('product_id', $productId)->first();
            if ($cartItem) {
                $cartItem->quantity = $quantity;
                $cartItem->subtotal = $cartItem->product_price * $quantity;
                $cartItem->save();
            }
        } else {
            PackageCart::update($productId, $quantity);
        }

        $productTotal = $this->getProductTotal($productId);

        return response(['status' => 'success', 'message' => 'Product Quantity Updated!', 'product_total' => $productTotal]);
    }

    public function updateCheckedStatus(Request $request)
    {
        $productId = $request->product_id;
        $checked = $request->checked;

        if (Auth::check()) {
            $cartItem = UserCart::where('user_id', Auth::id())->where('product_id', $productId)->first();
            if ($cartItem) {
                $cartItem->checked = $checked;
                $cartItem->save();
                return response(['status' => 'success', 'message' => 'Product checked status updated!']);
            }
        }

        return response(['status' => 'error', 'message' => 'Product not found!'], 404);
    }
    /** Show cart page  */
    public function cartDetails()
    {
        if (Auth::check()) {
            $userId = Auth::id();
            $cartItems = UserCart::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
            foreach ($cartItems as $item) {
                if(!$item->updated_at->isToday()){
                    $item->checked = 'no';
                    $item->save();
                }
            }
        } else {
            $cartItems = PackageCart::content();
        }

        if ($cartItems->isEmpty()) {
            Session::forget('coupon');
            toastr('Please add some products in your cart to view the cart page', 'warning', 'Cart is empty!');
            return redirect()->route('home');
        }

        $cartpage_banner_section = Adverisement::where('key', 'cartpage_banner_section')->first();
        $cartpage_banner_section = json_decode($cartpage_banner_section?->value);

        return view('frontend.pages.cart-detail', compact('cartItems', 'cartpage_banner_section'));
    }

    public function getProductTotal($productId)
    {
        if (Auth::check()) {
            $cartItem = UserCart::where('user_id', Auth::id())->where('product_id', $productId)->first();
            $total = $cartItem->product_price * $cartItem->quantity;
        } else {
            $cartItem = PackageCart::get($productId);
            $total = ($cartItem->price + $cartItem->options->variants_total) * $cartItem->qty;
        }
        return $total;
    }

    /** get cart total amount */
    public function cartTotal()
    {
        $total = 0;
        foreach (PackageCart::content() as $product) {
            $total += $this->getProductTotal($product->rowId);
        }

        return $total;
    }

    /** clear all cart products */
    public function clearCart()
    {
        PackageCart::destroy();

        return response(['status' => 'success', 'message' => 'Cart cleared successfully']);
    }

    /** Remove product form cart */    
    public function removeProduct($rowId)
    {
        if (Auth::check()) {
            // Remove product from the database cart
            ModelCart::where('user_id', Auth::id())
            ->where('product_id', $rowId)
            ->delete();
        } else {
            // Remove product from the session-based cart
            PackageCart::remove($rowId);
        }

        toastr('Product removed successfully!', 'success', 'Success');
        return redirect()->back();
    }

    /** Get cart count */
    public function getCartCount()
    {
        return PackageCart::content()->count();
    }

    /** Get all cart products */
    public function getCartProducts()
    {
        return PackageCart::content();
    }

    /** Romve product form sidebar cart */
    public function removeSidebarProduct(Request $request)
    {
        PackageCart::remove($request->rowId);

        return response(['status' => 'success', 'message' => 'Product removed successfully!']);
    }

    /** Apply coupon */
    public function applyCoupon(Request $request)
    {
        if ($request->coupon_code === null) {
            return response(['status' => 'error', 'message' => 'Coupon filed is required!']);
        }

        $coupon = Coupon::where(['code' => $request->coupon_code, 'status' => 1])->first();

        if ($coupon === null) {
            return response(['status' => 'error', 'message' => 'Coupon does not exist!']);
        } elseif ($coupon->start_date > date('Y-m-d')) {
            return response(['status' => 'error', 'message' => 'Coupon does not exist!']);
        } elseif ($coupon->end_date < date('Y-m-d')) {
            return response(['status' => 'error', 'message' => 'Coupon is expired!']);
        } elseif ($coupon->total_used >= $coupon->quantity) {
            return response(['status' => 'error', 'message' => 'You cannot apply this coupon!']);
        }

        if ($coupon->discount_type === 'amount') {
            Session::put('coupon', [
                'coupon_name' => $coupon->name,
                'coupon_code' => $coupon->code,
                'discount_type' => 'amount',
                'discount' => $coupon->discount
            ]);
        } elseif ($coupon->discount_type === 'percent') {
            Session::put('coupon', [
                'coupon_name' => $coupon->name,
                'coupon_code' => $coupon->code,
                'discount_type' => 'percent',
                'discount' => $coupon->discount
            ]);
        }

        return response(['status' => 'success', 'message' => 'Coupon applied successfully!']);
    }

    /** Calculate coupon discount */
    public function couponCalculation()
    {
        if (Session::has('coupon')) {
            $coupon = Session::get('coupon');
            $subTotal = getCartTotal();
            if ($coupon['discount_type'] === 'amount') {
                $total = $subTotal - $coupon['discount'];
                return response(['status' => 'success', 'cart_total' => $total, 'discount' => $coupon['discount']]);
            } elseif ($coupon['discount_type'] === 'percent') {
                $discount = ($subTotal * $coupon['discount'] / 100);
                $total = $subTotal - $discount;
                return response(['status' => 'success', 'cart_total' => $total, 'discount' => $discount]);
            }
        } else {
            $total = getCartTotal();
            return response(['status' => 'success', 'cart_total' => $total, 'discount' => 0]);
        }
    }
}
