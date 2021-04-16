<?php 

if ($this->view->parametros->tipo_relatorio == "A") {

	$divId = "dialog-email-analitico";

	$assunto = "Relatório Gerencial de Descontos Concedidos - Analítico - ";

} else {

	$divId = "dialog-email-sintetico";

	$assunto = "Relatório Gerencial de Descontos Concedidos - Sintético  - ";

}

 ?>


<style type="text/css">

.mensagem_custom{
	width: 453px; 	
	width: 500px !important\9;	
}

</style>

<div id="<?php echo $divId ?>" title="Enviar o relatório por e-mail" class="invisivel">

	<div id="loader_email" style="margin-top: 70px; position: relative;" class="carregando invisivel"></div>

	<div class="formulario">

		<div style="margin-left: 0px; margin-right: 0px;" id="email_mensagem_alerta" class="mensagem mensagem_custom alerta invisivel">Existem campos obrigatórios não preenchidos.</div>

		<form id="form_envio_email">

			<input type="hidden" name="acao" value="enviarEmail" />

			<input type="hidden" name="tipo" value="<?php echo $this->view->parametros->tipo_relatorio ?>" />	


			<?php if (isset($this->view->parametros->tipo_relatorio) && $this->view->parametros->tipo_relatorio == "S") : ?>		

				<input type="hidden" name="tipo_pesquisa" value="<?php echo $this->view->parametros->tipo_resultado ?>" />

			<?php endif ?>

			<div class="clear"></div>

			<div class="campo menor">
				<label id="lbl_email_para">Para: *</label>
			</div>
			<div class="campo">
				<input type="text" style="width: 500px;" name="email_para" value="<?php echo $this->view->parametros->emailUsuarioLogado ?>" /> 
			</div>

			<div class="clear"></div>

			<div class="campo menor">
				<label>CC:</label>
			</div>
			<div class="campo" style="text-align: right">
				<input type="text" style="width: 500px;" name="email_cc" class="desabilitado" readonly value="<?php echo implode(';', $this->view->parametros->usuariosAprovadoresCc) ?>" /> 
			</div>

			<div class="clear"></div>

			<div class="campo menor">
				<label>Assunto:</label>
			</div>
			<div class="campo" style="text-align: right">
				<input type="text" style="width: 500px;" name="email_assunto" class="desabilitado" readonly value="<?php echo $assunto . $this->view->parametros->periodo_inclusao_ini ?> a <?php echo $this->view->parametros->periodo_inclusao_fim ?>." /> 
			</div>                                                                  

			<div class="clear"></div>

			<div class="campo menor">
				<label id="lbl_email_corpo">Corpo: *</label>
			</div>

			<div class="campo">
				<textarea cols="100" rows="8" style="width: 500px;" name="email_corpo">
Prezado(s),
Segue anexo, o Relatório Gerencial de Descontos Concedidos referente o período de <?php echo $this->view->parametros->periodo_inclusao_ini ?> a <?php echo $this->view->parametros->periodo_inclusao_fim ?>.
Att.
<?php echo $_SESSION['usuario']['nome_completo'] ?>
				</textarea>
			</div>

		</form>
	</div>
</div>