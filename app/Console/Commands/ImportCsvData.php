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


    //Pomoc da se ne razdvaja tekst unutar description zbog zareza, da ne pomisli program da su to razliciti atributi
    //Trazicemo 8. zarez, kako je to poslednji koji razdvaja pretposlednji atribut i description
    function findNthComma($string, $n) {

        $commaCount = 0;
        $insideQuotes = false;
        $length = strlen($string);

        //Citanje svakog karaktera
        for($i=0; $i<$length; $i++){
            $char = $string[$i];

            //Promena stanja insideQuotes ako se naidje na "
            //prvi put kad se naidje, flag = true
            //drugi put kad se naidje, flag = false, tada i izlazimo iz " ... "
            if($char == '"'){
                $insideQuotes = !$insideQuotes;
            }

            //Izbroj zareze samo ako nismo unutar "
            //(to su podaci koji treba da se razdvajaju na atribute, unutar " je obicno description)
            if($char == ',' && !$insideQuotes){
                $commaCount++;
            }

            //Kada dodjemo do n-tog zareza koji smo zadali, vrati poziciju u tekstu
            if($commaCount == $n){
                return $i;
            }
        }

        //Ako n-ti zarez slucajno ne postoji
        return false;
    }


    //Srediti podatke u csv-u
    function sanitize_csv_row($line){

        //Izbrisati ;;;;; sa kraja
        $lineTrimmed = rtrim($line, ";");
        //print($lineTrimmed);

        //Sredjivanje navodnika u fajlu, kada dodje do loseg eksporta pa imamo situaciju tipa """tekst"
        //Potrebno je sacuvati navodnike tamo gde nam trebaju, recimo 13", kao oznaka za ince, tako da se ne mogu svi obrisati

        //Prvo se sredjuje neparna grupa navodnika, tako da ostane samo paran broj (paran moze da lepo parsira str_getcsv metoda)
        //Zatim sve uzastopne duple ("") pretvaramo u single ("), upravo zbog situacije za npr. 13"

        //Ideja: brojac kad god naidjemo na navodnik, ako je neparan broj navodnika, oduzimanje -1, da ostanu samo parni
        //(ako je samo jedan " - brise se)
        //Ako je paran broj navodnika, ne oduzima se nista
        //Ne raditi nad originalnim stringom jer ne moze da se izbrise karakter, tj. ne moze da se setuje na '',
        //vec samo prepisivanje odabranih charova u line2

        //Inicijalizacija
        $navodnici_counter = 0;
        $line2 = "";

        for($i = 0; $i < strlen($lineTrimmed); $i++){

            if($navodnici_counter > 0){ //Ako smo vec naisli na grupu navodnika u tekstu
                if($lineTrimmed[$i] == '"'){ //Ako je trenutan char "
                    $navodnici_counter++;
                }else{ //Ako trenutan char nije ", proveravamo stanje, neparan ili paran broj
                    if($navodnici_counter % 2 != 0){ //Ako je neparan broj, umanji counter za 1
                        $navodnici_counter--;
                    }

                    while($navodnici_counter > 0){ //Konkatenacija, tj. upisivanje novog, parnog broja navodnika u line2
                        //Ako smo naisli samo na 1 ", on se nece preneti
                        $line2 .= '"';
                        $navodnici_counter--; //Nakon svakog upisa u line2, smanjiti za 1
                    }

                    $line2 .= $lineTrimmed[$i]; //Konkatenacija ostatka teksta
                }
            }
            else{ //Ako nismo naisli na grupu navodnika (pocetno stanje i stanje nakon svake grupe)
                if($lineTrimmed[$i] == '"'){
                    $navodnici_counter++;
                }else{
                    $line2 .= $lineTrimmed[$i];
                }
            }
        }

        //Izuzetak - ako ostanu " na kraju reda, tj. ako se ne naidje na non-" karakter do samog kraja,
        //nece se uci u granu gde trenutan char NIJE " i onda se nece nista proveriti i upisati
        //Jos jedna provera onda o neparnosti i upisu u line2
        if($navodnici_counter % 2 != 0){
            $navodnici_counter--;
        }

        while($navodnici_counter > 0){
            $line2 .= '"';
            $navodnici_counter--;
        }


        //Sada izbrisati duple " znakove i pretvoriti u single, da ostane tamo gde treba (kao za npr. 13")
        $cleanedLine = str_replace('""', '"', $line2);


        //Jos jedan izuzetak - nakon celog ovog ciscenja, ako ostane samo prvi ili poslednji znak " u description-u,
        //mora da se zatvori sa suprotne strane (tako ce str_getcsv lepo parsirati),
        //i tako obezbedjujemo lepo procitan tekst gde nam , u opisu ne smetaju

        $pocetak_description_sekcije = $this->findNthComma($cleanedLine, 8);

        if($pocetak_description_sekcije < strlen($cleanedLine)-1){ //Sredjivanje pozicije, ako nije na kraju teksta
            $pocetak_description_sekcije += 1;
        }

        if ($cleanedLine[strlen($cleanedLine)-1] == '"' && $cleanedLine[$pocetak_description_sekcije] != '"'){
            //Dodavanje " na pocetak
            $cleanedLine = substr($cleanedLine, 0, $pocetak_description_sekcije) . '"' . substr($cleanedLine, $pocetak_description_sekcije);
        } elseif ($cleanedLine[strlen($cleanedLine)-1] != '"' && $cleanedLine[$pocetak_description_sekcije] == '"'){
            //Dodavanje " na kraj
            $cleanedLine .= '"';
        }

        //str_getcsv metoda za parsiranje
        $returnVal = str_getcsv($cleanedLine);

        //print_r($returnVal);
        return $returnVal;
    }



    //Funkcija za izvrsavanje
    public function handle()
    {

        //Testiranje nad jednim lose formatiranim redom iz .csv fajla
//        $csvLine = '"PAS-213BLACK,Laptop Bags & Cases,PHOTO/COMMODITIES,Case Logic,85854213523,4392386,34.99,19.99,""CASE LOGIC Sleeve for 13"""" Apple MacBook Pro: Compatible with 13"""" Apple MacBook Pro"; suede and neoprene materials; zippered Power Pocket; USB drive pocket; slimline design;" asymmetrical zipper""";;;;;;;;';
//
//        $this->info($csvLine);
//        $this->sanitize_csv_row($csvLine);


        $file = $this->argument('file'); //Argument iz signature stringa na pocetku, preko poziva komande

        if(!file_exists($file) || !is_readable($file)){
            $this->error("Fajl nije pronadjen ili ne moze da se uspesno procita!");
            return false;
        }

        //Prvobitna inicijalizacija
        $header = null;
        $data = [];

        if(($fileReading = fopen($file, 'r')) !== false) { //Ako se uspesno otvorio fajl u modu za citanje
            while(($row = fgets($fileReading)) !== false) { //Dok ne stignemo do kraja reda

                $row = rtrim($row);
                $row = $this->sanitize_csv_row($row);
                //$this->info(print_r($row));

                if(!$header) { //Za prvo citanje prvog reda, pretvaramo u zaglavlje
                    $header = $row; //I cuvamo taj prvi red u promenljivoj zaglavlja
                    //$this->info(print_r($header));
                }
                else{
                    if (count($header) === count($row)) {
                        $data[] = array_combine($header, $row);
                        //Kombinacija zaglavlja sa podacima iz trenutnog reda
                        //pravi se asocijativni niz ['product_number' => '12345', 'category_name' => 'Laptops']
                    } else {
                        $this->info("Nije jednak broj zaglavlja i redova.");
                        $this->info(print_r($row));
                    }
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


            //Unos proizvoda
            //Product::create([
            Product::updateOrInsert( //Zbog kombinovanog primarnog kljuca
                [
                    'product_number' => $row['product_number'],
                    'upc' => $row['upc']
                ], [
                    'sku' => $row['sku'],
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
