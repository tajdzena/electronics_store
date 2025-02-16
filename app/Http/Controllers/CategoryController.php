<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        return response()->json($categories, 200, [], JSON_PRETTY_PRINT);
    }

    public function destroy($category_id)
    {
        $category = Category::find($category_id);

        if(!$category){
            return response()->json(['error' => 'Kategorija nije pronadjena!'], 404);
        }

        $category->delete();
        return response()->json(['message' => 'Kategorija je uspesno obrisana.'], 200);
    }


    //Izmena imena kategorije
    public function update($category_id, Request $request)
    {
        //Validacija novog naziva kategorije
        $request->validate([
            'newName' => 'required|string|max:100', //limit u bazi
        ]);

        $category = Category::find($category_id);

        if(!$category){
            return response()->json(['error' => 'Kategorija nije pronadjena!'], 404);
        }

        //Izmena naziva
        $category->category_name = $request->input('newName');
        $category->save();

        return response()->json(['message' => 'Naziv kategorije je uspesno izmenjen.'], 200);
    }
}
