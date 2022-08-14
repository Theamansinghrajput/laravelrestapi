<?php

namespace App\Http\Controllers;

use App\Models\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiResponseController extends Controller
{
    public function  responseformating(array $output_response_data)
    {
        return response($output_response_data);//output
    }
}
