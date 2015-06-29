<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class WarehouseControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function __constructor() {
        $mockMaps = $this->helpMockSingleton('App\Services\MapsService');
        parent::_constructor();
    }

    public function testCreateInvalidInput()
    {
        $post = $this->post(
            '/api/warehouses',
            [
                'name'       => 'Utah Location',
                'address' => '']);

        $response = $post->response;

        $this->assertEquals(400, $response->getStatusCode(), '400 status code not returned');

        $data = json_decode($response->getContent());
        $this->assertTrue(isset($data->errorFields->name), 'Name was not seen as invalid.');
        $this->assertTrue(isset($data->errorFields->address), 'Address was not seen as invalid.');
    }

    public function testCreateSuccess() {
        $post = $this->post(
            '/api/warehouses',
            [
                'name'       => 'New Location',
                'address' => '7618 Tyler Creek Lane, Humble, TX 77396, USA']);

        $response = $post->response;

        $this->assertEquals(200, $response->getStatusCode());

        $warehouse = DB::table('warehouse')->where('name', 'New Location')->first();
        $this->assertTrue(!empty($warehouse));
    }

    public function testIndexSuccess() {
        $get = $this->get('/api/warehouses');

        $this->assertResponseOk();

        $data = json_decode($get->response->getContent());
        $this->assertTrue(count($data) > 0, 'Warehouses were not found');
        $this->assertTrue(isset($data[0]->name), 'Warehouse name was not set');
    }
}
