<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Flow\Flow;

use App\Models\Instance;
use App\Models\Role;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\User;
use App\Models\InstanceInvite;
use App\Models\Message;
use App\Models\Module;
use App\Models\InstanceModule;

class InstanceController extends Controller
{

    /* ==================== BASIC CONTEXT ==================== */

    // Triggered when the (GET) instance context Route is called
    // Route Name: getInstanceContext
    // This will show the basic backend interface
    function getInstanceContext(Request $request, $instance_id = null) {
        if ($instance_id) {
            if (\Auth::user()->canAccessInstance($instance_id)) {
                $instance = Instance::find($instance_id);
                $instance_modules = $instance->getActiveModules();
                return view('instance.index', [
                    'user' => \Auth::user(),
                    'instance_id' => $instance_id,
                    'instance_modules' => $instance_modules,
                ]);
            } else {
                return redirect()->route('getInstanceContext');
            }
        }
        return view('instance.index', [
            'user' => \Auth::user()
        ]);
    }

    // Triggered when the (POST) instance creation Route is called
    // Route Name: postCreateInstance
    // This will process a instance creation request
    function postCreateInstance(Request $request) {
        $flow = new Flow($request);
        $instance = Instance::create([
            'name' => 'Neue Instanz',
            'owner_id' => \Auth::user()->id,
        ]);
        $instance->addUser(\Auth::user()->id);
        $flow->redirect(route('getInstanceContext', ['instance_id' => $instance->id]));
        return $flow->response();
    }

    /* ==================== SETTINGS ==================== */

    // Triggered when the (GET) instance settings Route is called
    // Route Name: getInstanceSettings
    // This will show the settings of an instance
    function getInstanceSettings(Request $request, $instance_id) {
        if (!\Auth::user()->hasPermission(2, $instance_id)) { return redirect()->route('getInstanceContext', ['instance_id' => $instance_id]); }
        $instance = Instance::find($instance_id);
        return view('instance.settings', [
            'user' => \Auth::user(),
            'instance_id' => $instance_id,
            'instance' => $instance,
            'instance_modules' => $instance->getActiveModules(),
        ]);
    }

    // Triggered when the (POST) instance settings update Route is called
    // Route Name: postInstanceUpdate
    // This will process an instance settings update request
    function postInstanceUpdate(Request $request, $instance_id) {
        $flow = new Flow($request);
        if (!\Auth::user()->hasPermission(2, $instance_id)) { return $flow->redirect(route('getInstanceContext', ['instance_id' => $instance_id]))->response(); }
        $data = $flow->getData();
        $instance = Instance::find($instance_id);
        $instance->name = $data->instance_name;
        $instance->save();
        $flow->redirect(route('getInstanceSettings', ['instance_id' => $instance_id]));
        return $flow->response();
    }

    // Triggered when the (POST) instance delete Route is called
    // Route Name: postDeleteInstance
    // This will process an instance delete request
    function postDeleteInstance(Request $request, $instance_id) {
        $flow = new Flow($request);
        if (!\Auth::user()->hasPermission(2, $instance_id)) { return $flow->redirect(route('getInstanceContext', ['instance_id' => $instance_id]))->response(); }
        $instance = Instance::find($instance_id);
        if (!$request->input('confirmed')) {
            return $flow->popup(view('popups.confirmation', [
                'title' => 'Instanz löschen',
                'content' => 'Möchten Sie die Instanz "' . $instance->name . '" wirklich löschen?<br>Diese Aktion kann nicht rückgängig gemacht werden.',
                'confirm_url' => route('postDeleteInstance', ['instance_id' => $instance_id, 'confirmed' => true]),
            ])->render(), null)->response();
        }
        // Delete Roles
        foreach ($instance->getRoles() as $role) {
            $role->delete();
        }
        // Delete Instance
        $instance->delete();
        $flow->redirect(route('getInstanceContext'));
        return $flow->response();
    }

    // Triggered when the (POST) instance API key renewal Route is called
    // Route Name: postRenewApiKey
    // This will process an instance api key renewal request
    public function postRenewApiKey(Request $request, $instance_id) {
        $flow = new Flow($request);
        if (!\Auth::user()->hasPermission(2, $instance_id)) { return $flow->redirect(route('getInstanceContext', ['instance_id' => $instance_id]))->response(); }
        if (!$request->input('confirmed')) {
            return $flow->popup(view('popups.confirmation', [
                'title' => 'API-Schlüssel erneuern',
                'content' => 'Möchten Sie wirklich den API-Schlüssel dieser Instanz erneuern? Dieser Vorgang kann nicht rückgängig gemacht werden.',
                'confirm_url' => route('postRenewApiKey', ['instance_id' => $instance_id, 'confirmed' => true]),
            ])->render(), null)->response();
        }
        $instance = Instance::find($instance_id);
        $new_api_key = Flow::generateUID(null, 128);
        $instance->api_key = $new_api_key;
        $instance->save();
        $flow->modify(BY_ID, VALUE, "api_key", $new_api_key);
        return $flow->response();
    }

