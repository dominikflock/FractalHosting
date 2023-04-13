<h3>{{$title}}</h3>
<br>
<div>
    {!! $content !!}
    <br><br>
    <div style="float: right;">
        <button class="btn btn-primary popup-close" flow="{{$confirm_url}}">Ja, ich bin mir sicher</button>
        <button class="btn btn-default popup-close">Abbrechen</button>
    </div>
</div>