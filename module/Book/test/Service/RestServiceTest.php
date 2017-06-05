<?php
use Book\Service\RestService;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Zend\Stdlib\ArrayUtils;

class RestServiceTest extends AbstractHttpControllerTestCase{

    protected $traceError = true;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../../../config/application.config.php',
            include __DIR__ . '/../../config/module.config.php'
        );
        parent::setUp();

    }

    /**
    * @expectedException Book\Service\ResponseError
    */
    public function testResponseError()
    {
        $restService = new RestService('FakeUser','FakePass','http://www.fakeurl.com');
        $restService->sendRequest('/noone');
    }

    public function testResponseValid(){
        $config = $this->getApplicationServiceLocator()->get('Config');
        $book_constants = $config['book_constants'];

        $restService = new RestService($book_constants['auth']['username'],$book_constants['auth']['password'],$book_constants['endpoint']['base_url']);
        $response = $restService->sendRequest($book_constants['endpoint']['routes']);
        $data = json_decode($response, true);
        $this->assertArrayHasKey('flightroutes', $data);
    }


}