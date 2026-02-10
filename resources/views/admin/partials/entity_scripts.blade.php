<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    .select2-container--default .select2-selection--single { 
        height: 48px !important; border: 1px solid #dee2e6 !important; border-radius: 10px !important; padding-top: 10px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 46px !important; }
    .parent-warning { font-size: 0.75rem; font-weight: 500; display: none; }
    .select2-container--disabled .select2-selection--single { background-color: #f8f9fa !important; cursor: not-allowed !important; }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    const $searchSelects = $('.select2-remote, #faculty_dean_id');

    $searchSelects.each(function() {
        const $this = $(this);
        const parentSelector = $this.data('parent-filter'); // This must be "#faculty_id"
        const parentType = $this.data('parent-type') || 'parent';
        const warningId = `warn-${$this.attr('id')}`;

        // Create warning
        if (!$(`#${warningId}`).length) {
            $this.after(`<small id="${warningId}" class="parent-warning text-amber-600 mt-1"><i class="fas fa-exclamation-triangle me-1"></i> Please select a ${parentType} first.</small>`);
        }
        const $warning = $(`#${warningId}`);

        function updateState() {
            // If there's no parent filter attribute at all, it's a standalone (like an Institute)
            if (!parentSelector) {
                $this.prop('disabled', false);
                $warning.hide();
            } else {
                // It's a dependent entity (Department or Unit)
                const parentVal = $(parentSelector).val();
                const isEnabled = (parentVal !== null && parentVal !== "");

                if (!isEnabled) {
                    $this.prop('disabled', true);
                    $warning.show();
                } else {
                    $this.prop('disabled', false);
                    $warning.hide();
                }
            }

            // Re-init Select2
            $this.select2({
                placeholder: "Search staff...",
                allowClear: true,
                minimumInputLength: 2,
                width: '100%',
                ajax: {
                    url: "{{ $searchRoute }}",
                    dataType: 'json',
                    delay: 250,
                    data: params => ({
                        q: params.term,
                        parent_id: parentSelector ? $(parentSelector).val() : null,
                        parent_type: parentType // 'faculty' for Depts, 'office' for Units, null for Institutes
                    }),
                    processResults: data => ({ results: data }),
                    cache: true
                }
            });
        }

        // Initial Call
        updateState();

        // Listen for changes on the parent
        if (parentSelector) {
            $(document).on('change', parentSelector, function() {
                console.log("Parent changed to: " + $(this).val()); // Debugging line
                $this.val(null).trigger('change');
                updateState();
            });
        }
    });

    // Auto-uppercase
    $(document).on('input', 'input[name*="_code"]', function() {
        $(this).val($(this).val().toUpperCase().replace(/[^A-Z0-9\-]/g, ''));
    });

    // Submit Loading
    $('#{{ $formId }}').on('submit', function() {
        const $btn = $('button[form="{{ $formId }}"]');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');
    });
});
</script>