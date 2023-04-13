@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-12 box mb-2">
            <h1>Einladung</h1>
            <hr>
            <div class="row">
                <div class="col-12">
                    @if (isset($invite) && isset($instance))
                        Sie sind der Instanz {{$instance->name}} beigetreten.
                    @else
                        Der Einladungs-Link ist ungültig.
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection