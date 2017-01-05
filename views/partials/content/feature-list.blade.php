<div class="content-item content-feature-list {{$content->containerCSS()}}">
    <ul>
        @foreach($content->features() as $feature)
        <li>
            {!! $feature->icon()->img() !!}
            <div>
                <h3>{{$feature->title()}}</h3>
                {!! $feature->description() !!}
            </div>
        </li>
        @endforeach
    </ul>
</div>