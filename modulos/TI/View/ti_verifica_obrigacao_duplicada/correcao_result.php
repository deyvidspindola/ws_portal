
<div align="center">
    <form name="vod_form" id="vod_form" class="form" method="post" action="ti_verifica_obrigacao_duplicada.php" enctype="multipart/form-data">
        <ul class="ul_containner">
            <li class="ul_containner_titulo"><h1>Verifica Obriga&ccedil;&otilde;es Duplicadas</h1></li>
            <ul class="ul_content">
                <li class="li_content_titulo"><h2>Pesquisar</h2></li>
                <li style="width: 5%;padding: 7px;"><p>Contrato:</p></li>
                <li style="width: 90%;">
                    <input style="float: left;  margin: 5px;" type="text" id="connumero" class="connumero verifi_campo" name="connumero" value="">
                </li>
                <li class="li_content_rodape">
                    <input type="submit" id="pesquisar" class="botao" value="Pesquisar"  style="width:90px; display: block;">
                    <input type="hidden" name="acao" value="P">
                </li>
            </ul>
        </ul>
    </form>
</div>

	<table class="tableMoldura">
		<tr class="tableRodapeModelo1">
			<td colspan="4" align="center">
				<h2><?php echo($resultado); ?></h2>
            </td>
        </tr>
    </table>