    /* ==================== PERMISSIONS ==================== */

    // Triggered when the (GET) instance permission overview Route is called
    // Route Name: getInstancePermissions
    // This will show the instance permissions
    function getInstancePermissions(Request $request, $instance_id, $role_id = null) {
        if (!\Auth::user()->hasPermission(1, $instance_id)) { return redirect()->route('getInstanceContext', ['instance_id' => $instance_id]); }
        $instance = Instance::find($instance_id);
        $roles = Instance::find($instance_id)->getRoles();
        $instance_modules = $instance->getActiveModules();
        if ($role_id) {
            $role_selected = Role::find($role_id);
            $permission_groups = PermissionGroup::all();
            return view('instance.permissions', [
                'user' => \Auth::user(),
                'instance_id' => $instance_id,
                'role_selected' => $role_selected,
                'roles' => $roles,
                'permission_groups' => $permission_groups,
                'instance_modules' => $instance_modules
            ]);
        }
        return view('instance.permissions', [
            'user' => \Auth::user(),
            'instance_id' => $instance_id,
            'roles' => $roles,
            'instance_modules' => $instance_modules
        ]);
    }

    // Triggered when the (POST) instance create role Route is called
    // Route Name: postCreateRole
    // This will process an instance role creation request
    function postCreateRole(Request $request, $instance_id) {
        $flow = new Flow($request);
        if (!\Auth::user()->hasPermission(1, $instance_id)) { return $flow->redirect(route('getInstanceContext', ['instance_id' => $instance_id]))->response(); }
        $role = Role::create([
            'name' => "Neue Rolle",
            'instance_id' => $instance_id,
        ]);
        $flow->redirect(route('getInstancePermissions', ['instance_id' => $instance_id, 'role_id' => $role->id]));
        return $flow->response();
    }

    // Triggered when the (POST) instance delete role Route is called
    // Route Name: postDeleteRole
    // This will process an instance role delete request
    function postDeleteRole(Request $request, $instance_id, $role_id) {
        $flow = new Flow($request);
        if (!\Auth::user()->hasPermission(1, $instance_id)) { return $flow->redirect(route('getInstanceContext', ['instance_id' => $instance_id]))->response(); }
        $role = Role::where([['id', $role_id], ['instance_id', $instance_id]])->first();
        if (!$request->input('confirmed')) {
            return $flow->popup(view('popups.confirmation', [
                'title' => 'Rolle löschen',
                'content' => 'Möchten Sie die Rolle "' . $role->name . '" wirklich löschen?<br>Diese Aktion kann nicht rückgängig gemacht werden.',
                'confirm_url' => route('postDeleteRole', ['instance_id' => $instance_id, 'role_id' => $role->id, 'confirmed' => true]),
            ])->render(), null)->response();
        }
        $role->delete();
        $flow->redirect(route('getInstancePermissions', ['instance_id' => $instance_id]));
        return $flow->response();
    }

    // Triggered when the (POST) instance update role Route is called
    // Route Name: postUpdateRole
    // This will process an instance role update request
    function postUpdateRole(Request $request, $instance_id, $role_id) {
        $flow = new Flow($request);
        if (!\Auth::user()->hasPermission(1, $instance_id)) { return $flow->redirect(route('getInstanceContext', ['instance_id' => $instance_id]))->response(); }
        $role = Role::where([['id', $role_id], ['instance_id', $instance_id]])->first();
        $data = $flow->getData();
        $role->name = $data->role_name;
        $role->save();
        $role->clearPermissions();
        foreach (Permission::all() as $permission) {
            if (property_exists($data, "permission-$permission->id")) {
                $role->addPermission($permission->id);
            } else {
                $role->removePermission($permission->id);
            }
        }
        $flow->redirect(route('getInstancePermissions', ['instance_id' => $instance_id, 'role_id' => $role->id]));
        return $flow->response();
    }

    /* ==================== USERS ==================== */

