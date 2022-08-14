<?php
//fetch kyc related information of user
namespace App\Http\Controllers;

use App\Models\KycAccess;
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

class KycAccessController extends Controller
{

    public function kyc_information(Request $request)
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

         if (DB::table('account_company_information')->whereRaw("user_id = '".$user_id."'")->exists())
            {
            //fetching logins
            $data_from_db = DB::table('account_company_information')
            ->join('companies_category', 'account_company_information.category', '=', 'companies_category.id')
            ->join('cities', 'account_company_information.city_id', '=', 'cities.id')
            ->join('states', 'account_company_information.state_id', '=', 'states.id')
            ->join('pincodes', 'account_company_information.pincode_id', '=', 'pincodes.id')
            ->select('account_company_information.*', 'companies_category.name as category', 'cities.name as cityname', 'states.name as statename','pincodes.name as pincode')
            ->offset(0)
            ->limit(1)
            ->get();

            $data_from_db_add = collect($data_from_db[0])
                    ->all();
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
            $Message='User kyc information found.';
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
            $Message='Invaid account info, User kyc information not found. Please try again later.';
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
