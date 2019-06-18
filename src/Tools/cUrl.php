<?php

namespace App\Tools;


class cUrl
{
    public $ch;
    protected $url;
    protected $response;
    protected $info;
    public function __construct(String $url)
    {
        $this->ch = curl_init();
        $this->url = $url;
    }

    public function initialize()
    {
        curl_setopt_array($this->ch, [
            CURLOPT_URL            => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 20,
            CURLOPT_HEADER  => 0,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0',
            CURLOPT_FOLLOWLOCATION => true
        ]);
        return $this;
    }


    public function execute()
    {
        curl_exec($this->ch);
        return $this;
    }

    public function getError()
    {
        return curl_errno($this->ch);
    }


    public function getInfo()
    {
        return curl_getinfo($this->ch);
    }
    public function close()
    {
        curl_close($this->ch);
    }

}