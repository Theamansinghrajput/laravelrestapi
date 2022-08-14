<?php

namespace App\Http\Controllers;

use App\Models\SocialLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use OpenApi\Examples\Petstore30\Controllers\Store;
use phpDocumentor\Reflection\Types\Null_;

class SocialLoginController extends Controller
{
    public function social_login_validate(Request $request)
    {

        /* anand */
     $validation=$request->validate([
         'user_consent' => 'required',
         'user_login' => 'sometimes|required|email|max:100|min:6',
         'password' => 'sometimes|required|max:18|min:6',
         'api_acces_token' => 'required|string',
         ]);
      $email = $request->post('user_login');
      $passowrd_crypt = $request->post('password');
      $api_acces_token = $request->post('api_acces_token');
      //$passowrd_crypt = Crypt::decryptString($passowrd);
      //$passowrd_crypt = Crypt::encryptString($passowrd);

     //app('App\Http\Controllers\ApiValidationController')->api_access_validate();
     //api validation for request
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

     if(DB::table('account_logins')->whereRaw("email = '".$email."' and password = '".$passowrd_crypt."'" )->exists())
         {
         //fetching logins data
         $data_from_db = DB::table('account_logins as tbl1')
         ->selectRaw('tbl1.email, tbl1.first_name, tbl1.last_name, tbl1.mobile, tbl1.created_at, tbl1.user_id, tbl1.type')
                 ->whereRaw("email = '".$email."' and password = '".$passowrd_crypt."'" )
                 ->offset(0)
                 ->limit(1)
                 ->get();
         $data_from_db = collect($data_from_db)
                 ->all();
                 $latest_user_token=Str::random(32);

         foreach ($data_from_db  as $value_arra)
                 {
                     $new_array_db = $value_arra;
                 }

         //
         DB::table('account_logins_token')->insert([
                 'user_id' => data_get($new_array_db,'user_id'),
                 'user_login_token' => $latest_user_token
                 ]);

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
         $Message='You logged in successfully.';
         $response_status_code = (new ErrorCodeController)->status_code_handler_from_event($key,$StatusCode,$CodeMessage,$Message);

         //preparing output of api
         $output_response_data=array(
                 "status" => $api_status_code,
                 "result" => $response_status_code,
                 "requested_data" => $request->all(),
                 "output_data" => $new_array_db,
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
         $Message='Invaid Login, Please try again later.';
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
