<?php

declare(strict_types=1);

namespace Controller;

use Ypf\Controller\Rest;

class Index extends Rest
{
    public function index($request)
    {
        $data = ['status' => true, 'code' => 200, 'message' => 'succeful'];
        $headers = [];
        $headers['content-type'] = 'application/json';
        $payload = json_encode($data);

        //print_r($this->request);

        return $this->json($payload);
    }
}
