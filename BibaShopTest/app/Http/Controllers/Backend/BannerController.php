<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Facades\Image;

class BannerController extends Controller
{
    public function AllBanner(){
        $banners = Banner::latest()->get();
        return view('backend.banner.banner_all' , compact('banners'));
    }

    public function AddBanner(){
        return view('backend.banner.banner_add');
    }

    public function StoreBanner(Request $request){

        $image_file = $request->file('banner_image');
        $name_gen = hexdec(uniqid()).'.'.$image_file->getClientOriginalExtension();


          // create image manager with desired driver
          $manager = new ImageManager(new Driver());

          // read image from file system
          $image = @($manager->read($image_file));

          // resize image proportionally to 300px width
        //  $image->scale(width: 300 , height: 300);
        $image->resize(768, 450);

         // save modified image in new format
        $image->toPng()->save('upload/banner/'.$name_gen);

        $save_url = 'upload/banner/'.$name_gen;

        Banner::insert([
           'banner_title' =>  $request->banner_title,
           'banner_url' => $request->banner_url,
           'banner_image' => $save_url,
        ]);

        $notification = array(
            'message' => 'Banner Inserted Successfully',
             'alert-type' => 'info'
        );

        return redirect()->route('all.banner')->with($notification);

        }

        public function EditBanner($id){
            $banners = Banner::findOrFail($id);
            return view('backend.banner.banner_edit' , compact('banners'));
        }


        public function UpdateBanner(Request $request){

            $banner_id = $request->id;
            $old_img = $request->old_image;

            if($request->file('banner_image')){

                $image_file = $request->file('banner_image');
                $name_gen = hexdec(uniqid()).'.'.$image_file->getClientOriginalExtension();

                // create image manager with desired driver
                $manager = new ImageManager(new Driver());

                // read image from file system
                $image = $manager->read($image_file);

                // resize image proportionally to 300px width
              //  $image->scale(width: 300 , height: 300);
               $image->resize(768,450);

               // save modified image in new format
              $image->toPng()->save('upload/banner/'.$name_gen);

              $save_url = 'upload/banner/'.$name_gen;

              if(file_exists($old_img)){
                unlink($old_img);
              }

              Banner::findOrFail($banner_id)->update([
                'banner_title' => $request->banner_title,
                'banner_url' =>  $request->banner_url,
                'banner_image' => $save_url,
              ]);

              $notification = array(
                  'message' => 'Banner Updated With Image Successfully',
                   'alert-type' => 'success'
              );

              return redirect()->route('all.banner')->with($notification);
            }
            else{
                Banner::findOrFail($banner_id)->update([
                    'banner_title' => $request->banner_title,
                    'banner_url' =>  $request->banner_url   ,
                  ]);


                 $notification = array(
                     'message' => 'Banner Updated Without Image Successfully',
                      'alert-type' => 'success'
                 );

                 return redirect()->route('all.banner')->with($notification);
            }
        }

        public function DeleteBanner($id){
            $banner = Banner::findOrFail($id);
            $img = $banner->banner_image;
            unlink($img);

            Banner::findOrFail($id)->delete();
            $notification = array(
                'message' => 'Banner Deleted Successfully',
                 'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);
        }


}
