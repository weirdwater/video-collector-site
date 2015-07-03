# imp-her

A school assignment. The idea is to make a website where users can search for their favorite gaming videos, create lists and share them. It's now my task to create youtube support for the website.

## Installation
1. Have a server running PHP 5.6.8 or higher.
2. Open `api/v1/includes/settings.php`.
3. Change the database settings so they match your situation
4. Go to your DBMS of your database and import `imp-her.sql`, it will make it's own database.
5. Add categories by visiting `BASE_URL/api/v1?action=insert&type=category&title=CATEGORYTITLE`.
6. You're all set, start collecting!

* * *

## Installatie
1. Zorg er voor dat je server PHP 5.6.8 of hoger heeft.
2. Open het bestand `api/v1/includes/settings.php`.
3. Verander de instellingen die relevant zijn voor jouw situatie.
4. Ga naar je DBMS en importeer `imp-her.sql`, je hoeft niet eerst een database bij te maken.
5. Voeg een nieuwe categorie toe door te gaan naar `BASE_URL/api/v1?action=insert&type=category&title=CATEGORYTITLE`.
6. Klaar is kees, start maar met video's verzamelen.

Live versie op [mijn website](www.weridwater.net/imp-her) en de code ook op [GitHub](http://www.github.com/weirdwater/video-collector-site).


## Veranderingen
Ten eerste heb ik mijn database anders aangepakt. Ik heb in plaats van de gegeven structuur een andere genomen. Ten eerste sla ik van videos alleen de youtube_id en de titel op. Ik heb de url en afbeelding locatie niet in de database opgeslagen, omdat je die kan opstellen met het youtube id. Het is namelijk altijd `http://www.youtube.com/watch?v=` `+` `youtube_id` en voor afbeeldingen ook. Die plak ik dus samen in het php gedeelte van mijn site. De website ontvangt altijd json met volledige informatie.
Vervolgens heb ik de categorieën ook in een aparte tabel gestopt en door middel van een many to many relationship videos gekoppeld aan categorieën.

Er laden ook een stuk meer videos, zodat de gebruiker meer videos kan bekijken voordat hij/zij de query moet aanpassen.

Ook heb ik er voor gekozen om een dropdown menu te maken voor op de zoek pagina. Zo worden er niet per ongeluk nieuwe categorieën aangemaakt wanneer de gebruiker het fout spelt. Nu is het alleen iets lastiger om een categorie toe te voegen.