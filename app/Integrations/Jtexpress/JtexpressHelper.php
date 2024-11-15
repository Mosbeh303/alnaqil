<?php

namespace App\Integrations\Jtexpress;

use App\Models\Jtexpress;
use App\Models\Shipment ;
//use App\Models\Store ;
use Illuminate\Support\Facades\Http;
use Log;
use Throwable;

//use GuzzleHttp\Client;



class JtexpressHelper{

    private  $apiAccount;
    private  $privateKey;
    private  $customerCode;
    private  $pwd ;

    public function __construct()
    {
        $this->apiAccount = env('JT_API_ACCOUNT');
        $this->privateKey = env('JT_PRIVATE_KEY');
        $this->customerCode = env('JT_CUSTOMER_CODE');
        $this->pwd = env('JT_PWD');
    }

    public static function createOrder (Shipment $shipment, $cod = 0){
        try {
            $self = new static; //OBJECT INSTANTIATION
            $apiAccount = $self->apiAccount;
            $privateKey = $self->privateKey;
            $customerCode = $self->customerCode;
            $pwd = $self->pwd;

            $ecryptedPwd = strtoupper(md5($pwd . 'jadada236t2'));
            $bizdigest = base64_encode(md5($customerCode . $ecryptedPwd . $privateKey , true));





            $receiver = array(
                'address' => $shipment->street . ', ' . $shipment->neighborhood . ', ' . $shipment->city,
                'street' => $shipment->street,
                'city' => $shipment->city,
                'mobile' => $shipment->receiver_phone,
                'mailBox' => '',
                'phone' => '',
                'countryCode' => 'KSA',
                'name' => $shipment->receiver_name,
                'company' => '',
                'postCode' => '',
                'prov' => $shipment->city,
            );

            $sender = array(
                'address' => 'Al-Azizia - Sufyan Bin Awf Street',
                'street' => 'Sufyan Bin Awf Street',
                'city' => 'Riyadh',
                'mobile' => '966590700745',
                'mailBox' => 'info@alnaqil.sa',
                'phone' => '966590700745',
                'countryCode' => 'KSA',
                'name' => 'Alnaqil Almahalli',
                'company' => 'Alnaqil Almahalli',
                'postCode' => '14513',
                'prov' => 'Riyadh',
            );

            if ($shipment->return_back == 1){
                $aux = $sender;
                $sender = $receiver;
                $receiver = $aux ;
            }


            $data = array(
                'customerCode' => $customerCode,
                'digest' => $bizdigest,
                'serviceType' => '01',
                'orderType' => '2',
                'deliveryType' => '04',
                'countryCode' => 'KSA',
                'receiver' => $receiver,
                'expressType' => 'EZKSA',
                'length' => 0,
                'weight' => $shipment->weight == 0 ? 0.5 : $shipment->weight,
                'remark' => '',
                'txlogisticId' => $shipment->number,
                'goodsType' => 'ITN1',
                'priceCurrency' => 'SAR',
                'totalQuantity' => $shipment->number_of_pieces,
                'sender' => $sender,
                'itemsValue' => $cod,
                'offerFee' => 0,
                'items' => array(
                    // array(
                    //     'englishName' => 'iphone',
                    //     'number' => 2,
                    //     'itemType' => 'ITN1',
                    //     'itemName' => 'sample',
                    //     'priceCurrency' => 'SAR',
                    //     'itemValue' => '2000',
                    //     'itemUrl' => 'http://www.baidu.com',
                    //     'desc' => 'file',
                    // ),
                ),
                'operateType' => 1,
                'payType' => 'PP_PM',
                'isUnpackEnabled' => 0,
            );

            $json = json_encode($data);

            $digest = base64_encode(md5($json . $privateKey , true));

            $response = Http::withHeaders([
                'apiAccount'   => $apiAccount,
                    'digest'       => $digest,
                    'Timestamp'    => '1638428570653',
                    'Content-Type' => 'application/x-www-form-urlencoded'
            ])->asForm()->post(env('JT_URL') . 'order/addOrder?uuid=855bfc15b747416396b187b1d5e0f254',[
                'bizContent' => $json,

            ]);

            if (json_decode($response)->code != 1)
                Log::error($response);

            return $response;

        } catch (Throwable $th) {
            Log::error($th);
        }
    }

    public static function printOrder($waybillCode){
        $self = new static; //OBJECT INSTANTIATION
        $apiAccount = $self->apiAccount;
        $privateKey = $self->privateKey;
        $customerCode = $self->customerCode;
        $pwd = $self->pwd;

        //Log::error($apiAccount);

        $ecryptedPwd = strtoupper(md5($pwd . 'jadada236t2'));
        $bizdigest = base64_encode(md5($customerCode . $ecryptedPwd . $privateKey , true));

        $data = array(
            'customerCode' => $customerCode,
            'digest' => $bizdigest,
            'billCode' => $waybillCode
        );

        $json = json_encode($data);

        $digest = base64_encode(md5($json . $privateKey , true));

       $response = Http::withHeaders([
            'apiAccount'   => $apiAccount,
                'digest'       => $digest,
                'Timestamp'    => '1638428570653',
                'Content-Type' => 'application/x-www-form-urlencoded'
        ])->asForm()->post(env('JT_URL') . 'order/printOrderUrl?uuid=855bfc15b747416396b187b1d5e0f254',[
            'bizContent' => $json,

        ]);

        return $response;
    }

    public static function cancelOrder(Jtexpress $jt){
        $self = new static; //OBJECT INSTANTIATION
        $apiAccount = $self->apiAccount;
        $privateKey = $self->privateKey;
        $customerCode = $self->customerCode;
        $pwd = $self->pwd;

        $ecryptedPwd = strtoupper(md5($pwd . 'jadada236t2'));
        $bizdigest = base64_encode(md5($customerCode . $ecryptedPwd . $privateKey , true));

        $data = array(
            'customerCode' => $customerCode,
            'digest' => $bizdigest,
            'billCode' => $jt->bill_code,
            'txlogisticId' => $jt->txlogistic_id,
            'orderType' => '2',
            'reason' => 'Customer cancel the order'
        );

        $json = json_encode($data);

        $digest = base64_encode(md5($json . $privateKey , true));

       $response = Http::withHeaders([
            'apiAccount'   => $apiAccount,
                'digest'       => $digest,
                'Timestamp'    => '1638428570653',
                'Content-Type' => 'application/x-www-form-urlencoded'
        ])->asForm()->post(env('JT_URL') . 'order/cancelOrder?uuid=855bfc15b747416396b187b1d5e0f254',[
            'bizContent' => $json,

        ]);

        return $response;
    }

}