    // Triggered when the (GET) instance user overview Route is called
    // Route Name: getInstanceUsers
    // This will show the user overview of an instance
    public function getInstanceUsers(Request $request, $instance_id, $user_id = null) {
        if (!\Auth::user()->hasPermission(4, $instance_id)) { return redirect()->route('getInstanceContext', ['instance_id' => $instance_id]); }
        $instance = Instance::find($instance_id);
        $instance_users = $instance->getUsers();
        $instance_modules = $instance->getActiveModules();
        if ($user_id) {
            $instance_user_selected = User::find($user_id);
            $roles = $instance->getRoles();
            return view('instance.users', [
                'user' => \Auth::user(),
                'instance' => $instance,
                'instance_id' => $instance_id,
                'instance_users' => $instance_users,
                'instance_user_selected' => $instance_user_selected,
                'roles' => $roles,
                'instance_modules' => $instance_modules
            ]);
        }
        return view('instance.users', [
            'user' => \Auth::user(),
            'instance_id' => $instance_id,
            'instance_users' => $instance_users,
            'instance_modules' => $instance_modules
        ]);
    }

    // Triggered when the (POST) instance user update Route is called
    // Route Name: postUpdateUser
    // This will process an user update request (e.g. adding roles etc.)
    public function postUpdateUser(Request $request, $instance_id, $user_id) {
        $flow = new Flow($request);
        if (!\Auth::user()->hasPermission(4, $instance_id)) { return $flow->redirect(route('getInstanceContext', ['instance_id' => $instance_id]))->response(); }
        $data = $flow->getData();
        $user = User::find($user_id);
        foreach (Role::where('instance_id', $instance_id)->get() as $role) {
            if (property_exists($data, "role-$role->id")) {
                $user->addRole($role->id);
            }
            else {
                $user->removeRole($role->id);
            }
        }
        $flow->redirect(route('getInstanceUsers', ['instance_id' => $instance_id, 'user_id' => $user->id]));
        return $flow->response();
    }

    // Triggered when the (POST) instance user kick Route is called
    // Route Name: postKickUser
    // This will process an user kick from instance request
    public function postKickUser(Request $request, $instance_id, $user_id) {
        $flow = new Flow($request);
        if (!\Auth::user()->hasPermission(4, $instance_id)) { return $flow->redirect(route('getInstanceContext', ['instance_id' => $instance_id]))->response(); }
        $instance = Instance::find($instance_id);
        if (!$request->input('confirmed')) {
            return $flow->popup(view('popups.confirmation', [
                'title' => 'Benutzer entfernen',
                'content' => 'Möchten Sie den Benutzer wirklich aus der Instanz entfernen?',
                'confirm_url' => route('postKickUser', ['instance_id' => $instance_id, 'user_id' => $user_id, 'confirmed' => true]),
            ])->render(), null)->response();
        }
        if ($instance->owner_id == $user_id) {
            $flow->redirect(route('getInstanceUsers', ['instance_id' => $instance_id]));
            return $flow->response();
        }
        $instance->removeUser($user_id);
        $flow->redirect(route('getInstanceUsers', ['instance_id' => $instance_id]));
        return $flow->response();
    }

    /* ==================== INSTANCE INVITES ==================== */

    // Triggered when the (GET) instance invite Route is called
    // Route Name: getInstanceInvite
    // This will show the interface to invite an user to an instance
    public function getInstanceInvite(Request $request, $instance_id) {
        if (!\Auth::user()->hasPermission(4, $instance_id)) { return redirect()->route('getInstanceContext', ['instance_id' => $instance_id]); }
        $instance = Instance::find($instance_id);
        $instance_modules = $instance->getActiveModules();
        return view('instance.invite', [
            'user' => \Auth::user(),
            'instance_id' => $instance_id,
            'instance_modules' => $instance_modules
        ]);
    }

