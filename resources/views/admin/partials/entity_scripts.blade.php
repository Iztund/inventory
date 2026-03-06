<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    .select2-container--default .select2-selection--single { 
        height: 48px !important; 
        border: 1px solid #dee2e6 !important; 
        border-radius: 10px !important; 
        padding-top: 10px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow { 
        height: 46px !important; 
    }
    .parent-warning { 
        font-size: 0.75rem; 
        font-weight: 500; 
        display: none; 
    }
    .select2-container--disabled .select2-selection--single { 
        background-color: #f8f9fa !important; 
        cursor: not-allowed !important; 
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Select all remote search selects
    const $searchSelects = $('.select2-remote, #faculty_dean_id, #institute_director_id, #office_head_id');

    $searchSelects.each(function() {
        const $this = $(this);
        const parentSelector = $this.data('parent-filter'); // e.g., "#faculty_id" for departments
        const parentType = $this.data('parent-type'); // e.g., 'faculty', 'office', 'institute', or null
        const warningId = `warn-${$this.attr('id')}`;

        // Determine entity type based on select ID if not explicitly set
        let entityType = parentType;
        if (!entityType) {
            // Auto-detect entity type for parent entities
            if ($this.attr('id') === 'faculty_dean_id') {
                entityType = 'faculty';
            } else if ($this.attr('id') === 'office_head_id') {
                entityType = 'office';
            } else if ($this.attr('id') === 'institute_director_id') {
                entityType = 'institute';
            }
        }

        console.log(`Initializing ${$this.attr('id')} with entityType: ${entityType}, parentSelector: ${parentSelector}`);

        // Create warning for dependent entities only
        if (parentSelector && !$(`#${warningId}`).length) {
            const parentName = entityType === 'faculty' ? 'faculty' : 'office';
            $this.after(`<small id="${warningId}" class="parent-warning text-amber-600 mt-1">
                <i class="fas fa-exclamation-triangle me-1"></i> Please select a ${parentName} first.
            </small>`);
        }
        const $warning = $(`#${warningId}`);

        function updateState() {
            let isEnabled = true;
            
            // Check if this is a dependent entity (has a parent selector)
            if (parentSelector) {
                const parentVal = $(parentSelector).val();
                isEnabled = (parentVal !== null && parentVal !== "" && parentVal !== undefined);

                if (!isEnabled && $warning) {
                    $this.prop('disabled', true);
                    $warning.show();
                } else if ($warning) {
                    $this.prop('disabled', false);
                    $warning.hide();
                }
            } else {
                // Independent entity (Faculty, Office, Institute) - always enabled
                $this.prop('disabled', false);
            }

            // Initialize/Re-initialize Select2
            if ($this.hasClass('select2-hidden-accessible')) {
                $this.select2('destroy');
            }

            $this.select2({
                placeholder: "Search staff...",
                allowClear: true,
                minimumInputLength: 2,
                width: '100%',
                disabled: !isEnabled,
                ajax: {
                    url: "{{ $searchRoute }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        const data = {
                            q: params.term
                        };

                        // For dependent entities (Department/Unit), send parent info
                        if (parentSelector) {
                            data.parent_id = $(parentSelector).val();
                            data.parent_type = entityType; // 'faculty' or 'office'
                        } else if (entityType) {
                            // For parent entities (Faculty/Office/Institute), filter by entity type
                            data.parent_type = entityType;
                        }

                        console.log('AJAX data being sent:', data);
                        return data;
                    },
                    processResults: function(data) {
                        console.log('Results received:', data);
                        return { results: data };
                    },
                    cache: true
                }
            });
        }

        // Initial state
        updateState();

        // Listen for parent changes (for dependent entities only)
        if (parentSelector) {
            $(document).on('change', parentSelector, function() {
                console.log(`Parent ${parentSelector} changed to:`, $(this).val());
                $this.val(null).trigger('change');
                updateState();
            });
        }
    });

    // Auto-uppercase for code fields
    $(document).on('input', 'input[name*="_code"]', function() {
        $(this).val($(this).val().toUpperCase().replace(/[^A-Z0-9\-]/g, ''));
    });

    // Form submission loading state
    $('#{{ $formId }}').on('submit', function() {
        const $btn = $('button[form="{{ $formId }}"]');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');
    });
});
</script>