This program is designed to read Alko’s latest product catalog from an Excel file provided online. The data is parsed and stored in a MySQL database, and then displayed through a web interface. The system also includes an update feature, which downloads the newest price list and refreshes the database automatically (update requires a password once per session).

Process overview:

-Installing Composer and MySQL in a Proxmox container.
-Creating a dedicated MySQL user and password.
-Creating a database table with the required column structure.
-Using PHP to handle the core functionality and database connection.
-Using Shuchkin SimpleXLSX to parse the Excel file and convert it for database insertion.
-Updating hinnasto_paivitysaika.txt with the timestamp extracted from the Excel file.(A password was added to the update page since the database and text file are writable — mainly for educational and security demonstration purposes. The password is included in the assignment return file.)
-Using HTML, CSS and Bootstrap to build the front-end layout.
