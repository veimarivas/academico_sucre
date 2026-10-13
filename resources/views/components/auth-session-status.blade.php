@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'alert alert-success mb-4']) }}>
        {{ $status }}
    </div>
@endif