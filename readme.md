#Warehouse
## Running the Tests
To run the tests first install dependencies:

```
composer install
```
Then run phpunit

```
php vendor/bin/phpunit
```

## Working Locally
The application is already running on heroku for use. If you want to run it locally though, be sure to set up your .env file (there are a couple of examples in place already) and serve it using artisan.

If you want to run things locally, you will need to update the WarehouseCLICommand serviceUrl value to point to localhost:8000 instead of the Heroku application.

You can use the database for the queue driver.

```
./artisan serve
```

### Design Decisions

* I went with Lumen 5.1 because I have previous experience using Laravel (4.1) and wanted to try the bleeding edge. It is still not quite stable enough for me to want to use it again though. 
* I am using IronMQ for a queuing service so that I can have a separate worker and web server, and that is because I am too cheap to pay for servers/dynos. 
* I decided to not use the Eloquent models, mostly to have a bit more of my own code in the system. 
* The queue worker is running on my home server for simplicity, but it will get the job done. 
* I decided to have separate CLI from application because with something as complex as choosing shipment location, heuristics can change, and it much simpler to scale and change a web application than a CLI application.
* The queue allows the requests to continue moving quickly, even when we have hundreds of warehouses and thousands of products. 
* I went with the Iron MQ version 2.0.0 packagist package because version 4.0 pull requests are not working with v1 of the Iron MQ API (using a v3 URL only). So it is an older version, but a working version.

## Working Locally
To test local changes, you can run ./bin/waho instead of using the phar.