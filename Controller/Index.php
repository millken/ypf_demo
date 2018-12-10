<?php

declare(strict_types=1);

namespace Controller;

use Ypf\Controller\Rest;

class Index extends Rest
{
    public function index($request)
    {
        $data = ['status' => true, 'code' => 200, 'message' => 'succeful', 'pid' => getmypid()];
        $headers = [];
        $headers['content-type'] = 'application/json';

        $data['table'] = $this->db->select('area', '*', [
            [
                'field' => 'en',
                'operator' => 'is not null',
            ],
            [
                'field' => 'id',
                'operator' => '>',
                'value' => 1,
            ],
            [
                'field' => 'zh',
                'operator' => 'LIKE',
                'value' => '%华%',
            ],
            [
                'connector' => 'OR',
                'subexpression' => [
                    'id' => 2,
                    [
                        'connector' => 'OR',
                        'field' => 'zh',
                        'operator' => 'LIKE',
                        'value' => '%北%',
                    ],
                ],
            ],
        ]
        );

        $data['table2'] = $this->db->select([
            'table' => ['area', 'country'],
            'join' => 'left join',
            'on' => ['area.country_id', 'country.id'],
        ], 'area.*', [
            [
                'field' => 'area.en',
                'operator' => 'is not null',
            ],
            [
                'field' => 'area.id',
                'operator' => '>',
                'value' => 1,
            ],
            [
                'field' => 'area.zh',
                'operator' => 'LIKE',
                'value' => '%华%',
            ],
            [
                'connector' => 'OR',
                'subexpression' => [
                    'area.id' => 2,
                    [
                        'connector' => 'OR',
                        'field' => 'area.zh',
                        'operator' => 'LIKE',
                        'value' => '%北%',
                    ],
                ],
            ],
        ]
        );

        $data['sql'] = $this->db->sql();
        $payload = json_encode($data);
        //print_r($this->request);

        return $this->json($payload);
    }
}
