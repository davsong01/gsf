<script>
    window.AppraisalSubmission = window.AppraisalSubmission || {
        prepare(status, form) {
            if (!form) {
                return true;
            }

            const actionLabel = status === 'published' ? 'publish' : 'save this as a draft';
            const confirmMessage = `Are you sure you want to ${actionLabel}?`;

            if (!window.confirm(confirmMessage)) {
                return false;
            }

            const statusInput = form.querySelector('input[name="status"]');
            if (statusInput) {
                statusInput.value = status;
            }

            const requiredFields = form.querySelectorAll('[data-appraisal-required="1"]');
            requiredFields.forEach((field) => {
                field.required = status === 'published';
            });

            return true;
        }
    };
</script>
