@php
    $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
@endphp

@foreach ($manifest as $entry => $urls)
    @if (str_ends_with($entry, '.css'))
        <link rel="stylesheet" href="{{ asset('build/' . $urls['file']) }}">
    @endif
    @if (str_ends_with($entry, '.js'))
        <script type="module" src="{{ asset('build/' . $urls['file']) }}"></script>
    @endif
@endforeach