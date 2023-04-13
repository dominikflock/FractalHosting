<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Instance;

class ApiController extends Controller
{
    // Triggered when the API Route is called - Methods GET, POST, PUT, PATCH and DELETE are allowed
    // Route Name: api
    // This will call all matchApiCall module hooks of the instance which the api_access_key belongs to
    public function matchApiCall(Request $request, $api_access_key) {
        $instance = Instance::where('api_key', $api_access_key)->first();
        if (!$instance) { abort(404); }
        $instance_modules = $instance->getActiveModules();
        $results = [];
        foreach ($instance_modules as $instance_module) {
            $result = $instance_module->module->matchApiCall($request, $instance);
            if ($result) {
                $results[$instance_module->module->name] = $result;
            }
        }
        return $results != [] ? json_encode($results) : '';
    }
}
