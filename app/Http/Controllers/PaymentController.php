<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Flow\Flow;

use App\Models\Instance;
use App\Models\InstanceModule;
use App\Models\Order;


class PaymentController extends Controller
{
    // Triggered when the (GET) module payment Route is called
    // Route Name: getModuleCheckout
    // This will show the payment form for a module
    public function getModuleCheckout($instance_id, $instance_module_id) {
        if (!\Auth::user()->hasPermission(3, $instance_id)) { return redirect()->route('getInstanceContext', ['instance_id' => $instance_id]); }
        $instance_module = InstanceModule::find($instance_module_id);
        $instance = Instance::find($instance_id);
        $module = $instance_module->module;
        return view('payment.module', [
            'instance_module' => $instance_module,
            'instance' => $instance,
            'module' => $module,
            'user' => \Auth::user()
        ]);
    }

    // Triggered when the (POST) module payment Route is called
    // Route Name: getModuleCheckout
    // This will show the payment form for a module
    public function postModuleCheckout(Request $request, $instance_id, $instance_module_id) {
        $flow = new Flow($request);
        if (!\Auth::user()->hasPermission(3, $instance_id)) { return $flow->redirect(route('getInstanceContext', ['instance_id' => $instance_id]))->response(); }
        $data = $flow->getData();
        if (!property_exists($data, "duration")) { return $flow->redirect(route('getInstanceContext', ['instance_id' => $instance_id]))->response(); }
        $instance_module = InstanceModule::find($instance_module_id);
        $instance = Instance::find($instance_id);
        $module = $instance_module->module;
        $order = Order::create([
            'instance_id' => $instance_id,
            'instance_module_id' => $instance_module_id,
            'module_id' => $module->id,
            'payment_status' => 'pending',
            'duration_booked' => $data->duration,
        ]);
        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey(env('MOLLIE_API_KEY'));
        $payment = $mollie->payments->create([
            "amount" => [
                "currency" => "EUR",
                "value" => number_format($instance_module->calculatePriceByMonths($data->duration), 2)
            ],
            "description" => "Payment for Module: " . $module->name,
            "redirectUrl" => route('getInstanceContext', ['instance_id' => $instance_id]),
            "webhookUrl" => route('postWebhook'),
            "metadata" => [
                "order_id" => $order->id,
            ],
        ]);
        $order->mollie_transaction_id = $payment->id;
        $order->save();
        return $flow->redirect($payment->getCheckoutUrl())->response();
    }

    // Triggered when the (POST) module payment webhook Route is called
    // Route Name: postWebhook
    // This will process the webhook from Mollie
    public function postWebhook(Request $request) {
        $mollie_transaction_id = $request->input('id');
        if (!$mollie_transaction_id) { return; }
        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey(env('MOLLIE_API_KEY'));
        $payment = $mollie->payments->get($mollie_transaction_id);
        $order = Order::where('mollie_transaction_id', $mollie_transaction_id)->first();
        if (!$order) { return; }
        $order->payment_status = $payment->status;
        $order->save();
        $instance_module = InstanceModule::find($order->instance_module_id);
        switch ($payment->status) {
            case "paid":
                if (!$order->finished_at) {
                    $instance_module->addRuntimeAsMonths($order->duration_booked);
                }
                $instance_module->onTransactionPaid($order);
                break;
            case "expired":
                $instance_module->onTransactionExpired($order);
                break;
            case "canceled":
                $instance_module->onTransactionCanceled($order);
                break;
        }
        if (!$order->finished) {
            $order->finished_at = now();
            $order->save();
        }
    }
}
