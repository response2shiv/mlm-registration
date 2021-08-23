<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CurrencyConverter;
use App\Models\OrderConversion;
use Log;

class Products extends Model
{
    protected $table = 'products';

    const TICKET_PURCHASE_PRODUCT = 38;
    const ISBO = 1;
    const BASIC = 2;
    const VISIONARY = 3;
    const FX = 10;
    
    public static function getProductLevel($productId)
    {
        switch ($productId) {
            case 1:
                return 'level0';
                break;
            case 2:
                return 'level1';
                break;
            case 3:
                return 'level2';
                break;
            case 10:
                return 'level3';
                break;
            default:
                return false;
                break;
        }
    }

    protected $primaryKey = 'id';

    public function getDateFormat()
    {
         return 'Y-m-d H:i:s.u';
    }

    public static function getSubscriptionProducts()
    {
        $productResults = static::query()->whereIn('id', [1,2,3,10])->get();

        $products = [];

        /**
         * @var Products $productResult
         */
        foreach ($productResults as $productResult) {
            $id = $productResult->id;
            $products[$id] = $productResult->toArray();
        }


        return $products;
    }

    public static function getSubscriptionProductsWithConversion($country)
    {
        $productResults = static::query()->whereIn('id', [1,2,3,4,10])->get();

        $products = [];

        if(!isset($country)){
            $country = "US";
        }

        if(env('FORCE_USD')){
            $country = "USD";
        }

        /**
         * @var Products $productResult
         */
        foreach ($productResults as $productResult) {
            $id = $productResult->id;

            //Converting the currency
            $convertObject = CurrencyConverter::convertCurrency(number_format($productResult->price,2,'',''), $country, null);
            $orderConversion = new OrderConversion();

            //Saving Order Conversion
            $orderConversion->fill([
                'session_id' => session_id(),
                'original_amount' => number_format($productResult->price,2,'',''),
                'original_currency' => $country,
                'converted_amount' => $convertObject["amount"],
                'converted_currency' => $convertObject['currency'],
                'exchange_rate' => $convertObject['exchange_rate'],
                'expires_at' => now()->addMinutes(30)
            ]);

            $orderConversion->save();

            $products[$id] = $productResult->toArray();

            //Preparing data to show the correct conversion
            $products[$id]['conversion']['display_amount'] = $convertObject['display_amount'];
            $products[$id]['conversion']['converted_currency'] = $convertObject['currency'];
            $products[$id]['conversion']['exchange_rate'] = $convertObject['exchange_rate'];
            $products[$id]['conversion']['order_conversion_id'] = $orderConversion->id;
            $products[$id]['conversion']['expiration'] = $orderConversion->expires_at->timestamp;

        }
        session(['products_conversion' => $products]);
        // Log::info("conversion array -> ",$products);
        return $products;
    }

    public static function getShippingValue()
    {
        if(session('product_id') == self::BASIC && session('billing_country')->code == 'US '){
            return 10.00;
        }elseif(session('product_id') == self::BASIC && session('billing_country')->code != 'US '){
            return 15.00;
        }else{
            return 00.00;
        }
    }
}
