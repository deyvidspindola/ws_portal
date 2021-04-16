<!-- 
	1º View - Reset de Senha
	Formulário de busca do usuário para reset de senha
	Busca por Nome do Usuário e Login
-->

<div align="center">
    <form name="rs_form" id="rs_form" class="form" method="post" action="ti_reset_senha.php" enctype="multipart/form-data">
        <ul class="ul_containner">
            <li class="ul_containner_titulo"><h1>Reset de Senha</h1></li>
            <ul class="ul_content">
                <li class="li_content_titulo"><h2>Pesquisar Usu&aacute;rio</h2></li>
                <li style="width: 5%;padding: 7px;"><p>Nome:</p></li>
                <li style="width: 90%;">
                    <input style="float: left;  margin: 5px;" type="text" id="rs_nm_usuario" class="rs_nm_usuario verifi_campo" name="rs_nm_usuario" value="">
                </li>
            </br>
                <li style="width: 5%;padding: 7px;"><p>Login:</p></li>
                <li style="width: 90%;">
                    <input style="float: left; margin: 5px;" type="text" id="rs_ds_login" class="rs_ds_login verifi_campo" name="rs_ds_login" value="">
                </li>
                <li class="li_content_rodape">
                    <input type="submit" id="bt_pesquisar" class="botao" value="Pesquisar"  style="width:90px; display: block;">
                    <input type="hidden" name="acao" value="P">
                </li>
            </ul>
        </ul>
    </form>
</div>

<?php
if(isset($resultado)){
?>
<div align="center">
    <table class="tableMoldura" width="98%">
    	<?php if($resultado->login == '') { ?>
    		<tr class="tableTitulo">
    			<td><h1 style="font-size: 12px; text-transform: uppercase">Erro ao redefinir senha</h1></td>
    		</tr>
    		<tr>
    		<td colspan="3">
    			<p style="font-size: 12px; text-align: center;" >
    			<?php 
    			print_r ($resultado->mensagem);
    			?>
    			</b></p>	
            </td>
        </tr>
    	<?php } elseif ($resultado->email == 'ErroEnvioEmail') {?>
    	<tr class="tableTitulo">
    			<td><h1 style="font-size: 12px; text-transform: uppercase">Erro ao redefinir senha</h1></td>
    		</tr>
    		<tr>
    		<td colspan="3">
    			<p style="font-size: 12px; text-align: center;" >
    			Não foi possível encaminhar o E-mail para o usuário, segue abaixo informações para acesso ao sistema:
    			<br/><b>Usuario:  <?php print_r ($resultado->login );?></b>
    			<br/><b>Senha:  <?php print_r ($resultado->senha );?></b>
    			<br/><br/>
    			</b></p>	
            </td>
        </tr>
    	<?php } else {?>
        <tr class="tableTitulo">
            <td><h1 style="font-size: 12px; text-transform: uppercase">Senha Resetada com Sucesso</h1></td>
        </tr>
        <tr>
            <td colspan="3">
                <p style="font-size: 12px; text-align: center;" >
                	A senha para o login: <b><?php echo $resultado->login ?></b>,
                	foi resetada com sucesso. </br> 
                	Um email com maiores informacoes foi disparado para a caixa de entrada: <b><?php echo $resultado->email; ?></b></p>	
            </td>
        </tr>
        <?php } ?>
    </table>   
</div>
<?php } ?>