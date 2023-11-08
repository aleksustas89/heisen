<?php

namespace App\Http\Controllers;
use App\Models\Cdek;

class CdekController extends Controller
{

    public $Cdek = NULL;

    public function __construct()
    {
        $this->Cdek = Cdek::find(1);
    }

    /**
     * @return Cdek with fresh token
    */
    public function Token()
    {
        if (strtotime(date("Y-m-d H:i:s")) > strtotime('+1 hour', strtotime($this->Cdek->updated_at))) {

            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.cdek.ru/v2/oauth/token?grant_type=client_credentials&client_id=raHsvosp1lzzVdhtBeG5xxvdM8AcPIOJ&client_secret=2WrwnXn7Tr8gXhfsiwgb2k8cEGIiDTMw',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'client_id: raHsvosp1lzzVdhtBeG5xxvdM8AcPIOJ',
                'client_secret: 2WrwnXn7Tr8gXhfsiwgb2k8cEGIiDTMw'
            ),
            ));
            
            $response = curl_exec($curl);

            $response = json_decode($response);
            
            curl_close($curl);
    

            if (isset($response->access_token)) {
                $this->Cdek->token = $response->access_token;
                $this->Cdek->save();
            }

            curl_close($curl);
        }
    }

    public function getRegions()
    {

        $this->Token();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.cdek.ru/v2/location/regions?country_codes=RU',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $this->Cdek->token
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response);

        curl_close($curl);

        return !isset($response->requests->errors) ? $response : false;
    }

    public function getCities()
    {

        $this->Token();
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.cdek.ru/v2/location/cities?country_codes=RU',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $this->Cdek->token
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response);

        curl_close($curl);

        return !isset($response->requests->errors) ? $response : false;
    }

    public function getOffices()
    {
        $this->Token();
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.cdek.ru/v2/deliverypoints',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $this->Cdek->token
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response);

        curl_close($curl);

        return !isset($response->requests->errors) ? $response : false;
    }

}