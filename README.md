<p> -- Setup projekta -- </p>
<br>
<p> • Framework: Laravel </p> <br>
<p> • Okruženja: PhpStorm (JetBrains), phpMyAdmin, Postman </p> <br>
<p> • Jezici: PHP, MySQL </p> <br>
<p> • Alati: Composer, Eloquent ORM, Artisan </p> <br>
<p> • Server: XAMPP Control Panel </p> <br> <br> <br>


<p> -- Neke informacije o projektu -- </p> <br>
<p> Za povezivanje na bazu podataka, .env fajl je konfigurisan na sledeći način: </p> <br>
<p> 	DB_CONNECTION=mysql </p> <br>
<p> 	DB_HOST=127.0.0.1 </p> <br>
<p> 	DB_PORT=3306 </p> <br>
<p> 	DB_DATABASE=electronics_store </p> <br>
<p> 	DB_USERNAME=root </p> <br>
<p> 	DB_PASSWORD= </p> <br> <br>

<p> Komanda pomoću koje se pokreće druga tražena funkcionalnost, uvoz podataka iz .csv priloženog fajla u bazu: </p> <br>
<p> 	php artisan import:csv putanja/do/product_categories.csv </p> <br> <br>

<p> Komanda pomoću koje se pokreće lokalni server: </p> <br>
<p> 	php artisan serve </p> <br> <br>

<p> REST putanje koje imaju PUT i DELETE zahteve testirane su pomoću platforme Postman, s obzirom na to da projekat nema korisnički interfejs. </p> <br>
<p> Ostale putanje, sa GET zahtevom, mogu se testirati i preko browser URL-a. </p> <br> <br>

<p> Svaka funkcionalnost vezana za podatke prikazana je u JSON formatu, za bonus zadatak, fajl se kreira na putanji storage/app/public. </p>