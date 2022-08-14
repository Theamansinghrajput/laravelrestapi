<?php

namespace App\Http\Controllers;

use App\Models\AccountValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AccountValidationController extends Controller
{

    public function account_token_validate_api(Request $request)
    {

        /* swati */
        $validation=$request->validate([
            'user_id' => 'required|integer',
            'user_login_token' => 'required|string',
            'api_acces_token' => 'required|string',
            ]);
         $user_id = $request->post('user_id');
         $user_login_token = $request->post('user_login_token');
         $api_acces_token = $request->post('api_acces_token');

         //api validation
         $api_result = (new ApiValidationController)->api_access_validate($api_acces_token);
         if($api_result=='failed')
         {
            //creating code of response
            $api_result = (new ApiValidationController)->api_access_error_message();
            //returing response to api
            $key=1;
        $StatusCode='';
        $CodeMessage='API key error.';
        $Message='API key validation failed, Please check you API key.';
        $api_status_code = (new ErrorCodeController)->status_code_handler_from_event($key,$StatusCode,$CodeMessage,$Message);
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

         if (DB::table('account_logins_token')->whereRaw("user_id = '".$user_id."' and user_login_token = '".$user_login_token."' and status = 1" )->exists())
            {
             //updating token state
             DB::table('account_logins_token')
              ->where('user_id', $user_id)
              ->where('user_login_token', $user_login_token)
              ->update(['status' => 0]);
             // update info pending
            			//api status code
                        $key=6;
                        $StatusCode='';
                        $CodeMessage='API requested successfully.';
                        $Message='';
                        $api_status_code = (new ErrorCodeController)->status_code_handler_from_event($key,$StatusCode,$CodeMessage,$Message);
                        //creating code of response
                        $key=7;
                        $StatusCode='';
                        $CodeMessage='Response successfully.';
                        $Message='User account succesfully found.';
                        $response_status_code = (new ErrorCodeController)->status_code_handler_from_event($key,$StatusCode,$CodeMessage,$Message);

                        //preparing output of api
                        $data_from_db = null;
                        $output_response_data=array(
                                "status" => $api_status_code,
                                "result" => $response_status_code,
                                "requested_data" => $request->all(),
                                "output_data" => $data_from_db,
                                                );

                        //returing response to api
                        $api_reso_result = (new ApiResponseController)->responseformating($output_response_data);
                        return $api_reso_result;

             }
             else
            {
			//api status code
            $key=6;
            $StatusCode='';
            $CodeMessage='API requested successfully.';
            $Message='';
            $api_status_code = (new ErrorCodeController)->status_code_handler_from_event($key,$StatusCode,$CodeMessage,$Message);
            //creating code of response
            $key=8;
            $StatusCode='';
            $CodeMessage='Response Failed.';
            $Message='Invaid account info, User account token not found information not found. Please try again later.';
            $response_status_code = (new ErrorCodeController)->status_code_handler_from_event($key,$StatusCode,$CodeMessage,$Message);
            //preparing output of api
            $output_data=null;
            $output_response_data=array(
                    "status" => $api_status_code,
                    "result" => $response_status_code,
                    "requested_data" => $request->all(),
                    "output_data" => $output_data,
                                    );
            //returing response to api
            $api_reso_result = (new ApiResponseController)->responseformating($output_response_data);
            return $api_reso_result;

            }




    }

    public function account_token_validate_class($user_id,$user_login_token)
    {

         if (DB::table('account_logins_token')->whereRaw("user_id = '".$user_id."' and user_login_token = '".$user_login_token."' and status = 1" )->exists())
         {
            return 'success';
        }
        else
        {
            return 'failed';
        }
}
//api validation error message
public function account_token_error_message()
{
    $key=1;
    $StatusCode='';
    $CodeMessage='Account validation failed.';
    $Message='Either Token/Account was blocked or not found.';
    $api_status_code = (new ErrorCodeController)->status_code_handler_from_event($key,$StatusCode,$CodeMessage,$Message);
    return $api_status_code;
}
//api validation success message
public function account_token_success_message()
{

    $key=0;
    $StatusCode='';
    $CodeMessage='account validated successfully.';
    $Message='Token and other information validated successfully.';
    $api_status_code = (new ErrorCodeController)->status_code_handler_from_event($key,$StatusCode,$CodeMessage,$Message);
    return $api_status_code;

}
}
