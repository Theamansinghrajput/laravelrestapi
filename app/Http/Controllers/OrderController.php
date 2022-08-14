<?php

namespace App\Http\Controllers;

use App\Models\Order;
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

class OrderController extends Controller
{

    public function create_new_order(Request $request)
    {
        /* anand */
      $validation=$request->validate([
        //order_type
        'order_type' => 'required|integer|max:10|min:1',
        //shiping info
        'Shipping_first_name' => 'required|string|max:50|min:3',
        'Shipping_last_name' => 'required|string|max:50|min:3',
         'Shipping_name' => 'required|string|max:50|min:3',
         'Shipping_mobile' => 'required|string|max:10|min:10',
         'Shipping_address' => 'required|string|max:500|min:5',
         'Shipping_address_optional' => 'string|max:500|min:5',
         'Shipping_city_id' => 'required|integer',
         'Shipping_state_id' => 'required|integer',
         'Shipping_pincode_id' => 'required|integer',
         'Shipping_type' => 'required|integer|max:1|min:0',
         'Shipping_is_same' => 'required|integer|max:1|min:0',
         //billing info
         'billing_first_name' => 'required|string|max:50|min:3',
         'billing_last_name' => 'required|string|max:50|min:3',
         'billing_name' => 'required|string|max:50|min:3',
         'billing_mobile' => 'required|string|max:10|min:10',
         'billing_gst_details' => 'required|string|max:15|min:15',
         'billing_address' => 'required|string|max:500|min:5',
         'billing_address_optional' => 'string|max:500|min:5',
         'billing_city_id' => 'required|integer',
         'billing_state_id' => 'required|integer',
         'billing_pincode_id' => 'required|integer',
         //product weight
         'weight' => 'required|numeric',
         'length' => 'required|numeric',
         'width' => 'required|numeric',
         'height' => 'required|numeric',
         //product description
         'name' => 'required|string|max:100|min:5',
         'quantity' => 'required|numeric',
         'amount' => 'required|numeric',
         'sku' => 'required|string',
        //product final pricing
         'shiping_charges' => 'required|numeric',
         'COD_charges' => 'required|numeric',
         'tax_amount' => 'required|numeric',
         'discount' => 'required|numeric',
         'user_login_token' => 'required|string',
         'api_acces_token' => 'required|string',
         'user_id' => 'required|integer',
      ]);


      $id = $request->post('id');
      $order_type = $request->post('order_type');
      //shipping data
      $Shipping_first_name = $request->post('Shipping_first_name');
      $Shipping_last_name = $request->post('Shipping_last_name');
      $Shipping_name = $request->post('Shipping_name');
      $Shipping_mobile = $request->post('Shipping_mobile');
      $Shipping_address = $request->post('Shipping_address');
      $Shipping_address_optional = $request->post('Shipping_address_optional');
      $Shipping_type = $request->post('Shipping_type');
      $Shipping_is_same = $request->post('Shipping_is_same');
      $Shipping_city_id = $request->post('Shipping_city_id');
      $Shipping_state_id = $request->post('Shipping_state_id');
      $Shipping_pincode_id = $request->post('Shipping_pincode_id');
      //billing
      $billing_first_name = $request->post('billing_first_name');
      $billing_last_name = $request->post('billing_last_name');
      $billing_name = $request->post('billing_name');
      $billing_mobile = $request->post('billing_mobile');
      $billing_gst_details = $request->post('billing_gst_details');
      $billing_address = $request->post('billing_address');
      $billing_address_optional = $request->post('billing_address_optional');
      $billing_city_id = $request->post('billing_city_id');
      $billing_state_id = $request->post('billing_state_id');
      $billing_pincode_id = $request->post('billing_pincode_id');
      //product weight
      $weight = $request->post('weight');
      $length = $request->post('length');
      $width = $request->post('width');
      $height = $request->post('height');
      //product description
      $name = $request->post('name');
      $quantity = $request->post('quantity');
      $amount = $request->post('amount');
      $sku = $request->post('sku');
      //product final pricing
      $shiping_charges = $request->post('shiping_charges');
      $COD_charges = $request->post('COD_charges');
      $tax_amount = $request->post('tax_amount');
      $discount = $request->post('discount');
      //authentication
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

        $order_id=DB::table('order_create')->insertGetId([
            'user_id' => $user_id,
            'order_type' => $order_type,
                  ]);

             DB::table('order_shipping_information')->insert([
             'id' => $order_id,
             'user_id' => $user_id,
             'first_name' => $Shipping_first_name,
             'last_name' => $Shipping_last_name,
             'company_name' => $Shipping_name,
             'mobile_number' => $Shipping_mobile,
             'address' => $Shipping_address,
             'address_optional' => $Shipping_address_optional,
             'address_type' => $Shipping_type,
             'same_as_billing' => $Shipping_is_same,
             'city_id' => $Shipping_city_id,
             'state_id' => $Shipping_state_id,
             'pincode_id' => $Shipping_pincode_id,
             ]);

             DB::table('order_billing_information')->insert([
                'id' => $order_id,
                'user_id' => $user_id,
                'first_name' => $billing_first_name,
                'last_name' => $billing_last_name,
                'company_name' => $billing_name,
                'mobile_number' => $billing_mobile,
                'gst_number' => $billing_gst_details,
                'address' => $billing_address,
                'address_optional' => $billing_address_optional,
                'city_id' => $billing_city_id,
                'state_id' => $billing_state_id,
                'pincode_id' => $billing_pincode_id,
                ]);

            DB::table('order_weight_information')->insert([
                    'id' => $order_id,
                    'user_id' => $user_id,
                    'weight' => $weight,
                    'length' => $length,
                    'width' => $width,
                    'height' => $height,
                     ]);
            DB::table('order_pricing_information')->insert([
                        'id' => $order_id,
                        'user_id' => $user_id,
                        'shiping_charges' => $shiping_charges,
                        'cod_charges' => $COD_charges,
                        'tax_amount' => $tax_amount,
                        'discount' => $discount,
                         ]);

            DB::table('order_multiple_product_information')->insert([
                            'order_id' => $order_id,
                            'user_id' => $user_id,
                            'name' => $name,
                            'quantity' => $quantity,
                            'amount' => $amount,
                            'sku' => $sku,
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

    public function order_listing(Request $request)
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

      if (DB::table('order_create')->whereRaw("user_id = '".$user_id."'")->exists())
         {
         //fetching logins

         $data_from_db = DB::table('order_create')
         ->join('order_billing_information', 'order_create.id', '=', 'order_billing_information.id')
         ->join('order_pricing_information', 'order_create.id', '=', 'order_pricing_information.id')
         ->join('order_shipping_information', 'order_create.id', '=', 'order_shipping_information.id')
         ->join('order_weight_information', 'order_create.id', '=', 'order_weight_information.id')
         ->join('order_type', 'order_create.order_type', '=', 'order_type.id')
         ->crossjoin('order_multiple_product_information', 'order_create.id', '=', 'order_multiple_product_information.order_id')
         ->join('cities as billing_city', 'order_billing_information.city_id', '=', 'billing_city.id')
         ->join('states as billing_state', 'order_billing_information.state_id', '=', 'billing_state.id')
         ->join('pincodes as billing_pincode', 'order_billing_information.pincode_id', '=', 'billing_pincode.id')
         ->join('cities as shipping_city', 'order_shipping_information.city_id', '=', 'shipping_city.id')
         ->join('states as shipping_state', 'order_shipping_information.state_id', '=', 'shipping_state.id')
         ->join('pincodes as shipping_pincode', 'order_shipping_information.pincode_id', '=', 'shipping_pincode.id')
         ->select('order_create.*','order_weight_information.*','order_billing_information.id as billing_id', 'order_billing_information.user_id as billing_user_id', 'order_billing_information.first_name as billing_first_name', 'order_billing_information.last_name as billing_last_name', 'order_billing_information.company_name as billing_company_name', 'order_billing_information.mobile_number as billing_mobile', 'order_billing_information.address as billing_address', 'order_billing_information.address_optional as billing_address_optional', 'order_billing_information.city_id as billing_city_id', 'order_billing_information.state_id as billing_state_id', 'order_billing_information.pincode_id as billing_pincode_id', 'order_billing_information.gst_number as billing_gst_number', 'order_billing_information.created_at as billing_created_at', 'order_billing_information.updated_at as billing_updated_at','order_pricing_information.*','order_shipping_information.id as shipping_id', 'order_shipping_information.user_id as shipping_user_id', 'order_shipping_information.first_name as shipping_first_name', 'order_shipping_information.last_name as shipping_last_name', 'order_shipping_information.company_name as shipping_company_name', 'order_shipping_information.mobile_number as shipping_mobile_number', 'order_shipping_information.address as shipping_address', 'order_shipping_information.address_optional as shipping_address_optional', 'order_shipping_information.city_id as shipping_city_id', 'order_shipping_information.state_id as shipping_state_id', 'order_shipping_information.pincode_id as shipping_pincode_id', 'order_shipping_information.address_type as shipping_address_type', 'order_shipping_information.same_as_billing as shipping_same_as_billing', 'order_shipping_information.created_at as shipping_created_at', 'order_shipping_information.updated_at as shipping_updated_at','shipping_pincode.name as shipping_pincode', 'shipping_state.name as shipping_state', 'shipping_city.name as shipping_city','billing_pincode.name as billing_pincode', 'billing_state.name as billing_state', 'billing_city.name as billing_city', 'order_multiple_product_information.id as product_id', 'order_multiple_product_information.name as product_name', 'order_multiple_product_information.quantity as product_quantitiy', 'order_multiple_product_information.amount as product_amount', 'order_multiple_product_information.sku as product_sku', 'order_multiple_product_information.created_at as product_created_at', 'order_multiple_product_information.updated_at as product_updated_at', 'order_multiple_product_information.status as product_status' )
         //->offset(0)
         //->limit(1)
         //->groupBy('order_multiple_product_information')
         ->get();


        if (DB::table('order_multiple_product_information')->whereRaw("user_id = '".$user_id."'")->exists())
            {
                $data_from_db_add1 = DB::table('order_multiple_product_information')
                ->whereRaw("user_id = '".$user_id."'")
                ->get();

            }

        //add array
        $respomse_final_array=array('orders' => $data_from_db, 'products' =>$data_from_db_add1);

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
                 "output_data" => $respomse_final_array,
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

    public function filtered_order_listing(Request $request)
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

      if (DB::table('order_create')->whereRaw("user_id = '".$user_id."'")->exists())
         {
         //fetching logins

         $data_from_db = DB::table('order_create')
         ->join('order_billing_information', 'order_create.id', '=', 'order_billing_information.id')
         ->join('order_pricing_information', 'order_create.id', '=', 'order_pricing_information.id')
         ->join('order_shipping_information', 'order_create.id', '=', 'order_shipping_information.id')
         ->join('order_weight_information', 'order_create.id', '=', 'order_weight_information.id')
         ->join('order_type', 'order_create.order_type', '=', 'order_type.id')
         ->crossjoin('order_multiple_product_information', 'order_create.id', '=', 'order_multiple_product_information.order_id')
         ->join('cities as billing_city', 'order_billing_information.city_id', '=', 'billing_city.id')
         ->join('states as billing_state', 'order_billing_information.state_id', '=', 'billing_state.id')
         ->join('pincodes as billing_pincode', 'order_billing_information.pincode_id', '=', 'billing_pincode.id')
         ->join('cities as shipping_city', 'order_shipping_information.city_id', '=', 'shipping_city.id')
         ->join('states as shipping_state', 'order_shipping_information.state_id', '=', 'shipping_state.id')
         ->join('pincodes as shipping_pincode', 'order_shipping_information.pincode_id', '=', 'shipping_pincode.id')
         ->select('order_create.*','order_weight_information.*','order_billing_information.id as billing_id', 'order_billing_information.user_id as billing_user_id', 'order_billing_information.first_name as billing_first_name', 'order_billing_information.last_name as billing_last_name', 'order_billing_information.company_name as billing_company_name', 'order_billing_information.mobile_number as billing_mobile', 'order_billing_information.address as billing_address', 'order_billing_information.address_optional as billing_address_optional', 'order_billing_information.city_id as billing_city_id', 'order_billing_information.state_id as billing_state_id', 'order_billing_information.pincode_id as billing_pincode_id', 'order_billing_information.gst_number as billing_gst_number', 'order_billing_information.created_at as billing_created_at', 'order_billing_information.updated_at as billing_updated_at','order_pricing_information.*','order_shipping_information.id as shipping_id', 'order_shipping_information.user_id as shipping_user_id', 'order_shipping_information.first_name as shipping_first_name', 'order_shipping_information.last_name as shipping_last_name', 'order_shipping_information.company_name as shipping_company_name', 'order_shipping_information.mobile_number as shipping_mobile_number', 'order_shipping_information.address as shipping_address', 'order_shipping_information.address_optional as shipping_address_optional', 'order_shipping_information.city_id as shipping_city_id', 'order_shipping_information.state_id as shipping_state_id', 'order_shipping_information.pincode_id as shipping_pincode_id', 'order_shipping_information.address_type as shipping_address_type', 'order_shipping_information.same_as_billing as shipping_same_as_billing', 'order_shipping_information.created_at as shipping_created_at', 'order_shipping_information.updated_at as shipping_updated_at','shipping_pincode.name as shipping_pincode', 'shipping_state.name as shipping_state', 'shipping_city.name as shipping_city','billing_pincode.name as billing_pincode', 'billing_state.name as billing_state', 'billing_city.name as billing_city', 'order_multiple_product_information.id as product_id', 'order_multiple_product_information.name as product_name', 'order_multiple_product_information.quantity as product_quantitiy', 'order_multiple_product_information.amount as product_amount', 'order_multiple_product_information.sku as product_sku', 'order_multiple_product_information.created_at as product_created_at', 'order_multiple_product_information.updated_at as product_updated_at', 'order_multiple_product_information.status as product_status' )
         //->offset(0)
         //->limit(1)
         //->groupBy('order_multiple_product_information')
         ->get();


        if (DB::table('order_multiple_product_information')->whereRaw("user_id = '".$user_id."'")->exists())
            {
                $data_from_db_add1 = DB::table('order_multiple_product_information')
                ->whereRaw("user_id = '".$user_id."'")
                ->get();

            }

        //add array
        $respomse_final_array=array('orders' => $data_from_db, 'products' =>$data_from_db_add1);

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
                 "output_data" => $respomse_final_array,
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

    public function details_order_listing($order_id,Request $request)
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

      if (DB::table('order_create')->whereRaw("user_id = '".$user_id."'")->exists())
         {
         //fetching logins

         $data_from_db = DB::table('order_create')
         ->join('order_billing_information', 'order_create.id', '=', 'order_billing_information.id')
         ->join('order_pricing_information', 'order_create.id', '=', 'order_pricing_information.id')
         ->join('order_shipping_information', 'order_create.id', '=', 'order_shipping_information.id')
         ->join('order_weight_information', 'order_create.id', '=', 'order_weight_information.id')
         ->join('order_type', 'order_create.order_type', '=', 'order_type.id')
         ->crossjoin('order_multiple_product_information', 'order_create.id', '=', 'order_multiple_product_information.order_id')
         ->join('cities as billing_city', 'order_billing_information.city_id', '=', 'billing_city.id')
         ->join('states as billing_state', 'order_billing_information.state_id', '=', 'billing_state.id')
         ->join('pincodes as billing_pincode', 'order_billing_information.pincode_id', '=', 'billing_pincode.id')
         ->join('cities as shipping_city', 'order_shipping_information.city_id', '=', 'shipping_city.id')
         ->join('states as shipping_state', 'order_shipping_information.state_id', '=', 'shipping_state.id')
         ->join('pincodes as shipping_pincode', 'order_shipping_information.pincode_id', '=', 'shipping_pincode.id')
         ->select('order_create.*','order_weight_information.*','order_billing_information.id as billing_id', 'order_billing_information.user_id as billing_user_id', 'order_billing_information.first_name as billing_first_name', 'order_billing_information.last_name as billing_last_name', 'order_billing_information.company_name as billing_company_name', 'order_billing_information.mobile_number as billing_mobile', 'order_billing_information.address as billing_address', 'order_billing_information.address_optional as billing_address_optional', 'order_billing_information.city_id as billing_city_id', 'order_billing_information.state_id as billing_state_id', 'order_billing_information.pincode_id as billing_pincode_id', 'order_billing_information.gst_number as billing_gst_number', 'order_billing_information.created_at as billing_created_at', 'order_billing_information.updated_at as billing_updated_at','order_pricing_information.*','order_shipping_information.id as shipping_id', 'order_shipping_information.user_id as shipping_user_id', 'order_shipping_information.first_name as shipping_first_name', 'order_shipping_information.last_name as shipping_last_name', 'order_shipping_information.company_name as shipping_company_name', 'order_shipping_information.mobile_number as shipping_mobile_number', 'order_shipping_information.address as shipping_address', 'order_shipping_information.address_optional as shipping_address_optional', 'order_shipping_information.city_id as shipping_city_id', 'order_shipping_information.state_id as shipping_state_id', 'order_shipping_information.pincode_id as shipping_pincode_id', 'order_shipping_information.address_type as shipping_address_type', 'order_shipping_information.same_as_billing as shipping_same_as_billing', 'order_shipping_information.created_at as shipping_created_at', 'order_shipping_information.updated_at as shipping_updated_at','shipping_pincode.name as shipping_pincode', 'shipping_state.name as shipping_state', 'shipping_city.name as shipping_city','billing_pincode.name as billing_pincode', 'billing_state.name as billing_state', 'billing_city.name as billing_city', 'order_multiple_product_information.id as product_id', 'order_multiple_product_information.name as product_name', 'order_multiple_product_information.quantity as product_quantitiy', 'order_multiple_product_information.amount as product_amount', 'order_multiple_product_information.sku as product_sku', 'order_multiple_product_information.created_at as product_created_at', 'order_multiple_product_information.updated_at as product_updated_at', 'order_multiple_product_information.status as product_status' )
         //->offset(0)
         //->limit(1)
         //->groupBy('order_multiple_product_information')
         ->get();


        if (DB::table('order_multiple_product_information')->whereRaw("user_id = '".$user_id."'")->exists())
            {
                $data_from_db_add1 = DB::table('order_multiple_product_information')
                ->whereRaw("user_id = '".$user_id."'")
                ->get();

            }

        //add array
        $respomse_final_array=array('orders' => $data_from_db, 'products' =>$data_from_db_add1);

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
                 "output_data" => $respomse_final_array,
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

    public function update_order_listing($order_id,Request $request)
                {
                    /* anand */
                $validation=$request->validate([
                    //order_type
                    'order_type' => 'required|integer|max:10|min:1',
                    //shiping info
                    'Shipping_first_name' => 'required|string|max:50|min:3',
                    'Shipping_last_name' => 'required|string|max:50|min:3',
                    'Shipping_name' => 'required|string|max:50|min:3',
                    'Shipping_mobile' => 'required|string|max:10|min:10',
                    'Shipping_address' => 'required|string|max:500|min:5',
                    'Shipping_address_optional' => 'string|max:500|min:5',
                    'Shipping_city_id' => 'required|integer',
                    'Shipping_state_id' => 'required|integer',
                    'Shipping_pincode_id' => 'required|integer',
                    'Shipping_type' => 'required|integer|max:1|min:0',
                    'Shipping_is_same' => 'required|integer|max:1|min:0',
                    //billing info
                    'billing_first_name' => 'required|string|max:50|min:3',
                    'billing_last_name' => 'required|string|max:50|min:3',
                    'billing_name' => 'required|string|max:50|min:3',
                    'billing_mobile' => 'required|string|max:10|min:10',
                    'billing_gst_details' => 'required|string|max:15|min:15',
                    'billing_address' => 'required|string|max:500|min:5',
                    'billing_address_optional' => 'string|max:500|min:5',
                    'billing_city_id' => 'required|integer',
                    'billing_state_id' => 'required|integer',
                    'billing_pincode_id' => 'required|integer',
                    //product weight
                    'weight' => 'required|numeric',
                    'length' => 'required|numeric',
                    'width' => 'required|numeric',
                    'height' => 'required|numeric',
                    //product description
                    'name' => 'required|string|max:100|min:5',
                    'quantity' => 'required|numeric',
                    'amount' => 'required|numeric',
                    'sku' => 'required|string',
                    //product final pricing
                    'shiping_charges' => 'required|numeric',
                    'COD_charges' => 'required|numeric',
                    'tax_amount' => 'required|numeric',
                    'discount' => 'required|numeric',
                    'user_login_token' => 'required|string',
                    'api_acces_token' => 'required|string',
                    'user_id' => 'required|integer',
                ]);


                $id = $request->post('id');
                $order_type = $request->post('order_type');
                //shipping data
                $Shipping_first_name = $request->post('Shipping_first_name');
                $Shipping_last_name = $request->post('Shipping_last_name');
                $Shipping_name = $request->post('Shipping_name');
                $Shipping_mobile = $request->post('Shipping_mobile');
                $Shipping_address = $request->post('Shipping_address');
                $Shipping_address_optional = $request->post('Shipping_address_optional');
                $Shipping_type = $request->post('Shipping_type');
                $Shipping_is_same = $request->post('Shipping_is_same');
                $Shipping_city_id = $request->post('Shipping_city_id');
                $Shipping_state_id = $request->post('Shipping_state_id');
                $Shipping_pincode_id = $request->post('Shipping_pincode_id');
                //billing
                $billing_first_name = $request->post('billing_first_name');
                $billing_last_name = $request->post('billing_last_name');
                $billing_name = $request->post('billing_name');
                $billing_mobile = $request->post('billing_mobile');
                $billing_gst_details = $request->post('billing_gst_details');
                $billing_address = $request->post('billing_address');
                $billing_address_optional = $request->post('billing_address_optional');
                $billing_city_id = $request->post('billing_city_id');
                $billing_state_id = $request->post('billing_state_id');
                $billing_pincode_id = $request->post('billing_pincode_id');
                //product weight
                $weight = $request->post('weight');
                $length = $request->post('length');
                $width = $request->post('width');
                $height = $request->post('height');
                //product description
                $name = $request->post('name');
                $quantity = $request->post('quantity');
                $amount = $request->post('amount');
                $sku = $request->post('sku');
                //product final pricing
                $shiping_charges = $request->post('shiping_charges');
                $COD_charges = $request->post('COD_charges');
                $tax_amount = $request->post('tax_amount');
                $discount = $request->post('discount');
                //authentication
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

                    $order_id=DB::table('order_create')->insertGetId([
                        'user_id' => $user_id,
                        'order_type' => $order_type,
                            ]);

                        DB::table('order_shipping_information')->insert([
                        'id' => $order_id,
                        'user_id' => $user_id,
                        'first_name' => $Shipping_first_name,
                        'last_name' => $Shipping_last_name,
                        'company_name' => $Shipping_name,
                        'mobile_number' => $Shipping_mobile,
                        'address' => $Shipping_address,
                        'address_optional' => $Shipping_address_optional,
                        'address_type' => $Shipping_type,
                        'same_as_billing' => $Shipping_is_same,
                        'city_id' => $Shipping_city_id,
                        'state_id' => $Shipping_state_id,
                        'pincode_id' => $Shipping_pincode_id,
                        ]);

                        DB::table('order_billing_information')->insert([
                            'id' => $order_id,
                            'user_id' => $user_id,
                            'first_name' => $billing_first_name,
                            'last_name' => $billing_last_name,
                            'company_name' => $billing_name,
                            'mobile_number' => $billing_mobile,
                            'gst_number' => $billing_gst_details,
                            'address' => $billing_address,
                            'address_optional' => $billing_address_optional,
                            'city_id' => $billing_city_id,
                            'state_id' => $billing_state_id,
                            'pincode_id' => $billing_pincode_id,
                            ]);

                        DB::table('order_weight_information')->insert([
                                'id' => $order_id,
                                'user_id' => $user_id,
                                'weight' => $weight,
                                'length' => $length,
                                'width' => $width,
                                'height' => $height,
                                ]);
                        DB::table('order_pricing_information')->insert([
                                    'id' => $order_id,
                                    'user_id' => $user_id,
                                    'shiping_charges' => $shiping_charges,
                                    'cod_charges' => $COD_charges,
                                    'tax_amount' => $tax_amount,
                                    'discount' => $discount,
                                    ]);

                        DB::table('order_multiple_product_information')->insert([
                                        'order_id' => $order_id,
                                        'user_id' => $user_id,
                                        'name' => $name,
                                        'quantity' => $quantity,
                                        'amount' => $amount,
                                        'sku' => $sku,
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
    public function delete_order_listing($order_id,Request $request)
    {

    }

    public function import_order_listing(Request $request)
    {

    }

    public function export_order_listing(Request $request)
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

            if (DB::table('order_create')->whereRaw("user_id = '".$user_id."'")->exists())
                {
                //fetching logins

                $data_from_db = DB::table('order_create')
                ->join('order_billing_information', 'order_create.id', '=', 'order_billing_information.id')
                ->join('order_pricing_information', 'order_create.id', '=', 'order_pricing_information.id')
                ->join('order_shipping_information', 'order_create.id', '=', 'order_shipping_information.id')
                ->join('order_weight_information', 'order_create.id', '=', 'order_weight_information.id')
                ->join('order_type', 'order_create.order_type', '=', 'order_type.id')
                ->crossjoin('order_multiple_product_information', 'order_create.id', '=', 'order_multiple_product_information.order_id')
                ->join('cities as billing_city', 'order_billing_information.city_id', '=', 'billing_city.id')
                ->join('states as billing_state', 'order_billing_information.state_id', '=', 'billing_state.id')
                ->join('pincodes as billing_pincode', 'order_billing_information.pincode_id', '=', 'billing_pincode.id')
                ->join('cities as shipping_city', 'order_shipping_information.city_id', '=', 'shipping_city.id')
                ->join('states as shipping_state', 'order_shipping_information.state_id', '=', 'shipping_state.id')
                ->join('pincodes as shipping_pincode', 'order_shipping_information.pincode_id', '=', 'shipping_pincode.id')
                ->select('order_create.*','order_weight_information.*','order_billing_information.id as billing_id', 'order_billing_information.user_id as billing_user_id', 'order_billing_information.first_name as billing_first_name', 'order_billing_information.last_name as billing_last_name', 'order_billing_information.company_name as billing_company_name', 'order_billing_information.mobile_number as billing_mobile', 'order_billing_information.address as billing_address', 'order_billing_information.address_optional as billing_address_optional', 'order_billing_information.city_id as billing_city_id', 'order_billing_information.state_id as billing_state_id', 'order_billing_information.pincode_id as billing_pincode_id', 'order_billing_information.gst_number as billing_gst_number', 'order_billing_information.created_at as billing_created_at', 'order_billing_information.updated_at as billing_updated_at','order_pricing_information.*','order_shipping_information.id as shipping_id', 'order_shipping_information.user_id as shipping_user_id', 'order_shipping_information.first_name as shipping_first_name', 'order_shipping_information.last_name as shipping_last_name', 'order_shipping_information.company_name as shipping_company_name', 'order_shipping_information.mobile_number as shipping_mobile_number', 'order_shipping_information.address as shipping_address', 'order_shipping_information.address_optional as shipping_address_optional', 'order_shipping_information.city_id as shipping_city_id', 'order_shipping_information.state_id as shipping_state_id', 'order_shipping_information.pincode_id as shipping_pincode_id', 'order_shipping_information.address_type as shipping_address_type', 'order_shipping_information.same_as_billing as shipping_same_as_billing', 'order_shipping_information.created_at as shipping_created_at', 'order_shipping_information.updated_at as shipping_updated_at','shipping_pincode.name as shipping_pincode', 'shipping_state.name as shipping_state', 'shipping_city.name as shipping_city','billing_pincode.name as billing_pincode', 'billing_state.name as billing_state', 'billing_city.name as billing_city', 'order_multiple_product_information.id as product_id', 'order_multiple_product_information.name as product_name', 'order_multiple_product_information.quantity as product_quantitiy', 'order_multiple_product_information.amount as product_amount', 'order_multiple_product_information.sku as product_sku', 'order_multiple_product_information.created_at as product_created_at', 'order_multiple_product_information.updated_at as product_updated_at', 'order_multiple_product_information.status as product_status' )
                //->offset(0)
                //->limit(1)
                //->groupBy('order_multiple_product_information')
                ->get();


                if (DB::table('order_multiple_product_information')->whereRaw("user_id = '".$user_id."'")->exists())
                    {
                        $data_from_db_add1 = DB::table('order_multiple_product_information')
                        ->whereRaw("user_id = '".$user_id."'")
                        ->get();

                    }

                //add array
                $respomse_final_array=array('orders' => $data_from_db, 'products' =>$data_from_db_add1);

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
                        "output_data" => $respomse_final_array,
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

