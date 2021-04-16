<html>
	<head>
		<title>SASCAR Tecnologia e Segurança Automotiva</title>
		<link type="text/css" rel="stylesheet" href="includes/css/base_form.css">
		<link type="text/css" rel="stylesheet" href="modulos/web/css/fin_rescisao.css">
        <script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>
	</head>

	<body>

		<div id="carta" style="margin-left: 15px;" width="95%">

			<table width="95%" border="0">
				<tr>
					<td align="left">
						<img src="images/sascar_logo_175.png">
					</td>
					<td align="right">
						<!-- <img src="images/bvqi.jpg" height="70" width="180"> -->
					</td>
				</tr>
			</table>

			<table width="95%">
				<tr>
					<td colspan="2" align="right">
						<?= date('d/m/Y', strtotime($rescisaoMae['resmcarta'])) ?><br>
					</td>
				</tr>

				<tr>
					<td colspan="2" align="left">
                        <b>&Agrave; <br><br><?= $rescisaoMae['clinome'] ?></b><br>

						<?php 
							echo $rescisaoMae['clino_res']     ? trim($rescisaoMae['clirua_res']) : '';
							echo $rescisaoMae['clino_res']     ? ', ' . trim($rescisaoMae['clino_res']) : ''; 
							echo $rescisaoMae['clicompl_res']  ? ', ' . trim($rescisaoMae['clicompl_res']) : '';
							echo $rescisaoMae['clibairro_res'] ? ', ' . trim($rescisaoMae['clibairro_res']) : '';
							echo $rescisaoMae['clino_cep_res'] ? 'CEP: ' . trim($rescisaoMae['clino_cep_res']) . '<br>' : '';

							if( !empty($rescisaoMae['clicidade_res']) AND !empty($rescisaoMae['cliuf_res']) ) {
								echo '<br>' . $rescisaoMae['clicidade_res'] . ' - ' . $rescisaoMae['cliuf_res'];
							} 
						?>
						<br><br>

						<b>Ref.: Rescisão contratual</b><br>
						
						<?php 
            				$totalContratos = 0;
            				$totalTaxaRetirada = 0;
            				$totalMensalidadesVencidas = 0;

							if($contratos) {
	            				foreach($contratos as $contrato) {
	                        		$totalContratos += $contrato['rescvl_locacao'] + $contrato['rescvl_monitoramento'];
	            				}
	            			}

	            			if($taxasRetirada) {
	            				foreach($taxasRetirada as $taxa) {
	            					$totalTaxaRetirada += $taxa['rescrvl_retirada'];
	            				}
	            			}

	            			if($mensalidadesVencidas) {
	            				foreach($mensalidadesVencidas as $mensalidades) {
		                        	if($mensalidades) {
		                        		$hasMensalidadesVencidas = true;
		                        	   	foreach ($mensalidades as $mensalidade) {
		                        			$totalMensalidadesVencidas += $mensalidade['titvl_titulo'];
		                        		}	
		                        	}
	            				}
	            			}

	            			$totalRescisao = $totalContratos + $totalTaxaRetirada + $totalMultasLocacao;
                		?>
                		<?=$strContratos;?>
						<br><br>

						Prezado(s) Senhor(es),
						<br><br>
                       	
                    	Em referência ao pedido de cancelamento dos serviços prestados pela SASCAR efetuado, via telefone por V.Sa., vimos por meio desta formalizar o procedimento de rescisão 
                    	do CONTRATO DE PRESTAÇÃO DE SERVIÇOS E OUTRAS AVENÇAS, firmado entre SASCAR TECNOLOGIA E SEGURANÇA AUTOMOTIVA S.A. ("SASCAR") e V.Sa em <?=$maiorDataRescisao?>.
                    	<br><br>
	                    
	                    Desta forma, em razão da rescisão do(s) contrato(s), V.Sa. deverá efetuar o pagamento do montante de R$<?= toMoney($valorTotalRescisao)?> (<?=$dao->valorPorExtenso(toMoney($valorTotalRescisao))?>), abaixo discriminado:
                    	<br><br><br>
					</td>
				</tr>
			</table>

			<?php if($contratos) : ?>
	            <!-- Bloco de contratos -->
				<table width="95%">
					<tr>
						<td colspan="8"><b>Multa rescisória</b></td>
					</tr>
				</table>

				<table width="95%" class="tabela" border="1">
	                <tr>
	                    <th width="10%">Contrato</th>
	                    <th width="10%">Data da solicitação</th>
	                    <th width="10%">Placa</th>
	                    <th width="15%">Quantidade de meses restantes</th>
	                    <th width="15%">Percentual multa rescisória</th>
	                    <th width="15%">Valor multa</th>
	                    <th width="15%">Desconto <br> (Dias não utilizados)</th>
	                </tr>

	                <? foreach ($contratos as $contrato): ?>
	                    <?
	                        $totalContrato = $contrato['rescvl_locacao'] + $contrato['rescvl_monitoramento'];
	                        $totalMultaRescisao += $totalContrato;
	                    ?>
	                    <tr>
	                        <td><?= $contrato['connumero'] ?></td>
	                        <td><?= date('d/m/Y', strtotime($contrato['rescfax'])) ?></td>
	                        <td><?= $contrato['veiplaca'] ?></td>
	                        <td><?= $contrato['rescmeses'] >= 0 ? $contrato['rescmeses'] : 0 ?></td>
	                        <td><?= toMoney($contrato['rescperc_multa']) ?>%</td>
	                        <td>R$<?= toMoney($totalContrato) ?></td>
	                        <td>R$<?= toMoney($contrato['rescvl_desconto_monitoramento'] + $contrato['rescvl_desconto_locacao']) ?></td>

	                    </tr>
	                <? endforeach ?>
				</table>
			<?php endif;?>

			<!-- Bloco de taxas de retirada -->
            <? if ($taxasRetirada): ?>
                <br>
                <table width="95%" border="0">
					<tr>
                        <td colspan="5"><b>Taxa de remoção dos equipamentos</b></td>
					</tr>
				</table>

				<table width="95%" class="tabela" border="1">
					<tr>
					    <th width="40%">Equipamento / Acessório</th>
					    <th width="20%">Valor</th>
					    <th width="20%">Data de vencimento</th>
					    <th width="20%">Forma de pagamento</th>
					</tr>

					<? foreach ($taxasRetirada as $taxa): ?>
                        <? $totalTaxaRetirada += $taxa['rescrvl_retirada'] ?>
						<tr>
							<td><?= $taxa['obr_servico'] ?></td>
							<td>R$<?= toMoney($taxa['rescrvl_retirada']) ?></td>
							<td><?=$titven?></td>
							<td>Boleto</td>
						</tr>
					<? endforeach ?>

				</table>
            <? endif ?>

			
			<? if ($taxaNaoRetirada): ?>
                <br>
                <table width="95%" border="0">
					<tr>
                        <td colspan="5"><b>Multa por não devolução do(s) equipamento(s)</b></td>
					</tr>
				</table>

				<table width="95%" class="tabela" border="1">
					<tr>
					    <th width="40%">Equipamento / Acessório</th>
					    <th width="20%">Valor</th>
					    <th width="20%">Data de vencimento</th>
					    <th width="20%">Forma de pagamento</th>
					</tr>

					<? foreach ($taxaNaoRetirada as $taxa): ?>
                        <? $totalTaxaRetirada += $taxa['rescrvl_nao_retirada'] ?>
						<tr>
							<td><?= $taxa['obr_servico'] ?></td>
							<td>R$<?= toMoney($taxa['rescrvl_nao_retirada']) ?></td>
							<td><?=$titven?></td>
							<td>Boleto</td>
						</tr>
					<? endforeach ?>

				</table>
            <? endif ?>


            <!-- Bloco de multas de locação -->
			<? if ($hasMensalidadesVencidas): ?>
				<br>

				<table width="95%" border="0">
					<tr>
						<td colspan="4">
                            <b>Mensalidades vencidas</b>
                        </td>
					</tr>
				</table>

				<table width="95%" class="tabela" border="1">
					<tr>
						<th width="10%">NF</th>
						<th width="20%">Data de vencimento</th>
						<th width="10%">Valor</th>
					</tr>
			        <?php foreach ($mensalidadesVencidas as $mensalidades): ?>
			            <?php if($mensalidades) : ?>
				            <?php foreach ($mensalidades as $mensalidade) : ?>
					            <tr>
									<td><?= $mensalidade['nota'] ?></td>
									<td><?= $mensalidade['titdt_vencimento'] ?></td>
									<td>R$<?= toMoney($mensalidade['titvl_titulo']) ?></td>
								</tr>
							<? endforeach; ?>
						<? endif; ?>
			        <? endforeach; ?>
			    </table>

			    <br>
				O valor de R$<?= toMoney($totalMensalidadesVencidas) ?> (<?=$dao->valorPorExtenso(toMoney($totalMensalidadesVencidas))?>), referente às parcelas vencidas, deverá ser pago por meio dos títulos já enviados anteriormente.
				<br>
            <? endif ?>

            <!-- Observação da carta -->
            <?php if(!empty($rescisaoBaixaIntegral)) : ?>
            	<br><b>Observação: </b>Pedimos a gentileza de não mais realizar pagamentos referente as parcelas dos carnês abaixo mencionados visto que já foram baixadas pela Sascar.

            	<br><br>

            	<table width="95%" border="0">
					<tr>
						<td colspan="4">
                            <b>Faturas</b>
                        </td>
					</tr>
				</table>

				<table width="95%" class="tabela" border="1">
					<tr>
						<th width="10%">NF</th>
						<th width="20%">Data de vencimento</th>
						<th width="10%">Valor</th>
					</tr>

			        <? foreach ($rescisaoBaixaIntegral as $rescisaoBaixa): ?>
			            <tr>
							<td><?= $rescisaoBaixa['nota'] ?></td>
							<td><?= date('d/m/Y', strtotime($rescisaoBaixa['vencimento'])) ?></td>
							<td>R$<?= toMoney($rescisaoBaixa['valor']) ?></td>
						</tr>
			        <? endforeach ?>
			    </table>

            <?php elseif(!empty($rescisaoBaixaParcial)) : ?>
            	<br><b>Observação: </b>Será reenviado novo carnê com os devidos descontos. Pedimos a gentileza de desconsiderar os carnês abaixo mencionados e efetuar os pagamentos somente com o novo carnê que será encaminhado.

            	<br><br>

            	<table width="95%" border="0">
					<tr>
						<td colspan="4">
                            <b>Faturas</b>
                        </td>
					</tr>
				</table>

				<table width="95%" class="tabela" border="1">
					<tr>
						<th width="10%">NF</th>
						<th width="20%">Data de vencimento</th>
						<th width="10%">Valor</th>
					</tr>

			        <? foreach ($rescisaoBaixaParcial as $rescisaoBaixa): ?>
			            <tr>
							<td><?= $rescisaoBaixa['nota'] ?></td>
							<td><?= date('d/m/Y', strtotime($rescisaoBaixa['vencimento'])) ?></td>
							<td>R$<?= toMoney($rescisaoBaixa['valor']) ?></td>
						</tr>
			        <? endforeach ?>
			    </table>

            <?php endif; ?>

            <br>
        	Para efetuar o agendamento da desinstalação dos equipamentos, favor entrar em contato com a Central de Atendimento da SASCAR, por meio de um dos seguintes telefones 4002-6004 (capitais e regiões metropolitanas)
        	ou 0800-648-6004 (demais localidades), sob pena do pagamento do valor de referência conforme tabela vigente, além das demais penalidades legalmente previstas.        	
        	<br><br>

        	<b>ATENÇÃO:</b> O contrato somente será considerado rescindido, sendo concedida a mais ampla, geral, irretratável e irrevogável quitação entre as partes com relação ao contrato, após a devolução dos equipamentos.        	
        	<br><br>
        	
        	A SASCAR agradece vossa atenção e coloca-se à disposição para quaisquer esclarecimentos que se fizerem necessários.
        	<br><br>
        	
        	Atenciosamente, 
        	<br><br>

       		<b>SASCAR TECNOLOGIA E SEGURANÇA AUTOMOTIVA S.A.</b>

		</div>
		
			</br>
			<div style="display:none" class="container-loader loader"></div>
			<center>
				<?php if($email != '') : ?>
    				<button id="btnEnviarEmail" class="botao">Enviar e-mail</button>
    			<?php endif;?>

    			<a href="fin_rescisao2.php">
			        <button type="button" class="botao">
			            Voltar
			        </button>
		        </a>

    			<input type="hidden" id="titven" value="<?=$titven?>">
    			<input type="hidden" id="email" value="<?=$email?>">
    			<input type="hidden" id="idsbaixa" value="<?=$idsbaixa?>">
    			<input type="hidden" id="SITEURL" value="<?= _PROTOCOLO_ . _SITEURL_?>">
                        
			</center>
	</body>
