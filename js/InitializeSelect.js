$(document).ready(function () {
    $('.select-basic-single').prepend('<option></option>')
    $('.select-basic-single').select2({
        placeholder: {
            id: '', // important to prevent error
        },
        width: '100%',
        allowClear: true,
        closeOnSelect: true
    });

    // Fix z-index when dropdown opens
    $('.select-basic-single').on('select2:open', function () {
        $('.select2-container--open').css('z-index', 0);
    });
});
