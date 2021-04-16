jQuery(document).ready(function(){

    var loadCalendario = function() {

        var dt_ini = jQuery('#periodo_data_inicial');
        var dt_fim = jQuery('#periodo_data_final');

        jQuery(dt_ini).datepicker("destroy");
        jQuery(dt_fim).datepicker("destroy");

        jQuery(dt_ini).datepicker({

            dateFormat      : 'dd/mm/yy',
            dayNamesMin     : [ 'D', 'S', 'T', 'Q', 'Q', 'S', 'S' ],
            monthNames      : [ 'Janeiro', 'Fevereiro', 'Março', 'Abril',
            'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro',
            'Novembro', 'Dezembro' ],
            monthNamesShort: [ 'Jan', 'Fev', 'Mar', 'Abr',
            'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out',
            'Nov', 'Dez' ],
            nextText: 'Próximo',
            prevText: 'Anterior',
            buttonText: 'Calendário',
            showOn          : 'both',
            buttonImage     : site + 'images/calendar_cal.gif',
            buttonImageOnly : true,
            changeMonth: true,
            changeYear: true,
            onClose: function(selectedDate) {
                if(jQuery.trim(jQuery(dt_ini).val().replace(/[^0-9]+/, '')) != "") {
                    jQuery(dt_fim).datepicker( "option", "minDate", selectedDate );

                    // var maxDate = $.datepicker.parseDate('dd/mm/yy', selectedDate);
                    // maxDate.setDate(maxDate.getDate() + 1);
                    // jQuery(dt_fim).datepicker( "option", "maxDate", maxDate );
                }
            }
        });

        jQuery(dt_fim).datepicker({

            dateFormat      : 'dd/mm/yy',
            dayNamesMin     : [ 'D', 'S', 'T', 'Q', 'Q', 'S', 'S' ],
            monthNames      : [ 'Janeiro', 'Fevereiro', 'Março', 'Abril',
            'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro',
            'Novembro', 'Dezembro' ],
            monthNamesShort: [ 'Jan', 'Fev', 'Mar', 'Abr',
            'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out',
            'Nov', 'Dez' ],
            nextText: 'Próximo',
            prevText: 'Anterior',
            buttonText: 'Calendário',
            showOn          : 'both',
            buttonImage     : site + 'images/calendar_cal.gif',
            buttonImageOnly : true,
            changeMonth: true,
            changeYear: true,
            onClose: function(selectedDate) {
                if(jQuery.trim(jQuery(dt_fim).val().replace(/[^0-9]+/, '')) != "") {
                    jQuery(dt_ini).datepicker( "option", "maxDate", selectedDate );

                    // var minDate = $.datepicker.parseDate('dd/mm/yy', selectedDate);
                    // minDate.setDate(minDate.getDate() - 1);
                    // jQuery(dt_ini).datepicker( "option", "minDate", minDate );

                }
            }
        });

        jQuery(dt_ini).mask('99/99/9999');
        jQuery(dt_fim).mask('99/99/9999');
    };

    loadCalendario();

    jQuery('#form').submit(function(event) {
        event.preventDefault();

        var dt_ini = jQuery('#periodo_data_inicial').datepicker("getDate");
        var dt_fim = jQuery('#periodo_data_final').datepicker("getDate");
        var diffDays = parseInt((dt_fim - dt_ini) / (1000 * 60 * 60 * 24));

        if(diffDays > 2) {
            jQuery('#mensagem_erro').text('Período máximo de 2 dias para pesquisar').removeClass('invisivel');
        } else {
            jQuery(this).unbind('submit').submit();
        }
    })
});