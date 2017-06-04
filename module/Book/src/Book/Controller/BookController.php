<?php
namespace Book\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Book\Model;
use Book\Form\RouteForm;
use Book\Form\SelectForm;
use Zend\Json\Json;
use Zend\Form\Element\Csrf;
use Book\Library\BookRequest as BookRequest;
use Zend\Session\Container as Container;

class BookController extends AbstractActionController
{

    public function indexAction()
    {
        $session = new Container('base');
        if($session->offsetExists('table')){
            $session->offsetUnset('table');
        }
        if($session->offsetExists('time')){
            $session->offsetUnset('time');
        }

        $config = $this->getServiceLocator()->get('Config');
        $book_constants = $config['book_constants'];
        $route = new Model\Route($book_constants['endpoint']['routes']);
        $bookRequest = new BookRequest($book_constants,$route);

        $data = $bookRequest->getData();

        $form = new RouteForm();
        $csrf = new Csrf('csrf');
        $form->add($csrf);

        return new ViewModel(compact('data','form'));

    }

    public function selectAction()
    {
        $request = $this->getRequest();
        $table = array();

        if ($request->isPost()) {

            $config = $this->getServiceLocator()->get('Config');
            $book_constants = $config['book_constants'];

            $timeUsed = 0;

            $session = new Container('base');
            $existTable = $session->offsetExists('table');
            if(!$existTable){
                $requestData = $request->getPost();
                $validator = new Model\AvaibilityValidator($requestData);

                $flights = new Model\Availability($book_constants['endpoint']['availability'],$validator);
                $bookRequest = new BookRequest($book_constants,$flights);
                $table = $bookRequest->getData();

                $session->offsetSet('table', $table);
                $session->offsetSet('time', time());

            }else{
               $table = $session->offsetGet('table');
               $time =  $session->offsetGet('time');
               $timeUsed = (time() - $time) / 60;
            }
        }
        if(empty($table) || $timeUsed > $book_constants['session_limit']){
            return $this->redirect()->toUrl('/');
        }
        $load_css =  ['https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css'];
        $load_js = [
                'https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js',
                '/book/js/select.js'
            ];
        $this->layout()->load_css = $load_css;
        $this->layout()->load_js = $load_js;

        $form = new SelectForm();
        $csrf = new Csrf('csrf');
        $form->add($csrf);

        return new ViewModel(compact('table','form'));
    }

    public function overviewAction()
    {
        $request = $this->getRequest();
        $data = [];
        if ($request->isPost()) {
            $requestData = $request->getPost();
            $outbound = $requestData['outbound'];
            $return = $requestData['return'];

            $session = new Container('base');
            $existTable = $session->offsetExists('table');

            if($existTable){
                $table = $session->offsetGet('table');
                $overview = new Model\Overview($table,$outbound,$return);
                $data = $overview->extractResults();
            }
        }
        if(empty($data)){
            return $this->redirect()->toUrl('/');
        }
        return new ViewModel(compact('data'));
    }


    public function scheduleAction()
    {
        if($this->isAjaxRequest() && $data = $this->getRequest()->getPost()){
            $config = $this->getServiceLocator()->get('Config');
            $departure = $data['departure'];
            $arrival = $data['arrival'];

            $config = $this->getServiceLocator()->get('Config');
            $book_constants = $config['book_constants'];
            $schedule = new Model\Schedule($book_constants['endpoint']['schedules'],$departure,$arrival);
            $bookRequest = new BookRequest($book_constants,$schedule);

            echo Json::encode($bookRequest->getData());
            die;
        }

        echo json_encode('an error occurred, please try again later');
        die;
    }

    /**
     *
     * @return bool
     */
    private function isAjaxRequest()
    {
        return ( ! empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }
}