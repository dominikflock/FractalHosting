<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\LinkerRolePermission;
use App\Models\LinkerUserRole;

class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'instance_id'
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

    
    /*
     * The attributes that should be loaded by default.
     *
     * @var array<int, string>
     */
    protected $with = array('permissions');

    /**
     * Get the permissions for the role.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'linker_role_permissions');
    }

    public function delete() {
        LinkerRolePermission::where('role_id', $this->id)->delete();
        LinkerUserRole::where('role_id', $this->id)->delete();
        return parent::delete();
    }

    public function hasPermission($permissionId) {
        $permissions = $this->permissions;
        foreach($permissions as $permission) {
            if($permission->id == $permissionId) {
                return true;
            }
        }
        return false;
    }

    public function addPermission($permissionId) {
        if (Permission::find($permissionId) == null) {
            return;
        }
        LinkerRolePermission::create([
            'role_id' => $this->id,
            'permission_id' => $permissionId
        ]);
    }

    public function removePermission($permissionId) {
        LinkerRolePermission::where('role_id', $this->id)->where('permission_id', $permissionId)->delete();
    }

    public function clearPermissions() {
        LinkerRolePermission::where('role_id', $this->id)->delete();
    }
}
