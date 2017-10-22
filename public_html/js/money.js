$(document).on('click', '.js_send', function(e) {

    var jSelect2 = $("[name='user_to[]']"),
        jMoney = $('[name="money"]'),
        jsOption;

    $('.control-transfer').removeClass('has-error');

    if ( !jSelect2.select2('val') || !jMoney.val() || '$ 0.00' == jMoney.val()) {

        if (!jSelect2.select2('val')) {
            jSelect2.closest('.control-transfer').addClass('has-error');
        }

        if (!jMoney.val() || '$ 0.00' == jMoney.val()) {
            jMoney.closest('.control-transfer').addClass('has-error');
        }
        return false;
    }

    $.ajax({
        type     :'POST',
        cache    : false,
        url  : '/user/send/',
        data: { nickname: $('[name="user_to[]"]').val(), money: $('[name=money]').val() },
        success  : function(response) {
            if (response.success) {

                if (response.added_nickname) {
                    for(var k in response.added_nickname) {
                        jsOption = new Option(response.added_nickname[k], response.added_nickname[k], false, false);
                        jSelect2.append(jsOption).trigger('change');
                    }
                }

                jSelect2.select2('val', '[]');
                jMoney.val(0);

                $('.balance').text(response.balance);
            }

            $('.modal-body').html(response.message);
            $("#alert-modal").modal('show');
        }
    });

    return false;
});