</html>

<script type="text/javascript">

jQuery(function() {

	jQuery('#btnEnviarEmail').click(function() {
 		
 		jQuery('#btnEnviarEmail').hide();
 		
 		jQuery('.container-loader').show();
		
		var data = {
		  carta: jQuery("div#carta").html(),
		  titven: jQuery("#titven").val(),
		  email: jQuery("#email").val(),
		  strContratos: '<?=$strContratos?>'
		};

		if(jQuery("#idsbaixa").val() != null && jQuery("#idsbaixa").val() != ''){
			data.idsbaixa = jQuery("#idsbaixa").val();
		}

		jQuery.ajax({
		    type: "POST",
		    url: jQuery('#SITEURL').val()+'fin_rescisao.php?acao=enviarEmail',
		    data: data,
		    dataType: 'json',
		    success: function(response) {
		    	jQuery('.container-loader').hide();

		    	if (response.success == true) {
		    		alert('E-mail enviado com sucesso!');	
		    	} else {
		    		jQuery('#btnEnviarEmail').show();
		    		alert('Falha ao enviar email. Erro: ' + response.message);
		    	}
		    	
		    },
		    error : function (response) {
		    	jQuery('.container-loader').hide();
		    	jQuery('#btnEnviarEmail').show();
		    	alert("Falha ao enviar email. Erro: \n\n" + response.responseText);
		    }
		});
	});
});

</script>