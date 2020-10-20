<?php

namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Http\Request;

class Categories extends Controller
{
    public function index()
    {
        return view('categories');
    } 

    public function getsubcategories($id, $level)
    {
        $category = Category::Find($id);
        $subcategories = Category::where("parent_id",$id)->get();
        $subcategoryselectbox = 
        '<div class="col-12 col-md-3 level-'.$level.'">
            <div class="categories">
                <select class="form-control subcategory" data-id="'.$id.'" data-level="'.$level.'" data-name="'. ($category != null ? $category->name : __('Root')) .'" >
                    <option value="">' . ($level == 0 ? __('-- Root --') : __('-- Subcategory '.$level.' --')) . '</option>';
                    foreach($subcategories as $subcategory) {
                        $subcategoryselectbox .= '<option value="'.$subcategory->id.'" >'.$subcategory->name.'</option>'; 
                    }

        $subcategoryselectbox .=                    
                '</select>
            </div>
        </div>
        <div class="col-12 col-md-9 level-'.$level.'"></div>';

        return $subcategoryselectbox;
    }

    public function addsubcategory(Request $request)
    {
        $newcategory = New Category();
        $newcategory->name = $request->name;
        $newcategory->parent_Id = $request->id;
        $newcategory->level = $request->id == 0 ? 0 : $request->level;
        $newcategory->save();
    }

    public function removesubcategory(Request $request)
    {
        
        $category = Category::Find($request->id);
        if($category != null)
        {
            $current_ids = $category->id;
            $all_ids = $category->id;
            while($current_ids != null)
            {
                $current_ids = Category::whereIn('parent_id', $current_ids == null ? [] : explode(',', $current_ids))->pluck('id')->implode(',');
                $all_ids .= ','.$current_ids;
            }
            
            $all_ids = trim($all_ids,',');
            Category::whereIn('id',$all_ids == null ? [] : explode(',', $all_ids))->delete();

            return  $category->parent_id;
        }
        
        else
        {
            return 'false';
        }
    }
}
