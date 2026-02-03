<div class="modal fade" id="levelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Select Administrative Level</h4>
            </div>

            <div class="modal-body">
                @php
                    $selectedLevel = request()->level ?? 'chapter';
                @endphp

                <div class="form-check mb-2">
                    <input id="chapter" class="form-check-input level-option" type="radio"
                        name="insight_period" value="chapter"
                        {{ $selectedLevel === 'chapter' ? 'checked' : '' }}>
                    <label class="form-check-label" for="daily">Chapter</label>
                </div>

                <div class="form-check mb-2">
                    <input id="zone" class="form-check-input level-option" type="radio"
                        name="insight_period" value="zone"
                        {{ $selectedLevel === 'zone' ? 'checked' : '' }}>
                    <label class="form-check-label" for="zone">Zone</label>
                </div>

                <div class="form-check mb-2">
                    <input id="field"  class="form-check-input level-option" type="radio"
                        name="insight_period" value="field"
                        {{ $selectedLevel === 'field' ? 'checked' : '' }}>
                    <label class="form-check-label" for="field">Field</label>
                </div>
            </div>
        </div>
    </div>
</div>
