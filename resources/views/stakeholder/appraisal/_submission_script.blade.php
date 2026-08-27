<script>
    window.AppraisalSubmission = window.AppraisalSubmission || {
        prepare(status, form) {
            if (!form) {
                return true;
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
