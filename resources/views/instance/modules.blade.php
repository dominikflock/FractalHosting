@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-12 box mb-2">
            <h1>{{isset($instance_module) ? $instance_module->module->name : 'Module'}}</h1>
            <hr>
            @if (isset($instance_module))
                {!! $instance_module_BE_view ?? 'Dieses Modul hat keine Einstellungen.' !!}
            @else
                <form>
                    <div class="row">
                        @foreach ($modules as $module)
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <h2><i class="fa-solid {{$module->fa_icon}}"></i> {{ $module->name }}</h2>
                                {{ $module->description}}<br>
                                <label>
                                    <input type="checkbox" name="module-{{$module->id}}" {{ $instance_modules->contains('module_id', $module->id) ? 'checked' : ''}}>
                                    Modul aktivieren
                                </label>
                                @if ($instance_modules->contains('module_id', $module->id))
                                    <?php $instance_module = $instance_modules->where('module_id', $module->id)->first(); ?>
                                    <br>
                                    <label>
                                        <input type="checkbox" name="module-{{$module->id}}-public-access" {{ $instance_module->public_access ? 'checked' : ''}}>
                                        Frontend öffentlich zugänglich
                                    </label>
                                    <br>
                                    <i class="fa-solid fa-timer"></i>
                                    @if (!$instance_module->paid_until || $instance_module->paid_until < \Carbon\Carbon::now())
                                        Keine aktive Laufzeit<br>
                                        Sie können <a href="{{route('getModuleCheckout', ['instance_id' => $instance_id, 'instance_module_id' => $instance_module->id])}}">hier Laufzeit erwerben für "{{$instance_module->module->name}}"</a>
                                    @else
                                        Laufzeit gültig bis {{ $instance_module->paid_until->format('d.m.Y - H:i:s') }} Uhr
                                        <br>
                                        Sie können Ihre <a href="{{route('getModuleCheckout', ['instance_id' => $instance_id, 'instance_module_id' => $instance_module->id])}}">Laufzeit für "{{$instance_module->module->name}}" verlängern</a>
                                    @endif
                                    <br>
                                @endif
                            </div>
                        @endforeach
                        <div class="col-12">
                            <button class="btn btn-primary mt-3" flow="{{route('postSaveModules', ['instance_id' => $instance_id])}}">Speichern</button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection