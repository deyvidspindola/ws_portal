<html>
    <body>
		<div align="center">
	    	<table border=1>
	    		<tr>
					<td align="center"><h3>Equipamento</h3></td>
					<td align="center"><h3>Marca</h3></td>
					<td align="center"><h3>Modelo</h3></td>
					<td align="center"><h3>Cód. Identifica&ccedil;&atilde;o</h3></td>
					<td align="center"><h3>Identifica&ccedil;&atilde;o</h3></td>
					<td align="center"><h3>&Uacute;ltima Verifica&ccedil;&atilde;o / Calibra&ccedil;&atilde;o</h3></td>
					<td align="center"><h3>Pr&oacute;xima Verifica&ccedil;&atilde;o / Calibra&ccedil;&atilde;o</h3></td>
					<td align="center"><h3>Equipamento</h3></td>
					<td align="center"><h3>Status</h3></td>
				</tr>
				<?php
				$count = 0;
				for ($i = 0; $i < count($lista_equipamentos);$i++){
					if( $lista_equipamentos[$i]['mqhproxima'] == "C") $status="bom";
						elseif( $this->date_diff_($lista_equipamentos[$i]['mqhproxima'],date("d/m/y"),false) >= 0 ) $status = "APROVADO";
						else $status="ATRASADO";
					if($lista_equipamentos[$i]['maqstatus'] == "A") $status_eqpto="APROVADO";
						elseif($lista_equipamentos[$i]['maqstatus'] == "B") $status_eqpto="BAIXA PATRIMÔNIO";
						else $status_eqpto="NÃO APROVADO";
					$split_maqidentcodigo_1 = substr($lista_equipamentos[$i]['maqidentcodigo'],0,1);
					$split_maqidentcodigo_2 = substr($lista_equipamentos[$i]['maqidentcodigo'],1,3);
					$split_maqidentcodigo_3 = substr($lista_equipamentos[$i]['maqidentcodigo'],4,3);
					$resultado_maqidentcodigo = $split_maqidentcodigo_1.".".$split_maqidentcodigo_2.".".$split_maqidentcodigo_3;
				?>
				<tr>	
					<td><?=$lista_equipamentos[$i]['maqdescricao'];?></td>
					<td><?=$lista_equipamentos[$i]['mqmadescricao'];?></td>
					<td><?=$lista_equipamentos[$i]['mqmodescricao'];?></td>
					<td align="center"><?=$resultado_maqidentcodigo;?></td>
					<td><?=$lista_equipamentos[$i]['maqidentificacao'];?></td>
					<td align="center"><?=$lista_equipamentos[$i]['mqhultima'];?></td>
					<td align="center"><?=$lista_equipamentos[$i]['mqhproxima'];?></td>
					<td align="center"><?=$status_eqpto;?></td>
					<td align="center"><?=$status;?></td>
				</tr>
				<?php
					$count++;
				}
				?>
				<tr>	
					<td colspan='9'><b>Foram encontrados <?=$count;?> registros.</b></td>
				</tr>
			</table>
		</div>
	</body>
</html>