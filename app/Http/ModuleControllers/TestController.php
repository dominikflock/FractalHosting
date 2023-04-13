<?php

namespace App\Http\ModuleControllers;

use Illuminate\Http\Request;
use App\Flow\Flow;

use App\Models\Instance;
use App\Models\InstanceModule;
use App\Models\TestModuleMessages;
use App\Models\Order;

class TestController extends ModuleController
{

    // ================================ Hooks ================================

    // Triggered when activating a Module
    public function onActivation(InstanceModule $instance_module) : void {
        return;
    }
    
    // Triggered when deactivating a Module
    public function onDeactivation(InstanceModule $instance_module) : void {
        TestModuleMessages::where('instance_id', $instance_module->instance_id)->delete();
        return;
    }

    // Triggered when looking into the Module on the Backend
    public function getBackendView(InstanceModule $instance_module) {
        $messages = TestModuleMessages::where('instance_id', $instance_module->instance_id)->orderBy('created_at', 'desc')->get();
        TestModuleMessages::where([['instance_id', $instance_module->instance_id], ['seen', false]])->update(["seen" => true]);
        return view('modules.testmodule.backend.index', [
            'instance_module' => $instance_module,
            'messages' => $messages
        ]);
    }

    // Triggered when looking into the Module on Frontend
    public function getFrontendView(InstanceModule $instance_module) {
        return view('modules.testmodule.frontend.index', [
            'instance_module' => $instance_module
        ]);
    }

    // Triggered when looking into the Backend / the Navigation renders
    public function getNotificationCount(InstanceModule $instance_module) : int {
        return TestModuleMessages::where([['instance_id', $instance_module->id ], ['seen', false]])->count();
    }
    
    // Triggered when an API call is done
    public function matchApiCall(Request $request, Instance $instance) {
        if ($request->isMethod('get') && $request->input('show_message_list')) {
            return TestModuleMessages::where('instance_id', $instance->id)->get()->toArray();
        }
        if ($request->isMethod('delete') && $request->input('delete_message_list')) {
            return [
                "deleted" => TestModuleMessages::where('instance_id', $instance->id)->delete()
            ];
        }
        return;
    }

    // Triggered when a CronJob is executed
    public function cronTask() : void {
        TestModuleMessages::where('updated_by_cron', false)->update(["updated_by_cron" => true]);
        return;
    }

    // Triggered when the price of the Module is calculated
    public function calculatePriceByMonths(InstanceModule $instance_module, int $months_to_book) : float {
        return 9.99 * $months_to_book;
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

    // ================================ Frontend Functions ================================

    public function postSendMessage(Request $request, $instance_module_id) {
        $flow = new Flow($request);
        $instance_module = InstanceModule::find($instance_module_id);
        if (!$instance_module) {
            return $flow->modify(BY_ID, TEXT, "status", "Es gab einen Fehler beim senden der Nachricht!")->response();
        }
        $data = $flow->getData();
        if (!property_exists($data, "message") || empty($data->message)) {
            $flow->modify(BY_ID, TEXT, "lastMessage", "");
            return $flow->modify(BY_ID, TEXT, "status", "Bitte geben Sie eine Nachricht ein!")->response();
        }
        TestModuleMessages::create([
            'instance_id' => $instance_module->instance_id,
            'message' => $data->message
        ]);
        $flow->modify(BY_ID, VALUE, "message", "");
        $flow->modify(BY_ID, TEXT, "status", "Nachricht erfolgreich gesendet!");
        $flow->modify(BY_ID, TEXT, "lastMessage", $data->message);
        return $flow->response();
    }
}
