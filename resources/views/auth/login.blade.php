@extends('layouts.admin', [
    'hideSidebar' => true,
    'title' => 'Login',
])

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-12 box">
                <h2 class="text-center">Login</h2>
                <form>
                    <label for="email"><i class="fa-solid fa-at"></i> E-Mail</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="E-Mail" required autofocus>
                    <label for="password"><i class="fa-solid fa-key"></i> Passwort</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Passwort" required>
                    <br>
                    <button class="btn btn-primary btn-block w-100" type="submit" flow="{{route('postLogin')}}"><i class="fa-solid fa-right-to-bracket"></i> Anmelden</button>
                    <p class="text-center">Ich möchte einen <a href="{{route('getRegister')}}">neuen Account erstellen</a></p>
                </form>
            </div>
        </div>
    </div>
@endsection