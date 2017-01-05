@if(!$content->isShortCode())<div class="content-item content-code-block">@endif
    <pre class="{{$content->preCSSClasses()}}" {!! $content->preDataAttributes() !!}>
        <code class="language-{{$content->language()}}">{{$content->code()}}</code>
    </pre>
@if(!$content->isShortCode())</div>@endif