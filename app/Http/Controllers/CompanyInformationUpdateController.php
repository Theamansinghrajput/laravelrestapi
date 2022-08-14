<?php
//updae company infromation file
namespace App\Http\Controllers;

use App\Models\CompanyInformationUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use OpenApi\Examples\Petstore30\Controllers\Store;
use phpDocumentor\Reflection\Types\Null_;
use Illuminate\Support\Facades\Storage;

class CompanyInformationUpdateController extends Controller
{
    public function company_information_modification(Request $request)
    {
        /* anand */

        $validation=$request->validate([
         'name' => 'required|string|max:100|min:10',
         'category' => 'required',
         'website_url' => 'required|url|max:100|min:10',
         'email' => 'required|email|max:100|min:10',
         'mobile' => 'required|string|max:10|min:10',
         'PancardNumber' => 'required|string|max:10|min:10',
         'GstNumber' => 'required|string|max:15|min:15',
         'address' => 'required|string|max:500|min:5',
         'city_id' => 'required|integer',
         'state_id' => 'required|integer',
         'pincode_id' => 'required|integer',
         'logo_attachment' => 'required|file|max:1024|mimes:jpg,jpeg,png,pdf',
         'gst_attachment' => 'required|file|max:1024|mimes:jpg,jpeg,png,pdf',
         'authorised_signature_attachment' => 'required|file|max:1024|mimes:jpg,jpeg,png,pdf',
         'user_login_token' => 'required|string',
         'api_acces_token' => 'required|string',
         'user_id' => 'required|integer'
            ]);
      $name = $request->post('name');
      $category = $request->post('category');
      $website_url = $request->post('website_url');
      $email = $request->post('email');
      $mobile = $request->post('mobile');
      $PancardNumber = $request->post('PancardNumber');
      $GstNumber = $request->post('GstNumber');
      $address = $request->post('address');
      $city_id = $request->post('city_id');
      $state_id = $request->post('state_id');
      $pincode_id = $request->post('pincode_id');
      $logo_attachment = $request->file('logo_attachment');
      $gst_attachment = $request->file('gst_attachment');
      $authorised_signature_attachment = $request->file('authorised_signature_attachment');
      $user_login_token = $request->post('user_login_token');
      $user_id = $request->post('user_id');
      $random_unique_id=Str::random(32);
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


          //user validation
        $user_validation_result = (new AccountValidationController)->account_token_validate_class($user_id,$user_login_token);
        if($user_validation_result=='failed')
        {
           //creating code of response
           $user_validation_result = (new ApiValidationController)->api_access_error_message();
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
         //user validation end
         //file upload script start
         $logo_attachment_path = $logo_attachment->store('CompanyDocuments/'.$user_id.'/logo');
         $gst_attachment_path = $gst_attachment->store('CompanyDocuments/'.$user_id.'/gst');
         $authorised_signature_attachment_path = $authorised_signature_attachment->store('CompanyDocuments/'.$user_id.'/sign');
         //file upload script end

         //other scripts
         if (DB::table('account_company_information')->whereRaw("user_id = '".$user_id."'" )->doesntExist())
         {
            /*DB::table('account_information')->insert([
             'req_id' => $random_unique_id,
             'user_id' => $user_id,
             'name' => $name,
             'website_url' => $website_url,
             'email' => $email,
             'mobile' => $mobile,
             'PancardNumber' => $PancardNumber,
             'GstNumber' => $GstNumber,
             'address' => $address,
             'city_id' => $city_id,
             'state_id' => $state_id,
             'pincode_id' => $pincode_id,
             'logo_attachment' => $logo_attachment_path,
             'gst_attachment' => $gst_attachment_path,
             'authorised_signature_attachment' => $authorised_signature_attachment_path,
             'created_at' => now(),
             'status' => Null,
             ]);*/
         }

      if (DB::table('account_company_information')->whereRaw("user_id = '".$user_id."'" )->exists())
     {


             DB::table('account_company_information')
             ->where('user_id',$user_id)
             ->update([
                'name' => $name,
                'category' => $category,
                'website_url' => $website_url,
                'email' => $email,
                'mobile' => $mobile,
                'PancardNumber' => $PancardNumber,
                'GstNumber' => $GstNumber,
                'address' => $address,
                'city_id' => $city_id,
                'state_id' => $state_id,
                'pincode_id' => $pincode_id,
                'logo_attachment' => $logo_attachment_path,
                'gst_attachment' => $gst_attachment_path,
                'authorised_signature_attachment' => $authorised_signature_attachment_path,
                'updated_at' => now()
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
     $Message='User account kyc succesfully updated. We will review your KYCC and contact you after some time. ';
     $response_status_code = (new ErrorCodeController)->status_code_handler_from_event($key,$StatusCode,$CodeMessage,$Message);
     //preparing output of api
     $new_array_db=null;
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
         $Message='Failed! Account User account kyc updation was failed.';
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
