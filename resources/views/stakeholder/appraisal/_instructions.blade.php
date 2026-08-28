@php
    $instructionProfile = $instructionProfile ?? [];
    $instructionTitle = $instructionTitle ?? 'Appraisal Instructions';
@endphp

@if(!empty($instructionProfile))
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="mb-3">{{ $instructionTitle }}</h5>
            <div class="mb-2">
                <strong>Purpose:</strong> {{ $instructionProfile['purpose'] ?? '' }}
            </div>
            <div class="mb-2">
                <strong>Instruction:</strong> {{ $instructionProfile['instruction'] ?? '' }}
            </div>
            <div>
                <strong>Rating Scale:</strong> {{ $instructionProfile['rating_scale'] ?? '' }}
            </div>
        </div>
    </div>
@endif
