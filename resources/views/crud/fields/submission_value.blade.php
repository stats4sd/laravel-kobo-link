<!-- text input -->
<div class="col-md-6 d-flex justify-content-start align-items-center mb-3">

    <label class="mb-0">Raw value</label>

    <input
        type="text"
        name="{{ $field['name'] }}"
        value="{{ old(square_brackets_to_dots($field['name'])) ?? $field['value'] ?? $field['default'] ?? '' }}"
        class="flex-grow-1 ml-4"

        @include('crud::fields.inc.attributes')
    >
</div>
