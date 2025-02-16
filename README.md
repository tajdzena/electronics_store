<p> -- Setup projekta -- </p>
<p> • Framework: Laravel <br>
    • Okruženja: PhpStorm (JetBrains), phpMyAdmin, Postman <br>
    • Jezici: PHP, MySQL <br>
    • Alati: Composer, Eloquent ORM, Artisan <br>
    • Server: XAMPP Control Panel </p> <br>


<p> -- Neke informacije o projektu -- </p>
<p> Za lokalno povezivanje na bazu podataka, .env fajl je konfigurisan na sledeći način: </p>
<p> 	DB_CONNECTION=mysql </p>
<p> 	DB_HOST=127.0.0.1 </p> 
<p> 	DB_PORT=3306 </p>
<p> 	DB_DATABASE=electronics_store </p> <br>

<p> Komanda pomoću koje se pokreće druga tražena funkcionalnost, uvoz podataka iz .csv priloženog fajla u bazu: </p>
<p> 	php artisan import:csv putanja/do/product_categories.csv </p> <br>

<p> Komanda pomoću koje se pokreće lokalni server: </p>
<p> 	php artisan serve </p> <br>

<p> REST putanje koje imaju PUT i DELETE zahteve testirane su pomoću platforme Postman, s obzirom na to da projekat nema korisnički interfejs. </p>
<p> Ostale putanje, sa GET zahtevom, mogu se testirati i preko browser URL-a. </p> <br>

<p> Svaka funkcionalnost vezana za podatke prikazana je u JSON formatu, za bonus zadatak, fajl se kreira na putanji storage/app/public. </p>