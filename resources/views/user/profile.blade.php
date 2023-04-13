@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-12 box mb-2">
            <h1>Profil</h1>
            <hr>
            <form>
                <div class="row">
                    <div class="col-6">
                        <label>Vorname</label>
                        <input type="text" name="firstname" value="{{$user->firstname}}">
                    </div>
                    <div class="col-6">
                        <label>Nachname</label>
                        <input type="text" name="lastname" value="{{$user->lastname}}">
                    </div>
                    <div class="col-12 mt-3">
                        <button class="btn btn-primary" flow="{{route('postUpdateProfile')}}">Speichern</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection