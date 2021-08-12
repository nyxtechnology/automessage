<?php


namespace Tests\Feature;


use App\Http\Controllers\JadLogController;
use Tests\Faker\JadLogFaker;
use Tests\TestCase;

class JadLogControllerTest extends TestCase
{
    public static $shipmentId;

    public function testCreateOrder()
    {
        $jadLogFaker = new JadLogFaker();
        $jadLogFaker->orderFaker(['event' => 'createOrder']);
        $message = $jadLogFaker->getMessage();
        $message = ['params' => $message->metadata, 'webhook' => $message->webhook];
        $jadLog = new JadLogController();
        $result = $jadLog->createOrder($message);
        self::$shipmentId = json_decode($result)->shipmentId;
        $this->assertEquals('Solicitacao inserida com sucesso.', json_decode($result)->status);
    }

    public function testDeleteOrder()
    {
        $jadLogFaker = new JadLogFaker();
        $jadLogFaker->deleteOrderFaker(['shipmentId' => self::$shipmentId]);
        $message = $jadLogFaker->getMessage();
        $message = ['params' => $message->metadata, 'webhook' => $message->webhook];
        $jadLog = new JadLogController();
        $result = $jadLog->deleteOrder($message);
        $this->assertEquals('Cancelamento realizado com sucesso!', json_decode($result)->status);
    }

    public function testTrackingOrder()
    {
        $jadLogFaker = new JadLogFaker();
        $jadLogFaker->trackingOrderFaker(['shipmentId' => self::$shipmentId]);
        $message = $jadLogFaker->getMessage();
        $message = ['params' => $message->metadata, 'webhook' => $message->webhook];
        $jadLog = new JadLogController();
        $result = $jadLog->trackingOrder($message);
        $this->assertEquals(self::$shipmentId, json_decode($result)->consulta[0]->shipmentId);
    }
}
