{{-- Rows Per Page partial — usage:
    @include('partials.ui-rpp', [
        'id' => 'rowsPerPage',
        'default' => 10,
        'options' => [10, 25, 50, 'all']
    ])
--}}
@php
    $id = $id ?? 'rowsPerPage';
    $default = $default ?? 10;
    $options = $options ?? [10, 25, 50, 'all'];
@endphp
<div class="rpp-wrapper">
    <label for="{{ $id }}">Rows:</label>
    <select class="rpp-select" id="{{ $id }}">
        @foreach($options as $opt)
            <option value="{{ $opt }}" {{ $opt == $default ? 'selected' : '' }}>
                {{ $opt === 'all' ? 'All' : $opt }}
            </option>
        @endforeach
    </select>
</div>
