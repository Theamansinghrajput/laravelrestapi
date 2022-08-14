<?php

namespace App\Http\Controllers;

use App\Models\BeforeLogin;
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
class BeforeLoginController extends Controller
{
    /**
 * @OA\Post(
 * path="/login",
 * summary="Sign in",
 * description="Login by email, password",
 * operationId="authLogin",
 * tags={"auth"},
 * @OA\RequestBody(
 *    required=true,
 *    description="Pass user credentials",
 *    @OA\JsonContent(
 *       required={"email","password"},
 *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
 *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
 *       @OA\Property(property="persistent", type="boolean", example="true"),
 *    ),
 * ),
 *
 */
    public function login_validate(Request $request)
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

                 $latest_user_token=Str::random(32);


                 //add array
                 $data_from_db_add = collect($data_from_db[0])
                    ->all();
                 //Arr::prepend($data_from_db_add,'user_login_token',$latest_user_token);
                 $data_from_db = Arr::add($data_from_db_add,'user_login_token',$latest_user_token);


            /*foreach ($data_from_db  as $value_arra)
                    {
                        $new_array_db = $value_arra;
                    }
                    */

            //
            DB::table('account_logins_token')->insert([
                    'user_id' => data_get($data_from_db,'user_id'),
                    'user_login_token' => $latest_user_token
                    ]);
                    $array_1=[
                        'user_login_token' => $latest_user_token
                    ];


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


    public function forget_validate(Request $request)
    {
           /* swati */
        $validation=$request->validate([
            'user_consent' => 'required',
            'user_login' => 'sometimes|required|email|max:100|min:6',
            'api_acces_token' => 'required|string',
            ]);
         $email = $request->post('user_login');
         $api_acces_token = $request->post('api_acces_token');

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

         if (DB::table('account_logins as tbl1')->whereRaw("email = '".$email."'" )->exists())
            {
            //fetching logins data
            $data_from_db = DB::table('account_logins as tbl1')
                    ->selectRaw('tbl1.email, tbl1.first_name, tbl1.last_name, tbl1.mobile, tbl1.created_at, tbl1.user_id, tbl1.type')
                    ->whereRaw("tbl1.email = '".$email."'" )
                    ->offset(0)
                    ->limit(1)
                    ->get();
            $data_from_db_add = collect($data_from_db[0])
                    ->all();

                    foreach ($data_from_db  as $value_arra2)
                    {
                        $new_array_db= $value_arra2;
                    }
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

    public function create_an_account(Request $request)
    {
           /* anand */
         $validation=$request->validate([
            'first_name' => 'required|string|max:100|min:2',
            'user_consent' => 'required|string',
            'last_name' => 'required|string|max:100|min:2',
            'email_address' => 'required|email|max:100|min:6',
            'min_shipping_qunatity' => 'required|integer',
            'max_shipping_qunatity' => 'required|integer',
            'company_name' => 'required|max:100|min:10',
            'passowrd' => 'required|max:18|min:6',
            'mobile_number' => 'required|string|max:10|min:10',
            'country_code' => 'required|integer',
            'api_acces_token' => 'required|string',
         ]);
         $first_name = $request->post('first_name');
         $last_name = $request->post('last_name');
         $email = $request->post('email_address');
         $passowrd = $request->post('passowrd');
         //$passowrd = Crypt::encryptString($passowrd);
         $mobile_integer = $request->post('mobile_number');
         $country_code = $request->post('country_code');
         $min_shipping_qunatity = $request->post('min_shipping_qunatity');
         $max_shipping_qunatity = $request->post('max_shipping_qunatity');
         $company_name = $request->post('company_name');
         $user_id=Str::random(32);
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

         if (DB::table('account_logins')->whereRaw("email = '".$email."' or mobile = ".$mobile_integer."" )->doesntExist())
        {
            $user_id=DB::table('account_logins')->insertGetId([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'mobile' => $mobile_integer,
                'country_code' => $country_code,
                'password' => $passowrd
            ]);

            DB::table('account_company_information')->insert([
                    'user_id' => $user_id,
                    'name' => $company_name,
                    'min_shipping_quantity' => $min_shipping_qunatity,'max_shipping_quantity' => $max_shipping_qunatity
                    ]);

                    if (DB::table('account_logins as tbl1')->whereRaw("tbl1.user_id = '".$user_id."'")->exists())
                    {
                    //fetching logins data
                    $data_from_db = (array)DB::table('account_logins as tbl1')
                            ->selectRaw('tbl1.email, tbl1.first_name, tbl1.last_name, tbl1.mobile, tbl1.created_at, tbl1.user_id, tbl1.type')
                            ->whereRaw("tbl1.user_id = '".$user_id."'")
                            ->offset(0)
                            ->limit(1)
                            ->get();
            $data_from_db_add = collect($data_from_db[0])
                    ->all();

                            $data = array_map(function ($value) {
                                return (array)$value;
                            }, $data_from_db);

                            foreach ($data_from_db  as $value_arra4)
                            {
                                $new_array_db= $value_arra4;
                            }
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
            $Message='Account created Successfully..';
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
            $Message='Failed! Account information email or mobile no already found.';
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

