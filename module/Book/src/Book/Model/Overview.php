<?php
namespace Book\Model;

class Overview{
    private $_table;
    private $_outbound;
    private $_return;
    public function __construct($table,$outbound,$return)
    {
        $this->_table = $table;
        $this->_outbound = $outbound;
        $this->_return = $return;
    }

    public function extractResults(){
        $result = [
            'outbound'      => [
                'origin'       => '',
                'destiny'       => '',
                'price'         => 0,
                'seats'         => 0,
                'title_date'    => '',
                'time'          => '',
                'date'          => '',
             ],
            'return'        => [
                'origin'       => '',
                'destiny'       => '',
                'price'         => 0,
                'seats'         => 0,
                'title_date'    => '',
                'time'          => '',
                'date'          => '',
            ],
            'reservations'  => $this->_table['total_reservations'],
            'adults'        => $this->_table['adults'],
            'children'      => $this->_table['children'],
            'infant'        => $this->_table['infant'],
            'total_price'   => 0,
            'is_return'        => $this->_table['is_return']
        ];

        $total_price = 0;
        if(is_numeric($this->_outbound) && isset($this->_table['departure']["rows"][(int)$this->_outbound])){
            $result['outbound'] = $this->buildData($this->_table['departure']["rows"][(int)$this->_outbound]);
            $result['outbound']['title_date'] = $this->_table['departure']['title_date'];
            $total_price += ($result['outbound']['price'] * $this->_table['total_reservations']);
        }
        if(is_numeric($this->_return) && isset($this->_table['return']["rows"][(int)$this->_return])){
            $result['return'] = $this->buildData($this->_table['return']["rows"][(int)$this->_return],true);
            $result['return']['title_date'] = $this->_table['departure']['title_date'];
            $total_price += ($result['return']['price'] * $this->_table['total_reservations']);
        }

        setlocale(LC_MONETARY, 'be_BE');
        $result['total_price'] = money_format('%.2n', $total_price);
        return $result;
    }

    public function buildData($info,$is_return=false){
        $origin = isset($info["depart"]["airport"]["name"]) ? $info["depart"]["airport"]["name"] : '';
        $destiny = isset($info["arrival"]["airport"]["name"]) ? $info["arrival"]["airport"]["name"] : '';
        return [
            'origin'       => $is_return ? $destiny : $origin,
            'destiny'       => $is_return ? $origin : $destiny,
            'price'         => isset($info["price"]) ? $info["price"] : 0,
            'seats'         => isset($info["seatsAvailable"]) ? $info["seatsAvailable"] : 0,
            'time'          => isset($info["datetime"]) ? date_format(date_create_from_format('H:i:s', substr(strstr($info["datetime"],'T'),1)), 'H:i') : '',
            'date'          => isset($info["date"]) ? $info["date"] : '',
        ];
    }
}