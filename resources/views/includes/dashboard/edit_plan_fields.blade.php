@php
    $isDisabled = (auth()->user()->role != 1 ? 'disabled' : '');
    $participantAllowed = participantAllowedUpdateFields()
@endphp

@foreach($registrationFields as $field)
    @php
        $name = $field['name'];
        $label = $field['label'];
        $type = $field['type'];
        $required = !empty($field['required']);
        $hasOnchange = $field['has_onchange'] ?? null;

        $value = $filledFields[$name] ?? old("registration_fields.$name");
    @endphp

    <div class="col-md-6">
        <fieldset class="form-group mb-2">
            <label>{{ $label }}</label>

            {{-- SELECT --}}
            @if($type === 'select')
                <select name="registration_fields[{{ $name }}]"
                        class="form-control select2"
                        {{ !in_array($name, $participantAllowed) ? $isDisabled : '' }}
                        @if($required) required @endif
                        @if($hasOnchange) onchange="{{ $hasOnchange }}" @endif>

                    <option value="">--Select--</option>

                    @foreach(($field['options'] ?? []) as $optKey => $option)
                        @php
                            $optionValue = is_array($option) ? ($option['id'] ?? $optKey) : $optKey;
                            $optionLabel = is_array($option) ? ($option['name'] ?? ($option['title'] ?? $optionValue)) : $option;
                        @endphp

                        <option value="{{ $optionValue }}"
                                {{ (string)$value === (string)$optionValue ? 'selected' : '' }}>
                            {{ $optionLabel }}
                        </option>
                    @endforeach

                    @if(!empty($field['has_other_option']))
                        <option value="other" {{ $value === 'other' ? 'selected' : '' }}>Other</option>
                    @endif
                </select>

            {{-- TEXTAREA --}}
            @elseif($type === 'textarea')
                <textarea name="registration_fields[{{ $name }}]"
                        class="form-control h-100px"
                        {{ !in_array($name, $participantAllowed) ? $isDisabled : '' }}
                        placeholder="{{ $label }}"
                        @if($required) required @endif>{{ $value }}</textarea>

            {{-- INPUT --}}
            @else
                <input type="{{ $type }}"
                    name="registration_fields[{{ $name }}]"
                    class="form-control"
                    {{ !in_array($name, $participantAllowed) ? $isDisabled : '' }}
                    value="{{ $value }}"
                    placeholder="{{ $label }}"
                    @if($required) required @endif @if($name == 'no_of_participants' && $transaction->registration_user_type != 'moderator') disabled @endif >
            @endif
        </fieldset>
    </div>
@endforeach
