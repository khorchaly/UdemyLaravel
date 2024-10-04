<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderItem;
use Darryldecode\Cart\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;


class StripeController extends Controller
{

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


 public function StripeOrder(Request $request){

    $cart = $this->getCartInstance();
    // Retrieve cart content
    $cartContent = $cart->getContent();
    $cartQty = $cart->getTotalQuantity();
     // Calculate total price
    $totalPrice = 0;
    foreach ($cartContent as $item) {
    $totalPrice += $item->price * $item->quantity;
    }


    if(Session::has('coupon')){
        $total_amount = Session::get('coupon')['total_amount'];
    }else{

        $total_amount = round($totalPrice);
    }

 \Stripe\Stripe::setApiKey('sk_test_51Q2s4hGIhv0ioVsc0vFzxu1euAr3iDkKcJqIMVjU5kmq4oTmyn5HByInmwxhMdYyCJYrgnHFGZIK5aViOejBnFmB00haMwfefc');


  $token = $_POST['stripeToken'];

  $charge = \Stripe\Charge::create([
  'amount' => $total_amount*100,
  'currency' => 'usd',
  'description' => 'Shop shop',
  'source' => $token,
  'metadata' => ['order_id' => uniqid()],
  ]);

  $order_id = Order::insertGetId([
    'user_id' => Auth::id(),
    'division_id' => $request->division_id,
    'district_id' => $request->district_id,
    'state_id' => $request->state_id,
    'name' => $request->name,
    'email' => $request->email,
    'phone' => $request->phone,
    'address' => $request->address,
    'post_code' => $request->post_code,
    'notes' => $request->notes,

    'payment_type' => $charge->payment_method,
    'payment_method' => 'Stripe',
    'transaction_id' => $charge->balance_transaction,
    'currency' => $charge->currency,
    'amount' => $total_amount,
    'order_number' => $charge->metadata->order_id,

    'invoice_no' => 'EOS'.mt_rand(10000000,99999999),
    'order_date' => Carbon::now()->format('d F Y'),
    'order_month' => Carbon::now()->format('F'),
    'order_year' => Carbon::now()->format('Y'),
    'status' => 'pending',
    'created_at' => Carbon::now(),

  ]);


  foreach($cart as $crt){
    OrderItem::insert([
        'order_id' => $order_id,
        'product_id' => $crt->id,
        'vendor_id' => $crt->attributes->vendor,
        'color' => $crt->attributes->color,
        'size' => $crt->attributes->size,
        'vendor_id' => $crt->price,
        'created_at'=> Carbon::now(),
    ]);

  }


   if(Session::has('coupon')){

     Session::forget('coupon');

   }
   $notification = array(
    'message' => 'Your Order Place Successfully',
    'alert-type' => 'success'
);

return redirect()->route('dashboard')->with($notification);


}



}
