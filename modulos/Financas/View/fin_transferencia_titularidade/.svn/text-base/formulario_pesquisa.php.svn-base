<?php
$control = new FinTransferenciaTitularidade ();

$usuarios = $control->listUsuarioConcluirProposta ();

?>
  <?php require_once '_msgPrincipal.php'; ?>
  
<div class="bloco_titulo">Busca de Propostas De Transferência de
	Titularidade</div>
<div class="bloco_conteudo">
	<div class="formulario">

		<div class="campo maior">
			<label for="nomeCliente">Cliente</label> <input id="nomeCliente"
				 class="campo" type="text" value="<?php echo $this->view->parametros->nomeCliente;?>" name="nomeCliente">

		</div>
		
		<div class="clear"></div>

		<div class="campo maior">
			<label for="novoTitular">Novo Titular</label> <input id="novoTitular"
				 class="campo" type="text" value="<?php echo $this->view->parametros->novoTitular;?>" name="novoTitular">

		</div>
		<div class="clear"></div>

		<div class="campo data">
			<label><?php echo "Data de Cadastro *";?></label> <input
				class="campo" type="text" name="dt_ini" id="dt_ini" maxlength="10"
				value="<?php echo $this->view->parametros->dt_ini; ?>" />
		</div>

		<div style="margin-top: 23px !important; margin-right: 5px;"
			class="campo label-periodo">até</div>

		<div class="campo data">
			<label>&nbsp;</label> <input class="campo" type="text" name="dt_fim"
				id="dt_fim" maxlength="10" value="<?php echo $this->view->parametros->dt_fim;?>" />
		</div>
		
		<div class="clear"></div>
		
		<div class="campo medio">
			<label for="statusSolicitacaoTransDivida">Status da Transferencia de Divida</label>
			<select id="statusSolicitacaoTransDivida" name="statusSolicitacaoTransDivida">
				<option value="">Escolha</option>
				<option value="2" <? if ($this->view->parametros->statusSolicitacaoTransDivida=="2") echo " SELECTED"; ?>>Aprovado</option>
				<option value="1" <? if ($this->view->parametros->statusSolicitacaoTransDivida=="1") echo " SELECTED"; ?>>Pendente Transferencia Titularidade</option>
				<option value="3" <? if ($this->view->parametros->statusSolicitacaoTransDivida=="3") echo " SELECTED"; ?>>Reprovado Transferencia Titularidade</option>
			</select>
		</div>
		<div class="clear"></div>
		<div class="campo medio">
			<label for="statusSolicitacaoSerasa">Status SERASA</label> <select
				id="statusSolicitacaoSerasa" name="statusSolicitacaoSerasa">
				<option value="">Escolha</option>
				<option value="2" <? if ($this->view->parametros->statusSolicitacaoSerasa=="2") echo " SELECTED"; ?>>Aprovado</option>
				<option value="1" <? if ($this->view->parametros->statusSolicitacaoSerasa=="1") echo " SELECTED"; ?>>Pendente Analise Crédito</option>
				<option value="3" <? if ($this->view->parametros->statusSolicitacaoSerasa=="3") echo " SELECTED"; ?>>Reprovado Analise Crédito</option>
			</select>
		</div>
		<div class="clear"></div>
				<div class="campo medio">
			 <label for="numeroContrato">Número do Contrato</label>
            <input id="numeroContrato" maxlength="10" class="campo" type="text" value="<?php echo $this->view->parametros->numeroContrato;?>" name="numeroContrato">

		</div>
		<div class="clear"></div>

        <div class="clear"></div>
           <div class="campo medio">
            <label for="numeroSolicitacao">Número da Solicitação</label>
             <input id="numeroSolicitacao" maxlength="10" class="campo" type="text" value="<?php echo $this->view->parametros->numeroSolicitacao;?>" name="numeroSolicitacao">
        </div>
        <div class="clear"></div>
  
         <div class="clear"></div>
           <div class="campo data">
                    <label><?php echo "Data de Conclusão *";?></label>
                    <input class="campo"  type="text" name="dt_ini_conclusao" id="dt_ini_conclusao" maxlength="10" value="<?php echo $this->view->parametros->dt_ini_conclusao;?>" />
          </div>

                <div style="margin-top: 23px !important;  margin-right: 5px;" class="campo label-periodo">até</div>

                <div class="campo data">
                    <label>&nbsp;</label>
                    <input  class="campo"  type="text" name="dt_fim_conclusao" id="dt_fim_conclusao" maxlength="10" value= "<?php echo $this->view->parametros->dt_fim_conclusao;?>" />
                </div>
                <div class="clear"></div>
                
                <div class="campo medio">
            <label for="usuarios_conclusao">Usuario Conclusão</label>
                <select name="usuarios_conclusao" id="usuarios_conclusao">
							<option value="">Escolha</option>
							<?php foreach ($usuarios as $row=>$key) : 
							
							?>
							<option value="<?php echo $key['ptrausuoid_conclusao_proposta']; ?>" <? if ($this->view->parametros->usuarios_conclusao==$key['ptrausuoid_conclusao_proposta']) echo " SELECTED"; ?>><?php echo $key['ds_login']; ?></option>
							<?php endforeach;?>
				</select>
        </div>
        
                <div class="clear"></div>
	</div>
</div>

<div class="bloco_acoes">
	<button type="submit" id="bt_pesquisar_transferencia">Pesquisar</button>
	<button type="button" id="bt_limpar_pesquisa">Limpar</button>
	<button type="button" id="bt_novo">Novo</button>      
</div>







