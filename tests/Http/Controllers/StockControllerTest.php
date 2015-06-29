<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class StockControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreateInvalidInput()
    {
        $post = $this->post(
            '/api/stock',
            [
                'product' => '',
                'warehouse' => '',
                'count' => 'Not a number...']);

        $response = $post->response;

        $this->assertEquals(400, $response->getStatusCode(), '400 status code not returned');

        $data = json_decode($response->getContent());
        $this->assertTrue(isset($data->errorFields->product), 'Product was not seen as invalid.');
        $this->assertTrue(isset($data->errorFields->warehouse), 'Warehouse was not seen as invalid.');
        $this->assertTrue(isset($data->errorFields->count), 'Count was not seen as invalid.');
    }

    public function testCreateSuccess() {
        $post = $this->post(
            '/api/stock',
            [
                'product' => 'Scissors',
                'warehouse' => 'Utah Location',
                'count' => 10]);

        $response = $post->response;

        $this->assertEquals(200, $response->getStatusCode());

        $stock = DB::table('stock')
            ->join('product', 'product.id', '=', 'stock.product_id')
            ->join('warehouse', 'warehouse.id', '=', 'stock.warehouse_id')
            ->where('product.name', 'Scissors')
            ->where('warehouse.name', 'Utah Location')
            ->select('stock.*')
            ->first();
        $this->assertTrue(!empty($stock));
        $this->assertEquals(10, $stock->count);
    }

    public function testIndexSuccess() {
        $get = $this->get('/api/stock');

        $this->assertResponseOk();

        $data = json_decode($get->response->getContent());
        $this->assertTrue(count($data) > 0, 'Stocks were not found');
        $this->assertTrue(isset($data[0]->count), 'Stock count was not set');
    }
}
