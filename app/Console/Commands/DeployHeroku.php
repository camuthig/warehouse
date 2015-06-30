<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeployHeroku extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:heroku';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deploy the web application to the Heroku server';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $herokuOutput = [];
        $herokuConfig = [];
        exec('heroku apps:info -s', $herokuOutput);
        foreach ($herokuOutput as $line) {
            $var = explode("=", $line);
            $herokuConfig[$var[0]] = $var[1];
        }

        if (!$this->confirm("This command will deploy to ${herokuConfig['name']}. Are you sure you want to continue?")) {
            $this->info('Not deploying to ' . $herokuConfig['name']);
            return;
        }
        // Set maintenance mode
        $this->info('Setting the server to maintenance mode.');
        exec('heroku maintenance:on');

        // Check the DB URL and set it on the environment variables
        $url = parse_url(exec('heroku config:get DATABASE_URL'));
        $this->info('Enforcing database configuration on server.');
        exec('heroku config:set DB_CONNECTION=pgsql');
        exec('heroku config:set DB_HOST=' . $url['host']);
        exec('heroku config:set DB_PORT=' . $url['port']);
        exec('heroku config:set DB_DATABASE=' . substr($url["path"], 1));
        exec('heroku config:set DB_USERNAME=' . $url['user']);
        exec('heroku config:set DB_PASSWORD=' . $url['pass']);

        // Set basic locale values
        $this->info('Setting basic configuration values (APP_LOCALE, APP_FALLBACK_LOCALE, CACHE, SESSION and QUEUE drivers)');
        exec('heroku config:set APP_LOCALE=en');
        exec('heroku config:set APP_FALLBACK_LOCALE=en');

        // Set the Queue, Cache and Session drivers
        exec('heroku config:set CACHE_DRIVER=array');
        exec('heroku config:set SESSION_DRIVER=array');
        exec('heroku config:set QUEUE_DRIVER=iron');

        // Push the latest code
        $this->info('Pushing latest code to Heroku.');
        exec('git push heroku master');

        // Run the migrations
        $this->info('Running migrations on Heroku database.');
        exec('heroku run ./artisan migrate --force');

        // Turn off maintence mode
        $this->info('Turning off the server maintenance mode.');
        exec('heroku maintenance:off');

        $this->comment('Be sure to set APP_ENV, APP_DEBUG and APP_KEY manually for the environment.');
        $this->comment('For the queue, be sure to set IRON_TOKEN, IRON_PROJECT, IRON_HOST and IRON_API_VERSION.');
    }
}
