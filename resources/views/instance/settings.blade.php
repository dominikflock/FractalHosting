@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-12 box mb-2">
            <h1>Einstellungen</h1>
            <hr>
            <form>
                <label for="instance_name">Instanz Name</label>
                <input type="text" id="instance_name" name="instance_name" value="{{$instance->name}}">
                <br>
                <label for="api_key">API Schlüssel</label>
                <input type="text" id="api_key" name="api_key" value="{{$instance->api_key}}" placeholder="{{!$instance->api_key ? 'Kein API key vorhanden' : ''}}" disabled>
                <button class="btn btn-primary" flow="{{route('postRenewApiKey', ['instance_id' => $instance_id])}}">Neu generieren</button>
                <br><br>
                <button class="btn btn-primary" flow="{{route('postInstanceUpdate', ['instance_id' => $instance_id])}}">Einstellungen speichern</button>
                <button class="btn btn-default" flow="{{route('postDeleteInstance', ['instance_id' => $instance_id])}}">Instanz löschen</button>
            </form>
        </div>
    </div>
@endsection