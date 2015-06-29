<?php

class TestCase extends Laravel\Lumen\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    // public function tearDown() {
    //     Mockery::close();
    //     parent::tearDown();
    // }

    public function helpMock($class){
        $mock = Mockery::mock($class);
        App::instance($class, $mock);
        return $mock;
    }

    public function helpMockSingleton($class){
        $mock = Mockery::mock($class);
        App::singleton($class, $mock);
        return $mock;
    }
}
