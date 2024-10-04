<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Notifications\VendorApproveNotification;
use Illuminate\Support\Facades\Notification;

class AdminController extends Controller
{
    public function AdminDashboard(){
        return view('admin.index');
    }

    public function AdminLogin(){
        return view('admin.admin_login');
    }

    public function AdminDestroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }

    public function AdminProfile(){
        $id = Auth::user()->id;
        $adminData = User::find($id);
        return view('admin.admin_profile_view' , compact('adminData'));
    }

    public function AdminProfileStore(Request $request){

       $id = Auth::user()->id;
       $data = User::find($id);
       $data->name = $request->name;
       $data->email = $request->email;
       $data->phone = $request->phone;
       $data->address = $request->address;


       if ($request->file('photo')) {
           $file = $request->file('photo');
           @unlink(public_path('upload/admin_images/'.$data->photo));
           $filename = date('YmdHi').$file->getClientOriginalName();
           $file->move(public_path('upload/admin_images'),$filename);
           $data['photo'] = $filename;
       }

       $data->save();

       $notification = array(
       'message' => 'Admin Profil Updated Successfully',
        'alert-type' => 'success'
       );

       return redirect()->back()->with($notification);
    }

    public function AdminChangePassword(){
        return view('admin.admin_change_password');
    }

    public function AdminUpdatePassword(Request $request){
         // Validation
         $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);

        // Match The Old Password
        if (!Hash::check($request->old_password, auth::user()->password)) {
            return back()->with("error", "Old Password Doesn't Match!!");
        }

        // Update The new password
        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)

        ]);
        return back()->with("status", " Password Changed Successfully");

    }

    public function InactiveVendor(){
        $inActiveVendor = User::where('status' , 'inactive')->where('role' , 'vendor')->latest()->get();
        return view('backend.vendor.inactive_vendor' , compact('inActiveVendor'));
    }

    public function ActiveVendor(){
        $ActiveVendor = User::where('status' , 'active')->where('role' , 'vendor')->latest()->get();
        return view('backend.vendor.active_vendor' , compact('ActiveVendor'));
    }

    public function InactiveVendorDetails($id){
        $inactiveVendorDetails = User::findorFail($id);
        return view('backend.vendor.inactive_vendor_details',compact('inactiveVendorDetails'));
    }

    public function ActiveVendorApprove(Request $request){
     $vendor_id = $request->id;
     $user = User::findOrFail($vendor_id)->update([
         'status' => 'active',
     ]);

     $notification = array(
        'message' => 'Vendor Active Successfully',
         'alert-type' => 'success'
        );

        $vuser = User::where('role' , 'vendor')->where('id' , $vendor_id)->get();
        Notification::send($vuser , new VendorApproveNotification($request));
        return redirect()->route('active.vendor')->with($notification);
    }

    public function ActiveVendorDetails($id){
        $activeVendorDetails = User::findorFail($id);
        return view('backend.vendor.active_vendor_details',compact('activeVendorDetails'));
    }

    public function InactiveVendorApprove(Request $request){
        $vendor_id = $request->id;
        $user = User::findOrFail($vendor_id)->update([
            'status' => 'inactive',
        ]);

        $notification = array(
           'message' => 'Vendor Inactive Successfully',
            'alert-type' => 'success'
           );

           return redirect()->route('inactive.vendor')->with($notification);
       }

}
