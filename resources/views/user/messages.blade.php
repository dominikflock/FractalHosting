@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-12 box mb-2">
            <h1>Nachrichten</h1>
            <hr>
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-md-5 col-sm-12">
                    @foreach ($user->messages->reverse() as $message)
                        <a href="{{route('getMessages', ['message_id' => $message->id])}}" class="menuitem {{isset($message_selected) && $message_selected->id == $message->id ? 'active' : ''}}">
                            <i class="fa-solid fa-envelope"></i> {{$message->subject}}
                            @if (!$message->read)
                                <span>NEU</span>
                            @endif
                        </a>
                    @endforeach
                </div>
                <div class="col-xl-9 col-lg-8 col-md-7 col-sm-12">
                    @if (isset($message_selected))
                        <h2>{{$message_selected->subject}}</h2>
                        <p>{!! $message_selected->message !!}</p>
                        <br>
                        <button class="btn btn-default" flow="{{route('postDeleteMessage', ['message_id' => $message_selected->id])}}">Nachricht löschen</button>
                    @else
                        <h2 class="text-center">Keine Nachricht ausgewählt</h2>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection