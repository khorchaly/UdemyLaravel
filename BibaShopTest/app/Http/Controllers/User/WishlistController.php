<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function AddToWishList(Request $request, $product_id){

        if(Auth::check()){
            $exists = Wishlist::where('user_id',Auth::id())->where('product_id',$product_id )->first();

               if(!$exists){
               try {
                   // $data = $request->validated();
                   // $this->wishlistRepository->create($data);
                   Wishlist::insert([
                       'user_id' => Auth::id(),
                       'product_id' => $product_id,

                      ]);
                   return response()->json(['success' => 'Successfully Added On Your Wishlist']);
               }
               catch (\Throwable $th) {
                   return response()->with('error',$th);
               }
               }

               else{
               return response()->json(['error' => 'This Product Has Already On Your Wishlist']);
               }

             }
           else{
               return response()->json(['error' => 'At First Login Your Account']);
           }


    } // End Method


    public function AllWishlist(){

        return view('frontend.wishlist.view_wishlist');
    }// End Method

    public function GetWishlistProduct(){

        $wishlist = Wishlist::with('product')->where('user_id' , Auth::id())->latest()->get();

        $wishcount = Wishlist::count();

        return response()->json(['wishlist' => $wishlist , 'wishcount' => $wishcount ]);
    }// End Method
}