    // Triggered when the (POST) instance invite create Route is called
    // Route Name: postInviteUser
    // This will process an user invitation request
    public function postInviteUser(Request $request, $instance_id) {
        $flow = new Flow($request);
        if (!\Auth::user()->hasPermission(4, $instance_id)) { return $flow->redirect(route('getInstanceContext', ['instance_id' => $instance_id]))->response(); }
        $data = $flow->getData();
        if (strlen($data->email) == 0 || !filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            $flow->popup(view('popups.default', [
                'title' => 'Keine E-Mail-Adresse angegeben', 
                'content' => 'Bitte geben Sie eine gültige E-Mail-Adresse an.'
            ])->render());
            return $flow->response();
        }
        $user = User::where('email', $data->email)->first();
        if (!$user) {
            $flow->popup(view('popups.default', [
                'title' => 'Benutzer nicht gefunden', 
                'content' => "Der angegebene Benutzer $data->email konnte nicht gefunden werden."
            ])->render());
            return $flow->response();
        }
        if ($user->canAccessInstance($instance_id)) {
            $flow->popup(view('popups.default', [
                'title' => 'Benutzer ist bereits Mitglied', 
                'content' => "Der angegebene Benutzer $data->email ist bereits Mitglied dieser Instanz."
            ])->render());
            return $flow->response();
        }
        if ($invite = InstanceInvite::where([['user_id', $user->id], ['instance_id', $instance_id]])->first()) {
            $invite->delete();
        }
        $invite_token = Flow::generateUID(null, 64);
        InstanceInvite::create([
            'token' => $invite_token,
            'user_id' => $user->id,
            'instance_id' => $instance_id,
            'created_by' => \Auth::user()->id,
        ]);
        Message::create([
            'subject' => "Instanz-Einladung",
            'message' => "Du wurdest in die Instanz \"" . Instance::find($instance_id)->name . "\" eingeladen.<br>Du kannst über folgenden Link der Einladung folgen:<br><br><a href=\"" . route('getAcceptInvite', ['invite_token' => $invite_token]) . "\" class=\"btn btn-primary\">Hier nehmen Sie die Einladung an.</a>",
            'sender_id' => \Auth::user()->id,
            'receiver_id' => $user->id,
        ]);
        $flow->modify(
            BY_ID,
            VALUE,
            "email",
            "",
        );
        $flow->popup(view('popups.default', [
            'title' => 'Einladung gesendet', 
            'content' => "Der Benutzer $data->email wurde erfolgreich eingeladen."
        ])->render());
        return $flow->response();
    }

    /* ==================== INSTANCE MODULES ==================== */

    // Triggered when the (GET) instance module overview Route is called
    // Route Name: getInstanceModules
    // This will show all modules available to an instance and their current status
    public function getInstanceModules(Request $request, $instance_id, $instance_module_id = null) {
        if (!\Auth::user()->hasPermission(3, $instance_id)) { return redirect()->route('getInstanceContext', ['instance_id' => $instance_id]); }
        $instance = Instance::find($instance_id);
        $modules = Module::all();
        $instance_modules = $instance->getActiveModules();
        if ($instance_module_id) {
            $instance_module = InstanceModule::find($instance_module_id);
            if (!$instance_module) { return redirect()->route('getInstanceContext', ['instance_id' => $instance_id]); }
            if ($instance_module->instance_id != $instance_id) { return redirect()->route('getInstanceContext', ['instance_id' => $instance_id]); }
            $instance_module_BE_view = $instance_module->getBackendView();
            return view('instance.modules', [
                'user' => \Auth::user(),
                'instance_id' => $instance_id,
                'instance_module_id' => $instance_module_id,
                'instance_module' => $instance_module,
                'instance_module_BE_view' => $instance_module_BE_view,
                'instance_modules' => $instance_modules
            ]);
        }
        return view('instance.modules', [
            'user' => \Auth::user(),
            'instance_id' => $instance_id,
            'modules' => $modules,
            'instance_modules' => $instance_modules
        ]);
    }

    // Triggered when the (POST) instance module save/update Route is called
    // Route Name: postSaveModules
    // This will process an module save request (e.g. activating modules)
    public function postSaveModules(Request $request, $instance_id) {
        $flow = new Flow($request);
        if (!\Auth::user()->hasPermission(3, $instance_id)) { return $flow->redirect(route('getInstanceContext', ['instance_id' => $instance_id]))->response(); }
        $data = $flow->getData();
        $instance = Instance::find($instance_id);
        $modules = Module::all();
        $undeleteable_modules = [];
        // Check if is trying to deactivate a module with remaining paid time
        foreach ($modules as $module) {
            if (!property_exists($data, "module-$module->id")) {
                $instance_module = $instance->getModule($module->id);
                if ($instance_module && $instance_module->paid_until > \Carbon\Carbon::now()) {
                    $undeleteable_modules[] = $module->name;
                }
            }
        }
        if (count($undeleteable_modules) > 0) {
            return $flow->popup(view('popups.default', [
                'title' => 'Sie können diese Module nicht deaktivieren',
                'content' => 'Die folgenden Module können nicht deaktiviert werden, da sie noch bezahlte Zeit haben:<br><br>' . implode('<br>', $undeleteable_modules),
            ])->render(), 3000)->response();
        }
        foreach ($modules as $module) {
            if (property_exists($data, "module-$module->id")) {
                $instance_module = $instance->activateModule($module->id);
                $instance_module->setPublicAccess(property_exists($data, "module-$module->id-public-access"));
            } else {
                $instance->deactivateModule($module->id);
            }
        }
        return $flow->redirect(route('getInstanceModules', ['instance_id' => $instance_id]))->response();
    }
}
