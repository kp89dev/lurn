### What is this repository for? ###

* Lurn Nation code

### How do I get set up? ###

* Git Pull the code

##### Homestead
...

##### Docker
* Install docker-compose ([guide here](https://docs.docker.com/compose/install/#install-compose))
* `docker-compose up -d` - starts containers and in detached mode ( -d )

##### Both
   - Run the following commands from terminal
        1. ```rm -rf node_modules``` to remove the existing node modules that have been loaded
        2. ```npm i --save-dev npm``` to make sure we all have the same version of npm
        3. ```npx npm i``` to install node dependencies
        4. ```npx npm run dev``` to compile the js and less files (```./node.js npm run dev```)

* Create missing directories from storage/ 
```
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/app/storage/logs/sendlane
```    
* Although all db changes are done through migrations, when you first do that you'll have no data in your tables
that's why it's better to ask a db dump from a team member. 
* Ask for an .env file from a team member

For unit tests you should create a separate database, called lurn-central-testing ( check .env.testing ) which will be used
when runnint tests ```vendor/bin/phpunit``` when you do db modifications you may want to apply them to the testing db ```php artisan migrate:refresh --env=testing```
( for docker you have to connect to the container and run that command, if on linux: ```./php artisan migrate:refresh --env=testing```)
 
### Contribution guidelines ###

* Coding styles must respect PSR-2 ( https://www.php-fig.org/psr/psr-2/ ) - there are pluggins which you install for your IDE to help you with that
* All code must be unit/integration tested check tests/ folder
* Css and js goes in resources/
* For file uploads check examples in the project or ask ( it uses s3 )

### Local Testing Setup

1. Make sure you have a testing database named `lurn-central-testing`
2. create a `.env.dev` by copying the `.env.testing`
3. make sure the `TESTING_IP` in `.env.dev` is uncommented and pointed to `127.0.0.1`
4. set your `DB_HOST` in `.env.dev` to the ip of your local `lurn-central-testing` database
5. set the `DB_USERNAME` in `.env.dev` to your `lurn-central-testing` database username
6. set the `DB_PASSWORD` in `.env.dev` to your `lurn-central-testing` database password
7. To run tests, do the following:
    1. Migrate your testing database like so:
    
        ```
        php artisan migrate --env=dev
        ```
    2. To run tests, run:
    
        ```
        vendor/bin/phpunit --configuration phpunit-local.xml
        ```
    3. You can setup your bash/zsh with an alias (optional)
    
        1. Add the following lines to your local .bashrc (or .zshrc):
        
            ```
            alias phpunit="vendor/bin/phpunit --configuration phpunit-local.xml"
            ```
        2. Source your .bashrc or .zshrc like so (or just open a new terminal):
        
            ```
            source ~/.zshrc OR source ~/.bashrc
            ```
        3. Run your tests by simply typing `phpunit` in terminal.

### Who do I talk to? ###

* Anyone in the team
