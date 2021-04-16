<?php 
/*echo "<pre>";
if (!empty($_POST)) {
    print_r($_POST);
} else {
    echo "Notting to do here";
}

if (!empty($dados)) {
    print_r($dados);
} else {
    echo "<br>Nao foi o Dados";
}
print_r($dados);
echo "</pre>";*/
//require_once '_abas.php'; ?>
<div class="bloco_titulo">Transferencia de Titularidade e Alteração - Massivo</div>

<form id="form" name="form" action="fin_transferencia_titularidade.php?acao=novo" method="post" enctype="multipart/form-data">
	<input type="hidden" name="acao" id="form_acao" value="processarRetornoErroArquivoRPS">
	<div class="bloco_conteudo">
		<div class="formulario">
            <?php if($this->acao == 'pesquisar'): ?>
                <?php if(!empty($this->msgInfo)): ?><div class="mensagem info"><?php echo $this->msgInfo; ?></div><?php endif; ?>
                <?php if(!empty($this->msgAlerta)): ?><div class="mensagem alerta"><?php echo $this->msgAlerta; ?></div><?php endif; ?>
                <?php if(!empty($this->msgSucesso)): ?><div class="mensagem sucesso"><?php echo $this->msgSucesso; ?></div><?php endif; ?>
                <?php if(!empty($this->msgErro)): ?><div class="mensagem erro"><?php echo $this->msgErro; ?></div><?php endif; ?>
            <?php endif; ?>

            <div class="campo maior">
                <label style="margin-left: 1px;" for="numero_cpf_cnpj">*CPF/CNPJ</label>
                <input style="width:190px; margin-left: 1px;" type="text" name="numero_cpf_cnpj" id="numero_cpf_cnpj" value="<?php echo !empty($this->numeroCpfCnpj) ? $this->numeroCpfCnpj : null; ?>38.769.002/0001-12" class="campo" disabled/>
            </div>

            <div class="campo maior">
                <label style="!important;" for="atual_titular">*Atual Titular</label>
                <input style=" !important;" type="text" name="atual_titular" id="atual_titular" value="<?php echo !empty($this->atualTitular) ? $this->atualTitular : null; ?>GRYCAMP TRANSPORTES LTDA" class="campo" disabled/>
            </div>

            <div class="clear"></div>

            <div class="campo maior">
                <label style=" margin-left: 1px;" for="tipo_proposta">Tipo de Proposta:</label>
                <select style="margin-left: 1px; width: 260px; !important;" name="tipo_proposta" id="tipo_proposta" disabled>
                    <option value="">Transferência de Títularidade - Massivo</option>
                    <option value="alteracao" <?php echo !empty($this->tipoProposta) && $this->tipoProposta == "alteracao" ? 'selected' : null; ?>>Alteração - Massivo</option>
                    <option value="transferencia" <?php echo !empty($this->tipoProposta) && $this->tipoProposta == "transferencia" ? 'selected' : null; ?>>Transferência de Títularidade - Massivo</option>
                </select>
            </div>

            <div class="campo menor">
                <label  for="tipo_contrato">Contratos:</label>
                <select style="margin-top: 2px; width: 90px;  !important;" name="tipo_contrato" id="tipo_contrato" disabled>
                    <option value="todos">Ativos</option>
                    <option value="todos" <?php echo !empty($this->tipoContrato) && $this->tipoContrato == "1" ? 'selected' : null; ?>>Todos</option>
                    <option value="ativos" <?php echo !empty($this->tipoContrato) && $this->tipoContrato == "1" ? 'selected' : null; ?>>Ativos</option>
                    <option value="pendentes" <?php echo !empty($this->tipoContrato) && $this->tipoContrato == "1" ? 'selected' : null; ?>>Pendentes</option>
                </select>
            </div>

            <div class="campo menor">
                <label for="classe_contrato">Classe do Contrato:</label>
                <select style="margin-top: 2px; width: 150px; !important;" name="classe_contrato" id="classe_contrato" disabled>
                    <option value="todos">Todos</option>
                    <option value="todos" <?php echo !empty($this->classeContrato) && $this->classeContrato == "1" ? 'selected' : null; ?>>Todos</option>
                    <option value="sascar_full" <?php echo !empty($this->classeContrato) && $this->classeContrato == "1" ? 'selected' : null; ?>>Sascar Full</option>
                    <option value="sascar_full_sat" <?php echo !empty($this->classeContrato) && $this->classeContrato == "1" ? 'selected' : null; ?>>Sascar Full SAT 1000</option>
                </select>
            </div>
            
              <div class="campo maior contratoStatus">
                        <label for="newvigencia" style="margin-left: -44px;">
                            <input type="checkbox" class="checkbox" checked id="newvigenciaCheckbox" name="newvigencia" value = "13" >
                            Validacao da Nova Vigencia:
                        </label>
               </div>

            <div class="clear"></div>

            <fieldset class="menor">
                <legend>Informe o Novo Titular</legend>
                <div class="campo maior">
                    <label for="numero_cpf_cnpj">*CPF/CNPJ</label><br>
                    <input style="width:190px;" type="text" name="numero_cpf_cnpj" id="numero_cpf_cnpj" value="<?php echo $_POST["id_cliente"]; ?>" class="campo" />
                </div>

                <div class="campo maior">
                    <label style="margin-left: -184px !important;" for="atual_titular">*Atual Titular</label><br>
                    <input style="margin-left: -184px !important;" type="text" name="atual_titular" id="atual_titular" value="<?php echo $_POST["cliente"]; ?>" class="campo" />
                </div>
            </fieldset>

            <div class="campo menor"></div>

            <!-- 			<div class="campo maior">
				<label for="situacao_rps">Situação da RPS</label>
				<select name="situacao_rps" id="situacao_rps">
					<option value="">Selecione</option>
					<option value="1" <?php echo !empty($this->situacaoRps) && $this->situacaoRps == "1" ? 'selected' : null; ?>>NF sem erro</option>
					<option value="2" <?php echo !empty($this->situacaoRps) && $this->situacaoRps == "2" ? 'selected' : null; ?>>NF com erro</option>
					<option value="3" <?php echo !empty($this->situacaoRps) && $this->situacaoRps == "3" ? 'selected' : null; ?>>NF gerada</option>
				</select>
			</div> -->

            <div class="clear"></div>

            <div class="campo menor">
                <label for="numero_resultados" style="margin-top: -45px; margin-left: 814px;">Mostrar:</label>
                <select name="numero_resultados" id="numero_resultados" style="margin-left: 814px;  width:100px;">
                    <option value="all">Escolha</option>
                    <option value="10" <?php echo !empty($this->numeroResultados) && $this->numeroResultados == "10" ? 'selected' : null; ?>>10 registros</option>
                    <option value="25" <?php echo !empty($this->numeroResultados) && $this->numeroResultados == "25" ? 'selected' : null; ?>>25 registros</option>
                    <option value="50" <?php echo !empty($this->numeroResultados) && $this->numeroResultados == "50" ? 'selected' : null; ?>>50 registros</option>
                </select>
            </div>
            <div class="campo menor">
                <label for="ordena_resultados" style="margin-top: -45px; margin-left: 799px;">Ordenar:</label>
                <select name="ordena_resultados" id="ordena_resultados" style="margin-left: 799px;  width:126px;">
                    <option value="contrato">Escolha</option>
                    <option value="inicio_vigencia" <?php echo !empty($this->ordenaResultados) && $this->ordenaResultados == "vigencia" ? 'selected' : null; ?>>Data de Vigência</option>
                    <option value="contrato" <?php echo !empty($this->ordenaResultados) && $this->ordenaResultados == "contrato" ? 'selected' : null; ?>>Nº Termo/Contrato</option>
                    <option value="placa" <?php echo !empty($this->ordenaResultados) && $this->ordenaResultados == "placa" ? 'selected' : null; ?>>Placa</option>
                </select>
            </div>
            <div class="campo menor">
                <label for="classifica_resultados" style="margin-top: -45px; margin-left: 812px;">Classificar:</label>
                <select name="classifica_resultados" id="classifica_resultados" style="margin-left: 812px;  width:100px;">
                    <option value="asc">Escolha</option>
                    <option value="asc" <?php echo !empty($this->situacaoRps) && $this->situacaoRps == "asc" ? 'selected' : null; ?>>Ascendente</option>
                    <option value="desc" <?php echo !empty($this->situacaoRps) && $this->situacaoRps == "desc" ? 'selected' : null; ?>>Descendente</option>
                </select>
            </div>
            </td>
		</div>
	</div>
	<div class="bloco_acoes">
		<button type="submit">Processar</button>
	</div>
