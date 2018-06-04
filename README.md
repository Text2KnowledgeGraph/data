# Constructing Software Knowledge Graph from Software Text
This project proposes natural language processing techniques to extract ten subcategories of API caveat sentences from API documentation and link these sentences to API entities in an API caveats knowledge graph. The API caveats knowledge graph can support information retrieval based or entity-centric search of API caveats.
## Getting Started
This project was written and tested in macOS Sierra.So the commands and instructions shown in this README are in mac format.
### Prerequisties
1. Mysql. Server version: 10.1.26-MariaDB was used.
2. XAMPP. It can download from [https://www.apachefriends.org/download.html](). Version 7.0.23 / PHP 7.0.23 for OS X was used.

## Running the Search Webpage
This section introduces how to search in our webpage which is based on our API caveats knowledge graph.

1. Save folder "db1" into path "~/Applications/XAMPP/htdocs"
2. Save file "index.html" into "~/Applications/XAMPP"
3. Open manager-osx in Applications
4. Start MYSQL Database and Apache Web Server (See Known Issue if occuring problems)
5. Import the database (As the database is too large to upload, please contact with me to get the database) by adding tables in [http://localhost/phpmyadmin/]() or running the command `mysql -u root [path of the sql file]`
in the terminal
6. Go to [http://localhost/]() to use the search tool.

## Known Issue
### Cannot start MYSQL Database in XAMPP
1. Change the port number. The author used 3307. And try again.
2. Run 

`$ sudo killall mysqld`

`$ sudo /Applications/XAMPP/xamppfiles/bin/mysql.server start`
## Author
Database is built by Hongwei Li.

Codes of search tool and README are written by Sirui Li. 

Mail: u5831882@anu.edu.au
## Acknowledgements
I really appreciate the work of our team (Zhenchang Xing, Hongwei Li, Jiamou Sun).
