<?php

declare(strict_types=1);

namespace Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ypf\Controller\Controller;
use Psr\Http\Server\RequestHandlerInterface;
use GuzzleHttp\Psr7\Response;

class Text extends Controller
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        static::getContainer()->get(\Psr\Log\LoggerInterface::class)->info('Hello, '.$request->getAttribute('name', 'World!'));
        $mon = $this->db->table('17mon')->limit(4)->offset(0)->orderBy('id desc')->select();
        $data = [
            'mon' => $mon,
        ];

        return $this->view->render(new Response(), '/ip.html', $data);
    }
}
