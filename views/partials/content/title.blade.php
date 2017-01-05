<div class="content-item content-title {{$content->containerCSS()}}">
    @if ($content->title())
        <h1>{{$content->title()}}</h1>
    @endif
    @if ($content->subtitle())
        <h2>{{$content->subtitle()}}</h2>
    @endif
</div>