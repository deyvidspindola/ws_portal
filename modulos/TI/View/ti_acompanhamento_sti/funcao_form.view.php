<?php
/**
 * Módulo Principal
 *
 * SASCAR (http://www.sascar.com.br/)
 *
 * @author Jorge A. D. Kautzmann <jorge.kautzmann@sascar.com.br>
 * @description    View da página de detalhes
 * @version 10/03/2008 [0.0.1]
 * @package SASCAR Intranet
 */
// Conexão para montar listas COMBO BOX
global $conn;
// Dados do Formulário
?>
<div align="center">
  <form name="solstiform" id="solstiform" method="post" action="ti_acompanhamento_sti.php" enctype="multipart/form-data">
    <input type="hidden" name="acao" id="form_acao" value="">
    <input type="hidden" name="origem_req" id="origem_req" value="">
    <table class="tableMoldura" width="98%">
      <tr class="tableTitulo">
        <td><h1>STI - Solicitação de TI</h1></td>
      </tr>
      <?php
        // inclui abas
        require 'modulos/TI/View/ti_acompanhamento_sti/abas.view.php';
        ?>
      <tr>
        <td align="center" valign="top"><table width="98%" class="tableMoldura" align="center">
          <tr class="tableSubTitulo">
            <td colspan="4"><h2>Configuração de Funções</h2></td>
          </tr>
          <tr height="25">
            <td colspan="4"><span class="msg"><?php echo $vData['action_msg']; ?></span></td>
          </tr>
          <tr>
            <td width="8%" align="left"><label for="reqifcoid">Função *:</label></td>
            <td width="20%" align="left"><SELECT id="reqifcoid" name="reqifcoid" onChange="Javscript:funcaoBtChange();">
              <option value=""> Nova Função </option>
             <?php 
                $vOptions = array();
                $sqlQuery = "SELECT reqifcoid, reqifcdescricao
                    FROM req_informatica_funcao 
                    WHERE 
                        reqifcdt_exclusao is null 
                    ORDER BY reqifcdescricao LIMIT 500;
				";
                $rs = pg_query($conn, $sqlQuery);
                if(pg_num_rows($rs) > 0) {
                    for($i = 0; $i < pg_num_rows($rs); $i++) {
                        $vOptions = pg_fetch_array($rs);
                    ?>
                  <option value="<?php echo $vOptions['reqifcoid']; ?>" <?php if($vData['reqifcoid'] == $vOptions['reqifcoid']){ echo ' selected="selected"'; } ?>> <?php echo $vOptions['reqifcdescricao']; ?> </option>
                <?php 
                    }
                }
                ?>
            </SELECT>
			  </td>
            <td colspan="2" align="left">
			    <table width="100%">
				  <tr>
				    <td align="left" width="10%"><label for="reqifcdescricao">Descrição:</label></td>
				    <td align="left" width="85%" ><input type="text" id="reqifcdescricao" name="reqifcdescricao" value="<?php echo $vData['reqifcdescricao']; ?>" size="37" maxlength="50" style="height: 18px;"></td>
				  </tr>
			    </table>
			  </td>
          </tr>
			<tr>
			  <td colspan="4" align="left" height="36"> &nbsp; </td>
			</tr>
			<tr>
			  <td colspan="4" align="center"  class="tableRodapeModelo1">
				<?php if($_SESSION['funcao']['sti_edicao'] == 1){
					$vData['reqifcoid'] = (int) $vData['reqifcoid'];
					if($vData['reqifcoid'] == 0){
						$podeConfirmar = '';
						$podeExcluir = ' style="display:none;"';
					}else{ 
						$podeConfirmar = ' style="display:none;"';
						$podeExcluir = '';
					}
				?>
				  <input type="button" name="funcao_bt_excluir" id="funcao_bt_excluir" class="botao" value="Excluir" onclick="javascript:excluirFuncao();" <?php echo $podeExcluir; ?>/>
				  <input type="button" name="funcao_bt_confirmar" id="funcao_bt_confirmar" class="botao" value="Confirmar" onclick="javascript:confirmarNovaFuncao();" <?php echo $podeConfirmar; ?>/>
				<?php } ?>
			  </td>
			</tr>
        </table>
        </td>
      </tr>
      <tr>
        <td align="center" valign="top"><table width="98%" class="tableMoldura" align="center">
          <tr class="tableSubTitulo">
            <td colspan="4"><h2>Funções x Usuários</h2></td>
          </tr>
			<tr>
			  <td colspan="4" align="center" height="36">&nbsp;
			  </td>
			</tr>
         <tr>
            <td width="8%" align="left"><label for="reqifuusuoid">Usuário *:</label></td>
            <td width="30%" align="left"><SELECT id="reqifuusuoid" name="reqifuusuoid">
              <option value=""> Escolha </option>
             <?php 
                $vOptions = array();

                $departamento = "";
                $sqlQuery = "SELECT
                				sissti_departamento_usuario
                			FROM
                				sistema";

                $rs = pg_query($conn, $sqlQuery);
                if(pg_num_rows($rs) > 0)
                {
                		for($i = 0; $i < pg_num_rows($rs); $i++)
                		{
                			$departamento = 
                				pg_num_rows($rs) > 1 && $i < pg_num_rows($rs) - 1 ?
                				$departamento . (string)pg_fetch_result($rs, $i, "sissti_departamento_usuario") . "," :
                				$departamento . (string)pg_fetch_result($rs, $i, "sissti_departamento_usuario");
                		}
                }
                
                $sqlQuery = "SELECT
			                	cd_usuario,
			                	UPPER(NM_USUARIO) as nm_usuario
			                FROM
			                	USUARIOS
			                WHERE
			                	DT_EXCLUSAO IS NULL
			                	AND USUDEPOID IN ($departamento )
			                ORDER BY
			                	NM_USUARIO";

                $rs = pg_query($conn, $sqlQuery);
                if(pg_num_rows($rs) > 0) {
                    for($i = 0; $i < pg_num_rows($rs); $i++) {
                        $vOptions = pg_fetch_array($rs);
                    ?>
                  <option value="<?php echo $vOptions['cd_usuario']; ?>" <?php if($vData['reqifuusuoid'] == $vOptions['cd_usuario']){ echo ' selected="selected"'; } ?>> <?php echo $vOptions['nm_usuario']; ?> </option>
                <?php 
                    }
                }
                ?>
            </SELECT>
			  </td>
			  <td align="left" width="10%"><label for="reqifureqieoid"> Empresa *:</label></td>
			  <td align="left"><SELECT id="reqifureqieoid" name="reqifureqieoid">
					<option value=""> Escolha </option>
					<?php 
					$vOptions = array();
					$sqlQuery = "SELECT 
								   reqieoid, 
								   reqiedescricao 
								FROM 
								   req_informatica_empresa 
								WHERE 
								   reqiedt_exclusao is null 
								ORDER BY  reqiedescricao 
								LIMIT 1000;";
					$rs = pg_query($conn, $sqlQuery);
					if(pg_num_rows($rs) > 0) {
						for($i = 0; $i < pg_num_rows($rs); $i++) {
							$vOptions = pg_fetch_array($rs);
							?>
								<option value="<?php echo $vOptions['reqieoid']; ?>"  <?php if($vData['reqifureqieoid'] == $vOptions['reqieoid']){ echo ' selected="selected"'; } ?>> <?php echo $vOptions['reqiedescricao']; ?></option>
					<?php 
						}
					}
					?> 
					</SELECT>
				</td>
          </tr>
			<tr>
			  <td colspan="4" align="center" height="36"> &nbsp;
			  </td>
			</tr>
			<tr>
			  <td colspan="4" align="center"  class="tableRodapeModelo2">
				<?php if($_SESSION['funcao']['sti_edicao'] == 1){	?>
				  <input type="button" name="fase_funcao_bt_adicionar" id="fase_funcao_bt_adicionar" class="botao" value="Adicionar" onclick="javascript:adicionarFuncaoUsuario();"/>
				<?php } ?>
			  </td>
			</tr>
        </table>
        </td>
      </tr>
      <tr height="22">
        <td>&nbsp;&nbsp;&nbsp;&nbsp;Campos assinalados com (*) são de preenchimento obrigatório</td>
      </tr>
      <tr>
        <td align="center" valign="top">
			<table class="tableMoldura">
				<?php
				$vData['reqifcoid'] = (int) $vData['reqifcoid'];
				if($vData['reqifcoid'] > 0){
				?>
				<tr class="tableTituloColunas">
					<td width="50%"><h3>Usuário</h3></td>
					<td width="45%"><h3>Empresa</h3></td>
					<?php if($_SESSION['funcao']['sti_edicao'] == 1){ ?>
					<td width="5%"><h3>Excluir</h3></td>
					<?php } ?>
				</tr>
				<?php 
				$vOptions = array();
				$sqlQuery = " 
					SELECT 
					   reqifuoid as id,
					   nm_usuario as usuario,
					   reqiedescricao empresa
					FROM
					   req_informatica_funcao_usuario,
					   usuarios,
					   req_informatica_empresa
					WHERE 
					   cd_usuario = reqifuusuoid
					   and dt_exclusao is null
					   and reqieoid = reqifureqieoid
					   and reqifudt_exclusao is null
					   and reqifureqifcoid=" . $vData['reqifcoid'] . 
					"ORDER BY
						nm_usuario";
  				$rs = pg_query($conn, $sqlQuery);
								
				$classZebra = 'tde';
				$fPodeAltExc = true;
				if(pg_num_rows($rs) > 0) {
					for($i = 0; $i < pg_num_rows($rs); $i++) {
						$vOptions = pg_fetch_array($rs);
						$classZebra  = ($classZebra == 'tdc') ? 'tde' : 'tdc';
						$fPodeAltExc = ((strlen($vOptions['inicio_exec']) == 0) && ($_SESSION['funcao']['sti_edicao'] == 1)) ? true : false;
						$imgX = ($imgX == 'images/icones/t1/x.jpg') ? 'images/icones/tf1/x.jpg' : 'images/icones/t1/x.jpg';

				?>
				<tr class="<?php echo $classZebra; ?>">
				  <td><?php echo $vOptions['usuario']; ?></td>
				  <td><?php echo $vOptions['empresa']; ?></td>
				  <?php if($fPodeAltExc){ ?>
				  <td align="center"><a href="JavaScript:;" onclick="Javascript:excluirFuncaoUsuario(<?php echo $vOptions['id']; ?>);"><img src="<?php echo $imgX; ?>"></a></td>
				  <?php } ?>
				</tr>
				<?php }
				}else{
				?>
				<tr>
				 <td colspan="5"><span class="msg">Não há funções planejadas </span></td>
				</tr>
				<?php 
					}
				}
				?>
			</table>
			<input type="hidden" name="reqifuoid" id="reqifuoid" value="">
        </td>
      </tr>
    </table>
  </form>
</div>
