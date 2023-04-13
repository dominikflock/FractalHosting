@extends('layouts.frontend')

@section('content')
    <form>
        <input type="text" id="message" name="message" placeholder="Nachricht">
        <button flow="{{route('TestModule_postSendMessage', ['instance_module_id' => $instance_module->id])}}">Nachricht absenden</button>
        <br>
        Zuletzt gesendete Nachricht: <span id="lastMessage"></span>
        <br>
        <span id="status"></span>
    </form>
@endsection