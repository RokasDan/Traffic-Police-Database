# Traffic-Police-Database
![](1.png)

Traffic-Police-Database is a coursework project for Database and Interface module in the University of Nottingham. It's a web application that connects to a MySQL database to display and save data related to traffic offenders. The website has two user roles: regular officer and admin. Users can add new offenders, vehicles, and incidents to the database.

If you wish to read more about it on how to use it please visit my website at: https://rokasdanevicius.com/TrafficPolice

## Installation

To install and test this web application, follow the steps below:

1. Download and install XAMPP packege. This includes MySQL and Apache server emulation. You can find this tool here: https://www.apachefriends.org/
2. Download and install MySQL Workbench. You can get it from here: https://dev.mysql.com/downloads/workbench/
3. Once you have XAMPP and MySQL Workbench installed, start the MySQL server through XAMPP by pressing the start button.
4. Once MySQL server is running, open MySQL Workbench and create a server connection. Choose a connection name, set the host name and port to the settings of MySQL XAMPP service (by default, XAMPP sets host name to 127.0.0.1 and port to 3306). You can also set the password if you wish or leave it empty.
5. Once the connection is established to MySQL server with MySQL Workbench, create a new schema.
6. In the `psxrd2_InstallationFiles` folder of this repository, find a txt file called `Database-Instalation.txt` and copy all of the queries to MySQL Workbench and run them for your schema. This will create all of the relations needed for this web application and will fill them with random values.
7. In the XAMPP installation directory, find a folder called `htdocs`. In this folder, create a folder and call it "test". In the "test" folder, paste all of the contents inside from the `psxrd2_InstallationFiles` folder of this git hub repository.
8. Go to the `test` folder and find a folder called `php`. In this folder, find a file called `server_connection.php`. Open this file and make sure it has the following values saved:

    ```bash
    $servername = "localhost";
    $username = "root";
    $password = "Password for you MySQL connection if you made one, leave empty otherwise";
    $dbname = "your schema name you chose in MySQL Workbench";
    ```

9. In XAMPP, start the Apache server and if MySQL server is not running, start it again.
10. Now you can go to your browser and use this link to access the web application: http://localhost/test/
11. Congratulations, you have successfully run Traffic-Police-Database on your machine. To log in as an officer, use `Carter` for ID and `fuzz42` for password. If you wish to log in as an admin, use `haskins` for ID and `copper99` for password.
