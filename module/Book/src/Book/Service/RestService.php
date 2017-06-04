<?php


namespace Book\Service;


class RestService implements RestServiceInterface
{
    protected $_username;
    protected $_password;
    protected $_endpoint;

    public function __construct($username,$password,$endpoint)
    {
        $this->_username = $username;
        $this->_password = $password;
        $this->_endpoint = $endpoint;
    }

    public function sendRequest($context){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$this->_endpoint.'/'.$context);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->_username:$this->_password");
        $result=curl_exec ($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);

        if($status_code!=200){
            throw new ResponseError($context);
        }

        return $result;
    }
}