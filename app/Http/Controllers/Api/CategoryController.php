<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Category;
use App\Models\Subcategory;

class CategoryController extends Controller
{
   
	public function __construct(){
        $this->middleware('auth:api');
    }
	
    public function index()
    {
        $categories = Category::all();
        return response()->json([
            'status' => 'success',
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
		
        $validator = Validator::make($request->all(), [
			'category' => ['required', 'string'],
		]);
		
		if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }		
		
		$status_id = 1;
		if(isset($request->status_id) && !empty($request->status_id)){
			$status_id = $request->status_id;
		}
        $category = Category::create([
            'category' => $request->category,
            'status_id' => $status_id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Category created successfully',
            'category' => $category,
        ]);
    }

    
	public function show($id)
    {
        $category = Category::find($id);
        return response()->json([
            'status' => 'success',
            'category' => $category,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
			'category' => ['required', 'string'],
		]);
		
		if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $category = Category::find($id);
        $category->category = $request->category;
        $category->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Category updated successfully',
            'category' => $category,
        ]);
    }
	
	public function categoryStt(Request $request){
		$validator = Validator::make($request->all(), [
			'stt' => ['required'],
			'id' => ['required'],
		]);
		
		if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
		
		$category = Category::find($request->id);
        $category->status_id = $request->stt;
        $category->save();
		
		$subcategories = Subcategory::where("category_id","=", $request->id)
		->update([
			"status_id" => $request->stt
		]);

        return response()->json([
            'status' => 'success',
            'message' => 'Category updated successfully',
            'user' => $category,
        ]);
	}

    public function destroy($id)
    {	
		//Se elimina la categoria
        $category = Category::find($id);
        $category->delete();
		//se elimina la subcategoria asocia a la categoria eliminada
		$delete = Subcategory::where("category_id", $id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Category deleted successfully',
            'category' => $category,
        ]);
    }
	
}
