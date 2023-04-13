<h1 style="margin-top: 0; margin-bottom: 20px;">
    <img src="/img/Logo.svg" style="width: 75px; vertical-align: middle;">
    {{env('APP_NAME')}}
</h1>
<hr>
@if (isset($instance_id))
    <h4>Verwaltung</h4>
    <a href="{{route('getInstanceContext', ['instance_id' => $instance_id])}}" class="menuitem {{Route::is('getInstanceContext') ? 'active' : ''}}"><i class="fa-solid fa-house"></i> Dashboard</a>
    @if ($instance_modules->count() > 0)
        @foreach ($instance_modules as $instance_module)
            <a href="{{route('getInstanceModules', ['instance_id' => $instance_id, 'instance_module_id' => $instance_module->id])}}" class="menuitem {{Route::is('getInstanceModules', ['instance_id' => $instance_id, 'instance_module_id' => $instance_module->id]) && Request::route('instance_module_id') == $instance_module->id ? 'active' : ''}}"><i class="fa-solid {{$instance_module->module->fa_icon}}"></i> {{$instance_module->module->name}} <span class="{{$instance_module->getNotificationCount() == 0 ? 'hidden' : ''}}"><i class="fa-solid fa-bell"></i> {{$instance_module->getNotificationCount()}}</span></a>
        @endforeach
    @endif
    @if ($user->hasPermissionAny([1, 2, 3], $instance_id))
        <hr>
        <h4>Administration</h4>
        @if ($user->hasPermission(3, $instance_id))
            <a href="{{route('getInstanceModules', ['instance_id' => $instance_id])}}" class="menuitem {{Route::is('getInstanceModules') && !Request::route('instance_module_id') ? 'active' : ''}}"><i class="fa-solid fa-puzzle-piece"></i> Module</a>
        @endif
        @if ($user->hasPermission(4, $instance_id))
            <a href="{{route('getInstanceUsers', ['instance_id' => $instance_id])}}" class="menuitem {{Route::is('getInstanceUsers') ? 'active' : ''}}"><i class="fa-solid fa-user"></i> Benutzer</a>
        @endif
        @if ($user->hasPermission(1, $instance_id))
            <a href="{{route('getInstancePermissions', ['instance_id' => $instance_id])}}" class="menuitem {{Route::is('getInstancePermissions') ? 'active' : ''}}"><i class="fa-solid fa-screwdriver-wrench"></i> Berechtigungen</a>
        @endif
        @if ($user->hasPermission(2, $instance_id))
            <a href="{{route('getInstanceSettings', ['instance_id' => $instance_id])}}" class="menuitem {{Route::is('getInstanceSettings') ? 'active' : ''}}"><i class="fa-solid fa-cog"></i> Einstellungen</a>
        @endif
    @endif
    <hr>
@endif
<h4>Instanz</h4>
@foreach ($user->instances as $instance)
    <a href="{{route(isset($instance_id) ? \Request::route()->getName() : 'getInstanceContext', ['instance_id' => $instance->id])}}" class="menuitem {{isset($instance_id) && $instance_id == $instance->id ? 'active' : ''}}"><i class="fa-solid fa-house"></i> {{$instance->name}}</a>
@endforeach
<a class="menuitem" flow="{{route('postCreateInstance')}}"><i class="fa-solid fa-plus"></i> Neue Instanz</a>
<hr>
<h4>{{$user->firstname}} {{$user->lastname}}</h4>
<a href="{{route('getMessages')}}" class="menuitem {{Route::is('getMessages') ? 'active' : ''}}"><i class="fa-solid fa-envelope"></i> Nachrichten <span class="{{\Auth::user()->unreadMessages()->count() == 0 ? 'hidden' : ''}}"><i class="fa-solid fa-bell"></i> {{\Auth::user()->unreadMessages()->count()}}</span></a>
<a href="{{route('getProfile')}}" class="menuitem {{Route::is('getProfile') ? 'active' : ''}}"><i class="fa-solid fa-puzzle-piece"></i> Profil</a>
<a href="{{route('getLogout')}}" class="menuitem"><i class="fa-solid fa-cog"></i> Abmelden</a>