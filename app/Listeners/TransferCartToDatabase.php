<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;
use App\Models\Cart as ModelCart;
use Gloudemans\Shoppingcart\Facades\Cart as PackageCart;

class TransferCartToDatabase
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        try {
            $user = $event->user;
            $userId = $user->id;
            $cartItems = PackageCart::content();

            foreach ($cartItems as $item) {

                $cartItem = ModelCart::where('user_id', $userId)
                                ->where('product_id', $item->id)
                                ->first();

                if ($cartItem) {
                    // If the product is already in the cart, increment the quantity
                    $cartItem->quantity += $item->qty;
                    $cartItem->subtotal = $cartItem->quantity * $cartItem->product_price;
                    $cartItem->save();

                } else {
                    // If the product is not in the cart, add a new entry
                    $newCartItem = ModelCart::create([
                        'user_id' => $userId,
                        'product_id' => $item->id,
                        'product_name' => $item->name,
                        'product_price' => $item->price,
                        'quantity' => $item->qty,
                        'subtotal' => $item->price * $item->qty,
                        'product_image' => $item->options->image,
                        'created_at' => now(),
                        'prescription_approved' => 'no',
                    ]);

                }
            }

            // Clear the session-based cart
            PackageCart::destroy();
        } catch (\Exception $e) {
            Log::error('TransferCartToDatabase: Error occurred: ' . $e->getMessage());
        }
    }

    public function __construct()
    {
        //
    }
}