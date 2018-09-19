/// <reference path="./../typings/jquery/jquery.d.ts" />
(function () {
    'use strict';
    $('form[data-remote]').on('submit', function (e) {
        e.preventDefault();
        var $form = $(this);
        var fd = new FormData($form[0]);
        var $alert = $form.find('.alert.alert-danger');
        var method = $form.find('input[name="_method"]').val() || 'POST';
        var url = $form.prop('action');
        $form.find('button[type="submit"]').prop('disabled', true);
        $alert.addClass('hide');
        $.ajax({
            url: url,
            type: method,
            dataType: 'json',
            cache: false,
            data: fd,
            timeout: 10000,
            processData: false,
            contentType: false
        }).done(function (data, status, xhr) {
            redirect(data.payloads.location);
        }).fail(function (data, t1, t2) {
            $form.find('button[type="submit"]').prop('disabled', false);
            var errors = data.responseJSON.errors;
            $alert.html(errors);
            $alert.removeClass('hide');
        });
    });
    function redirect(to) {
        location.href = to;
    }
})();
