<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Flow\Flow;

class AuthController extends Controller
{
    
    // Triggered when the (GET) register Route is called
    // Route Name: getRegister
    // This will show the registration form
    function getRegister() {
        return view('auth.register');
    }

    // Triggered when the (POST) register Route is called
    // Route Name: postRegister
    // This will process an registration request
    function postRegister(Request $request) {
        $flow = new Flow($request);
        // Is already logged in?
        if (\Auth::check()) {
            return $flow->redirect(route('getInstanceContext'))->response();
        }
        $data = $flow->getData();
        if (empty($data->firstname) || empty($data->lastname) || empty($data->email) || empty($data->password) || empty($data->retype_password)) {
            return $flow->popup(view('popups.default', [
                'title' => 'Fehlende Angaben',
                'content' => 'Bitte fülle alle Felder aus.',
            ])->render(), 3000)->response();
        }
        if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            return $flow->popup(view('popups.default', [
                'title' => 'Ungültige E-Mail Adresse',
                'content' => 'Die angegebene E-Mail Adresse ist ungültig.',
            ])->render(), 3000)->response();
        }
        $user = \App\Models\User::where('email', $data->email)->first();
        if ($user) {
            return $flow->popup(view('popups.default', [
                'title' => 'Benutzer bereits vorhanden',
                'content' => 'Es existiert bereits ein Benutzer mit dieser E-Mail Adresse.',
            ])->render(), 3000)->response();
        }
        if ($data->password != $data->retype_password) {
            return $flow->popup(view('popups.default', [
                'title' => 'Passwörter stimmen nicht überein',
                'content' => 'Die beiden Passwörter stimmen nicht überein.',
            ])->render(), 3000)->response();
        }
        \App\Models\User::create([
            "firstname" => $data->firstname,
            "lastname" => $data->lastname,
            "email" => $data->email,
            "password" => md5($data->password),
        ]);
        return $flow->modify(BY_QUERY, OUTER_HTML, 'form', 'Sie haben sich erfolgreich registriert! Sie können sich <a href="'.route('getLogin').'">hier</a> anmelden.')->response();
    }

    // Triggered when the (GET) login Route is called
    // Route Name: getLogin
    // This will show the login form
    function getLogin() {
        return view('auth.login');
    }

    // Triggered when the (POST) login Route is called
    // Route Name: postLogin
    // This will process an login request
    function postLogin(Request $request) {
        $flow = new Flow($request);
        // Is already logged in?
        if (\Auth::check()) {
            return $flow->redirect(route('getInstanceContext'))->response();
        }
        $data = $flow->getData();
        $user = \App\Models\User::where([['email', $data->email],['password', md5($data->password)]])->first();
        if (!$user) {
            return $flow->popup(view('popups.default', [
                'title' => 'Benutzer nicht gefunden',
                'content' => 'Die angegebenen Zugangsdaten sind nicht korrekt.',
            ])->render(), 3000)->response();
        }
        \Auth::loginUsingId($user->id);
        return $flow->redirect(route('getInstanceContext'))->response();
    }

    // Triggered when the (GET) logout Route is called
    // Route Name: getLogout
    // This will logout the user
    function getLogout() {
        \Auth::logout();
        return redirect(route('getLogin'));
    }

    // Triggered when the (POST) logout Route is called
    // Route Name: postLogout
    // This will logout the user, with flow control
    function postLogout(Request $request) {
        $flow = new Flow($request);
        \Auth::logout();
        return $flow->redirect(route('getLogin'))->response();
    }
}
