<?php

namespace App\Api;

readonly class Pager
{
    /**
     * @var int
     */
    public int $limit;

    /**
     * @var int
     */
    public int $page;

    /**
     * @var int
     */
    public int $offset;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        // no need to have negative page number
        $this->page = isset($data['page']) && intval($data['page']) > 0 ? intval($data['page']) : 0;

        // rows number per page
        $this->limit = isset($data['limit']) && intval($data['limit']) > 0 ? intval($data['limit']) : 500;

        // calculated rows limit for db
        $this->offset = $this->page * $this->limit;
    }
}