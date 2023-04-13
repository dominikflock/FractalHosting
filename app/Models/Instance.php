<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\LinkerInstanceUser;
use App\Models\Role;
use App\Models\InstanceInvite;
use App\Models\InstanceModule;

class Instance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'owner_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    public function delete() {
        Role::where('instance_id', $this->id)->delete();
        LinkerInstanceUser::where('instance_id', $this->id)->delete();
        InstanceInvite::where('instance_id', $this->id)->delete();
        return parent::delete();
    }

    public function addUser($user_id) {
        $linker = LinkerInstanceUser::create([
            'instance_id' => $this->id,
            'user_id' => $user_id
        ]);
        return $this;
    }

    public function removeUser($user_id) {
        $user = User::find($user_id);
        if ($user->id == $this->owner_id) {
            return $this;
        }
        LinkerInstanceUser::where('instance_id', $this->id)->where('user_id', $user_id)->delete();
        $user->clearRoles($this->id);
        return $this;
    }

    public function getRoles() {
        return Role::where('instance_id', $this->id)->get();
    }

    public function getUsers() {
        $users = [];
        $linkers = LinkerInstanceUser::where('instance_id', $this->id)->get();
        foreach ($linkers as $linker) {
            $users[] = $linker->getUser();
        }
        return $users;
    }

    public function getActiveModules() {
        return InstanceModule::where('instance_id', $this->id)->get();
    }

    public function getModule($module_id) {
        return InstanceModule::where([['instance_id', $this->id],['module_id', $module_id]])->first();
    }

    public function activateModule($module_id) {
        $instance_module = InstanceModule::where([['instance_id', $this->id],['module_id', $module_id]])->first();
        if (!$instance_module) {
            $instance_module = InstanceModule::create([
                'instance_id' => $this->id,
                'module_id' => $module_id
            ]);
            $module = $instance_module->module = Module::find($module_id);
            $module->onActivation($instance_module);
        }
        return $instance_module;
    }

    public function deactivateModule($module_id) {
        $instance_module = InstanceModule::where([['instance_id', $this->id],['module_id', $module_id]])->first();
        if ($instance_module) {
            $module = $instance_module->module = Module::find($module_id);
            $module->onDeactivation($instance_module);
            $instance_module->delete();
        }
        return $this;
    }

}
