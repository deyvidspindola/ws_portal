<html>
	<head>
		<style type="text/css">
			body{
				text-align: center;
				font-size: 10pt;
				font-family: Arial;
			}	
			
			table{
				text-align: left;
			}		
		</style>
	</head>
	<body>
		<table border="0" width="720">
			<tr>
				<td>
					<img src="<?php echo _PROTOCOLO_ . _SITEURL_?>images/logo_sascar_nova.jpg" width="200"/>
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
			<tr>
				<td>
					&nbsp;
				</td>
				<td align="right">
					Santana do Parnaíba, <?php echo $arrParams['data']['dia'] ?> de <?php echo $arrParams['data']['mes'] ?> de <?php echo $arrParams['data']['ano'] ?>.
				</td>
			</tr>
			<tr>
				<td colspan="2">
					A/C 
					<br />
					Sr(s) <?php echo $arrParams['cliente']['nome_cliente'] ?>. 
					<br /><br />
					Prezado(s) Senhor(es), 
					<br /><br />
										
					A SASCAR informa ter identificado em seu sistema acionamento do botão de pânico, no dia <?php echo $arrParams['acionamento']['data'] ?>, às <?php echo $arrParams['acionamento']['hora_minuto_segundo'] ?>, para o veículo abaixo identificado:
					<br />
					<b>Placa:</b> <?php echo $arrParams['veiculo']['placa'] ?> <br /> 
					<b>Chassi:</b> <?php echo $arrParams['veiculo']['chassi'] ?> <br /> 
					<b>Contrato n º:</b> <?php echo $arrParams['cliente']['contrato'] ?> <br />
					<br /> 
					Após várias tentativas de contato telefônico, sem sucesso, solicitamos a V.Sa(s)  que retorne o contato com a Sascar, nos números abaixo informados. 
					<br /> <br /> 
					Central de Atendimento:<br /> 
					Grandes Centros 4002 6004 <br /> 
					Demais Localidades 0800 648 6004 <br /> 
					Exclusivo Roubo: 0800 648 6003<br /> 
					<br /> 
					Caso, em até 1 (Uma) hora,  a Sascar não receba retorno deste acionamento de pânico, lembramos que poderá ser cobrado o valor de R$20,00 (vinte reais) pelo acionamento indevido.
					<br /> 
					A SASCAR reforça a importância de manter seu cadastro atualizado a fim de garantir efetividade no contato. 
					<br /> 
					Atenciosamente,
					<br /> <br /> 
					
					Sascar  Tecnologia e Segurança Automotiva S.A.
					<br /> 
				</td>
			</tr>
		</tabe>	
	</body>
</html>