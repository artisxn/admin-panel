@php
    $transformName = str_replace(['.', '[]', '[', ']'], ['_', '', '.', ''], $name);
@endphp

<div class="form-group @if ($errors->has($transformName)){!! "has-error" !!}@endif">

    @if (isset($attributes['label']['title']))
        {!! Form::label($name, $attributes['label']['title'], (isset($attributes['label']['options'])) ? $attributes['label']['options'] : ['class' => 'col-sm-2 control-label']) !!}
    @endif

    <div class="col-sm-10">

        @foreach ($attributes['radios'] as $radio)
            <div><label> {!! Form::radio($name, $radio['value'], (! $value && $loop->first || $radio['value'] == $value) ? true : false, (isset($radio['options'])) ? $radio['options'] : []) !!} {{ $radio['label'] }} </label></div>
        @endforeach

        @foreach ($errors->get($transformName) as $message)
            <span class="help-block m-b-none">{{ $message }}</span>
        @endforeach

    </div>
</div>

<div class="hr-line-dashed"></div>