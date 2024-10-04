<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Facades\Image;



class CategoryControler extends Controller
{
    public function AllCategory(){
        $categories = Category::latest()->get();
        return view('backend.category.category_all' , compact('categories'));
    }

    public function AddCategory(){
        return view('backend.category.category_add');
    }



public function StoreCategory(Request $request){

    $image_file = $request->file('category_image');
    $name_gen = hexdec(uniqid()).'.'.$image_file->getClientOriginalExtension();


      // create image manager with desired driver
      $manager = new ImageManager(new Driver());

      // read image from file system
      $image = @($manager->read($image_file));

      // resize image proportionally to 300px width
    //  $image->scale(width: 300 , height: 300);
    $image->resize(120, 120);

     // save modified image in new format
    $image->toPng()->save('upload/category/'.$name_gen);

    $save_url = 'upload/category/'.$name_gen;

    Category::insert([
       'category_name' => $request->category_name,
       'category_slug' => strtolower(str_replace(' ' , '-', $request->category_name)),
       'category_image' => $save_url,
    ]);

    $notification = array(
        'message' => 'Category Inserted Successfully',
         'alert-type' => 'success'
    );

    return redirect()->route('all.category')->with($notification);

    }

    public function EditCategory($id){
        $category = Category::findOrFail($id);
        return view('backend.category.category_edit' , compact('category'));
    }


    public function UpdateCategory(Request $request){

        $cat_id = $request->id;
        $old_img = $request->old_image;

        if($request->file('category_image')){

            $image_file = $request->file('category_image');
            $name_gen = hexdec(uniqid()).'.'.$image_file->getClientOriginalExtension();

            // create image manager with desired driver
            $manager = new ImageManager(new Driver());

            // read image from file system
            $image = $manager->read($image_file);

            // resize image proportionally to 300px width
          //  $image->scale(width: 300 , height: 300);
           $image->resize(120, 120);

           // save modified image in new format
          $image->toPng()->save('upload/category/'.$name_gen);

          $save_url = 'upload/category/'.$name_gen;

          if(file_exists($old_img)){
            unlink($old_img);
          }

          Category::findOrFail($cat_id)->update([
             'category_name' => $request->category_name,
             'category_slug' => strtolower(str_replace(' ' , '-', $request->category_name)),
             'category_image' => $save_url,
          ]);

          $notification = array(
              'message' => 'Category Updated Successfully',
               'alert-type' => 'success'
          );

          return redirect()->route('all.category')->with($notification);
        }
        else{
            Category::findOrFail($cat_id)->update([
                'category_name' => $request->category_name,
                'category_slug' => strtolower(str_replace(' ' , '-', $request->category_name)),
             ]);

             $notification = array(
                 'message' => 'Category Updated Successfully',
                  'alert-type' => 'success'
             );

             return redirect()->route('all.category')->with($notification);
        }

    }

    public function DeleteCategory($id){
        $category = Category::findOrFail($id);
        $img = $category->category_image;
        unlink($img);

        Category::findOrFail($id)->delete();
        $notification = array(
            'message' => 'Category Deleted Successfully',
             'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

}
