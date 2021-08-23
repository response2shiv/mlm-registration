<?php

namespace Tests\Unit;

use App\Services\Twilio;
use Tests\TestCase;
use Twilio\Exceptions\ConfigurationException;

class MailGunTest extends TestCase
{
    public function testSendSms()
    {
        $mobile = '+15149736420';
        $message = 'This is a Twilio test!';

        try {
            $twilio = Twilio::sendSMS($mobile, $message);
        } catch (ConfigurationException $e) {
            echo $e->getMessage();
        }
    }

    public function testSendEnrollmentSuccessMessage()
    {
        $mobile = '+15149736420';
        $sponsorTsa = 'TSA4459813';
        $userId     = '59813';

        try {
            Twilio::sendEnrollmentSuccessMessage($sponsorTsa, $userId);
        } catch (ConfigurationException $e) {
            echo $e->getMessage();
        }
    }

}
