<?php

namespace Book\Model;

use Book\Service\RestService;
use Book\Model\TableInterface as TableInterface;

class Route implements TableInterface{
    protected $endPoint;
    public function __construct($endPoint)
    {
        $this->endPoint = $endPoint;
    }


    public function getEndpoint(){
        return $this->endPoint;
    }

    public function buildStructure($data){
        $flightRoutes = isset($data["flightroutes"]) ? $data["flightroutes"] : [];

        $departures = [];
        $info = [];
        foreach($flightRoutes as $route){
            $departures[$route['DepCode']][] = $route['RetCode'];
            if(!isset($info[$route['DepCode']])){
                $info[$route['DepCode']] = ['name'=>$route['DepName'],'country'=>$route['DepCountry'],'label'=>$this->getRouteLabel($route)];
            }
        }

        return [
            'departures' => $departures,
            'info'       => $info
        ];
    }

    private function getRouteLabel($route){
        return sprintf('%s (%s), %s', $route['DepName'], $route['DepCode'],$route['DepCountry']);
    }
}