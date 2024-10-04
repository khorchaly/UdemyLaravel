<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use PhpParser\Node\Stmt\TryCatch;

class BrandController extends Controller
{
    public function AllBrand(){
       $brands = Brand::latest()->get();
       return view('backend.brand.brand_all' , compact('brands'));
    }

    public function AddBrand(){
        return view('backend.brand.brand_add');
    }

    public function StoreBrand(Request $request){

    $image_file = $request->file('brand_image');
      $name_gen = hexdec(uniqid()).'.'.$image_file->getClientOriginalExtension();


      // create image manager with desired driver
      $manager = new ImageManager(new Driver());

      // read image from file system
      $image = @($manager->read($image_file));

      // resize image proportionally to 300px width
    //  $image->scale(width: 300 , height: 300);
    $image->scale(width: 300);

     // save modified image in new format
    $image->toPng()->save('upload/brand/'.$name_gen);

    $save_url = 'upload/brand/'.$name_gen;

    Brand::insert([
       'brand_name' => $request->brand_name,
       'brand_slug' => strtolower(str_replace(' ' , '-', $request->brand_name)),
       'brand_image' => $save_url,
    ]);

    $notification = array(
        'message' => 'Brand Inserted Successfully',
         'alert-type' => 'success'
    );

    return redirect()->route('all.brand')->with($notification);

    }

    public function EditBrand($id){
        $brand = Brand::findOrFail($id);
        return view('backend.brand.brand_edit' , compact('brand'));
    }

    public function UpdateBrand(Request $request){

        $brand_id = $request->id;
        $old_img = $request->old_image;

        if($request->file('brand_image')){

            $image_file = $request->file('brand_image');
            $name_gen = hexdec(uniqid()).'.'.$image_file->getClientOriginalExtension();

            // create image manager with desired driver
            $manager = new ImageManager(new Driver());

            // read image from file system
            $image = $manager->read($image_file);

            // resize image proportionally to 300px width
          //  $image->scale(width: 300 , height: 300);
           $image->resize(300, 300);

           // save modified image in new format
          $image->toPng()->save('upload/brand/'.$name_gen);

          $save_url = 'upload/brand/'.$name_gen;

          if(file_exists($old_img)){
            unlink($old_img);
          }

          Brand::findOrFail($brand_id)->update([
             'brand_name' => $request->brand_name,
             'brand_slug' => strtolower(str_replace(' ' , '-', $request->brand_name)),
             'brand_image' => $save_url,
          ]);

          $notification = array(
              'message' => 'Brand Updated Successfully',
               'alert-type' => 'success'
          );

          return redirect()->route('all.brand')->with($notification);
        }
        else{
            Brand::findOrFail($brand_id)->update([
                'brand_name' => $request->brand_name,
                'brand_slug' => strtolower(str_replace(' ' , '-', $request->brand_name)),
             ]);

             $notification = array(
                 'message' => 'Brand Updated Successfully',
                  'alert-type' => 'success'
             );

             return redirect()->route('all.brand')->with($notification);
        }

    }

    public function DeleteBrand($id){
        $brand = Brand::findOrFail($id);
        $img = $brand->brand_image;
        unlink($img);

        Brand::findOrFail($id)->delete();
        $notification = array(
            'message' => 'Brand Deleted Successfully',
             'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);

    }
}
