<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    .form-control, .form-select { padding: 0.75rem 1rem; border-radius: 10px; transition: all 0.2s ease; }
    .form-control:focus, .form-select:focus { background-color: #fff !important; box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1); border: 1px solid #0d6efd !important; }
    .select2-container--default .select2-selection--single { background-color: #f8fafc !important; border: none !important; height: 48px !important; border-radius: 10px !important; padding-top: 10px; }
</style>

<script>
    $(document).ready(function() {
    $('#faculty_dean_id').select2({
        placeholder: "Start typing name, username or email...",
        allowClear: true,
        minimumInputLength: 1, // Require at least 1 character to trigger search
        ajax: {
            url: "{{ route('admin.faculties.searchDeans') }}",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return { q: params.term };
            },
            processResults: function(data) {
                console.log("Data received:", data); // DEBUG: See if data arrives
                return { results: data };
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("AJAX Error:", textStatus, errorThrown); // DEBUG: See errors
            },
            cache: true
        }
    });
});
</script>