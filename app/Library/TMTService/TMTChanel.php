<?php

namespace App\Library\TMTService;

class TMTChanel extends TMTPayment
{
    private $id;

    private $name;

    private $currencies;

    /**
     * TMTChanel constructor.
     * @param $id
     * @param array $channelsList
     * @param $name
     * @param $currencies
     */
    public function __construct($id, $name, $currencies)
    {
        $this->id = $id;
        $this->name = $name;
        $this->currencies = $currencies;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCurrencies()
    {
        return $this->currencies;
    }












}