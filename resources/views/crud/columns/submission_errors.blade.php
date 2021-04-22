{{-- enumerate the values in an array  --}}
@php
    $value = data_get($entry, $column['name']);
    $column['escaped'] = $column['escaped'] ?? false;
    $column['prefix'] = $column['prefix'] ?? '';
    $column['suffix'] = $column['suffix'] ?? '';

    // the value should be an array wether or not attribute casting is used
    if (!is_array($value)) {
        $value = json_decode($value, true);
    }
@endphp

<span>
    @if($value && count($value))

        <span class="d-inline-flex text-wrap">
            @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_start')
            <ul>
                @foreach($value as $key => $errors)
                    <li>{{ $key }}:
                        <ul>
                        @foreach($errors as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
            @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_end')
        </span>

    @else
        -
    @endif
</span>
