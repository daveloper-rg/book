<?php
namespace Book\Model;
use Book\Model\FilterInterface as FilterInterface;

class AvaibilityValidator implements FilterInterface{

    protected $requestData;
    public function __construct($data)
    {
        $this->requestData = $data;
    }

    public function getData(){
        $data = $this->requestData;
        $requiredFields = ['trip-options','departure','arrival','departure-date','adults','children','infant'];
        foreach($requiredFields as $field){
            if(!isset($data[$field])){
                throw new Exception('Data not correct');
            }
        }
        if($data['trip-options']=='return' && !isset($data['return-date'])){
            throw new Exception('Data not correct');
        }

        $data['return-date'] = isset($data['return-date']) ?  date_format(date_create_from_format('d/m/Y', $data['return-date']), 'Ymd') : '';
        $adults = filter_var($data['adults'], FILTER_SANITIZE_NUMBER_INT);
        $children = filter_var($data['children'], FILTER_SANITIZE_NUMBER_INT);
        $infant = filter_var($data['infant'], FILTER_SANITIZE_NUMBER_INT);

        $data['adults'] = $adults;
        $data['children'] = $children;
        $data['infant'] = $infant;
        $data['is_return'] = $data['trip-options']=='return';

        $totalPassengers = $adults + $children + $infant;

        if($totalPassengers==0){
            throw new Exception('Data not correct');
        }

        $data['total_passengers'] = $totalPassengers;
        $data['departure-date'] = date_format(date_create_from_format('d/m/Y', $data['departure-date']), 'Ymd');

        return $data;
    }


}