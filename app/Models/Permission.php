<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
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
        'assignable',
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
    protected $with = array('group');

    /**
     * Get the group for the permission.
     */
    public function group()
    {
        return $this->belongsTo(PermissionGroup::class, 'permission_group');
    }
}
