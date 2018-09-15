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
        $logger = static::getContainer()->get(\Psr\Log\LoggerInterface::class);
        $logger->info('Hello, '.$request->getAttribute('name', 'World!'));

        $this->db->insert('user', [
            'name' => mt_rand(111, 3333),
            'status' => true,
            'date_added' => date('Y-m-d'),
            ]);
        $logger->info('insertID={id}', ['id' => $this->db->id()]);

        $data = $this->db->select('user', 'name,status', ['LIMIT' => [0, 3]]);

        $this->db->update('user', ['name' => 'test'], ['id' => 3]);
        $this->db->delete('user', ['id' => $this->db->id()]);
        $data = $this->db->get('user', 'name', ['id' => 3]);
        $logger->info('name={name}', ['name' => $data]);

        $data = $this->db->count('user', '*', ['id' => 3]);
        $logger->info('count={name}', ['name' => $data]);

        $logger->info($this->db->sql());

        return $this->view->render(new Response(), '/ip.html', []);
    }
}
