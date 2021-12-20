@if(!empty($align))
<div class="{{ $align }}">
@endif

    <button type="button" class="btn {{ $classname }} {{ $size }}" onclick="{{ $onclick }}">
        {!! $icon !!}
        <span>{!! $label !!}</span>
    </button>

@if(!empty($align))
</div>
@endif
