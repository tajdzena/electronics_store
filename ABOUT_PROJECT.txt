-- Setup projekta --
• Framework: Laravel
• Okruženja: PhpStorm (JetBrains), phpMyAdmin, Postman
• Jezici: PHP, MySQL
• Alati: Composer, Eloquent ORM, Artisan
• Server: XAMPP Control Panel


-- Neke informacije o projektu --
Za povezivanje na bazu podataka, .env fajl je konfigurisan na sledeći način:
	DB_CONNECTION=mysql
	DB_HOST=127.0.0.1
	DB_PORT=3306
	DB_DATABASE=electronics_store
	DB_USERNAME=root
	DB_PASSWORD= 

Komanda pomoću koje se pokreće druga tražena funkcionalnost, uvoz podataka iz .csv priloženog fajla u bazu: 
	php artisan import:csv putanja/do/product_categories.csv

Komanda pomoću koje se pokreće lokalni server:
	php artisan serve

REST putanje koje imaju PUT i DELETE zahteve testirane su pomoću platforme Postman, s obzirom na to da projekat nema korisnički interfejs.
Ostale putanje, sa GET zahtevom, mogu se testirati i preko browser URL-a.

Svaka funkcionalnost vezana za podatke prikazana je u JSON formatu, za bonus zadatak, fajl se kreira na putanji storage/app/public.