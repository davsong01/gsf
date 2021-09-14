 <div class="container">
        <!--Basic Modal -->
        <div class="modal fade" id="secretariatRejection{{ $report->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                
                    <!-- Modal Header -->
                    <div class="modal-header">
                    <h4 class="modal-title">Comments from National Secretariat</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>{!! $report->status_complete_reject_comment !!}</p>                    
                    </div>
                </div>
            </div>
        </div>
    </div>