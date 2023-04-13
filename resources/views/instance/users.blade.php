@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-12 box mb-2">
            <h1>Benutzer</h1>
            <hr>
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-md-5 col-sm-12">
                    @foreach ($instance_users as $instance_user)
                        <a href="{{route('getInstanceUsers', ['instance_id' => $instance_id, 'user_id' => $instance_user->id])}}" class="menuitem {{isset($instance_user_selected) && $instance_user_selected->id == $instance_user->id ? 'active' : ''}}">{{$instance_user->firstname}} {{$instance_user->lastname}}</a>
                    @endforeach
                    <a href="{{route('getInstanceInvite', ['instance_id' => $instance_id])}}" class="menuitem"><i class="fa-solid fa-plus"></i> Einladen</a>
                </div>
                <div class="col-xl-9 col-lg-8 col-md-7 col-sm-12">
                    @if (isset($instance_user_selected))
                        <form>
                            <h2>{{$instance_user_selected->firstname}} {{$instance_user_selected->lastname}} <small>({{$instance_user_selected->email}})</small></h2>
                            <hr>
                            <h3>Rollen</h3>
                            @if (count($roles) > 0)
                                @foreach ($roles as $role) 
                                    <label>
                                        <input type="checkbox" name="role-{{$role->id}}" {{$instance_user_selected->hasRole($role->id) ? 'checked' : ''}}>
                                        {{$role->name}}
                                    </label>
                                @endforeach
                            @else
                                <h3 class="text-center">Es existieren keine Rollen.</h3>
                            @endif 
                            <br>
                            <br>
                            <button class="btn btn-primary" flow="{{route('postUpdateUser', ['instance_id' => $instance_id, 'user_id' => $instance_user_selected->id])}}">Speichern</button>
                            @if ($instance->owner_id != $instance_user_selected->id)
                                <button class="btn btn-default" flow="{{route('postKickUser', ['instance_id' => $instance_id, 'user_id' => $instance_user_selected->id])}}">Benutzer entfernen</button>
                            @endif
                        </form>
                    @else
                        <h2 class="text-center">Kein Benutzer ausgewählt</h2>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection