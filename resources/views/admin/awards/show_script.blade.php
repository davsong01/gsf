<style>
    /* Premium Page Layout Structure */
    .premium-page-wrapper {
        background-color: #f8fafc;
        color: #1e293b;
        font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
    }

    /* Top Profile Meta Deck Frame with Integrated Image Box */
    .overview-banner-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #f8fafc;
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    }
    .header-avatar-preview {
        width: 90px;
        height: 90px;
        object-fit: cover;
        border-radius: 10px;
        border: 3px solid rgba(255, 255, 255, 0.15);
        cursor: zoom-in;
        transition: transform 0.2s ease, border-color 0.2s;
    }
    .header-avatar-preview:hover {
        transform: scale(1.04);
        border-color: #3b82f6;
    }
    .header-avatar-placeholder {
        width: 90px;
        height: 90px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.05);
        border: 2px dashed rgba(255, 255, 255, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
    }
    .meta-label {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #94a3b8;
        font-weight: 600;
    }
    .meta-value {
        font-size: 0.95rem;
        font-weight: 500;
        color: #f1f5f9;
    }

    /* Modern Dynamic Form Cards */
    .section-card {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.05);
    }
    .form-field-group {
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 20px;
        margin-bottom: 20px;
    }
    .form-field-group:last-child {
        border-bottom: none;
        padding-bottom: 0;
        margin-bottom: 0;
    }

    label {
        font-weight: 600;
        color: #334155;
        font-size: 0.875rem;
        text-transform: capitalize;
    }
    .form-control {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 10px 14px;
        font-size: 0.875rem;
        transition: all 0.15s ease-in-out;
    }
    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }

    /* Media File Handling Layouts */
    .file-thumbnail-preview {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        cursor: zoom-in;
        transition: opacity 0.2s;
    }
    .file-thumbnail-preview:hover {
        opacity: 0.85;
    }

    /* Sticky Bottom Actions Command Station */
    .sticky-action-buttons {
        position: fixed;
        bottom: 30px;
        right: 30px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        z-index: 1050;
    }
    .btn-circle {
        position: relative;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        transition: transform 0.2s ease, background-color 0.15s;
        border: none;
    }
    .btn-circle:hover {
        transform: translateY(-2px) scale(1.05);
    }
    .btn-circle .btn-label {
        position: absolute;
        right: 68px;
        white-space: nowrap;
        background: #0f172a;
        padding: 6px 12px;
        border-radius: 6px;
        opacity: 0;
        color: #ffffff;
        font-size: 11px;
        font-weight: 600;
        pointer-events: none;
        transition: opacity 0.2s ease;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05);
    }
    .btn-circle:hover .btn-label {
        opacity: 1;
    }
</style>


@section('extra_scripts')
<script>
    $('.trigger-zoom-modal').on('click', function() {
    let fileUrl = $(this).data('file-url');
    let fileLabel = $(this).data('label');

    // Set text labels
    $('#zoomModalLabel').text(fileLabel ? 'Focus View: ' + fileLabel : 'File Asset Focus');

    let $img = $('#zoomedTargetImageElement');
    let $spinner = $('#zoomModalSpinnerElement');

    // 1. Hide the image and display the loader before inserting the source
    $img.hide();
    $spinner.show();

    // 2. Open the Bootstrap modal structure
    $('#imageZoomSystemModal').modal('show');

    // 3. Bind the onload hook to handle sizing animations safely after down-streaming finishes
    $img.attr('src', fileUrl).on('load', function() {
        $spinner.hide();
        $img.fadeIn(200);

        // Recalculate centering dynamically now that the dimensions are known
        let modalInstance = bootstrap.Modal.getInstance(document.getElementById('imageZoomSystemModal'));
        if (modalInstance && typeof modalInstance.handleUpdate === 'function') {
            modalInstance.handleUpdate();
        }
    });
});
</script>
@endsection
