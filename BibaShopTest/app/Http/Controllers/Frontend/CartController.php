<?php

namespace App\Http\Controllers\Frontend;

use Carbon\Carbon;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ShipDivision;
use Darryldecode\Cart\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    // Function to create a Cart instance with dependencies
    public function getCartInstance()
    {
        // Resolve dependencies from the service container
        $session = Session::getFacadeRoot();
        $events = Event::getFacadeRoot();
        $config = Config::get('cart');

        // Define the instance name and session key
        $instanceName = 'default';
        $session_key = 'cart_session';

        // Instantiate the Cart class
        return new Cart($session, $events, $instanceName, $session_key, $config);
    }


    public function AddToCart(Request $request , $id){

        if(Session::has('coupon')){
            Session::forget('coupon');
        }

        $product = Product::findOrFail($id);
        // Create an instance of the Cart class

        $cart = $this->getCartInstance();

        if ($product->discount_price == NULL) {

        // Add the product to the cart
        $cart->add([
            'id' => $id,
            'name' => $request->product_name,
            'quantity' => $request->quantity,
            'price' => $product->selling_price,
            'weight' => 1,
            'attributes' => array(
                'image' => $product->product_thambnail,
                'color' => $request->color,
                'size' => $request->size,
                'vendor_id' => $request->vendor_id,
            ),
        ]);
        return response()->json(['success' => 'Successfully Added on Your Cart' ]);
        }
        else{
            $cart->add([
                'id' => $id,
                'name' => $request->product_name,
                'quantity' => $request->quantity,
                'price' => $product->discount_price,
                'weight' => 1,
                'attributes' => array(
                    'image' => $product->product_thambnail,
                    'color' => $request->color,
                    'size' => $request->size,
                    'vendor_id' => $request->vendor_id,
                ),
            ]);
        return response()->json(['success' => 'Successfully Added on Your Cart' ]);
        }
    }

    public function AddMiniCart(){
          // Create an instance of the CustomCart class
          $cart = $this->getCartInstance();

          // Retrieve cart content
          $cartContent = $cart->getContent();

          $cartQty = $cart->getTotalQuantity();

            // Calculate total price
         $totalPrice = 0;
         foreach ($cartContent as $item) {
         $totalPrice += $item->price * $item->quantity;
         }


          return response()->json([
            'cartContent' => $cartContent,
            'cartQty' => $cartQty,
            'totalPrice' => $totalPrice
        ]);
    }

    public function RemoveMiniCart($id){
        $cart = $this->getCartInstance();
        $cart->remove($id);

        return response()->json(['success' => 'Product removed from the cart']);
    }

    public function MyCart(){

        return view('frontend.mycart.view_mycart');

    }// End Method

    public function AddToCartDetails(Request $request , $id){

        if(Session::has('coupon')){
            Session::forget('coupon');
        }

        $product = Product::findOrFail($id);
        // Create an instance of the Cart class

        $cart = $this->getCartInstance();

        if ($product->discount_price == NULL) {

        // Add the product to the cart
        $cart->add([
            'id' => $id,
            'name' => $request->product_name,
            'quantity' => $request->quantity,
            'price' => $product->selling_price,
            'weight' => 1,
            'attributes' => array(
                'image' => $product->product_thambnail,
                'color' => $request->color,
                'size' => $request->size,
                'vendor' => $request->vendor,
            ),
        ]);
        return response()->json(['success' => 'Successfully Added on Your Cart' ]);
        }
        else{
            $cart->add([
                'id' => $id,
                'name' => $request->product_name,
                'quantity' => $request->quantity,
                'price' => $product->discount_price,
                'weight' => 1,
                'attributes' => array(
                    'image' => $product->product_thambnail,
                    'color' => $request->color,
                    'size' => $request->size,
                    'vendor' => $request->vendor,
                ),
            ]);
        return response()->json(['success' => 'Successfully Added on Your Cart' ]);
        }
    }

    public function GetCartProduct(){
           // Create an instance of the CustomCart class
           $cart = $this->getCartInstance();

           // Retrieve cart content
           $cartContent = $cart->getContent();

           $cartQty = $cart->getTotalQuantity();

             // Calculate total price
          $totalPrice = 0;
          foreach ($cartContent as $item) {
          $totalPrice += $item->price;
          }


           return response()->json([
             'cartContent' => $cartContent,
             'cartQty' => $cartQty,
             'totalPrice' => $totalPrice,
         ]);

    }

    public function CartRemove($id){
        $cart = $this->getCartInstance();

        $cart->remove($id);

        if(Session::has('coupon')){
            $cart = $this->getCartInstance();
            $cartContent = $cart->getContent();

            $totalPrice = 0;
            foreach ($cartContent as $item) {
            $totalPrice += $item->price * $item->quantity ;
            }

            $coupon_name = Session::get('coupon')['coupon_name'];
            $coupon = Coupon::where('coupon_name',$coupon_name)->first();

           Session::put('coupon',[
                'coupon_name' => $coupon->coupon_name,
                'coupon_discount' => $coupon->coupon_discount,
                'discount_amount' => round($totalPrice * $coupon->coupon_discount/100),
                'total_amount' => round( $totalPrice - $totalPrice * $coupon->coupon_discount/100)
            ]);
        }

        return response()->json(['success' => 'Successfully Remove From Cart']);
    }

    public function cartDecrement($id)
    {
    $cart = $this->getCartInstance();
    $item = $cart->get($id);

    // Ensure the item exists before attempting to decrement
    if ($item) {
        $newQty = $item->quantity - 1;

        if ($newQty > 0) {
            $cart->update($id, [
                'quantity' => [
                    'relative' => false,
                    'value' => $newQty,
                ],
            ]);
            if(Session::has('coupon')){


                $cart = $this->getCartInstance();
                $cartContent = $cart->getContent();

                $totalPrice = 0;
                foreach ($cartContent as $item) {
                $totalPrice += $item->price * $item->quantity ;
                }

                $coupon_name = Session::get('coupon')['coupon_name'];
                $coupon = Coupon::where('coupon_name',$coupon_name)->first();

               Session::put('coupon',[
                    'coupon_name' => $coupon->coupon_name,
                    'coupon_discount' => $coupon->coupon_discount,
                    'discount_amount' => round($totalPrice * $coupon->coupon_discount/100),
                    'total_amount' => round( $totalPrice - $totalPrice * $coupon->coupon_discount/100 )
                ]);

            }
        }
        else{
            $cart->remove($id);
        }

        return response()->json('Decrement');
    } else {
        return response()->json(['error' => 'Item not found in cart'], 404);
    }

}

