@php
    use Carbon\Carbon;

    $fields = collect(awardFormFields())
        ->filter(fn ($field) => in_array(
            $field['award_type'] ?? 'both',
            ['both', $award->type]
        ));

    $entryValues = $award?->entry?->getAttributes() ?? [];

    $isDisabled = ! ($canEdit ?? true);
    $isAdmin = $isAdmin ?? false;
@endphp

<div class="row g-4">

    @foreach ($fields as $key => $field)

        @php
            $value = old("entries.{$key}", $entryValues[$key] ?? null);

            $type = $field['type'];
            $label = $field['label'];
            $accept = $field['accept'] ?? null;
            $options = $field['options'] ?? [];
            $step = $field['step'] ?? null;

            $isFile = in_array($type, ['file', 'image']);

            if (
                $key === 'chapter_id'
                && filled($value)
                && isset($award)
                && $award->relationLoaded('chapter')
                && $award->chapter
                && ! collect($options)->contains(
                    fn ($option) => (string) $option['value'] === (string) $value
                )
            ) {
                $options[] = [
                    'value' => $award->chapter->id,
                    'label' => $award->chapter->name,
                ];
            }
        @endphp

        <div class="col-12 col-md-6">

            <div class="form-field-group border-0 h-100">

                <label
                    for="entry-{{ $key }}"
                    class="form-label text-dark fw-semibold font-sm mt-1 d-flex align-items-center gap-2"
                >
                    <span>{{ $label }}</span>

                    {{-- @if ($isAdmin && $isFile && filled($value) && isset($award->id))
                        <a
                            href="{{ route('award.sync.asset', [$award->id, $key]) }}"
                            class="badge bg-light text-primary border fw-normal"
                        >
                            <i class="fa fa-sync-alt"></i>
                            Re-sync
                        </a>
                    @endif --}}
                </label>

                {{-- FILE / IMAGE --}}
                @if ($isFile)

                    <div class="d-flex align-items-center gap-3 border rounded-3 p-2 bg-white" style="min-height:70px">

                        @if (filled($value) && isset($award->id))

                            @php
                                $downloadRoute = $isAdmin
                                    ? 'admin.protected.download'
                                    : 'protected.download';
                            @endphp

                            <img
                                src="{{ route($downloadRoute, ['file' => $value]) }}"
                                class="file-thumbnail-preview trigger-zoom-modal"
                                data-file-url="{{ route($downloadRoute, ['file' => $value]) }}"
                                data-label="{{ $label }}"
                                alt="{{ $label }}"
                            >

                        @else

                            <div
                                class="bg-light text-muted rounded d-flex align-items-center justify-content-center"
                                style="width:50px;height:50px"
                            >
                                <i class="fa fa-file-image-o"></i>
                            </div>

                        @endif

                        <div class="flex-grow-1">

                            <input
                                id="entry-{{ $key }}"
                                type="file"
                                class="form-control form-control-sm border-0 p-0 shadow-none"
                                name="entries[{{ $key }}]"
                                accept="{{ $accept }}"
                                @disabled($isDisabled)
                            >

                            @if ($accept)
                                <small class="text-muted">
                                    Accepted formats:
                                    {{ strtoupper(str_replace(',', ', ', str_replace('.', '', $accept))) }}
                                </small>
                            @endif

                        </div>

                    </div>

                {{-- SELECT --}}
                @elseif ($type === 'select')

                    <select
                        id="entry-{{ $key }}"
                        class="form-select"
                        name="entries[{{ $key }}]"
                        @disabled($isDisabled)
                    >
                        <option value="">-- Select {{ $label }} --</option>

                        @foreach ($options as $option)
                            <option
                                value="{{ $option['value'] }}"
                                @selected((string) $value === (string) $option['value'])
                            >
                                {{ $option['label'] }}
                            </option>
                        @endforeach
                    </select>

                {{-- DATE --}}
                @elseif ($type === 'date')

                    <input
                        id="entry-{{ $key }}"
                        type="date"
                        class="form-control"
                        name="entries[{{ $key }}]"
                        value="{{ filled($value) ? Carbon::parse($value)->format('Y-m-d') : '' }}"
                        @disabled($isDisabled)
                    >

                {{-- TEXTAREA --}}
                @elseif ($type === 'textarea')

                    <textarea
                        id="entry-{{ $key }}"
                        class="form-control"
                        rows="4"
                        name="entries[{{ $key }}]"
                        @disabled($isDisabled)
                    >{{ $value }}</textarea>

                {{-- NUMBER --}}
                @elseif ($type === 'number')

                    <input
                        id="entry-{{ $key }}"
                        type="number"
                        step="{{ $step }}"
                        class="form-control"
                        name="entries[{{ $key }}]"
                        value="{{ $value }}"
                        @disabled($isDisabled)
                    >

                {{-- EMAIL --}}
                @elseif ($type === 'email')

                    <input
                        id="entry-{{ $key }}"
                        type="email"
                        class="form-control"
                        name="entries[{{ $key }}]"
                        value="{{ $value }}"
                        @disabled($isDisabled)
                    >

                {{-- DEFAULT --}}
                @else

                    <input
                        id="entry-{{ $key }}"
                        type="text"
                        class="form-control"
                        name="entries[{{ $key }}]"
                        value="{{ $value }}"
                        @disabled($isDisabled)
                    >

                @endif

            </div>

        </div>

    @endforeach

</div>
