<!-- 
	3º View - Reset de Senha
	Resultado do usuário selecionado na view pesquisa_result.php
	Possui o campo email em aperto para atualização e o botão que reseta a senha de fato.
-->
<div id="mesnagem"></div>
<div align="center">
	<form name="rs_usu_form" id="rs_usu_form" class="form" method="post"
		action="ti_reset_senha.php" enctype="multipart/form-data">
		
		<table class="tableMoldura" width="98%">
			<tr class="tableTitulo">
				<td><h1>Reset de Senha</h1></td>
			</tr>
			<td align="center"><br />

				<table class="tableMoldura">
					<tr class="tableSubTitulo">
						<td colspan="4"><h2>Dados do Usuário</h2></td>
					</tr>
					<tr>
						<td><label>Nome: </label></td>
						<td width="85%">
							<input type="text" name="nm_usuario" id="nm_usuario"  size="40" value = "<?php echo $usuario['nome'];?>" disabled="disabled">
							<input id="" type="hidden" name="codigo" value="<?php echo $usuario['codigo'];?>" >
						</td>
					</tr>
					<tr>
						<td><label>E-Mail : </label></td>
						<td width="85%">
							<input type="text" id="usu_email" class="usu_email" name="usu_email" size="40" value = "<?php echo $usuario['email'];?>">
						</td>
					</tr>
					<tr>
						<td><label>Acesso Externo : </label></td>
						<td width="85%">
							<input type="text" name="usuacesso_externo" id="usuacesso_externo"  size="15" value = "<?php echo $usuario['externo'];?>" disabled="disabled">
						</td>
					</tr>
					<tr>
						<td><label>Ativo : </label></td>
						<td width="85%">
							<input type="text" name="usuativo" id="usuativo"  size="15" value = "<?php echo $usuario['ativo'];?>" disabled="disabled">
						</td>
					</tr>
					<tr>
						<td><label>Bloqueado : </label></td>
						<td width="85%">
							<input type="text" name="usubloqueado" id="usubloqueado"  size="15" value = "<?php echo $usuario['bloqueado'];?>" disabled="disabled">
						</td>
					</tr>
					<tr>
						<td><label>Loga por AD? : </label></td>
						<td width="85%">
							<input type="text" name="ususeqloginad" id="ususeqloginad"  size="15" value = "<?php echo $usuario['ad'];?>" disabled="disabled">
						</td>
					</tr>
				</table>

					<tr class="tableRodapeModelo1">
						<td colspan="4" align="center">
							<input type="submit" id="bt_pesquisar" class="botao" value="Resetar" style="width: 90px; margin: 0 auto;"> 
						<input type="hidden"name="acao" value="R"></td>
					</tr>
				</table></td>
		</table>
	</form>
</div>