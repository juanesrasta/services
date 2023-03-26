<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Subcategory;

class SubcategoryController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api');
    }
	
    public function index()
    {
        $subcategory = Subcategory::select(
			"subcategories.id",
			"subcategories.subcategory",
			"subcategories.status_id",
			"subcategories.created_at",
			"categories.category"
		)->join(
			"categories", "categories.id", "=", "subcategories.category_id"
		)->get()->toArray();
        return response()->json([
            'status' => 'success',
            'subcategory' => $subcategory,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
			'category' => ['required'],
			'subcategory' => ['required'],
		]);
		
		if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }		

        $subcategory = Subcategory::create([
            'subcategory' => $request->subcategory,
            'category_id' => $request->category,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Subcategory created successfully',
            'subcategory' => $subcategory,
        ]);
    }

    public function show(string $id)
    {
        $subcategory = Subcategory::select(
			"subcategories.id",
			"subcategories.subcategory",
			"categories.id as cid",
			"categories.category"
		)->join("categories", 
			"categories.id", "=", "subcategories.category_id"
		)->where("subcategories.id", $id)
		->get()->toArray();
        return response()->json([
            'status' => 'success',
            'subcategory' => $subcategory,
        ]);
    }

    public function update(Request $request, string $id)
    {
         $validator = Validator::make($request->all(), [
			'subcategory' => ['required', 'string'],
			'category' => ['required'],
		]);
		
		if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $subcategory = Subcategory::find($id);
		
        $subcategory->subcategory = $request->subcategory;
        $subcategory->category_id = $request->category;
        $subcategory->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Subcategory updated successfully',
            'subcategory' => $subcategory,
        ]);
    }

    public function destroy(string $id)
    {
        $subcategory = Subcategory::find($id);
        $subcategory->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Subcategory deleted successfully',
            'subcategory' => $subcategory,
        ]);
    }
	
	public function subcategorystt(Request $request){
		$validator = Validator::make($request->all(), [
			'stt' => ['required'],
			'id' => ['required'],
		]);
		
		if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
		
		$subcategory = Subcategory::find($request->id);
        $subcategory->status_id = $request->stt;
        $subcategory->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Subcategory updated successfully',
            'user' => $subcategory,
        ]);
		
	}
	////////////////////////////////////////////
	public function subCatOfCat(Request $request){
		$validator = Validator::make($request->all(), [
			'category_id' => ['required'],
		]);
		
		if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
		
		$subcategories = Subcategory::select("subcategories.id","subcategories.subcategory")
		->where('category_id', $request->category_id)
		->get()
        ->toArray();
		
		return response()->json([
            'status' => 'success',
            'subcategories' => $subcategories,
        ]);
	}
}
