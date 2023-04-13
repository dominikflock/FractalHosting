<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\LinkerUserRole;
use App\Models\Message;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /*
     * The attributes that should be loaded by default.
     *
     * @var array<int, string>
     */
    protected $with = array('instances', 'roles');

    /**
     * Get the instances for the user.
     */
    public function instances()
    {
        return $this->belongsToMany(Instance::class, 'linker_instance_users');
    }

    /**
     * Get the roles for the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'linker_user_roles');
    }

    /**
     * User has role
     */
    public function hasRole($role_id)
    {
        return $this->roles->contains($role_id);
    }

    /**
     * Can user Access Instance
     */
    public function canAccessInstance($instance_id)
    {
        return $this->instances->contains($instance_id);
    }

    /**
     * Add user Role
     */
    public function addRole($role_id) {
        if (!$this->hasRole($role_id)) {
            LinkerUserRole::create([
                'user_id' => $this->id,
                'role_id' => $role_id
            ]);
        }
    }

    /**
     * Remove user Role
     */
    public function removeRole($role_id) {
        if ($this->hasRole($role_id)) {
            LinkerUserRole::where([['user_id', $this->id], ['role_id', $role_id]])->delete();
        }
    }

    /**
     * Clear user Roles on Instance
     */
    public function clearRoles($instance_id) {
        $roles = $this->roles->where('instance_id', $instance_id);
        foreach ($roles as $role) {
            $this->removeRole($role->id);
        }
    }


    /**
     * Has user access, Cached
     */
    protected $permissionsCache = null;
    public function hasPermission($permission_id, $instance_id) {
        if ($this->canAccessInstance($instance_id)) {
            $instance = $this->instances->find($instance_id);
            if ($instance->owner_id == $this->id) {
                return true;
            }
            if ($this->permissionsCache == null) {
                foreach ($this->roles as $role) {
                    if (!isset($this->permissionsCache[$role->instance_id])) {
                        $this->permissionsCache[$role->instance_id] = array();
                    }
                    foreach ($role->permissions as $permission) {
                        $this->permissionsCache[$role->instance_id][$permission->id] = true;
                    }
                }
            }
            return isset($this->permissionsCache[$instance_id][$permission_id]);
        }
        return false;
    }
    public function hasPermissionAny($permissions, $instance_id) {
        foreach ($permissions as $permission_id) {
            if ($this->hasPermission($permission_id, $instance_id)) {
                return true;
            }
        }
        return false;
    }

    /**
     * === ALTERNATIVE TO BE TESTED ON HIGH LOADS | UNCACHED === Has user access
     */
    /*public function hasPermission($permission, $instance_id) {
        if ($this->canAccessInstance($instance_id)) {
            $instance = $this->instances->find($instance_id);
            if ($instance->owner_id == $this->id) {
                return true;
            }
            if ($permissionsCache == null) {
                foreach ($this->roles as $role) {
                    if ($role->instance_id == $instance_id) {
                        if ($role->permissions->contains('name', $permission)) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }*/

    /**
     * Get the messages for the user.
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Get unread messages for the user.
     */
    public function unreadMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id')->where('read', null);
    }
    
}
