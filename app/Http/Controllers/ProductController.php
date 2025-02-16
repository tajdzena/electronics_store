<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    //Prikaz svih proizvoda
    public function index()
    {
        //$products = Product::get()->first();
        $products = Product::all();

        //return $products->toJson(JSON_PRETTY_PRINT);
        //JSON_PRETTY_PRINT za razmake izmedju zagrada i elemenata

        //Ovakav print je najpregledniji :)
        return response()->json($products, 200, [], JSON_PRETTY_PRINT);
    }


    // Prikaz proizvoda po kategoriji, ukoliko se prosledi id
    public function byCategoryId($category_id)
    {
        //Direktno preko id-a, napraviti za unesen naziv?
        $products = Product::where('category_id', $category_id)->get();

        if($products->isEmpty()){
            return response()->json(['error' => 'Nema proizvoda za ovu kategoriju'], 404);
        }

        return response()->json($products, 200, [], JSON_PRETTY_PRINT);
    }

    //Prikaz proizvoda po kategoriji, ukoliko se prosledi ime kategorije
    public function byCategoryName(Request $request)
    {
        //$categoryName = urlencode($request->query('name'));
        $categoryName = $request->query('name'); // Dobijamo kategoriju iz query parametra

        if(!$categoryName){
            return response()->json(['error' => 'Naziv kategorije je obavezan!'], 400);
        }

        $category = Category::where('category_name', $categoryName)->first();

        if(!$category){
            return response()->json(['error' => 'Kategorija nije pronadjena!'], 404);
        }

        $products = Product::where('category_id', $category->category_id)->get();

        if($products->isEmpty()){
            return response()->json(['error' => 'Nema proizvoda za ovu kategoriju!'], 404);
        }

        return response()->json($products, 200, [], JSON_PRETTY_PRINT);
    }


    //Brisanje proizvoda
    public function destroy($upc)
    {
        $product = Product::find($upc);

        if(!$product){
            return response()->json(['error' => 'Proizvod nije pronadjen!'], 404);
        }

        $product->delete();
        return response()->json(['message' => 'Proizvod je uspesno obrisan.'], 200);
    }


    //Izmena proizvoda
    public function update($upc, Request $request)
    {
        //Validacija ulaznih podataka, koji god da unesemo
        $request->validate([
            //Upc bi isto mogao da se promeni, ali mi licno nema smisla ako je vec potpuno unique
            'product_number' => 'string|max:100',
            'sku' => 'integer', //Zbog unique uslova u bazi, nece se izvrsiti izmena ako unesemo sku isti kao za drugi proizvod
            'regular_price' => 'decimal:2,digits_between:1,7',
            'sale_price' => 'decimal:2,digits_between:1,7',
            'description' => 'nullable|string|max:2000',
            'category_id' => 'exists:category,category_id', //Proverava postojanje id u tabelama
            'department_id' => 'exists:department,department_id',
            'manufacturer_id' => 'exists:manufacturer,manufacturer_id'
        ]);

        //return response()->json($request->input('sku'));

        //Nalazenje proizvoda
        //$product = Product::where('upc', $upc)->first();
        $product = Product::find($upc);

        if(!$product){
            return response()->json(['error' => 'Proizvod nije pronadjen!'], 404);
        }

        //U skladu sa poslatim parametrima, azuriramo ih
        if($request->has('product_number')){
            $product->product_number = $request->input('product_number');
        }

        if($request->has('sku')){
            //return response()->json($request->input('sku'));
            $product->sku = $request->input('sku');
        }

        if($request->has('regular_price')){
            $product->regular_price = $request->input('regular_price');
        }

        if($request->has('sale_price')){
            $product->sale_price = $request->input('sale_price');
        }

        if($request->has('description')){
            $product->description = $request->input('description');
        }

        if($request->has('category_id')){
            $product->category_id = (int)$request->input('category_id');
        }

        if($request->has('department_id')){
            $product->department_id = (int)$request->input('department_id');
        }

        if($request->has('manufacturer_id')){
            $product->manufacturer_id = (int)$request->input('manufacturer_id');
        }

        //Cuvanje promena
        $product->save();

        return response()->json([
            'message' => 'Proizvod je uspesno azuriran.',
            'product' => $product,  //Nova verzija proizvoda
        ], 200, [], JSON_PRETTY_PRINT);
    }



    //Generisanje .csv fajla
    public function generateCSV($category_id)
    {
        //Dohvatanje kategorije
        $category = Category::find($category_id);

        if(!$category){
            return response()->json(['error' => 'Kategorija nije pronadjena!'], 404);
        }

        //Proizvodi koji pripadaju datoj kategoriji
        $products = Product::where('category_id', $category_id)->get();

        if($products->isEmpty()){
            return response()->json(['error' => 'Nema proizvoda u ovoj kategoriji!'], 404);
        }

        //Sablon za ime fajla
        //Zamena karaktera koji nisu alfanumericki sa _
        $category_name = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($category->category_name));
        //Datum i vreme
        $date = now()->format('Y_m_d-H_i'); // Format: godina(sve 4 cifre)_mesec_dan-sat_minut
        $file_name = "{$category_name}_{$date}.csv";

        //Otvaranje fajla za pisanje (ukoliko ne postoji, kreira se)
        $file = fopen(storage_path("app/public/{$file_name}"), 'w');

        //Zaglavlja, ista kao u originalnom .csv
        fputcsv($file, ['product_number', 'category_name', 'department_name', 'manufacturer_name',
                        'upc', 'sku', 'regular_price', 'sale_price', 'description']);

        //Dodavanje proizvoda
        foreach ($products as $product) {
            fputcsv($file, [
                $product->product_number,
                $product->category->category_name,
                $product->department->department_name,
                $product->manufacturer->manufacturer_name,
                $product->upc,
                $product->sku,
                $product->regular_price,
                $product->sale_price,
                $product->description
            ]);
        }

        //Zatvaranje fajla
        fclose($file);

        return response()->json(['message' => 'CSV fajl je uspesno generisan!', 'file' => storage_path("app/public/{$file_name}")]);
    }

}
