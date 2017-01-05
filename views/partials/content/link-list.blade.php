<div class="content-item content-link-list {{$content->containerCSS()}}">
    <ul>
        @foreach($content->links() as $link)
            <li>{!! $link->renderLinkTag() !!}</li>
        @endforeach
    </ul>
</div>