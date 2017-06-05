<?php
namespace BookTest\Controller;

use Book\Controller\BookController;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Zend\Stdlib\ArrayUtils;

class BookControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../../../config/application.config.php'
        );
        parent::setUp();

    }

    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/book');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Book');
        $this->assertControllerName('Book\Controller\Book');
        $this->assertControllerClass('BookController');
        $this->assertMatchedRouteName('book');
    }

    public function testSelectActionRedirectEmptyPost(){
        $this->dispatch('/book/select');
        $this->assertRedirectTo('/');
    }

    public function testOverviewActionRedirectEmptyPost(){
        $this->dispatch('/book/overview');
        $this->assertRedirectTo('/');
    }

    public function testSelectActionValidPost(){
        $postData = [
            'trip-options'      => 'return',
            'departure'         => 'AGP',
            'arrival'           => 'BRU',
            'departure-date'    => '11/06/2017',
            'return-date'       => '12/06/2017',
            'adults'            => '2 Adults',
            'children'          => '1 Child',
            'infant'            => '1 Baby'
        ];
        $this->dispatch('/book/select','POST',$postData);
        $this->assertResponseStatusCode(200);
        $postData = [
            'outbound'      => 0,
            'return'        => 0
        ];
        $this->dispatch('/book/overview','POST',$postData);
        $this->assertResponseStatusCode(200);

    }

}