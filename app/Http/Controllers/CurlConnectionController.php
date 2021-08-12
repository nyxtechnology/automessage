<?php


namespace App\Http\Controllers;


use Illuminate\Support\Facades\Log;

class CurlConnectionController
{
    private $curl;

    public function __construct()
    {
        $this->curl = curl_init();
    }

    private function testCurl(){
        $result = false;
        try {
            if (FALSE === $this->curl)
                throw new \Exception('Failed to initialize curl.');
            $result =  true;
        }catch (\Exception $e){
            Log::error('CurlConnectionController: ' . $e->getMessage());
        }finally {
            return $result;
        }
    }

    public function sendMessage($endpoint, $hearder = null, $data){
        if($this->testCurl()) {
            try {
                curl_setopt($this->curl, CURLOPT_URL, $endpoint);
                curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($this->curl, CURLOPT_TIMEOUT, 100);
                curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($this->curl, CURLOPT_POST, true);
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
                if($hearder)
                    curl_setopt($this->curl, CURLOPT_HTTPHEADER, $hearder);
                $content = curl_exec($this->curl);
                if (FALSE === $content)
                    throw new \Exception(curl_error($this->curl), curl_errno($this->curl));
                curl_close($this->curl);
                return $content;
            } catch (\Exception $e) {
                Log::error('CurlConnectionController: ' . $e->getMessage() . ' When you try to send the Json: ' . json_encode($data));
                return $e->getMessage();
            }
        }
    }
}
