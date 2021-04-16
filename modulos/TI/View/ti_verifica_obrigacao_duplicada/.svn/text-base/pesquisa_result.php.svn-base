
<div align="center">
    <form name="vod_form" id="vod_form" class="form" method="post" action="ti_verifica_obrigacao_duplicada.php" enctype="multipart/form-data">
        <ul class="ul_containner">
            <li class="ul_containner_titulo"><h1>Verifica Obriga&ccedil;&otilde;es Duplicadas</h1></li>
            <ul class="ul_content">
                <li class="li_content_titulo"><h2>Pesquisar</h2></li>
                <li style="width: 5%;padding: 7px;"><p>Contrato:</p></li>
                <li style="width: 90%;">
                    <input style="float: left;  margin: 5px;" type="text" id="connumero" class="connumero verifi_campo" name="connumero" value="<?php echo($_POST['connumero']); ?>">
                </li>
                <li class="li_content_rodape">
                    <input type="submit" id="pesquisar" class="botao" value="Pesquisar"  style="width:90px; display: block;">
                    <input type="hidden" name="acao" value="P">
                </li>
            </ul>
        </ul>
    </form>
</div>

<?php
if(pg_num_rows($resultado) > 0){ ?>
	<div id="" class="" >
        <div align="center">
            <form name="vod_form" id="vod_form" class="form" method="post" action="ti_verifica_obrigacao_duplicada.php" enctype="multipart/form-data">
                <ul class="ul_content">
                        <li style="width: 90%;">
                            <center> Total de registros: <?php echo(pg_num_rows($resultado)); ?> <br /> <input type="submit" id="corrigir" class="botao" value="Corrigir"  style="width:90px; display: block;"> </center>
			     	<input type="hidden" name="connumero" value="<?php echo($_POST['connumero']); ?>">
				<input type="hidden" name="acao" value="C">
                        </li>
                    </ul>
                </ul>
            </form>
        </div>
		<table width="98%">
			<tbody id="result">
				<tr class="tableTituloColunas">
                    <td>Ultima Obrigacao</td>
					<td>Contrato</td>
                    <td>Obroid</td>
		    <td>Obrigacao</td>
                    <td>ID Contrato Servico</td>
                    <td>Situacao</td>
                    <td>Data Validade</td>
                    <td>Data Habilitacao</td>
		 </tr>
				<?php
				for($i = 0; $i < pg_num_rows($resultado); $i++) {
					$result = pg_fetch_array($resultado);
				?>
					<form name="mostra_usuario" id="mostra_usuario" method="post" action="ti_reset_senha.php" enctype="multipart/form-data">
						<tr class="tr_info">
							<td class="td_info" ><?php echo $result['max']; ?></td>
							<td class="td_info" ><?php echo $result['consconoid']; ?></td>
							<td class="td_info" ><?php echo $result['consobroid']; ?></td>
							<td class="td_info" ><?php echo $result['obrobrigacao']; ?></td>
							<td class="td_info" ><?php echo $result['consoid']; ?></td>
							<td class="td_info" ><?php echo $result['conssituacao']; ?></td>
							<td class="td_info" ><?php echo $result['consdt_validade']; ?></td>
							<td class="td_info" ><?php echo $result['consinstalacao']; ?></td>
						</tr>
					</form>
				<?php
				}
				?>
            </tbody>
        </table>
	</div>
<?php }else{ ?>
	<table class="tableMoldura">
		<tr class="tableRodapeModelo1">
			<td colspan="4" align="center">
				<h2>Sem registros para a pesquisa</h2>
            </td>
        </tr>
    </table>
<?php } ?>
