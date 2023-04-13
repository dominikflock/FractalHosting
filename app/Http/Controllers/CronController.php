<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Module;

class CronController extends Controller
{
    // Triggered when the (GET) cronjob Route is called
    // Route Name: cron
    // This will call all cron module hooks
    public function cron($cron_access_key) {
        if ($cron_access_key != env('CRON_ACCESS_KEY')) { abort(404); }
        $time_start = microtime(true);
        $modules = Module::all();
        foreach($modules as $module) {
            $module->cronTask();
        }
        $time_end = number_format(microtime(true) - $time_start, 4);
        return "Cron finished in $time_end seconds";
    }
}
