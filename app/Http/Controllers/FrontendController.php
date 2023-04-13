<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Flow\Flow;

use App\Models\InstanceModule;

class FrontendController extends Controller
{
    // Triggered when the (GET) frontend Route is called
    // Route Name: getFrontendView
    // This will show the frontend of an module belonging to an instance
    public function getFrontendView($instance_module_id = null) {
        if (!$instance_module_id) { abort(404); }
        $instance_module = InstanceModule::find($instance_module_id);
        if (!$instance_module) { abort(404); }
        if (!$instance_module->module) { abort(500); }
        if (!$instance_module->public_access && (!\Auth::check() || !\Auth::user()->canAccessInstance($instance_module->instance_id))) { abort(404); }
        if ($instance_module->paid_until < \Carbon\Carbon::now()) { abort(404); }
        return $instance_module->getFrontendView($instance_module);
    }
}
