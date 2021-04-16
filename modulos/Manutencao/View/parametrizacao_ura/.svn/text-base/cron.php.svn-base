<? require_once '_header.php' ?>

<script type="text/javascript" src="modulos/web/js/man_parametrizacao_ura.js"></script>

<form method="post" id="cron_form" action="">
<input type="hidden" name="acao" id="acao" value="" />
    
    <div class="bloco_titulo">Cron Assistência</div>    
    <div class="bloco_conteudo">
		<div class="formulario">
			<fieldset class="medio">
				<legend>Envio</legend>
				<input type="radio" id="opcao_assistencia_ativar_envio" name="cron_assistencia_envio" value="A" <?php echo ($form['assistencia']['envio'] == 'A') ? 'checked' : ''; ?> />
				<label for="opcao_assistencia_ativar_envio">Ativar</label>
				<input type="radio" id="opcao_assistencia_inativar_envio" name="cron_assistencia_envio" value="I" <?php echo ($form['assistencia']['envio'] == 'I') ? 'checked' : ''; ?> />
				<label for="opcao_assistencia_inativar_envio">Inativar</label>
				<input type="radio" id="opcao_assistencia_parcial_envio" name="cron_assistencia_envio" value="P" <?php echo ($form['assistencia']['envio'] == 'P') ? 'checked' : ''; ?> />
				<label for="opcao_assistencia_parcial_envio">Parcial</label>
			</fieldset>
			<div class="clear"></div>

			<fieldset class="medio">
				<legend>Reenvio</legend>
				<input type="radio" id="opcao_assistencia_ativar_reenvio" name="cron_assistencia_reenvio" value="A" <?php echo ($form['assistencia']['reenvio'] == 'A') ? 'checked' : ''; ?> />
				<label for="opcao_assistencia_ativar_reenvio">Ativar</label>
				<input type="radio" id="opcao_assistencia_inativar_reenvio" name="cron_assistencia_reenvio" value="I" <?php echo ($form['assistencia']['reenvio'] == 'I') ? 'checked' : ''; ?> />
				<label for="opcao_assistencia_inativar_reenvio">Inativar</label>
			</fieldset>
			<div class="clear"></div>

			<fieldset class="medio">
				<legend>Insucesso</legend>
				<input type="radio" id="opcao_assistencia_ativar_insucesso" name="cron_assistencia_insucesso" value="A" <?php echo ($form['assistencia']['insucesso'] == 'A') ? 'checked' : ''; ?> />
				<label for="opcao_assistencia_ativar_insucesso">Ativar</label>
				<input type="radio" id="opcao_assistencia_inativar_insucesso" name="cron_assistencia_insucesso" value="I" <?php echo ($form['assistencia']['insucesso'] == 'I') ? 'checked' : ''; ?> />
				<label for="opcao_assistencia_inativar_insucesso">Inativar</label>
			</fieldset>
			<div class="clear"></div>
			
			<fieldset class="medio">
				<legend>Verificação discador</legend>
				<input type="radio" id="opcao_assistencia_ativar_adicional" name="cron_assistencia_adicional" value="A" <?php echo ($form['assistencia']['adicional'] == 'A') ? 'checked' : ''; ?> />
				<label for="opcao_assistencia_ativar_adicional">Ativar</label>
				<input type="radio" id="opcao_assistencia_inativar_adicional" name="cron_assistencia_adicional" value="I" <?php echo ($form['assistencia']['adicional'] == 'I') ? 'checked' : ''; ?> />
				<label for="opcao_assistencia_inativar_adicional">Inativar</label>
			</fieldset>
			<div class="clear"></div>    
        </div>
    </div>  
    
   <div class="bloco_titulo block-margin">Cron Estatística</div>    
    <div class="bloco_conteudo">
		<div class="formulario">
			<fieldset class="medio">
				<legend>Envio</legend>
				<input type="radio" id="opcao_estatistica_ativar_envio" name="cron_estatistica_envio" value="A" <?php echo ($form['estatistica']['envio'] == 'A') ? 'checked' : ''; ?> />
				<label for="opcao_estatistica_ativar_envio">Ativar</label>
				<input type="radio" id="opcao_estatistica_inativar_envio" name="cron_estatistica_envio" value="I" <?php echo ($form['estatistica']['envio'] == 'I') ? 'checked' : ''; ?> />
				<label for="opcao_estatistica_inativar_envio">Inativar</label>
			</fieldset>
			<div class="clear"></div>

			<fieldset class="medio">
				<legend>Reenvio</legend>
				<input type="radio" id="opcao_estatistica_ativar_reenvio" name="cron_estatistica_reenvio" value="A" <?php echo ($form['estatistica']['reenvio'] == 'A') ? 'checked' : ''; ?> />
				<label for="opcao_estatistica_ativar_reenvio">Ativar</label>
				<input type="radio" id="opcao_estatistica_inativar_reenvio" name="cron_estatistica_reenvio" value="I" <?php echo ($form['estatistica']['reenvio'] == 'I') ? 'checked' : ''; ?> />
				<label for="opcao_estatistica_inativar_reenvio">Inativar</label>
			</fieldset>
			<div class="clear"></div>

			<fieldset class="medio">
				<legend>Insucesso</legend>
				<input type="radio" id="opcao_estatistica_ativar_insucesso" name="cron_estatistica_insucesso" value="A" <?php echo ($form['estatistica']['insucesso'] == 'A') ? 'checked' : ''; ?> />
				<label for="opcao_estatistica_ativar_insucesso">Ativar</label>
				<input type="radio" id="opcao_estatistica_inativar_insucesso" name="cron_estatistica_insucesso" value="I" <?php echo ($form['estatistica']['insucesso'] == 'I') ? 'checked' : ''; ?> />
				<label for="opcao_estatistica_inativar_insucesso">Inativar</label>
			</fieldset>
			<div class="clear"></div>    
        </div>
    </div>  
   
   <div class="bloco_titulo block-margin">Cron Pânico</div>
     <div class="bloco_conteudo">
		<div class="formulario">
			<fieldset class="medio">
				<legend>Envio</legend>
				<input type="radio" id="opcao_panico_ativar_envio" name="cron_panico_envio" value="A" <?php echo ($form['panico']['envio'] == 'A') ? 'checked' : ''; ?> />
				<label for="opcao_panico_ativar_envio">Ativar</label>
				<input type="radio" id="opcao_panico_inativar_envio" name="cron_panico_envio" value="I" <?php echo ($form['panico']['envio'] == 'I') ? 'checked' : ''; ?> />
				<label for="opcao_panico_inativar_envio">Inativar</label>
				<input type="radio" id="opcao_panico_parcial_envio" name="cron_panico_envio" value="P" <?php echo ($form['panico']['envio'] == 'P') ? 'checked' : ''; ?> />
				<label for="opcao_panico_parcial_envio">Parcial</label>
			</fieldset>
			<div class="clear"></div>

			<fieldset class="medio">
				<legend>Reenvio</legend>
				<input type="radio" id="opcao_panico_ativar_reenvio" name="cron_panico_reenvio" value="A" <?php echo ($form['panico']['reenvio'] == 'A') ? 'checked' : ''; ?> />
				<label for="opcao_panico_ativar_reenvio">Ativar</label>
				<input type="radio" id="opcao_panico_inativar_reenvio" name="cron_panico_reenvio" value="I" <?php echo ($form['panico']['reenvio'] == 'I') ? 'checked' : ''; ?> />
				<label for="opcao_panico_inativar_reenvio">Inativar</label>
			</fieldset>
			<div class="clear"></div>

			<fieldset class="medio">
				<legend>Insucesso</legend>
				<input type="radio" id="opcao_panico_ativar_insucesso" name="cron_panico_insucesso" value="A" <?php echo ($form['panico']['insucesso'] == 'A') ? 'checked' : ''; ?> />
				<label for="opcao_panico_ativar_insucesso">Ativar</label>
				<input type="radio" id="opcao_panico_inativar_insucesso" name="cron_panico_insucesso" value="I" <?php echo ($form['panico']['insucesso'] == 'I') ? 'checked' : ''; ?> />
				<label for="opcao_panico_inativar_insucesso">Inativar</label>
			</fieldset>
			<div class="clear"></div>
			
			<fieldset class="medio">
				<legend>Ignorar pânico</legend>
				<input type="radio" id="opcao_panico_ativar_adicional" name="cron_panico_adicional" value="A" <?php echo ($form['panico']['adicional'] == 'A') ? 'checked' : ''; ?> />
				<label for="opcao_panico_ativar_adicional">Ativar</label>
				<input type="radio" id="opcao_panico_inativar_adicional" name="cron_panico_adicional" value="I" <?php echo ($form['panico']['adicional'] == 'I') ? 'checked' : ''; ?> />
				<label for="opcao_panico_inativar_adicional">Inativar</label>
			</fieldset>
			<div class="clear"></div>    
        </div>
    </div>    
    
    <div class="bloco_acoes">
        <button type="button" name="botao_salvar_cron" id="botao_salvar_cron">Salvar</button>            
    </div>
</form>

<?php require_once '_footer.php' ?>