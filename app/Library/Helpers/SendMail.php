<?php

use App\Models\MailTemplate;
use App\Models\User;

class SendMail
{
    public function __construct()
    {

    }

    public static function sendDistributorRegistrationMail($parentTSA, $userId)
    {
        try {
            $parentInfo = \App\Models\User::where('distid', $parentTSA)->first();
            $distributor = \App\Models\User::where('id', $userId)->first();
            $firstName = $distributor->firstname;
            $lastName = $distributor->lastname;
            $email = $distributor->email;
            $tsa = \App\Models\User::find($userId)->distid;
            $mobileNumber = $distributor->mobilenumber;
            
            //
            $parentFirstName = $parentInfo->firstname;
            $parentLastName = $parentInfo->lastname;
            $parentEmail = $parentInfo->email;
            //$sponsorEmailSubject = $parentFirstName . ' You have a New Team Member!';

            $template = MailTemplate::getRec(MailTemplate::TYPE_NEW_DISTIBUTOR_ENROLLMENT);
            if ($template->is_active == 1) {
                
                $subject = $template->subject;
                $content = $template->content;
                $content = str_replace("<firstName>", $firstName, $content);
                $content = str_replace("<lastName>", $lastName, $content);
                $content = str_replace("<username>", strtolower($distributor->username), $content);
                $content = str_replace("<tsa>", $tsa, $content);
                $content = str_replace("<email>", $email, $content);
                $content = str_replace("<mobileNumber>", $mobileNumber, $content);
                $content = str_replace("<parentFirstName>", $parentFirstName, $content);
                $content = str_replace("<parentLastName>", $parentLastName, $content);
                $content = str_replace("<parentEmail>", $parentEmail, $content);
                $content = nl2br($content);
                $data['content'] = $content;

                Mail::send('mail_template.base_template', $data, function ($message) use ($parentEmail, $subject) {
                    $message->to($parentEmail)->subject($subject);
                });
            }


            //Welcome Email
            // \Mail::send('emails.sponsor-email', $data, function ($message) use (
            //     $firstName,
            //     $lastName,
            //     $mobileNumber,
            //     $parentFirstName,
            //     $parentEmail,
            //     $sponsorEmailSubject
            // ) {
            //     $message->to(
            //         $parentEmail,
            //         $firstName,
            //         $lastName,
            //         $mobileNumber,
            //         $parentFirstName
            //     )->subject($sponsorEmailSubject);
            // });
        } catch (\Exception $ex) {
            Log::critical('Error occurred while trying to send message. Error:' . $ex->getMessage());
        }
    }
}
