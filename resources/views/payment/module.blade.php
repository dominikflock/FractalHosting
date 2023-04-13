@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-12 box mb-2">
            <h1>Laufzeitverlängerung</h1>
            <hr>
            <div class="row justify-content-center">
                <div class="col-6 text-center">
                    <form>
                        <label for="duration">Um die Laufzeit des Moduls "{{$module->name}}" zu verlängern, wählen Sie bitte eine der folgenden Optionen aus.</label>
                        <select id="duration" name="duration">
                            <option value="1">1 Monat ({{$instance_module->calculatePriceByMonths(1);}}€)</option>
                            <option value="3">3 Monat ({{$instance_module->calculatePriceByMonths(3);}}€)</option>
                            <option value="6">6 Monat ({{$instance_module->calculatePriceByMonths(6);}}€)</option>
                            <option value="12">12 Monat ({{$instance_module->calculatePriceByMonths(12);}}€)</option>
                        </select>
                        <br>
                        <button class="btn btn-primary" flow="{{route('postModuleCheckout', ['instance_id' => $instance->id, 'instance_module_id' => $instance_module->id])}}">Jetzt kostenpflichtig buchen</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection