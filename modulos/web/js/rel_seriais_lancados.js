jQuery(document).ready(function(){
	estadoListener();
	csvListener();
	verificaCidadesEstado();
	btnVoltarListener();
});

function csvListener(){
	jQuery("#bt-csv").on('click', function(e) {
		jQuery("#acao").val("gerar-csv");
		jQuery("#form_pesquisa").submit();
	});
}

function estadoListener() {
	jQuery("#estado").on('change', function(e) {
		verificaCidadesEstado();
	});
}

function btnVoltarListener() {
	jQuery("#bt_voltar").on('click',function(e) {
		e.preventDefault();
		window.location="principal.php?menu=Relatorios";
	});
}

function verificaCidadesEstado() {
	var estado = jQuery("#estado").val();

	if(estado.length == 0) {
		jQuery("#cidade").val('');
		jQuery("#cidade").find('option').remove(); 
		jQuery("#cidade").append('<option value="">Escolha</option>');
		jQuery("#cidade").attr('disabled','disabled');
	} else if(jQuery("#cidade").val().length == 0) {
		buscaCidadesIdUF(estado);
	}
}

function buscaCidadesIdUF(estado) {

	jQuery.ajax({
		type: 'post',
		data:{
			acao :'listar-cidades',
			uf : estado,
		},
		dataType: "JSON",
		success:function(data) {
			jQuery("#cidade").find('option').remove();  
			jQuery("#cidade").append('<option value="">Escolha</option>');
			jQuery.each(data, function(item) {
				jQuery("#cidade").append('<option value=' + this.ciddescricao + '>' + this.ciddescricao + '</option>');
			});
			jQuery("#cidade").removeAttr("disabled");
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Erro ao processar a busca por cidades.");
        }
	});

};

