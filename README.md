# Centre Learning Commons Database
This database application stores tutoring schedule information for the use of Centre students and CLC administrators.

## Feature Overview

This application:
* stores information relating to
   * academic courses
   * tutoring locations
   * tutor shift schedules
   * drop-in tutoring schedules
* supports adding, editing, and deleting stored information
* supports filtering and searching for specific information
* displays stored information in an easy-to-navigate way.

## Getting Started

### Requirements
This application makes uses the **LAMP stack** software bundle, i.e.,
* Linux
* Apache
* MariaDB
* PHP

Please make sure these softwares are installed on your device before proceeding.
To install AMP on a Linux terminal, you can run the command:
```sudo apt install apache2 libapache2-mod-php mariadb-server php git vim php8.2-mysql```

To initialize MariaDB, run the command:
```sudo mysql_secure_installation```

If you are new to MariaDB, you will need to log into MariaDB with superuser privileges, create a database user for yourself, and grant yourself permissions to modify databases in the client.

<details>
<summary>Setting Up MariaDB Tutorial</summary>
    <p>1. After installation, log in to MariaDB with superuser privileges on the terminal:</p>
    <pre>$ sudo mariadb</pre>
    <p>2. Create a new database user:
    <pre>> CREATE USER 'user_name'@'localhost' IDENTIFIED BY 'password';</pre>
    <p>Replace 'user_name' and 'password' refers to your username and password of choice.</p>
    <p>3. Grant yourself privileges:</p>
    <pre>> GRANT ALL PRIVILEGES ON *.* TO 'user_name'@'localhost';</pre>
    <p>Once again, replace 'user_name' with the username you just defined. Use this command with caution in a team production environment.</p>
</details>

<br>

This application also makes use of **MySQLI** extension to connect the database to the web application.
To install MySQLI, run the command:
```$ sudo apt-get install php-mysqli```
and restart Apache:
```sudo systemctl restart apache2```

### Installing

The database application can be easily installed by downloading the ZIP file. Click the green "Code" button on the top right of the repository, and click "Download ZIP."

Developers can also fork the repository and clone it to their local devices with Git.

**Note that:**
* The project folder should be downloaded in your home directory.
* The ```var/www/html``` folder that Apache recognizes by default should contain a link to the ```html``` folder inside the repository folder. You will need to make this manually.
* You will need to create your own ```mysqli.ini``` file. This is so that PHP can parse the credentials to log into your MariaDB database. Place this file in your home directory with the host, user, and password information correlating to your database credentials. It follows the same syntax as ```php.ini```. See <a href="https://www.php.net/manual/en/function.parse-ini-file.php">documentation</a>.

### Executing

When you have installed the program, you have to manually run the source code. Log into MariaDB and run the command:
```> SOURCE clc_database_main.sql;```

This will build the database from scratch according to the ```clc_database_schema.sql```, ```clc_database_data.sql```, and ```clc_database_data.sql``` files. Developers who wish to modify the structure or default insertion values of the database can modify these files.

At this point, the web application will be usable and viewable through the link, ```http://your_ip_address/repo_name/html/src```. Replace 'your_ip_address' with your external IP address and 'repo_name' with the name of the link you made that redirects Apache to the repository's html folder.

And then you are done!

## Acknowledgments

Thank you to Jessica Chisley, our client for this project, and to Dr. William Bailey, our professor during this term who helped debug and made recommendations for the application.