@props([
    'level' => null,
    'title' => null,
    'comment' => '',
    'award' => null
])

<div class="container-fluid p-0">
    <div class="modal fade" id="{{ $level }}Rejection{{ $award->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
            
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Comments from {{ $title }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <!-- Modal Body -->
                <div class="modal-body">
                    @if(filled($comment))
                        <p>{!! $comment !!}</p>                    
                    @else
                        <p class="text-muted italic">No comment or reason was left by this reviewer.</p>
                    @endif
                </div>

                <!-- Modal Footer (Optional but standard for clean look) -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
</div>