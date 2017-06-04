<?php

namespace Book\Model;

use Book\Service\RestService;
use Book\Model\TableInterface as TableInterface;

class Schedule implements TableInterface{

    protected $endPoint;
    private $_departure;
    private $_return;

    public function __construct($endPoint,$departure,$return)
    {
        $this->endPoint = $endPoint;
        $this->_departure = $departure;
        $this->_return = $return;
    }

    public function getEndpoint(){
        $context = $this->endPoint;
        $context .= "?departureairport={$this->_departure}&destinationairport={$this->_return}";
        $context .= "&returndepartureairport={$this->_return}&returndestinationairport={$this->_departure}";
        return $context;
    }

    public function buildStructure($data){
        $flightSchedules = isset($data["flightschedules"]) ? $data["flightschedules"] : [];

        $today = date('Y-m-d');

        $validDepartedDates = [];
        if(isset($flightSchedules['OUT']) && is_array($flightSchedules['OUT'])){
            $validDepartedDates = $this->getValidDates($flightSchedules['OUT']);
        }

        $validReturnDates = [];
        if(isset($flightSchedules['RET']) && is_array($flightSchedules['RET'])){
            $validReturnDates = $this->getValidDates($flightSchedules['RET']);
        }

        return [
            'departure_dates' => $validDepartedDates,
            'return_dates'  => $validReturnDates
        ];
    }


    private function getValidDates($list){
        $validDates = [];
        $today = date('Y-m-d');
        foreach($list as $schedule){
            if(isset($schedule['date']) && $schedule['date']>=$today){
                $validDates[] = $schedule['date'];
            }
        }
        return $validDates;
    }
}