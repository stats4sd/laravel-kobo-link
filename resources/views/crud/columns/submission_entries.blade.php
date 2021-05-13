{{-- enumerate the values in an array  --}}
@php
    $value = data_get($entry, $column['name']);
    $column['escaped'] = $column['escaped'] ?? false;
    $column['prefix'] = $column['prefix'] ?? '';
    $column['suffix'] = $column['suffix'] ?? '';

    // the value should be an array whether or not attribute casting is used
    if (!is_array($value)) {
        $value = json_decode($value, true);
    }
@endphp

<span>
    @if($value && count($value))

        <span class="d-flex flex-wrap">
            <ul>
                @foreach($value as $model => $entries)
                    <li>{{ $model }}: <span class="font-weight-bold">{{ count($entries) ?? 'none' }}</span></li>
                @endforeach
            </ul>
        </span>

    @else
        -
    @endif
</span>
