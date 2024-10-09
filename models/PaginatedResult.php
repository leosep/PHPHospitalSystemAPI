<?php

class PaginatedResult {
    public $data;
    public $totalRecords;
    public $page;
    public $pageSize;

    public function __construct($data, $totalRecords, $page, $pageSize) {
        $this->data = $data;
        $this->totalRecords = $totalRecords;
        $this->page = $page;
        $this->pageSize = $pageSize;
    }
}
