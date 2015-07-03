# imp-her

A school assignment. The idea is to make a website where users can search for their favorite gaming videos, create lists and share them. It's now my task to create youtube support for the website.

## Installatie
1. Have a server running PHP 5.6.8 or higher.
2. Open `api/v1/includes/settings.php`
3. Change the database settings so they match your situation
4. Go to your DBMS of your database and import `imp-her.sql`, it will make it's own database.
5. Add categories by visiting `http://localhost/api/v1?action=insert&type=category&title=<CATEGORYTITLE>`
