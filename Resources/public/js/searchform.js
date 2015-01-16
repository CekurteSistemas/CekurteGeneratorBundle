jQuery(document).ready(function($) {

    $('.form-search').on('submit', function(event) {

        event.preventDefault();

        var filters   = [];
        var inputName = 'filters';

        $(this).find('input').each(function(index, element) {

            var value = $(this).val();

            if (value.length > 0) {

                var field     = $(this).data('field');
                var operation = $(this).data('operation');

                if (typeof field !== 'undefined' && typeof operation !== 'undefined') {
                    filters.push(field + ':' + operation + ':' + value);
                }
            }
        });

        if (!filters.length > 0) {

            var alertSearch = $('.alert-search', this);

            var container = $('<div>')
                .addClass('alert')
                .addClass('alert-warning')
                .addClass('alert-dismissible')
                .attr('role', 'alert')
            ;

            var button = $('<button>')
                .addClass('close')
                .attr('type', 'button')
                .attr('data-dismiss', 'alert')
                .attr('aria-label', $(alertSearch).data('close'))
                .append($('<span>').attr('aria-hidden', 'true').html('&times;'))
            ;

            alertSearch.append(
                container
                    .append(button)
                    .append($('<strong>').text($(alertSearch).data('title')))
                    .append(' ' + $(alertSearch).data('message'))
            );

            return false;
        }

        var inputFilters = $('<input>')
            .attr('type', 'hidden')
            .attr('name', inputName)
            .val(filters.join())
        ;

        var hackForm = $('<form>')
            .attr('method', 'GET')
            .attr('action', $(this).attr("action"))
            .append(inputFilters)
        ;

        $(hackForm).insertBefore(this);

        $(hackForm).submit();
    });
});