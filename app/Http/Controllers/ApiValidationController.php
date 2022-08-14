<?php

namespace App\Http\Controllers;

use App\Models\ApiValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiValidationController extends Controller
{
    //api validation method
    public function api_access_validate($api_token_access)
    {
        if (DB::table('api_key_setup')->whereRaw("api_key = '".$api_token_access."' and status = 1" )->exists())
            {
                return 'success';
            }
            else
            {
                return 'failed';
            }
    }
//api validation error message
    public function api_access_error_message()
    {
        $key=1;
        $StatusCode='';
        $CodeMessage='API key error.';
        $Message='API key validation failed, Please check you API key.';
        $api_status_code = (new ErrorCodeController)->status_code_handler_from_event($key,$StatusCode,$CodeMessage,$Message);
        return $api_status_code;
    }
//api validation success message
    public function api_access_success_message()
    {
        $key=0;
        $StatusCode='';
        $CodeMessage='API connected successfully.';
        $Message='API key has been authenticated succesfully.';
        $api_status_code = (new ErrorCodeController)->status_code_handler_from_event($key,$StatusCode,$CodeMessage,$Message);
        return $api_status_code;
    }
}