</form>


<!--Inicio - ORGMKTOTVS-826 - ERP - Alterar tela de geração de remessa para a Prefeitura de Barueri-->

<input type="hidden" name="INTEGRACAO_TOTVS_ATIVA" id="INTEGRACAO_TOTVS_ATIVA" value="<?php echo (INTEGRACAO_TOTVS_ATIVA); ?>">



<?php if(INTEGRACAO_TOTVS_ATIVA == true): ?>
<div class="hidden" id="processarRetornoErroArquivoRPS">


<div class="separador"></div>

<?php if($this->action == 'processarRetornoErroArquivoRPS'): ?>

<div class="bloco_titulo" >Erros Notas Fiscais</div>
<div class="bloco_conteudo">
	<div class="listagem">

			<table>
				<thead>
					<tr>
						<th style="text-align: center;"></th>
						<th style="text-align: center;">NF</th>
						<th style="text-align: center;">Série</th>
						<th style="text-align: center;">Cod/Cli</th>
						<th style="text-align: center;">Cliente</th>
						<th style="text-align: center;">CPF/CNPJ</th>
						<th style="text-align: center;">Valor</th>
						<th style="text-align: center;">Ocorrência</th>
					</tr>
				</thead>
				<?php if(!empty($this->notas_file)): ?>
				<tbody>
					<?php foreach($this->notas_file as $nf): ?>
					<tr class="par">
						<td align="center">
						</td>
						<td style="text-align: center;"><?php echo $nf['numeroRPS']; ?></td>
						<td style="text-align: center;"><?php echo $nf['serieNfe']; ?></td>
						<td style="text-align: center;"><?php echo $nf['clioid']; ?></td>
						<td><?php echo $nf['nomeTomador']; ?></td>
						<td style="text-align: center;"><?php echo $nf['numeroDocumento']; ?></td>
						<td style="text-align: center;"><?php echo number_format($nf['valorFatura'], 2, ",", "."); ?></td>
						<td style="text-align: left;"><?php echo $nf['ocorrencias']; ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
				<?php endif; ?>

				<tfoot>
					<?php if(!empty($this->notas_file)): ?>

					<tr class="tableRodapeModelo3">
						<td align="center">
						</td>
						<td colspan="10" align="center">
						<?php $acao = 'index'; ?>
							<input type="submit" id="fechar_rps" value="Fechar" class="botao">
					<tr><td colspan="11" style="text-align: center;"><?php echo count($this->notas_file); ?> registro(s) encontrado(s)</td></tr>
					<?php else: ?>
					<tr><td colspan="11" style="text-align: center;">Nenhum resultado encontrado.</td></tr>
					<?php endif; ?>
				</tfoot>
			</table>
	</div>

</div>
<?php endif; ?>
</div>
<?php endif; ?>
<!--Fim - ORGMKTOTVS-826 - ERP - Alterar tela de geração de remessa para a Prefeitura de Barueri-->
