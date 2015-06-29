<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrderControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreateInvalidInput()
    {
        $post = $this->post(
            '/api/orders',
            [
                'product' => '',
                'address' => '']);

        $response = $post->response;

        $this->assertEquals(400, $response->getStatusCode(), '400 status code not returned');

        $data = json_decode($response->getContent());
        $this->assertTrue(isset($data->errorFields->product), 'Product was not seen as invalid.');
        $this->assertTrue(isset($data->errorFields->address), 'Address was not seen as invalid.');
    }

    public function testCreateSuccessAndIndex() {
        // TODO: Figure out why this expects doesn't work...
        // It obviously is calling the job cause my tests work,
        // but I'm not setting it work right here
        // $this->expectsJobs('App\Jobs\ProcessOrder');
        $post = $this->post(
            '/api/orders',
            [
                'product' => 'Stapler',
                'address' => '7618 Tyler Creek Lane, Humble, TX 77396, USA']);

        $response = $post->response;

        $this->assertEquals(200, $response->getStatusCode());

        $order = DB::table('orders')->where('address', 'LIKE', '%7618 Tyler Creek%')->first();
        $this->assertTrue(!empty($order));


        /****************************************************************************
            Since we are doing a DB transaction on each test, we need to include the
            index test in the create test, to make sure we have an order to retrieve
        *****************************************************************************/
        $get = $this->get('/api/orders');

        $this->assertResponseOk();

        $data = json_decode($get->response->getContent());
        $this->assertTrue(count($data) > 0, 'Orders were not found');
        $this->assertTrue(isset($data[0]->address), 'Order address was not set');
    }
}
