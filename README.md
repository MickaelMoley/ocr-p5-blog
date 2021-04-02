# Welcome to project 5 of the DA course

Welcome to this project 5 of the DA course - Project 5.
We are going to see all the steps in order to install this project locally.


# Installation du projet en local

First of all, you have to clone the project locally

    git clone git@github.com:MickaelMoley/ocr-p5-blog.git 
## Installation des dépendences
To install the project libraries, run the following command **at the root of the project**

    composer install



## Project configuration 

To be able to use the project, you will have to configure the connection to the database.

To do this, you need to open the file `env.yaml` which is located in `App/` so `App/env.yaml`.

then modify the following lines to match your database configuration: 

    #app/env.yaml
    
    database:  
      driver: pdo_mysql  
      user: 'yourusername'
      password: 'yourpassword'
      dbname: 'yourdatabaename'
Finally we need to synchronize our database with our project.

In case the database is not yet created, you can create it by following this command:

    php vendor\doctrine\orm\bin\doctrine orm:schema-tool:create
In case of modification/implementation, the following command must be run to synchronize the src/Entity fields with the database.
    
    php vendor\doctrine\orm\bin\doctrine orm:schema-tool:update --force


