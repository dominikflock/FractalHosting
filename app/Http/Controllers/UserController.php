<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Flow\Flow;
use App\Models\Message;
use App\Models\InstanceInvite;
use App\Models\Instance;
use App\Models\User;

class UserController extends Controller
{

    /* ==================== PROFILE ==================== */

    // Triggered when the (GET) user profile Route is called
    // Route Name: getProfile
    // This will show an users profile
    public function getProfile(Request $request) {
        return view('user.profile', [
            'user' => \Auth::user(),
        ]);
    }

    // Triggered when the (POST) user profile update Route is called
    // Route Name: postUpdateProfile
    // This will process an request to update a users profile
    public function postUpdateProfile(Request $request) {
        $flow = new Flow($request);
        $data = $flow->getData();
        $user = \Auth::user();
        $user->firstname = $data->firstname;
        $user->lastname = $data->lastname;
        $user->save();
        $flow->redirect(route('getProfile'));
        return $flow->response();
    }

    /* ==================== INVITE ==================== */

    // Triggered when the (POST) accept instance invite Route is called
    // Route Name: getAcceptInvite
    // This will show & process an users invitation accept request
    public function getAcceptInvite(Request $request, $invite_token) {
        $invite = InstanceInvite::where([['token', $invite_token],['user_id', \Auth::user()->id]])->first();
        if ($invite) {
            $instance = Instance::find($invite->instance_id);
            $instance->addUser(\Auth::user()->id);
            $invite->delete();
            return view('user.invite', [
                'user' => User::find(\Auth::user()->id),
                'invite' => $invite,
                'instance' => $instance
            ]);
        }
        return view('user.invite', [
            'user' => \Auth::user(),
        ]);
    }

    /* ==================== MESSAGES ==================== */

    // Triggered when the (POST) user message overview Route is called
    // Route Name: getMessages
    // This will show the messages of an user
    public function getMessages(Request $request, $message_id = null) {
        if ($message_id) {
            Message::where([['id', $message_id],['receiver_id', \Auth::user()->id]])->update(['read' => \Carbon\Carbon::now()->timestamp]);
            return view('user.messages', [
                'user' => \Auth::user(),
                'messages' => \Auth::user()->messages(),
                'message_selected' => \Auth::user()->messages()->where('id', $message_id)->first()
            ]);
        }
        return view('user.messages', [
            'user' => \Auth::user(),
            'messages' => \Auth::user()->messages()
        ]);
    }

    // Triggered when the (POST) user message delete Route is called
    // Route Name: postDeleteMessage
    // This will process an request to delete a users messages
    public function postDeleteMessage(Request $request, $message_id) {
        $flow = new Flow($request);
        if ($message_id) {
            $message = Message::where([['id', $message_id],['receiver_id', \Auth::user()->id]])->first();
            if ($message) {
                $message->delete();
            }
        }
        return $flow->redirect(route('getMessages'))->response();
    }
}
