jQuery(document).ready(function($) {

    /**
     * --------------------------------------------------------------------------
     * Pergunta ao usuário se ele deseja realmente remover um registro da base de
     * dados
     */

    $('button[data-target="#modalDelete"]').on('click', function(event) {

        event.preventDefault();

        var form = $(this).parent();

        $('#modalDelete').find('a.confirm_delete').on('click', function() {

            $(form).submit();

            return false;
        });
    });

    /**
     * Button log details
     */

    $('button#btn-log-details').on('click', function(event) {

        event.preventDefault();

        $(this).find('.btn-log-details').toggleClass('hide');
        $(this).parent().parent().find('.log').toggleClass('hide');
    });

    /**
     * --------------------------------------------------------------------------
     * Habilita o Tooltip do Twitter Bootstrap
     */

    $('a, button[title]').tooltip({});

    /**
     * --------------------------------------------------------------------------
     * Select2
     */

    $('select').select2({
        width: '100%'
    });

    /**
     * --------------------------------------------------------------------------
     * Date picker
     */

    $('.datepicker').datepicker({});

});