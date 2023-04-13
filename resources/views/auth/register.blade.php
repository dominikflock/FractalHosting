@extends('layouts.admin', [
    'hideSidebar' => true,
    'title' => 'Registrierung',
])

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-12 box">
                <h2 class="text-center">Registrierung</h2>
                <form>
                    <label for="email"><i class="fa-solid fa-at"></i> E-Mail</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="E-Mail" required autofocus>
                    <label for="password"><i class="fa-solid fa-key"></i> Passwort</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Passwort" required>
                    <label for="password"><i class="fa-solid fa-key"></i> Passwort wiederholen</label>
                    <input type="password" name="retype_password" id="retype_password" class="form-control" placeholder="Passwort" required>
                    <label for="firstname">Vorname</label>
                    <input type="text" name="firstname" id="firstname" class="form-control" placeholder="Vorname" required>
                    <label for="lastname">Nachname</label>
                    <input type="text" name="lastname" id="lastname" class="form-control" placeholder="Nachname" required>
                    <br>
                    <button class="btn btn-primary btn-block w-100" flow="{{route('postRegister')}}"><i class="fa-solid fa-right-to-bracket"></i> Registrieren</button>
                    <p class="text-center">Ich habe bereits einen Account und möchte mich<br><a href="{{route('getLogin')}}">mit einem bestehenden Account anmelden</a></p>
                </form>
            </div>
        </div>
    </div>
@endsection