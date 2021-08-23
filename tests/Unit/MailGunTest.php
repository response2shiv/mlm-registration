<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Mail;
use SendMail;
use Tests\TestCase;

class MailGunTest extends TestCase
{
    public function testMail()
    {
        $data = [
            'firstName' => 'Andrew',
            'enrolleeFirstName' => 'John',
            'enrolleeLastName' => 'Smith',
            'enrolleeEmail' => 'john@smith.com',
            'enrolleePhone' => '514-772-2343',
        ];

        Mail::send('emails.new-enrollee-notification-email', $data, function($message) {
            $message->to('andrew@simplyphp.com')->subject('You have a New Team Member');
        });
    }

    public function testRawMail()
    {
        Mail::raw('Sending emails with Mailgun and Laravel is easy!', function($message) {
            $message->to('andrew@simplyphp.com');
        });
    }

    public function testSendEmail()
    {
        sendMail::sendDistributorRegistrationMail('TSA3484873', '286');
    }
}
