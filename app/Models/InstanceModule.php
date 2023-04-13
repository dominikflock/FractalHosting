<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Module;
use App\Models\Order;

class InstanceModule extends Model
{
    use HasFactory;
    
    /**
    * The attributes that are mass assignable.
    *
    * @var array<int, string>
    */
   protected $fillable = [
       'instance_id',
       'module_id',
   ];

   
   protected $casts = [
        'paid_until' => 'datetime',
    ];

   /*
     * The attributes that should be loaded by default.
     *
     * @var array<int, string>
     */
    protected $with = array('module');

    /**
     * Get the module
     */
    public function module() {
        return $this->belongsTo(Module::class);
    }

    // Triggered when activating a Module
    public function onActivation() : void {
        $this->module->onActivation($this);
    }
    
    // Triggered when deactivating a Module
    public function onDeactivation() : void {
        $this->module->onDeactivation($this);
    }
    
    // Triggered when looking into the Module on the Backend
    public function getBackendView() {
        return $this->module->getBackendView($this);
    }

    // Triggered when looking into the Module on Frontend
    public function getFrontendView() {
        return $this->module->getFrontendView($this);
    }

    // Triggered when looking into the Backend / the Navigation renders
    public function getNotificationCount() : int {
        return $this->module->getNotificationCount($this);
    }
    
    // Triggered when an API call is done
    public function matchApiCall(Request $request, Instance $instance) {
        return $this->module->matchApiCall($request, $instance);
    }

    // Triggered when a CronJob is executed
    public function cronTask() : void {
        $this->module->cronTask();
    }

    // Triggered when the price of the Module is calculated
    public function calculatePriceByMonths(int $months_to_book) : float {
        return $this->module->calculatePriceByMonths($this, $months_to_book);
    }

    // Triggered when instance module is paid
    public function onTransactionPaid(Order $order) : void {
        $this->module->onTransactionPaid($this, $order);
    }

    // Triggered when instance module transaction is expired
    public function onTransactionExpired(Order $order) : void {
        $this->module->onTransactionExpired($this, $order);
    }

    // Triggered when instance module is canceled
    public function onTransactionCanceled(Order $order) : void {
        $this->module->onTransactionCanceled($this, $order);
    }

    // Add runtime (months)
    public function addRuntimeAsMonths(int $months) : void {
        if ($this->paid_until == null) {
            $this->paid_until = now();
        }
        $this->paid_until = $this->paid_until->addMonths($months);
        if (!$this->wasRecentlyCreated) {
            $this->save();
        }
    }

    // Changes the public access of the Module instance
    public function setPublicAccess(bool $public_access) : void {
        $this->public_access = $public_access;
        if (!$this->wasRecentlyCreated) {
            $this->save();
        }
    }
}
