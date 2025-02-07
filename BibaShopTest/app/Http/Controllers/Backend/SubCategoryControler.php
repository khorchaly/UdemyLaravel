<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Facades\Image;

class SubCategoryControler extends Controller
{
    public function AllSubCategory(){
        $subcategories = SubCategory::latest()->get();
        return view('backend.subcategory.subcategory_all' , compact('subcategories'));
    }

    public function AddSubCategory(){
        $categories = Category::orderBy('category_name' , 'ASC')->get();
        return view('backend.subcategory.subcategory_add' , compact('categories'));
    }

    public function StoreSubCategory(Request $request){


        SubCategory::insert([
           'category_id' => $request->category_id,
           'subcategory_name' => $request->subcategory_name,
           'subcategory_slug' => strtolower(str_replace(' ' , '-', $request->subcategory_name)),
        ]);

        $notification = array(
            'message' => 'SubCategory Inserted Successfully',
             'alert-type' => 'success'
        );

        return redirect()->route('all.subcategory')->with($notification);

    }

    public function EditSubCategory($id){
        $categories = Category::orderBy('category_name' , 'ASC')->get();
        $subcategory = SubCategory::findOrFail($id);
        return view('backend.subcategory.subcategory_edit' , compact('categories' , 'subcategory'));
    }

    public function UpdateSubCategory(Request $request){

        $subcat_id = $request->id;

        SubCategory::findOrFail($subcat_id)->update([
           'category_id' => $request->category_id,
           'subcategory_name' => $request->subcategory_name,
           'subcategory_slug' => strtolower(str_replace(' ' , '-', $request->subcategory_name)),
        ]);

        $notification = array(
            'message' => 'SubCategory Updated Successfully',
             'alert-type' => 'success'
        );

        return redirect()->route('all.subcategory')->with($notification);

    }

    public function DeleteSubCategory($id){

        SubCategory::findOrFail($id)->delete();

        $notification = array(
            'message' => 'SubCategory Deleted Successfully',
             'alert-type' => 'success'
        );

        return redirect()->route('all.subcategory')->with($notification);

    }

    public function GetSubCategory($category_id){

      $subCat = SubCategory::where('category_id' , $category_id)->orderBy('subcategory_name' , 'ASC')->get();
      return json_encode($subCat);
    }

}
