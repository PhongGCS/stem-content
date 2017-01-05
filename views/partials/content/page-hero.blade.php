<div class="content-item content-hero {{$content->containerCSS()}}" style="@if($content->backgroundImage())background-image:url({{$content->backgroundImage()->src('hero-bg')}});@endif">
    @if($content->image())
        {!! $content->image()->img() !!}
    @endif
    @if($content->title())
        <h1>{{ $content->title() }}</h1>
    @endif
    @if($content->text())
        {!! $content->text() !!}
    @endif
    @if($content->linkURL())
        {!! $content->renderLinkTag() !!}
    @endif
</div>