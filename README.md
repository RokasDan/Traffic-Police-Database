# Traffic-Police-Database
![](1.png)

Traffic Police Database is a coursework project for the Database and Interface module at the University of Nottingham. It is a web application that connects to a MySQL database to display and save data related to traffic offenders. The website has two user roles: regular officer and admin. Users can add new offenders, vehicles, and incidents to the database.

If you want to learn more about how to use this application, please visit my website at: https://rokasdanevicius.com/TrafficPolice

## Installation

To install and test this web application, please follow the steps below:

1. Download and install the XAMPP package. This package includes MySQL and Apache server emulation. You can download this tool from: https://www.apachefriends.org/
2. Download and install MySQL Workbench. You can download it from: https://dev.mysql.com/downloads/workbench/
3. Once you have XAMPP and MySQL Workbench installed, start the MySQL server through XAMPP by clicking the start button.
4. Once the MySQL server is running, open MySQL Workbench and create a server connection. Choose a connection name, set the hostname and port to the settings of MySQL XAMPP service (by default, XAMPP sets the hostname to 127.0.0.1 and the port to 3306). You can also set the password if you wish or leave it empty.
5. Once the connection is established to the MySQL server with MySQL Workbench, create a new schema.
6. In the `psxrd2_InstallationFiles` folder of this repository, find a text file called `Database-Installation.txt` and copy all of the queries to MySQL Workbench and run them for your schema. This will create all the relations needed for this web application and will fill them with random values.
7. In the XAMPP installation directory, find a folder called `htdocs`. In this folder, create a new folder named `test`. In the `test` folder, paste all of the contents from the `psxrd2_InstallationFiles` folder of this GitHub repository.
8. Go to the `test` folder and find a folder named `php`. In this folder, find a file named `server_connection.php`. Open this file and make sure it has the following values saved:



    ```bash
    $servername = "localhost";
    $username = "root";
    $password = "Password for you MySQL connection if you made one, leave empty otherwise";
    $dbname = "your schema name you chose in MySQL Workbench";
    ```


9. In XAMPP, start the Apache server and if the MySQL server is not running, start it again.
10. Now, you can open your browser and use this link to access the web application: http://localhost/test/
11. Congratulations! You have successfully run the Traffic Police Database on your machine. To log in as an officer, use "Carter" for ID and "fuzz42" for the password. If you wish to log in as an admin, use "haskins" for the ID and "copper99" for the password.

