<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Cookie;
use App\Enum\CountriesAllowed;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class IndexController extends Controller
{

    /**
     * proxy validate user
     *
     * @param  Request  $request [user, pass]
     * @return Response $redirect (Success or Failure)
     */
    public function proxy(Request $request)
    {
        // The list of allowed countries is obtained
        $countries_allowed  = new CountriesAllowed();
        $countries_allowed  = $countries_allowed::getCountries();
        $countries_ISO      = array_keys($countries_allowed);

        // The parameters of the post are set
        $user = $request->input('user');
        $pass = $request->input('pass');
        
        // The geolocation is made by means of the IP address of the client (Visitor)
        // Made with https://ipgeolocation.io/documentation.html
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', "https://api.ipgeolocation.io/ipgeo", ['query' => [
            'apiKey' => env('IP_GEOLOCATION_API_KEY')
            ]]);

        // Get status code with request API Geolocation
        $statusCode = $response->getStatusCode();

        // In case of not getting results from the API it redirects to the error the IP address
        if($statusCode != "200"){
            return redirect("https://login.optolapp.com/optol/?erro=ip");  
            Log::error("Error in obtaining the geolocation of the visitor");   
        }

        // Get response the API Geolocation
        $response = json_decode($response->getBody(), true);

        // Validate that the country is allowed, that the user is numeric 
        // and that the pass is greater or equal to 6 characters
        if(in_array($response['country_code3'], $countries_ISO) && is_numeric($user) && strlen($pass) >= 6){
            $login = $this->login($user, $pass);

            if($login['statusCode'] == "200"){
                return redirect("https://login.optolapp.com/optol/?sucess=1");
            }else{
                return redirect("https://login.optolapp.com/optol/?erro=ip");
                Log::error("Error when sending data user");
            }

        }else{
            return redirect("https://login.optolapp.com/optol/?erro=ip");
            Log::error("Error in data validation user");
        }
    }

    /**
     * Login user created Cookies
     *
     * @param  Request  $request [user, pass]
     *
     * @return \Illuminate\Http\Response
     */
    public function login($user, $password)
    {     

        // The value of the cookie is obtained by making the request with the user's data
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', "https://login.optolapp.com/optol/ws/estado_servicio/", [
            'params' => [
                'user' => $user,
                'pass' => $password,
            ]
        ]);

        // Get the status code of the request
        $statusCode = $response->getStatusCode();

        // If status code 200 is not reached, it returns the error code
        if($statusCode != "200"){
            return ['statusCode' => $statusCode];
        }

        // Get response
        $response = json_decode($response->getBody(), true);

        //The cookie is created based on the request
        $userOptol = cookie('UserOptol', $response, 14400);

        return [
            'statusCode' => $statusCode, 
            'data'       => $response
        ];
    }

}
