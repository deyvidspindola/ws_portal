<!-- 
	2º View - Reset de Senha
	Resultado da busca de usuário (pesquisa_form.php).
	Seleciona o usuário para o reset de senha
-->

<div align="center">
    <form name="rs_form" id="rs_form" class="form" method="post" action="ti_reset_senha.php" enctype="multipart/form-data">
        <ul class="ul_containner">
            <li class="ul_containner_titulo"><h1>Reset de Senha</h1></li>
            <ul class="ul_content">
                <li class="li_content_titulo"><h2>Pesquisar Usu&aacute;rio</h2></li>
                <li style="width: 5%;padding: 7px;" size="40"><p>Nome:</p></li>
                <li style="width: 88%;">
                    <input style="float: left;  margin: 5px;" type="text" id="rs_nm_usuario" class="rs_nm_usuario verifi_campo" name="rs_nm_usuario" value="">
                </li>
            </br>
                <li style="width: 5%;padding: 7px;"><p>Login:</p></li>
                <li style="width: 88%;">
                    <input style="float: left; margin: 5px;" type="text" id="rs_ds_login" class="rs_ds_login verifi_campo" name="rs_ds_login" value="">    
                </li>
                <li class="li_content_rodape">
                    <input type="submit" id="bt_pesquisar" class="botao" value="Pesquisar"  style="width:90px; display: none;">
                    <input type="hidden" name="acao" value="P">
                </li>
            </ul>
        </ul>
    </form>
</div>

<?php
if(pg_num_rows($resultado) > 0){ ?>
	<div id="" class="" >
		<table width="98%">
			<tbody id="result">
				<tr class="tableTituloColunas">
                    <td>Nome</td>
                    <td>Login</td>
                    <td>Email</td>
                    <td>Externo</td>
                    <td>Ativo</td>
                    <td>Bloqueado</td>
                    <td>Paginas</td>
                    <td>Funcoes</td>
                    <td>Loga AD?</td>
                    <td>Acao</td>
                </tr>
				<?php
				for($i = 0; $i < pg_num_rows($resultado); $i++) {
					$usuario = pg_fetch_array($resultado);
				?>
					<form name="mostra_usuario" id="mostra_usuario" method="post" action="ti_reset_senha.php" enctype="multipart/form-data">
						<tr class="tr_info">
							<td class="td_info" ><?php echo $usuario['nome']; ?></td>
							<td class="td_info" ><?php echo $usuario['login']; ?></td>
							<td class="td_info" ><?php echo $usuario['email']; ?></td>
							<td class="td_info" ><?php echo $usuario['externo']; ?></td>
							<td class="td_info" ><?php echo $usuario['ativo']; ?></td>
							<td class="td_info" ><?php echo $usuario['bloqueado']; ?></td>
							<td class="td_info" ><?php echo $usuario['paginas']; ?></td>
							<td class="td_info" ><?php echo $usuario['funcoes']; ?></td>
							<td class="td_info" ><?php echo $usuario['ad']; ?></td>
							<td class="td_info">
								<?php if($usuario['ad'] == 'N&Atilde;O' && $usuario['ativo'] == 'Ativo'){ ?>
									<button type="submit">Selecionar</button>
									<input type="hidden" name="acao" value="U">
									<input type="hidden" name="id_usuario" value="<?php echo $usuario['codigo']?>">
								<?php }else if($usuario['ad'] == 'SMARTAGENDA') { ?>
									<button type="submit" style="cursor: pointer;">Reset Portal</button>
									<input type="hidden" name="acao" value="U">
									<input type="hidden" name="id_usuario" value="<?php echo $usuario['codigo']?>">
								<?php } else if($usuario['ativo'] == 'Inativo') { ?>
									<button disabled="true" type="submit" style="cursor: pointer;">Selecionar</button>
									<input type="hidden" name="acao" value="U">
									<input type="hidden" name="id_usuario" value="<?php echo $usuario['codigo']?>">
								<?php } else { ?>
									<button disabled="true" type="submit" style="cursor: pointer;">Selecionar</button>
									<input type="hidden" name="acao" value="U">
									<input type="hidden" name="id_usuario" value="<?php echo $usuario['codigo']?>">
								<?php } ?>

							</td>
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
				<h2>Sem registros para a pesquisa:<h1>
					<?php
                    if($_REQUEST['rs_nm_usuario'] != '' && $_REQUEST['rs_ds_login'] != '' ){
                        echo $_REQUEST['rs_nm_usuario'] . ' - ' . $_REQUEST['rs_ds_login'];
                        
                    }else if($_REQUEST['rs_nm_usuario'] != ''){
                        echo $_REQUEST['rs_nm_usuario'];
                        
                    }else{
                        echo $_REQUEST['rs_ds_login'];
                    }
                	?>
                </h1></h2>
            </td>
        </tr>
    </table>
<?php } ?>
