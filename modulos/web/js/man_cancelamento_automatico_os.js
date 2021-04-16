jQuery(document).ready(function() {
    //Botão novo
    jQuery("#bt_novo").click(function() {
        window.location = "cad_motivo_cancelamento_o_s.php?acao=cadastrar";
    });
//botao editar
    jQuery("a.editar").click(function() {
        var id = jQuery(this).attr('rel');
        window.location = "cad_motivo_cancelamento_o_s.php?acao=editar&osmcoid=" + id;
    });

    //botao voltar
    jQuery("#bt_voltar").click(function() {
        window.location = "cad_motivo_cancelamento_o_s.php?acao=pesquisar";
    });
});
