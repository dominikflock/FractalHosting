<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\InstanceModule;
use App\Models\Instance;
use App\Models\Order;

class Module extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'controller_path',
        'fa_icon',
    ];

    // Resolves the Controller of a Module
    private function getController() {
        $controller = $this->controller_path;
        return new $controller;
    }

    // Triggered when activating a Module
    public function onActivation(InstanceModule $instance_module) : void {
        $this->getController()->onActivation($instance_module);
    }
    
    // Triggered when deactivating a Module
    public function onDeactivation(InstanceModule $instance_module) : void {
        $this->getController()->onDeactivation($instance_module);
    }

    // Triggered when looking into the Module on the Backend
    public function getBackendView(InstanceModule $instance_module) {
        return $this->getController()->getBackendView($instance_module);
    }

    // Triggered when looking into the Module on Frontend
    public function getFrontendView(InstanceModule $instance_module) {
        return $this->getController()->getFrontendView($instance_module);
    }

    // Triggered when looking into the Backend / the Navigation renders
    public function getNotificationCount(InstanceModule $instance_module) : int {
        return $this->getController()->getNotificationCount($instance_module);
    }

    // Triggered when an API call is done
    public function matchApiCall(Request $request, Instance $instance) {
        return $this->getController()->matchApiCall($request, $instance);
    }

    // Triggered when a CronJob is executed
    public function cronTask() : void {
        $this->getController()->cronTask();
    }

    // Triggered when the price of the Module is calculated
    public function calculatePriceByMonths(InstanceModule $instance_module, int $months_to_book) : float {
        return $this->getController()->calculatePriceByMonths($instance_module, $months_to_book);
    }

    // Triggered when instance module is paid
    public function onTransactionPaid(InstanceModule $instance_module, Order $order) : void {
        $this->getController()->onTransactionPaid($instance_module, $order);
    }

    // Triggered when instance module transaction is expired
    public function onTransactionExpired(InstanceModule $instance_module, Order $order) : void {
        $this->getController()->onTransactionExpired($instance_module, $order);
    }

    // Triggered when instance module is canceled
    public function onTransactionCanceled(InstanceModule $instance_module, Order $order) : void {
        $this->getController()->onTransactionCanceled($instance_module, $order);
    }
}
