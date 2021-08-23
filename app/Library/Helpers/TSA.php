<?php

class TSA
{
    public function __construct()
    {

    }

    public function generate($userId)
    {
        $num = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        //$num = [3, 4];

        $tsaB = $userId;

        for ($i = 0; $i < (7 - strlen($userId)); $i++) {
            shuffle($num);
            $tsaB = $num[array_rand($num)] . $tsaB;
        }

        if(substr($tsaB, 0, 1) == 0){
            $first_number = rand(1, 9);
            $tsaB = $first_number.''.substr($tsaB, 1);
        }
        
        $tsa = 'N' . $tsaB;

        if ($this->checkTSA($tsa)) {
            $this->generate($userId);
        } else {
            return $tsa;
        }
    }

    private function checkTSA($tsa)
    {
        $tsaExists = \App\Models\User::where('distid', $tsa)->count();
        if ($tsaExists) {
            return true;
        }
        return false;
    }
}