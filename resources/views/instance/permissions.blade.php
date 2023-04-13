@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-12 box mb-2">
            <h1>Berechtigungen</h1>
            <hr>
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-md-5 col-sm-12">
                    @foreach ($roles as $role)
                        <a href="{{route('getInstancePermissions', ['instance_id' => $instance_id, 'role_id' => $role->id])}}" class="menuitem {{isset($role_selected) && $role_selected->id == $role->id ? 'active' : ''}}">{{$role->name}}</a>
                    @endforeach
                    <a class="menuitem" flow="{{route('postCreateRole', ['instance_id' => $instance_id])}}"><i class="fa-solid fa-plus"></i> Neue Rolle</a>
                </div>
                <div class="col-xl-9 col-lg-8 col-md-7 col-sm-12">
                    @if (isset($role_selected))
                        <form>
                            <label>Rollenname</label>
                            <input type="text" value="{{$role_selected->name}}" name="role_name">
                            <hr>
                            @foreach ($permission_groups as $group)
                                <h3>{{$group->name}}</h3>
                                <div class="row">
                                    @foreach ($group->getPermissions() as $permission)
                                        <div class="col-xl-4 col-lg-6 col-md-12 col-sm-12 mt-3">
                                            <label>
                                                <input type="checkbox" name="permission-{{$permission->id}}" {{$role_selected->hasPermission($permission->id) ? 'checked' : ''}}>
                                                {{$permission->name}}<br>
                                                <small>{{$permission->description}}</small>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                            <br>
                            <button class="btn btn-primary" flow="{{route('postUpdateRole', ['instance_id' => $instance_id, 'role_id' => $role_selected->id])}}">Speichern</button>
                            <button class="btn btn-default" flow="{{route('postDeleteRole', ['instance_id' => $instance_id, 'role_id' => $role_selected->id])}}">Rolle löschen</button>
                        </form>
                    @else
                        <h2 class="text-center">Keine Rolle ausgewählt</h2>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection