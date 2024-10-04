<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\ShipDistricts;
use App\Models\ShipState;
use Darryldecode\Cart\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;


class CheckoutController extends Controller
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

    public function DistrictGetAjax($division_id){

        $ship = ShipDistricts::where('division_id',$division_id)->orderBy('district_name','ASC')->get();
        return json_encode($ship);
    }

    public function StateGetAjax($district_id){

        $ship = ShipState::where('district_id',$district_id)->orderBy('state_name','ASC')->get();
        return json_encode($ship);

    }// End Method

    public function CheckoutStore(Request $request){

        $cart = $this->getCartInstance();
        $cartContent = $cart->getContent();

        $totalPrice = 0;
        foreach ($cartContent as $item) {
        $totalPrice += $item->price * $item->quantity ;
        }
        $data = array();
        $data['shipping_name'] = $request->shipping_name;
        $data['shipping_email'] = $request->shipping_email;
        $data['shipping_phone'] = $request->shipping_phone;
        $data['post_code'] = $request->post_code;

        $data['division_id'] = $request->division_id;
        $data['district_id'] = $request->district_id;
        $data['state_id'] = $request->state_id;
        $data['shipping_address'] = $request->shipping_address;
        $data['notes'] = $request->notes;
        $cartTotal = $totalPrice;

        if ($request->payment_option == 'stripe') {
           return view('frontend.payment.stripe',compact('data','cartTotal'));
        }elseif ($request->payment_option == 'card'){
            return 'Card Page';
        }else{
            return view('frontend.payment.cash',compact('data','cartTotal'));
        }


    }// End Method

}
