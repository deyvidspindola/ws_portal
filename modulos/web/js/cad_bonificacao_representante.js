jQuery(document).ready(function(){
   
   //botão novo
   jQuery("#bt_novo").click(function(){
       window.location.href = "cad_bonificacao_representante.php?acao=cadastrar";
   });
   
   //botão voltar
   jQuery("#bt_voltar").click(function(){
       window.location.href = "cad_bonificacao_representante.php";
   });

    jQuery('#bonrevalor_bonificacao').maskMoney({
        thousands     : '.',
        decimal       : ',',
        precision     : 2,
        defaultZero   : true,
        allowZero: true
    });

    jQuery('#bonreqtd_min_os').keyup(function() {
        somenteNumeros(this);
    }).change(function() {
        somenteNumeros(this);
    });
   
});

function excluir(bonreoid) {
    if (confirm('Deseja realmente excluir este registro?')) {
        window.location.href = "cad_bonificacao_representante.php?acao=excluir&bonreoid=" + bonreoid;
    }

    return false;
}

function cancelar(bonreoid) {
    if (confirm('Deseja realmente cancelar este registro?')) {
        window.location.href = "cad_bonificacao_representante.php?acao=cancelarBonificacao&bonreoid=" + bonreoid;
    }

    return false;
}

function somenteNumeros(elemento) {
    var padrao = /[^0-9]/g;
    var novoValor = jQuery(elemento).val().replace(padrao, '');

    jQuery(elemento).val(novoValor);
}