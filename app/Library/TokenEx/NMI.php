<?php

use App\Models\OrderConversion;

class NMI {

    private $nmiGWUsername;
    private $nmiGWPassword;
    private $t1GWUsername;
    private $t1GWPassword;
    private $metroUsername;
    private $metroPassword;
    private $currencyCode;

    //

    public function __construct() {
        $mode = Config::get('const.nmi.mode');

        $this->nmiGWUsername = Config::get('const.nmi.' . $mode . '.username');
        $this->nmiGWPassword = Config::get('const.nmi.' . $mode . '.password');
        //
        $this->t1GWUsername = Config::get('const.t1.' . $mode . '.username');
        $this->t1GWPassword = Config::get('const.t1.' . $mode . '.password');
        //
        $this->payArcUsername = Config::get('const.payarc.' . $mode . '.username');
        $this->payArcPassword = Config::get('const.payarc.' . $mode . '.password');
        //
        $this->metroUsername = Config::get('const.metropolitan.' . $mode . '.username');
        $this->metroPassword = Config::get('const.metropolitan.' . $mode . '.password');

        $this->currencyCode = Config::get('const.currency_code');
    }

    public function doPayment($cardInfo, $billingInfo, $orderId, $paymentType = null, $orderConversionId = null)
    {
        $tokenEx = new \TokenEx();
        $amount = $cardInfo['order_total'] * 100;
        $amount = (int) $amount;
        if (!empty($billingInfo) && empty($billingInfo['billing_first_name'])) {
            $billingInfo['billing_first_name'] = session()->get('sponsor_information')['firstname'];
        }

        if (!empty($billingInfo) && empty($billingInfo['billing_last_name'])) {
            $billingInfo['billing_last_name'] = session()->get('sponsor_information')['lastname'];
        }

        $currency = "USD";
        if ($orderConversionId) {
            $orderConversion = OrderConversion::find($orderConversionId);

            if (!$orderConversion) {
                return array(
                    'success' => false,
                    'message' => 'Please refresh the page and try checking out again.'
                );
            }

            $originalAmount = $orderConversion->original_amount;

            // Final verification, in case someone messed up some javascript and passed an old id
            if ($originalAmount != $amount) {
                $orderConversion->delete();
                return array(
                    'success' => false,
                    'message' => 'Please refresh the page and try checking out again'
                );
            }

            $this->currencyCode = $orderConversion->converted_currency;
            $amount = $orderConversion->converted_amount;
        }

        $postData = [
            "TransactionType" => 3,
            'TransactionRequest' => [
                'gateway' => [
                    'name' => 'NmiGateway',
                    'login' => $this->nmiGWUsername,
                    'password' => $this->nmiGWPassword,
                ],
                'credit_card' => [
                    'number' => $cardInfo['credit_card_number'],
                    'month' => $cardInfo['expiry_date_month'],
                    'year' => $cardInfo['expiry_date_year'],
                    'verification_value' => $cardInfo['cvv'],
                    'first_name' => trim($billingInfo['billing_first_name']),
                    'last_name' => trim($billingInfo['billing_last_name']),
                ],
                'transaction' => [
                    'order_id' => $orderId,
                    'currency' => $this->currencyCode,
                    'amount' => $amount,
                    'billing_address' => [
                        'address1' => trim($billingInfo['apt']) . ' ' . (isset($billingInfo['address1']) ? $billingInfo['address1'] : '') . ' ' . (isset($billingInfo['address2']) ? $billingInfo['address2'] : ''),
                        'city' => trim($billingInfo['city']),
                        'state' => isset($billingInfo['state']) ? trim($billingInfo['state']) : '',
                        'zip' => trim($billingInfo['postal_code']),
                        'country' => trim($billingInfo['country']),
                    ],
                ],
            ]
        ];

        if ($paymentType == \App\Models\PaymentMethodType::TYPE_T1_PAYMENTS) {
            $postData['TransactionRequest']['gateway']['login'] = $this->t1GWUsername;
            $postData['TransactionRequest']['gateway']['password'] = $this->t1GWPassword;
        } else if ($paymentType == \App\Models\PaymentMethodType::TYPE_PAYARC) {
            $t1Override = env('PAYARC_T1_OVERRIDE', false);

            if ($t1Override) {
                $postData['TransactionRequest']['gateway']['login'] = $this->t1GWUsername;
                $postData['TransactionRequest']['gateway']['password'] = $this->t1GWPassword;
            } else {
                $postData['TransactionRequest']['gateway']['login'] = $this->payArcUsername;
                $postData['TransactionRequest']['gateway']['password'] = $this->payArcPassword;
            }
        } else if ($paymentType == \App\Models\PaymentMethodType::TYPE_METROPOLITAN) {
            $postData['TransactionRequest']['gateway']['login'] = $this->metroUsername;
            $postData['TransactionRequest']['gateway']['password'] = $this->metroPassword;
        }
        try {
            $tokeExPaymentResponseJson = $tokenEx->processTransactionAndTokenize('ProcessTransactionAndTokenize', $postData);
        } catch (\Exception $ex) {
            return [
                'success' => false,
                'message' => $ex->getMessage()
            ];
        }
        $tokeExPaymentResponse = json_decode($tokeExPaymentResponseJson);
        if (!$tokeExPaymentResponse->TransactionResult) {
            $error = (string) $tokeExPaymentResponse->Error;
            $message = (string) $tokeExPaymentResponse->Message;
            $errorMessage = !empty($error) ? $error : $message;
            return [
                'success' => false,
                'message' => $errorMessage
            ];
        }
        return [
            'success' => true,
            'order_id' => $orderId,
            'response' => json_decode($tokeExPaymentResponseJson, true)
        ];
    }

}
