# PHP Training
This project is build according to Symfony framework.
## Installation using docker
Make sure you have docker installed. To see how you can install docker on Windows [click here](https://youtu.be/2ezNqqaSjq8). <br>
Make sure docker and docker-compose commands are available in command line.
1. Clone this project using git:
    ```
    $ git clone git@github.com:tinhthanhvo/demo-symfony.git
    ```
1. Run composer install:
    ```
    $ composer install
    ```
1. Navigate to the project root directory and run:
    ```
    $ docker-compose up -d --force-recreate --build --remove-orphans
    ```
8. Open in browser
    ```
    127.0.0.1:8080
    ```
