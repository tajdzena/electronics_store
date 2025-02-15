<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Department;
use App\Models\Manufacturer;
use App\Models\Product;
use Illuminate\Console\Command;

class ImportCsvData extends Command{

    //protected $signature = 'app:import-csv-data';
    protected $signature = 'import:csv {file}';  // Komanda u cmd: "php artisan import:csv putanja/fajl.csv"
    protected $description = 'Procesuiranje i uvoz podataka iz .csv fajla u bazu';


    //Funkcija za izvrsavanje
    public function handle(){
        $file = $this->argument('file'); //iz signature stringa

        if(!file_exists($file) || !is_readable($file)){
            $this->error("Fajl nije pronadjen ili ne moze da se uspesno procita!");
            return false;
        }

        //Prvobitna inicijalizacija
        $header = null;
        $data = [];

        if(($fileReading = fopen($file, 'r')) !== false) { //Ako se uspesno otvorio fajl u modu za citanje
            while(($row = fgetcsv($fileReading, 0, ",")) !== false) { //Dok ne stignemo do kraja reda

                //$this->info(print_r($row));

                if(!$header) { //Za prvo citanje prvog reda, pretvaramo u zaglavlje
                    $header = $row; //I cuvamo taj prvi red u promenljivoj zaglavlja
                    //$this->info(print_r($header));
                }
                else{
                    if (count($header) === count($row)) {
                        $data[] = array_combine($header, $row);
                    } else {
                        $this->info("Nije jednak broj zaglavlja i redova.");
                    }

                    //$data[] = array_combine($header, $row); //Kombinacija zaglavlja sa podacima iz trenutnog reda
                    //npr. ako je $header = ['product_number', 'category_name', ...]
                    //a $row = ['12345', 'Laptops'],
                    //pravi se asocijativni niz ['product_number' => '12345', 'category_name' => 'Laptops']
                }

            }
            fclose($fileReading); //Zatvaranje fajla
        }

        //Uvoz podataka, pozivanje metoda
        $this->importCategories($data);
        $this->importDepartments($data);
        $this->importManufacturers($data);
        $this->importProducts($data);

        $this->info('Uspesan uvoz podataka iz .csv fajla!'); //Feedback
    }

    private function importCategories($data){
        foreach($data as $row){
            Category::firstOrCreate(['category_name' => $row['category_name']]);
            //firstOrCreate metoda proverava da li vec postoji zapis sa datim category_name
            //Ukoliko ne postoji, stvara se novi, ako ne postoji, koristi postojeci
            //Resavanje duplikata i unique vrednosti
        }
    }

    private function importDepartments($data){
        foreach($data as $row){
            Department::firstOrCreate(['department_name' => $row['department_name']]);
        }
    }

    private function importManufacturers($data){
        foreach($data as $row) {
            Manufacturer::firstOrCreate(['manufacturer_name' => $row['manufacturer_name']]);
        }
    }

    private function importProducts($data){
        foreach($data as $row){
            //Nalazenje ID-eva, osiguravanje da sve postoji u bazi, prvo sve ovo pre proizvoda
            $category = Category::firstOrCreate(['category_name' => $row['category_name']]);
            $department = Department::firstOrCreate(['department_name' => $row['department_name']]);
            $manufacturer = Manufacturer::firstOrCreate(['manufacturer_name' => $row['manufacturer_name']]);

            //dd($category, $department, $manufacturer);

            //$this->info($category->id);

            //Unos proizvoda
            //Product::create([
            Product::updateOrInsert(
                [
                    'product_number' => $row['product_number'],
                    'upc' => $row['upc']
                ], [
                    'sku' => (int)$row['sku'],
                    'regular_price' => $row['regular_price'],
                    'sale_price' => $row['sale_price'],
                    'description' => $row['description'],
                    'category_id' => $category->category_id,
                    'department_id' => $department->department_id,
                    'manufacturer_id' => $manufacturer->manufacturer_id
                ]
            );
        }
    }
}
