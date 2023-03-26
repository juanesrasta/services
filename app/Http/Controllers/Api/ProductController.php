<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\Subcategory;

use App\Models\Product;
use App\Models\Productsubcategory;

class ProductController extends Controller
{
    
	public function __construct(){
        $this->middleware('auth:api');
    }
	
    public function index()
    {
        $products = Product::all();
        $datos = array();
		for($p=0; $p < count($products); $p++){
			$id = $products[$p]["id"];
			$product = $products[$p]["product"];
			$created_at = $products[$p]["created_at"];
			$categories = $this->getSubCategoryProduct($products[$p]["id"], 0)['categories'];
			$subcategories = $this->getSubCategoryProduct($products[$p]["id"], 0)['subcategories'];
			$datos[$p] = array("id"=>$id, "product"=>$product, "categories"=>$categories, "subcategories"=>$subcategories, "created_at" => $created_at);
		}

		return response()->json([
            'status' => 'success',
            'product' => $datos,
        ]);
		
    }
	
	public function getSubCategoryProduct($id_prod, $flag){
		$subcategories = Subcategory::select(
			"subcategories.id",
			"subcategories.subcategory",
			"categories.category"
		)
		->join("productsubcategories",
			"subcategories.id", "=", "productsubcategories.subcategory_id"
		)->join("categories",
			"categories.id", "=", "subcategories.category_id"
		)
		->whereRaw("productsubcategories.product_id =".$id_prod." AND subcategories.status_id = 1")
		->get()
        ->toArray();

		if(count($subcategories)>0){
			foreach($subcategories as $sb){
				$id[] = $sb['id'];
				$subca[] = $sb['subcategory'];
				$categ[] = $sb['category'];
			}
			if($flag == 0){
				$datos = array("subcategories"=>implode(", ",$subca), "categories"=>implode(", ",$categ));
				return ($datos);
			}else{
				$datos[] = array("id"=>0, "subcategory"=>implode(", ",$subca));
				return ($datos);
			}
			
		}else{
			return response()->json([
            'status' => 'fails',
				'message' => 'error',
			]);
		}
		
	}

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
			'product' => ['required', 'string'],
			'category' => ['required'], 'string'
		]);
		
		if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }		
		
		if(isset($request->subcategory) && !empty($request->subcategory)){
			
			$product = Product::create([
				'product' => $request->product,
			]);
			
			if($product->id){
				for($su=0; $su < count($request->subcategory); $su++){
					$prod_subcat = Productsubcategory::create([
						'product_id' => $product->id,
						'subcategory_id' => $request->subcategory[$su],
					]);
				}
			}
		}

        return response()->json([
            'status' => 'success',
            'message' => 'Product created successfully',
            'product' => $product,
        ]);
    }

    public function show(string $id)
    {
        $product = Product::find($id);
		$datos = $this->getSubCategoryProduct($id, 1);
        return response()->json([
            'status' => 'success',
            'products' => $product,
            'datos' => $datos,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
			'product' => ['required', 'string'],
		]);
		
		if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
		
		if(isset($request->subcategory) && !empty($request->subcategory)){
			
			if($id){
				//se eliminan los registros de subcategories existentes para este product
				$delete = Productsubcategory::where("product_id", $id)->delete();
				//se registran las nuevas subcategorias para este producto
				for($su=0; $su < count($request->subcategory); $su++){
					$prod_subcat = Productsubcategory::create([
						'product_id' => $id,
						'subcategory_id' => $request->subcategory[$su],
					]);
				}
			}
			
		}
		//se actaliza la descripcion del producto
        $product = Product::find($id);
        $product->product = $request->product;
        $product->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Product updated successfully',
            'product' => $product,
        ]);
    }

    public function destroy(string $id)
    {
		//se elimina el producto
        $product = Product::find($id);
        $product->delete();
		//se eliminan la asignacion de subcategorias
		$delete = Productsubcategory::where("product_id", $id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted successfully',
            'product' => $product,
        ]);
    }
}
