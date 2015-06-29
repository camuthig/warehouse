<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreateInvalidInput()
    {
        $post = $this->post(
            '/api/products',
            [
                'name'       => 'Laptop',
                'dimensions' => '',
                'weight'     => '']);

        $response = $post->response;

        $this->assertEquals(400, $response->getStatusCode(), '400 status code not returned');

        $data = json_decode($response->getContent());
        $this->assertTrue(isset($data->errorFields->name), 'Name was not seen as invalid.');
        $this->assertTrue(isset($data->errorFields->dimensions), 'Dimensions was not seen as invalid.');
        $this->assertTrue(isset($data->errorFields->weight), 'Weight was not seen as invalid.');
    }

    public function testCreateSuccess() {
        $post = $this->post(
            '/api/products',
            [
                'name'       => 'Test Product',
                'dimensions' => '1x1x1',
                'weight'     => '1g']);

        $response = $post->response;

        $this->assertEquals(200, $response->getStatusCode());

        $product = DB::table('product')->where('name', 'Test Product')->first();
        $this->assertTrue(!empty($product));
    }

    public function testIndexSuccess() {
        $get = $this->get('/api/products');

        $this->assertResponseOk();

        $data = json_decode($get->response->getContent());
        $this->assertTrue(count($data) > 0, 'Products were not found');
        $this->assertTrue(isset($data[0]->name), 'Product name was not set');
    }
}
