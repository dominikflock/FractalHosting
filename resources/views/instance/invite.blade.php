@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-12 box mb-2">
            <h1>Einladung</h1>
            <hr>
            <div class="row justify-content-center">
                <div class="col-6 text-center">
                    <form>
                        Wen möchten Sie einladen?
                        <input type="text" id="email" name="email" placeholder="E-Mail Adresse" class="mt-3">
                        <button class="btn btn-primary mt-3" flow="{{route('postInviteUser', ['instance_id' => $instance_id])}}">Einladen</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection