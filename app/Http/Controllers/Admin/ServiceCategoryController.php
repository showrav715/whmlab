<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;

class ServiceCategoryController extends Controller{

    public function all(){
        $pageTitle = 'Service Categories';
        $categories = ServiceCategory::paginate(getPaginate());
        return view('admin.service_category.all',compact('pageTitle', 'categories'));
    }
    
    public function add(Request $request){
		
		if(!preg_match("/^[0-9a-zA-Z-]+$/", $request->slug)){
			$notify[] = ['error', 'Please provide a valid slug'];
			return back()->withNotify($notify);
		}

    	$request->validate([
    		'name' => 'required|max:255',
    		'slug' => 'required|unique:service_categories|max:255',
    		'short_description' => 'required|max:65000',
    	]);
	
    	$category = new ServiceCategory; 
    	$category->name = $request->name;
    	$category->slug = $request->slug;
    	$category->short_description = $request->short_description;
    	$category->save();

    	$notify[] = ['success', 'Service category added successfully'];
	    return back()->withNotify($notify);
    }

	public function update(Request $request){
	
		if(!preg_match("/^[0-9a-zA-Z-]+$/", $request->slug)){
			$notify[] = ['error', 'Please provide a valid slug'];
			return back()->withNotify($notify);
		}

    	$request->validate([
    		'name' => 'required|max:255',
    		'slug' => 'required|max:255|unique:service_categories,slug,'.$request->id,
    		'short_description' => 'required|max:65000',
    	]);

    	$category = ServiceCategory::findOrFail($request->id); 
    	$category->name = $request->name;
    	$category->slug = $request->slug;
    	$category->short_description = $request->short_description;
    	$category->save();

    	$notify[] = ['success', 'Service category updated successfully'];
	    return back()->withNotify($notify);
    }

	public function status($id){ 
		return ServiceCategory::changeStatus($id);
	}

}
