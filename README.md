<p> -- Setup projekta -- </p>
<p> • Framework: Laravel <br>
    • Okruženja: PhpStorm (JetBrains), phpMyAdmin, Postman <br>
    • Jezici: PHP, MySQL <br>
    • Alati: Composer, Eloquent ORM, Artisan <br>
    • Server: XAMPP Control Panel </p> <br>


<p> -- Neke informacije o projektu -- </p>
<p> Za lokalno povezivanje na bazu podataka, .env fajl je konfigurisan na sledeći način: <br>
     	DB_CONNECTION=mysql <br>
     	DB_HOST=127.0.0.1 <br>
     	DB_PORT=3306 <br>
     	DB_DATABASE=electronics_store </p>

<p> Komanda pomoću koje se pokreće druga tražena funkcionalnost, uvoz podataka iz .csv priloženog fajla u bazu: <br>
     	php artisan import:csv putanja/do/product_categories.csv </p>

<p> Komanda pomoću koje se pokreće lokalni server: <br>
     	php artisan serve </p>

<p> REST putanje koje imaju PUT i DELETE zahteve testirane su pomoću platforme Postman, s obzirom na to da projekat nema korisnički interfejs. <br>
    Ostale putanje, sa GET zahtevom, mogu se testirati i preko browser URL-a. </p>

<p> Svaka funkcionalnost vezana za podatke prikazana je u JSON formatu, za bonus zadatak, fajl se kreira na putanji storage/app/public. </p>