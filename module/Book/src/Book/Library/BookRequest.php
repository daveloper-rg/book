<?php
namespace Book\Library;

use Book\Service\RestService;
use Zend\Json\Json;
use Book\Model\TableInterface as TableInterface;
use Zend\Session\Container;

class BookRequest{

    protected $_table;
    protected $_book_constants;

    public function __construct($book_constants,TableInterface $table)
    {
        $this->_table = $table;
        $this->_book_constants = $book_constants;
    }


    public function getData()
    {
        if (!isset($this->_book_constants['auth']['username'], $this->_book_constants['auth']['password'], $this->_book_constants['endpoint']['base_url'])) {
            throw new \Exception('Config parameters are not Correct');
        }
        $service = new RestService($this->_book_constants['auth']['username'], $this->_book_constants['auth']['password'], $this->_book_constants['endpoint']['base_url']);
        $response = $service->sendRequest($this->_table->getEndpoint());
        return $this->_table->buildStructure(Json::decode($response, Json::TYPE_ARRAY));
    }

}