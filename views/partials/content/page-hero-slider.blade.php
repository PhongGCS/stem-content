<div class="content-item content-hero-slider {{$content->containerCSS()}}">
    @foreach($content->heroes as $hero)
        {!! $hero->render() !!}
    @endforeach
</div>