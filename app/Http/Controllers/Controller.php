<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function generateErrorMessageFromValidator(Validator $validator)
    {
        $errorMessage = '';

        if ($validator->fails()) {
            foreach ($validator->messages()->all() as $message) {
                $errorMessage .= "<div> - " . $message . "</div>";
            }
        }

        return $errorMessage;
    }
}
