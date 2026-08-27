@php
    $answerValues = collect($answers ?? []);
    $isEditable = $editable ?? false;
    $audience = $audience ?? 'fill';
    $fieldPrefix = $fieldPrefix ?? 'answers';
    $prefillData = $prefillData ?? [];
    $questionNumber = 1;
    $isLocked = ! $isEditable;
@endphp

@foreach($sections as $section)
    @php
        $sectionName = trim((string) ($section->name ?? ''));
    @endphp
    <div class="appraisal-section-card mb-1">
        <div class="section-heading">
            <h4 class="mb-0">{{ $sectionName }}</h4>
        </div>

        <div class="p-2 p-md-2">
            @foreach($section->subsections as $subsection)
                @php
                    $subsectionName = trim((string) ($subsection->name ?? ''));
                    $showSubsectionHeading = $subsectionName !== '' && strcasecmp($sectionName, $subsectionName) !== 0;
                @endphp
                <div class="subsection-block">
                    @if($showSubsectionHeading)
                        <div class="subsection-heading mb-1">
                            <h5 class="mb-0">{{ $subsectionName }}</h5>
                        </div>
                    @endif

                    <div class="row">
                        @foreach($subsection->questions as $question)
                            @php
                                $answerKey = $question->slug;
                                if (! $isEditable && str_contains($audience, ':')) {
                                    $answerKey = $audience . ':' . $question->slug;
                                }

                                $storedAnswer = $answerValues->get($answerKey);
                                $value = old($fieldPrefix . '.' . $question->slug, $storedAnswer->answer_value ?? null);

                                if (($value === null || $value === '') && array_key_exists($question->slug, $prefillData)) {
                                    $value = $prefillData[$question->slug];
                                }

                                if (is_string($value)) {
                                    $decoded = json_decode($value, true);
                                    if (json_last_error() === JSON_ERROR_NONE) {
                                        $value = $decoded;
                                    }
                                }

                                if (is_array($value)) {
                                    $displayValue = implode(', ', array_map(fn ($item) => is_array($item) ? json_encode($item) : $item, $value));
                                } else {
                                    $displayValue = $value;
                                }
                            @endphp

                            <div class="{{ trim($question->width_class ?: 'col-md-6') }} mb-1">
                                <div class="appraisal-field">
                                    <label for="{{ $question->slug }}" class="appraisal-question-label d-block">
                                        <span>
                                            {{ $questionNumber }}. {{ $question->label }}
                                            @if($isEditable && ! empty($question->is_required))
                                                <span class="text-danger">*</span>
                                            @endif
                                        </span>
                                        @if(! $isEditable)
                                            <small class="text-muted d-block mt-1">Published response</small>
                                        @endif
                                    </label>

                                    @switch($question->type)
                                        @case('text')
                                        @case('number')
                                        @case('date')
                                        @case('month')
                                        @case('year')
                                            <input type="{{ $question->type }}"
                                                   class="form-control appraisal-control"
                                                   id="{{ $question->slug }}"
                                                   name="{{ $fieldPrefix }}[{{ $question->slug }}]"
                                                   value="{{ is_array($value) ? '' : $value }}"
                                                   @if($isLocked) disabled @endif
                                                   data-appraisal-required="{{ !empty($question->is_required) ? '1' : '0' }}">
                                            @break

                                        @case('textarea')
                                            <textarea class="form-control appraisal-control"
                                                      id="{{ $question->slug }}"
                                                      name="{{ $fieldPrefix }}[{{ $question->slug }}]"
                                                      rows="3"
                                                      @if($isLocked) disabled @endif
                                                      data-appraisal-required="{{ !empty($question->is_required) ? '1' : '0' }}">{{ is_array($value) ? '' : $value }}</textarea>
                                            @break

                                        @case('select')
                                            <select class="form-select appraisal-control"
                                                    id="{{ $question->slug }}"
                                                    name="{{ $fieldPrefix }}[{{ $question->slug }}]"
                                                    @if($isLocked) disabled @endif
                                                    data-appraisal-required="{{ !empty($question->is_required) ? '1' : '0' }}">
                                                <option value="">Select...</option>
                                                @foreach($question->options ?? [] as $option)
                                                    @php
                                                        $optionValue = $option['value'] ?? $option['label'] ?? $option;
                                                        $optionLabel = $option['label'] ?? $option['value'] ?? $option;
                                                    @endphp
                                                    <option value="{{ $optionValue }}" {{ (string) $value === (string) $optionValue ? 'selected' : '' }}>
                                                        {{ $optionLabel }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @break

                                        @case('file')
                                            @if(! empty($displayValue))
                                                <div class="mb-2">
                                                    <a href="{{ route('protected.download', ['file' => $displayValue]) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        {{ $isLocked ? 'View Uploaded File' : 'View Current File' }}
                                                    </a>
                                                </div>
                                            @endif
                                            <input type="file"
                                                   class="form-control appraisal-control"
                                                   id="{{ $question->slug }}"
                                                   name="{{ $fieldPrefix }}[{{ $question->slug }}]"
                                                   accept=".jpg,.jpeg,.png"
                                                   @if($isLocked) disabled @endif
                                                   data-appraisal-required="{{ !empty($question->is_required) ? '1' : '0' }}">
                                            <small class="text-muted d-block mt-1">Allowed file types: JPG, JPEG, PNG.</small>
                                            @break

                                        @default
                                            <textarea class="form-control appraisal-control"
                                                      id="{{ $question->slug }}"
                                                      name="{{ $fieldPrefix }}[{{ $question->slug }}]"
                                                      rows="3"
                                                      @if($isLocked) disabled @endif
                                                      data-appraisal-required="{{ !empty($question->is_required) ? '1' : '0' }}">{{ is_array($value) ? '' : $value }}</textarea>
                                    @endswitch

                                    @if($isLocked)
                                        <small class="text-muted d-block mt-1">Published response</small>
                                    @else
                                        @error($fieldPrefix . '.' . $question->slug)
                                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                                        @enderror
                                    @endif
                                </div>
                            </div>
                            @php $questionNumber++; @endphp
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endforeach
