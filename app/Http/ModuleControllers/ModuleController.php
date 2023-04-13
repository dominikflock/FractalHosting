<?php

namespace App\Http\ModuleControllers;

use Illuminate\Http\Request;
use App\Flow\Flow;

use App\Models\InstanceModule;
use App\Models\Instance;
use App\Models\Order;

class ModuleController extends \App\Http\Controllers\Controller
{

    // Triggered when activating a Module
    public function onActivation(InstanceModule $instance_module) : void {
        return;
    }
    
    // Triggered when deactivating a Module
    public function onDeactivation(InstanceModule $instance_module) : void {
        return;
    }
    
    // Triggered when looking into the Module on the Backend
    public function getBackendView(InstanceModule $instance_module) {
        return null;
    }

    // Triggered when looking into the Module on Frontend
    public function getFrontendView(InstanceModule $instance_module) {
        return null;
    }

    // Triggered when looking into the Backend / the Navigation renders
    public function getNotificationCount(InstanceModule $instance_module) : int {
        return 0;
    }
    
    // Triggered when an API call is done
    public function matchApiCall(Request $request, Instance $instance) {
        return null;
    }

    // Triggered when a CronJob is executed
    public function cronTask() : void {
        return;
    }

    // Triggered when the price of the Module is calculated
    public function calculatePriceByMonths(InstanceModule $instance_module, int $months_to_book) : float {
        return 0;
    }

    
    // Triggered when instance module is paid
    public function onTransactionPaid(InstanceModule $instance_module, Order $order) : void {
        return;
    }

    // Triggered when instance module transaction is expired
    public function onTransactionExpired(InstanceModule $instance_module, Order $order) : void {
        return;
    }

    // Triggered when instance module is canceled
    public function onTransactionCanceled(InstanceModule $instance_module, Order $order) : void {
        return;
    }
}
