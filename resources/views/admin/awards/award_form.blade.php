@php
    use Carbon\Carbon;

    $type ??= $award->type ?? null;

    $fields = collect(awardFormFields())
        ->filter(fn ($field) => in_array(
            $field['award_type'] ?? 'both',
            ['both', $type]
        ));

    $entryValues ??= $award?->entry?->getAttributes() ?? [];

    $canEdit ??= true;
    $isAdmin ??= false;
    $required ??= false;

    $isDisabled = ! $canEdit;
@endphp

<div class="row g-4">

    @foreach ($fields as $key => $field)

        @php
            $value = old("entries.{$key}", $entryValues[$key] ?? null);

            $type = $field['type'];
            $label = $field['label'];
            $options = $field['options'] ?? [];
            $accept = $field['accept'] ?? null;
            $step = $field['step'] ?? null;

            // Determine if the field is required based on field config or passed variable
            $isRequired = ($field['required'] ?? false) || ($required ?? false);

            $isFile = in_array($type, ['file', 'image']);

            /*
             |--------------------------------------------------------------
             | Ensure chapter label displays correctly
             |--------------------------------------------------------------
             */
            if ($key === 'chapter_id' && filled($value) && isset($award) && $award->chapter && !collect($options)->contains(fn ($option) => (string) $option['value'] === (string) $value)) {
                $options[] = ['value' => $award->chapter->id, 'label' => $award->chapter->name];
            }
        @endphp

        <div class="col-md-6">
            <div class="mb-3">
                <label for="entry-{{ $key }}" class="form-label fw-semibold">
                    {{ $label }}
                    @if ($isRequired)
                        <span class="text-danger">*</span>
                    @endif
                </label>

                {{-- FILE / IMAGE --}}
                @if ($isFile)
                    <div class="border rounded-3 p-3 bg-light">
                        @if (filled($value) && isset($award?->id))
                            @php $downloadRoute = $isAdmin ? 'admin.protected.download' : 'protected.download'; @endphp
                            <div class="mb-3">
                                <img src="{{ route($downloadRoute, ['file' => $value]) }}" class="file-thumbnail-preview trigger-zoom-modal" data-file-url="{{ route($downloadRoute, ['file' => $value]) }}" data-label="{{ $label }}" alt="{{ $label }}">
                            </div>
                        @endif
                        <input id="entry-{{ $key }}" type="file" class="form-control" name="entries[{{ $key }}]" accept="{{ $accept }}" @disabled($isDisabled) @required($isRequired)>
                        @if ($accept)
                            <small class="text-muted">Accepted: {{ strtoupper(str_replace(['.', ','], ['', ', '], $accept)) }}</small>
                        @endif
                    </div>

                {{-- SELECT --}}
                @elseif ($type === 'select')
                    <select id="entry-{{ $key }}" name="entries[{{ $key }}]" class="form-select" @disabled($isDisabled) @required($isRequired)>
                        <option value="">-- Select {{ $label }} --</option>
                        @foreach ($options as $option)
                            <option value="{{ $option['value'] }}" @selected((string) $value === (string) $option['value'])>{{ $option['label'] }}</option>
                        @endforeach
                    </select>

                {{-- DATE --}}
                @elseif ($type === 'date')
                    <input id="entry-{{ $key }}" type="date" class="form-control" name="entries[{{ $key }}]" value="{{ filled($value) ? Carbon::parse($value)->format('Y-m-d') : '' }}" @disabled($isDisabled) @required($isRequired)>

                {{-- TEXTAREA --}}
                @elseif ($type === 'textarea')
                    <textarea id="entry-{{ $key }}" rows="4" class="form-control" name="entries[{{ $key }}]" @disabled($isDisabled) @required($isRequired)>{{ $value }}</textarea>

                {{-- NUMBER --}}
                @elseif ($type === 'number')
                    <input id="entry-{{ $key }}" type="number" step="{{ $step ?? 'any' }}" class="form-control" name="entries[{{ $key }}]" value="{{ $value }}" @disabled($isDisabled) @required($isRequired)>

                {{-- EMAIL --}}
                @elseif ($type === 'email')
                    <input id="entry-{{ $key }}" type="email" class="form-control" name="entries[{{ $key }}]" value="{{ $value }}" @disabled($isDisabled) @required($isRequired)>

                {{-- DEFAULT TEXT --}}
                @else
                    <input id="entry-{{ $key }}" type="text" class="form-control" name="entries[{{ $key }}]" value="{{ $value }}" @disabled($isDisabled) @required($isRequired)>
                @endif
            </div>
        </div>
    @endforeach
</div>
