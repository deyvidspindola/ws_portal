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
            <td colspan="4"><h2>Configuração de Fluxos</h2></td>
          </tr>
          <tr height="25">
            <td colspan="4"><span class="msg"><?php echo $vData['action_msg']; ?></span></td>
          </tr>
          <tr>
            <td width="8%" align="left"><label for="reqifoid">Fluxo *:</label></td>
            <td width="20%" align="left"><SELECT id="reqifoid" name="reqifoid" onChange="Javscript:fluxoBtChange();">
              <option value=""> Novo Fluxo </option>
             <?php 
                $vOptions = array();
                $sqlQuery = "SELECT reqifoid, reqifdescricao
                        FROM req_informatica_fluxo 
                        WHERE 
                            reqifdt_exclusao is null 
                        ORDER BY reqifdescricao LIMIT 200;";
                $rs = pg_query($conn, $sqlQuery);
                if(pg_num_rows($rs) > 0) {
                    for($i = 0; $i < pg_num_rows($rs); $i++) {
                        $vOptions = pg_fetch_array($rs);
                    ?>
                  <option value="<?php echo $vOptions['reqifoid']; ?>" <?php if($vData['reqifoid'] == $vOptions['reqifoid']){ echo ' selected="selected"'; } ?>> <?php echo $vOptions['reqifdescricao']; ?> </option>
                <?php 
                    }
                }
                ?>
            </SELECT>
			  </td>
            <td colspan="2" align="left">
			    <table width="100%">
				  <tr>
				    <td align="left" width="15%"><label for="reqifdescricao">Descrição:</label></td>
				    <td align="left" width="85%" ><input type="text" id="reqifdescricao" name="reqifdescricao" value="<?php echo $vData['reqifdescricao']; ?>" size="37" maxlength="50" style="height: 18px;"></td>
				  </tr>
				  <tr>
				    <td align="left"><label for="reqifusuoid_responsavel"> Usuário:</label></td>
				    <td align="left">
						<SELECT id="reqifusuoid_responsavel" name="reqifusuoid_responsavel">
					     <option value=""> Escolha </option>
						  <?php 
							$vOptions = array();
							$sqlQuery = "SELECT DISTINCT cd_usuario, nm_usuario 
								FROM 
									req_informatica_funcao_usuario,
									usuarios 
								WHERE 
									reqifudt_exclusao is null 
									--AND reqifureqifcoid=7
									AND reqifuusuoid = cd_usuario
								ORDER BY nm_usuario 
								LIMIT 1000;
							";
							$rs = pg_query($conn, $sqlQuery);
							if(pg_num_rows($rs) > 0) {
								for($i = 0; $i < pg_num_rows($rs); $i++) {
									$vOptions = pg_fetch_array($rs);
								?>
								<option value="<?php echo $vOptions['cd_usuario']; ?>"  <?php if($vData['reqifusuoid_responsavel'] == $vOptions['cd_usuario']){ echo ' selected="selected"'; } ?>> <?php echo $vOptions['nm_usuario']; ?></option>
								<?php 
								}
							}
						  ?> 
						</SELECT>
					  </td>
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
					$vData['reqifoid'] = (int) $vData['reqifoid'];
					if($vData['reqifoid'] == 0){
						$podeConfirmar = '';
						$podeExcluir = ' style="display:none;"';
					}else{ 
						$podeConfirmar = ' style="display:none;"';
						$podeExcluir = '';
					}
				?>
				  <input type="button" name="fluxo_bt_excluir" id="fluxo_bt_excluir" class="botao" value="Excluir" onclick="javascript:excluirFluxo();" <?php echo $podeExcluir; ?>/>
				  <input type="button" name="fluxo_bt_confirmar" id="fluxo_bt_confirmar" class="botao" value="Confirmar" onclick="javascript:confirmarNovoFluxo();" <?php echo $podeConfirmar; ?>/>
				<?php } ?>
			  </td>
			</tr>
        </table>
        </td>
      </tr>
      <tr>
        <td align="center" valign="top"><table width="98%" class="tableMoldura" align="center">
          <tr class="tableSubTitulo">
            <td colspan="6"><h2>Fases x Funções</h2></td>
          </tr>
			<tr>
			  <td colspan="6" align="center" height="36">&nbsp;
			  </td>
			</tr>
         <tr>
            <td width="8%" align="left"><label for="reqiffreqifsoid">Fase *:</label></td>
            <td width="25%" align="left"><SELECT id="reqiffreqifsoid" name="reqiffreqifsoid">
              <option value=""> Escolha </option>
             <?php 
                $vOptions = array();
                $sqlQuery = "SELECT reqifsoid, reqifsdescricao
							 FROM req_informatica_fase 
							 WHERE reqifsdt_exclusao is null 
							 ORDER BY reqifsdescricao
							 LIMIT 1000";

                $rs = pg_query($conn, $sqlQuery);
                if(pg_num_rows($rs) > 0) {
                    for($i = 0; $i < pg_num_rows($rs); $i++) {
                        $vOptions = pg_fetch_array($rs);
                    ?>
                  <option value="<?php echo $vOptions['reqifsoid']; ?>" <?php if($vData['reqiffreqifsoid'] == $vOptions['reqifsoid']){ echo ' selected="selected"'; } ?>> <?php echo $vOptions['reqifsdescricao']; ?> </option>
                <?php 
                    }
                }
                ?>
            </SELECT>
			  </td>
 			  <td align="left"><label for="reqiffordem">N° Ordem:</label></td>
			  <td align="left"><input type="text" id="reqiffordem" name="reqiffordem" value="<?php echo $vData['reqiffordem']; ?>" size="5" maxlength="5" style="height: 18px;"></td>
			  <td align="left"><label for="reqiffreqifcoid"> Função:</label></td>
			  <td align="left"><SELECT id="reqiffreqifcoid" name="reqiffreqifcoid">
					<option value=""> Escolha </option>
					<?php 
					$vOptions = array();
					$sqlQuery = "SELECT reqifcoid, reqifcdescricao
								 FROM req_informatica_funcao 
								 WHERE reqifcdt_exclusao is null 
								 ORDER BY reqifcdescricao
								 LIMIT 1000;";
					$rs = pg_query($conn, $sqlQuery);
					if(pg_num_rows($rs) > 0) {
						for($i = 0; $i < pg_num_rows($rs); $i++) {
							$vOptions = pg_fetch_array($rs);
							?>
								<option value="<?php echo $vOptions['reqifcoid']; ?>"  <?php if($vData['reqiffreqifcoid'] == $vOptions['reqifcoid']){ echo ' selected="selected"'; } ?>> <?php echo $vOptions['reqifcdescricao']; ?></option>
					<?php 
						}
					}
					?> 
					</SELECT>
				</td>
          </tr>
			<tr>
			  <td colspan="6" align="center" height="36"> &nbsp;
			  </td>
			</tr>
			<tr>
			  <td colspan="6" align="center"  class="tableRodapeModelo2">
				<?php if($_SESSION['funcao']['sti_edicao'] == 1){	?>
				  <input type="button" name="fase_funcao_bt_adicionar" id="fase_funcao_bt_adicionar" class="botao" value="Adicionar" onclick="javascript:adicionarFaseFuncao();"/>
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
				$vData['reqifoid'] = (int) $vData['reqifoid'];
				if($vData['reqifoid'] > 0){
				?>
				<tr class="tableTituloColunas">
					<td width="55%"><h3>Fase</h3></td>
					<td width="20%"><h3>Ordem</h3></td>
					<td width="20%"><h3>Função</h3></td>
					<?php if($_SESSION['funcao']['sti_edicao'] == 1){ ?>
					<td width="5%"><h3>Excluir</h3></td>
					<?php } ?>
				</tr>
				<?php 
				$vOptions = array();
				$sqlQuery = " 
					SELECT 
					   reqiffoid as id,
					   reqifsdescricao as fase,
					   reqiffordem ordem,
					   reqifcdescricao as funcao 
					FROM
					   req_informatica_fluxo_fase,
					   req_informatica_fase,
					   req_informatica_funcao
					WHERE 
					   reqiffreqifsoid= reqifsoid
					   and reqiffreqifcoid = reqifcoid
					   and reqiffdt_exclusao is null
					   and reqiffreqifoid=" . $vData['reqifoid'];
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
				  <td><?php echo $vOptions['fase']; ?></td>
				  <td><?php echo $vOptions['ordem']; ?></td>
				  <td><?php echo $vOptions['funcao']; ?></td>
				  <?php if($fPodeAltExc){ ?>
				  <td align="center"><a href="JavaScript:;" onclick="Javascript:excluirFaseFuncao(<?php echo $vOptions['id']; ?>);"><img src="<?php echo $imgX; ?>"></a></td>
				  <?php } ?>
				</tr>
				<?php }
				}else{
				?>
				<tr>
				 <td colspan="5"><span class="msg">Não há fluxos planejados </span></td>
				</tr>
				<?php 
					}
				}
				?>
			</table>
			<input type="hidden" name="reqiffoid" id="reqiffoid" value="">
        </td>
      </tr>
    </table>
  </form>
</div>
