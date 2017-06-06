<?php

namespace Book\Model;

use Book\Service\RestService;
use Book\Model\TableInterface as TableInterface;
use Book\Model\FilterInterface as FilterInterface;

class Availability implements TableInterface{

    protected $endPoint;
    protected $selectData;

    public function __construct($endPoint,FilterInterface $filter)
    {
        $this->endPoint = $endPoint;
        $this->selectData = $filter->getData();
    }

    public function getEndpoint(){
        $context = $this->endPoint;
        $departure = $this->selectData['departure'];
        $arrival = $this->selectData['arrival'];

        $adults = $this->selectData['adults'];
        $children = $this->selectData['children'];
        $infant = $this->selectData['infant'];

        $departureDate = $this->selectData['departure-date'];

        $context .= "?departuredate={$departureDate}&departureairport={$departure}&destinationairport={$arrival}&adults={$adults}&children={$children}&infants={$infant}";

        if($this->selectData['trip-options']=='return'){
            $returnDate = $this->selectData['return-date'];
            $context .= "&returndepartureairport={$arrival}&returndestinationairport={$departure}&returndate={$returnDate}";
        }

        return $context;
    }

    public function buildStructure($data){

        $flights = isset($data["flights"]) ? $data["flights"] : [];

        $departures = isset($flights['OUT']) ? $flights['OUT'] : [];
        $return = isset($flights['RET']) ? $flights['RET'] : [];
        $minPriceDeparture = !empty($departures) ? min( array_column( $flights['OUT'], 'price' ) ) : 0;
        $minPriceReturn = !empty($return) ? min( array_column( $flights['RET'], 'price' ) ) : 0;
        $departureDateFormat = date_format(date_create_from_format('Ymd', $this->selectData['departure-date']), 'l, j F Y');
        $returnDateFormat = isset($this->selectData['return-date']) ? date_format(date_create_from_format('Ymd', $this->selectData['return-date']), 'l, j F Y') : '';

        return [
            'departure' => [
                'min_price' => $minPriceDeparture,
                'title_date'=> $departureDateFormat,
                'rows'      => $departures
            ],
            'return' => [
                'min_price' => $minPriceReturn,
                'title_date'=> $returnDateFormat,
                'rows'      => $return
            ],
            'total_reservations'=> $this->selectData['total_passengers'],
            'adults'            => $this->selectData['adults'],
            'children'          => $this->selectData['children'],
            'infant'            => $this->selectData['infant'],
            'is_return'         => $this->selectData['is_return']
        ];
    }



}