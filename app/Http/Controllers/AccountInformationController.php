<?php

namespace App\Http\Controllers;

use App\Models\AccountInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use OpenApi\Examples\Petstore30\Controllers\Store;
use phpDocumentor\Reflection\Types\Null_;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class AccountInformationController extends Controller
{
    public function account_info(Request $request)
    {

           /* swati */
        $validation=$request->validate([
            'user_id' => 'required|integer',
            'api_acces_token' => 'required|string',
            'user_login_token' => 'required|string',
            ]);
         $user_id = $request->post('user_id');
         $api_acces_token = $request->post('api_acces_token');
         $user_login_token = $request->post('user_login_token');

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

          //user validation
        $user_validation_result = (new AccountValidationController)->account_token_validate_class($user_id,$user_login_token);
        if($user_validation_result=='failed')
        {
           //creating code of response
           $user_validation_result = (new AccountValidationController)->account_token_error_message();
           //returing response to api
           $key=1;
       $StatusCode='';
       $CodeMessage='User validation failed.';
       $Message='User validation failed, Please try with valid user details.';
       $api_status_code = (new ErrorCodeController)->status_code_handler_from_event($key,$StatusCode,$CodeMessage,$Message);
           //preparing output of api
           $output_data=null;
           $output_response_data=array(
               "status" => $user_validation_result,
               "requested_data" => $request->all(),
               "output_data" => $output_data
                );
           //returing response to api
           $api_reso_result = (new ApiResponseController)->responseformating($output_response_data);
           return $api_reso_result;
         }

         if (DB::table('account_logins as tbl1')->whereRaw("tbl1.user_id = '".$user_id."'")->exists())
            {
            //fetching logins data
            $data_from_db = DB::table('account_logins as tbl1')
                    ->selectRaw('tbl1.email, tbl1.first_name, tbl1.last_name, tbl1.mobile, tbl1.created_at, tbl1.user_id, tbl1.type')
                    ->whereRaw("tbl1.user_id = '".$user_id."'")
                    ->offset(0)
                    ->limit(1)
                    ->get();
            $data_from_db_add = collect($data_from_db[0])
                    ->all();

            //Arr::add($data_from_db,'user_login_token',$latest_user_token);

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
            $Message='User account information found.';
            $response_status_code = (new ErrorCodeController)->status_code_handler_from_event($key,$StatusCode,$CodeMessage,$Message);

            //preparing output of api
            $output_response_data=array(
                    "status" => $api_status_code,
                    "result" => $response_status_code,
                    "requested_data" => $request->all(),
                    "output_data" => $data_from_db[0],
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
            $Message='Invaid account info, User account information not found. Please try again later.';
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
}
