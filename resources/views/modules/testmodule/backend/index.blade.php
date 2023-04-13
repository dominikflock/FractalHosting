@if ($messages->count() > 0)
    Hier sehen Sie alle abgesendete Nachrichten:
    <br>
    <br>
    <ul>
        @foreach($messages as $message)
            <li>
                {{$message->message}}
                <br>
                <small>Gesendet am {{$message->created_at->format('d.m.Y - H:i:s')}} Uhr</small>
            </li>
            <br>
        @endforeach
    </ul>
@else
    Es wurden bisher keine Nachrichten abgeschickt!
@endif