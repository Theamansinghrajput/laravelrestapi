<?php

namespace App\Http\Controllers;

use App\Models\ErrorCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use OpenApi\Examples\Petstore30\Controllers\Store;
use phpDocumentor\Reflection\Types\Null_;

class ErrorCodeController extends Controller
{
    protected function array_codes()
    {
            return array(
                array(
            "key" => 0,
            "StatusCode" => "404",
            "CodeMessage" => "Not Found (page or other resource doesn’t exist)",
            "Message" => "Page Not found."
                ),
                array(
            "key" => 1,
            "StatusCode" => "401",
            "CodeMessage" => "Not authorized (not logged in)",
            "Message" => "Not authorized or API key not validated or not logged in)."
                ),
                array(
            "key" => 2,
            "StatusCode" => "403",
            "CodeMessage" => "Logged in but access to requested area is forbidden.",
            "Message" => "Access not permitted for this user account."
                ),
                array(
            "key" => 3,
            "StatusCode" => "400",
            "CodeMessage" => "Bad request (something wrong with URL or parameters)",
            "Message" => "Please use valid url or parameters with api."
                ),
                array(
            "key" => 4,
            "StatusCode" => "422",
            "CodeMessage" => "Unprocessable Entity (validation failed)",
            "Message" => "Request is not working properly. Please try after some time."
                ),
                 array(
            "key" => 5,
            "StatusCode" => "500",
            "CodeMessage" => "Internal server error",
            "Message" => "Server not responding. Please try after some time."
                ),
                 array(
            "key" => 6,
            "StatusCode" => "200",
            "CodeMessage" => "Request successful.",
            "Message" => "Please handel response from result array key of StatusCode values as successsfull(0)/failure(1) based codes."
                 ),
                array(
            "key" => 7,
            "StatusCode" => "1",
            "CodeMessage" => "Request successful.",
            "Message" => "Please handel response as successsfull(0)."
                ),
                array(
            "key" => 8,
            "StatusCode" => "0",
            "CodeMessage" => "Request failed.",
            "Message" => "Please handel response as failure(1)."
                )
            );
    }
    public function status_code_handler_from_event(int $key,string $StatusCode,string $CodeMessage,string $Message)
    {
        $api_status_code=$this->array_codes();
             if($StatusCode!='')
             $api_status_code[$key]['StatusCode']=$StatusCode;
             if($CodeMessage!='')
             $api_status_code[$key]['CodeMessage']=$CodeMessage;
             if($Message!='')
             $api_status_code[$key]['Message']=$Message;

             return $api_status_code[$key];
    }

    public function status_code_handler($key)
    {
        $api_status_code=$this->array_codes();
        return $api_status_code[$key];
    }
    public function status_code_handler_update($id,Request $request)
    {
        $validation=$request->validate([
            'api_acces_token' => 'required|string',
            'CodeMessage' => 'required|string|max:100|min:6',
            'Message' => 'required|string|max:100|min:6',
            ]);
         $CodeMessage = $request->post('CodeMessage');
         $Message = $request->post('Message');
         $api_acces_token = $request->post('api_acces_token');

         //api validation
         $api_result = (new ApiValidationController)->api_access_validate($api_acces_token);
         if($api_result=='failed')
         {
            //creating code of response
            $api_result = (new ApiValidationController)->api_access_error_message();
            //returing response to api
            //preparing output of api
            $output_data=null;
            $output_response_data=array(
                "status" => $api_result,
                "requested_data" => $request->all(),
                "output_data" => $output_data
                 );
            //returing response to api
            $api_reso_result = (new ApiResponseController)->responseformating($output_response_data);
            return $api_reso_result;
          }

          $api_status_code=$this->array_codes();

             $api_status_code[$id]['CodeMessage']=$CodeMessage;
             $api_status_code[$id]['Message']=$Message;
             return $api_status_code[$id];
    }
}
