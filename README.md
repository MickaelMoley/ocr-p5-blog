# Welcome to project 5 of the DA course

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/746fa6585ef643dea96f30dd5bef091f)](https://app.codacy.com/gh/MickaelMoley/ocr-p5-blog?utm_source=github.com&utm_medium=referral&utm_content=MickaelMoley/ocr-p5-blog&utm_campaign=Badge_Grade_Settings)

Welcome to this project 5 of the DA course - Project 5.
We are going to see all the steps in order to install this project locally.


# Local installation of the project

First of all, you have to clone the project locally

    git clone git@github.com:MickaelMoley/ocr-p5-blog.git 
## Installation of dependencies
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
      dbname: 'yourdatabasename'
Finally we need to synchronize our database with our project.

In case the database is not yet created, you can create it by following this command:

    php vendor\doctrine\orm\bin\doctrine orm:schema-tool:create
In case of modification/implementation, the following command must be run to synchronize the src/Entity fields with the database.
    
    php vendor\doctrine\orm\bin\doctrine orm:schema-tool:update --force

## Start


As it is a PHP project, it is necessary to launch a web server. To do this, you have to go to the `public` folder **at the root of the project**. 

    cd public/

and run the following command : 

    php -S localhost:8000


This will launch a server on our local machine on port `8000`.

Then, we must access the given URL: [localhost:8000](localhost:8000)
The home page should be displayed.