public function cartIncrement($id)
{
    $cart = $this->getCartInstance();
    $item = $cart->get($id);

    // Ensure the item exists before attempting to increment
    if ($item) {
        $newQty = $item->quantity + 1;

        // Update the quantity
        $cart->update($id, [
            'quantity' => [
                'relative' => false,
                'value' => $newQty,
            ],
        ]);
        if(Session::has('coupon')){


            $cart = $this->getCartInstance();
            $cartContent = $cart->getContent();

            $totalPrice = 0;
            foreach ($cartContent as $item) {
            $totalPrice += $item->price * $item->quantity ;
            }

            $coupon_name = Session::get('coupon')['coupon_name'];
            $coupon = Coupon::where('coupon_name',$coupon_name)->first();

           Session::put('coupon',[
                'coupon_name' => $coupon->coupon_name,
                'coupon_discount' => $coupon->coupon_discount,
                'discount_amount' => round($totalPrice * $coupon->coupon_discount/100),
                'total_amount' => round( $totalPrice - $totalPrice * $coupon->coupon_discount/100 )
            ]);

        }

        return response()->json('Increment');
    } else {
        return response()->json(['error' => 'Item not found in cart'], 404);
    }
}


public function CouponApply(Request $request){

    $coupon = Coupon::where('coupon_name',$request->coupon_name)->where('coupon_validity','>=',Carbon::now()->format('Y-m-d'))->first();

    $cart = $this->getCartInstance();
    $cartContent = $cart->getContent();

    $totalPrice = 0;
    foreach ($cartContent as $item) {
    $totalPrice += $item->price * $item->quantity ;
    }


    if ($coupon) {
        Session::put('coupon',[
            'coupon_name' => $coupon->coupon_name,
            'coupon_discount' => $coupon->coupon_discount,
            'discount_amount' => round( $totalPrice * $coupon->coupon_discount/100),
            'total_amount' => round( $totalPrice - $totalPrice * $coupon->coupon_discount/100 )
        ]);

        return response()->json(array(
            'validity' => true,
            'success' => 'Coupon Applied Successfully'

        ));


    } else{
        return response()->json(['error' => 'Invalid Coupon']);
    }

}// End Method

public function CouponCalculation(){

    $cart = $this->getCartInstance();
    $cartContent = $cart->getContent();

    // Calculate total price
    $totalPrice = 0;
    foreach ($cartContent as $item) {
       $totalPrice += $item->price * $item->quantity;
    }


    if (Session::has('coupon')) {

        return response()->json(array(
         'subtotal' =>  $totalPrice,
         'coupon_name' => session()->get('coupon')['coupon_name'],
         'coupon_discount' => session()->get('coupon')['coupon_discount'],
         'discount_amount' => session()->get('coupon')['discount_amount'],
         'total_amount' => session()->get('coupon')['total_amount'],
        ));
    }else{
        return response()->json(array(
            'total' =>  $totalPrice,
        ));
    }
}

// End Method

public function CouponRemove(){

    Session::forget('coupon');
    return response()->json(['success' => 'Coupon Remove Successfully']);

}// End Method

public function CheckoutCreate(){

    if (Auth::check()) {
        // Create an instance of the CustomCart class
        $cart = $this->getCartInstance();
       // Retrieve cart content
       $cartContent = $cart->getContent();
       $cartQty = $cart->getTotalQuantity();
        // Calculate total price
       $totalPrice = 0;
       foreach ($cartContent as $item) {
       $totalPrice += $item->price * $item->quantity;
       }
    //    dd($cartContent);

        if ( $totalPrice > 0) {
           $divisions = ShipDivision::orderBy('division_name','ASC')->get();
           return view('frontend.checkout.checkout_view',compact('cartContent','cartQty','totalPrice','divisions'));

        }
        else{

        $notification = array(
        'message' => 'Shopping At list One Product',
        'alert-type' => 'error'
        );
        return redirect()->to('/')->with($notification);
        }

    }
    else{

         $notification = array(
        'message' => 'You Need to Login First',
        'alert-type' => 'error'
    );

    return redirect()->route('login')->with($notification);
    }


}// End Method






}
