<?php

if(!$acao){
	$prpsdt_ultima_acao_inicio_busca = date("d/m/Y");
	$prpsdt_ultima_acao_final_busca   = $prpsdt_ultima_acao_inicio_busca;
} 
    
?>
    
    <br>
    <center>
    <style media="print">
    #filtro_pesquisa{
    	display:none;
    }
    #botoes_geradores{
    	display:none;
    }
    #cabecalho{
    	display:none;
    }
    #bt_pesquisar{
    	display:none;
    }

    
    
    </style>

    <table class="tableMoldura">
        <tr class="tableTitulo">
            <td><h1>Proposta Seguradora</h1></td>
        </tr>
		<?
        if($mensagem !="") {
	        ?>
	        <tr>
	            <td class="msg"><b><?=$mensagem?><b></td>
	        </tr>
	        <?
        }
        abas();
        ?>
							
        <tr>
            <td align="center" valign="top">
                <table class="tableMoldura" id="filtro_pesquisa">
                    <tr class="tableSubTitulo">
                        <td colspan="4"><h2>Dados para Pesquisa</h2></td>
                    </tr>
                    <tr>
                        <td width="10%"><label>Período:</label></td>
                        <td width="40%">
                            <input type="text" id="prpsdt_ultima_acao_inicio_busca" name="prpsdt_ultima_acao_inicio_busca" value="<?php echo $prpsdt_ultima_acao_inicio_busca;?>" size="10" maxlength="10" onKeyUp="formata_dt(this);" onBlur="ValidaDataPC(prpsdt_ultima_acao_inicio_busca);">
                            <img src="images/calendar_cal.gif" align="absmiddle" border="0" alt="Calendário..." onclick="displayCalendar(document.forms[0].prpsdt_ultima_acao_inicio_busca,'dd/mm/yyyy',this)">
                            &nbsp; a &nbsp;
                            <input type="text" id="prpsdt_ultima_acao_final_busca" name="prpsdt_ultima_acao_final_busca" value="<?php echo $prpsdt_ultima_acao_final_busca;?>" size="10" maxlength="10" onKeyUp="formata_dt(this);" onBlur="ValidaDataPC(prpsdt_ultima_acao_final_busca);">
                            <img src="images/calendar_cal.gif" align="absmiddle" border="0" alt="Calendário..." onclick="displayCalendar(document.forms[0].prpsdt_ultima_acao_final_busca,'dd/mm/yyyy',this)">
                        </td>
                        <td width="10%"><label>Grupo:&nbsp;</label></td>
                        <td>
                            <SELECT name="tcgoid_busca" id="tcgoid_busca"  onchange="busca_tipo_contrato()" >
                            <?php
                            $whereContratoGrupos = '';
                            $listaContratoGrupos = array();
                            $listaContratoTipo = false;
                            if ($tipoUsuario->tipo != 'INTERNO') {
                            	preg_match('/^{(.*)}$/', $tipoUsuario->grupos, $matches);
                            	$listaContratoGrupos = explode(',',$matches[1]);
                            	$whereContratoGrupos = " AND tcgoid IN (".implode(',', $listaContratoGrupos).") ";
                            	$listaContratoTipo = true;
                            } else {
                            	?>
                            	<option value=''></option>
                            	<?php 
                            }
                            
                            $sql_tipo_contrato_grupo = "
							SELECT 
								tcgoid,
								tcgdescricao
							FROM 
								tipo_contrato_grupo
							WHERE
								tcgseguradora = true
							AND
								tcgexclusao IS NULL
								$whereContratoGrupos
							";

							$query_tipo_contrato_grupo = pg_query($conn,$sql_tipo_contrato_grupo);
							while($mTipo_contrato_grupo = pg_fetch_array($query_tipo_contrato_grupo))
							{
							?>
								<option value = '<?php echo $mTipo_contrato_grupo['tcgoid'];?>'  <?php if($tcgoid_busca == $mTipo_contrato_grupo['tcgoid']){ echo 'selected';} ?>   ><?php echo $mTipo_contrato_grupo['tcgdescricao'];?></option>
							<?php 
							}											
                            ?>
                            </SELECT>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Nº Proposta:</label></td>
                        <td><input type="text" id="prpsproposta_busca" name="prpsproposta_busca" onkeypress="return numero(event);" onblur="revalidar(this,'@');"  size="15" maxlength="12" value="<?php echo $prpsproposta_busca;?>"></td>
                        <td><label>Tipo de Contrato:</label></td>
                        <td>
                        	<div id='div_tipo_contrato' name='div_tipo_contrato'>
                                <SELECT name="tipo_contrato_busca" id="tipo_contrato_busca" style='width:450'   style='width:450'> 
                                    <option value="">Escolha</option>
                                </SELECT>
                        	</div>   
                        	<?php if ($listaContratoTipo):?>
                        	<script type="text/javascript">
                        		busca_tipo_contrato();
                        	</script>     
                        	<?php endIf;?>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Placa:</label></td>
                        <td><input type="text" id="placa_busca" name="placa_busca" size="9" maxlength="9" value="<?php echo $placa_busca?>"></td>
                        <td><label>Chassi:</label></td>
                        <td><input type="text" id="chassi_busca" name="chassi_busca" size="30" maxlength="17" value="<?php echo $chassi_busca?>"></td>
                    </tr>
                    
                    <tr>
                        <td><label>Status:</label></td>
                        <td>
                            <SELECT id="prpsprpssoid_busca" name="prpsprpssoid_busca">
								<option value=''></option>
							<?php
							$sql_proposta_seguradora_status = "
															SELECT 
                                                                prpssoid,
                                                                prpssdescricao,
                                                                prpsstatus_principal
                                                            FROM 
                                                                proposta_seguradora_status
                                                            WHERE
                                                                (prpsdt_exclusao IS NULL 
                                                                    AND prpsstatus_principal = 'TRUE')
                                                                OR
                                                                (prpssoid = 8) -- ADD ASM 2691
                                                            ORDER BY    
                                                                prpssdescricao 
															";
							$query_proposta_seguradora_status = pg_query($conn,$sql_proposta_seguradora_status);
							while($mProposta_seguradora_status = pg_fetch_array($query_proposta_seguradora_status))
							{
							?>
								<option value='<?php echo $mProposta_seguradora_status['prpssoid'];?>'  <?php if($mProposta_seguradora_status['prpssoid'] == $prpsprpssoid_busca){ echo 'selected';} ?> ><?php echo $mProposta_seguradora_status['prpssdescricao'];?></option>							
							<?php	
							}
							?>	
                            </SELECT>
                            
                        </td>
                        <td><label>Ação:</label></td>
                        <td>
                            <SELECT id="prpsultima_acao_busca" name="prpsultima_acao_busca" style='width:450'>
                                <option value=''></option>
							<?php

							$sql_proposta_seguradora_acao = "
															SELECT 
																prpsaoid,
																prpsadescricao,
																prpsatipo_solic,
																UPPER(prpsaresponsavel) as prpsaresponsavel
															FROM 
																proposta_seguradora_acao
															WHERE
																prpsadt_exclusao IS NULL
															ORDER BY prpsadescricao
															";							
							$query_proposta_seguradora_acao = pg_query($conn,$sql_proposta_seguradora_acao);
							while($mProposta_seguradora_acao = pg_fetch_array($query_proposta_seguradora_acao))
							{
							?>
                                <option value='<?php echo $mProposta_seguradora_acao['prpsaoid'];?>'   <?php if($prpsultima_acao_busca == $mProposta_seguradora_acao['prpsaoid']){ echo 'selected';}  ?>  ><?php echo $mProposta_seguradora_acao['prpsadescricao'];?></option>							
							<?php	
							}											
							?>
                            </SELECT>
                        </td>
                        
                    </tr>
                    <tr>
                        <td><label>Segurado:</label></td>
                        <td>
                        	<input type="text" id="prpssegurado_busca" name="prpssegurado_busca" size="50" maxlength="30" value="<?php echo $prpssegurado_busca?>">
                        </td>
                        <td>	
                        	<label>Motivo:</label>
						</td>
						<td>
							<select id='prpshpsmtoid_busca' name='prpshpsmtoid_busca' >
								<option value=''></option>
								<?php
								$sql_motivo = " SELECT 
													psmtoid, 
													psmtdescricao 
												FROM proposta_seguradora_motivo 
											WHERE 
												psmtdt_exclusao IS NULL
											AND
												psmtmodulo_proposta = TRUE ";
											
		                                                                 if($_SESSION['usuario']['depoid'] == 56){ 
                                                                                $sql_motivo.="AND psmtenvia_seguradora='t' ";

                                                                               }

										$sql_motivo.="ORDER BY psmtdescricao ";
								$query_motivo = pg_query($conn,$sql_motivo);
								while($mMotivo = pg_fetch_array($query_motivo))
								{
								?>
									<option value='<?php echo $mMotivo['psmtoid'];?>'   <?php if($prpshpsmtoid_busca == $mMotivo['psmtoid']){ echo 'SELECTED';}?>   ><?php echo $mMotivo['psmtdescricao'];?></option>
								<?php	
								}
								?>
							</select>
                         </td>
                    </tr>     
					<tr>
						<td colspan='2' >
							<label>
								<input id="prpsdt_ultimo_processamento_busca" type="checkbox" value="1" <?php if($prpsdt_ultimo_processamento_busca == '1'){echo 'CHECKED';}?> name="prpsdt_ultimo_processamento_busca"> Apresentar apenas Processamento Diário
							</label>
						</td>                        
                    </tr>                 
                    <?php if ($_SESSION['funcao']['proposta_seguradora_devolucao'] == 1): ?> 
                    
                    <!-- PLANILHA DE DEVOLUÇÃO -->
                    <tr>
                    	<td colspan="2">
                    		<label>
                    			<input type="checkbox" name="gerar_planilha_devolucao">
                    			Gerar Planilha de Devolu&ccedil;&atilde;o:
                    		</label>
                    	</td>
                    </tr>   
                    <tr>
                        <td colspan="4">&nbsp;</td>
                    </tr>  
                    <tr class="frame_filtro_planilha_devolucao" style="display:none">
                    	<td>
                    		<label>Per&iacute;odo:</label>
                    	</td>
                    	<td width="40%">
                            <input type="text" id="devolucaodt_solicitacao_inicio" name="devolucaodt_solicitacao_inicio" value="<?php echo $prpsdt_ultima_acao_inicio_busca;?>" size="10" maxlength="10" onKeyUp="formata_dt(this);" onBlur="ValidaDataPC(prpsdt_ultima_acao_inicio_busca);">
                            <img src="images/calendar_cal.gif" align="absmiddle" border="0" alt="Calendário..." onclick="displayCalendar(document.forms[0].devolucaodt_solicitacao_inicio,'dd/mm/yyyy',this)">
                            &nbsp; a &nbsp;
                            <input type="text" id="devolucaodt_solicitacao_fim" name="devolucaodt_solicitacao_fim" value="<?php echo $prpsdt_ultima_acao_final_busca;?>" size="10" maxlength="10" onKeyUp="formata_dt(this);" onBlur="ValidaDataPC(prpsdt_ultima_acao_final_busca);">
                            <img src="images/calendar_cal.gif" align="absmiddle" border="0" alt="Calendário..." onclick="displayCalendar(document.forms[0].devolucaodt_solicitacao_fim,'dd/mm/yyyy',this)">
                        </td>
                        <td>
                        	<labe>
                        		Seguradoras:
                        	</labe>
                        </td>
                        <td>
                        	<select id="devolucao_tpcoid" name="devolucao_tpcoid">
                        		<?php foreach ($contratosPlanilhaDevolucao as $indice => $tcg): ?>
                        		<option value="<?php echo $tcg->tcgoid ?>"><?php echo $tcg->tcgdescricao ?></option>
                        		<?php endForEach;?>
                        	</select>
                        </td>
                    </tr>    
                    <tr class="frame_filtro_planilha_devolucao" style="display:none">
                    	<td>
                    		<label>
                    			Nº Contatos:
                    		</label>
                    	</td>
                    	<td>
                    		<input type="text" id="devolucao_numero_contatos" name="devolucao_numero_contatos" size="2" maxlength="2" onkeypress="return numero(event, false, false);" onblur="revalidar(this,'@');" />
                    	</td>
                    </tr>
                    <?php endIf;?>      
                    <tr>
                        <td colspan="4">&nbsp;</td>
                    </tr>                 
                    <tr class="tableRodapeModelo1">
                        <td colspan="4" align="center">
                        	<input type="button" style="display:none" name="bt_gerar_planilha_devolucao" id="bt_gerar_planilha_devolucao" class="botao" value="Gerar Planilha de Devolução" />
                            <input type="button" name="bt_pesquisar" id="bt_pesquisar" class="botao" value="Pesquisar" onclick="javascript:pesquisar();"/>
                            <?php if ($_SESSION['funcao']['proposta_seguradora_edicao']==1) { ?>
                            <input type="button" name="bt_incluir" id="bt_incluir" class="botao" value="Novo" onclick="javascript:nova_proposta();"/>
                            <?php } ?>
                        </td>
                    </tr>

                </table>
                <?php if ($_SESSION['funcao']['proposta_seguradora_devolucao'] == 1): ?> 
                <table id="resultado_pesquisa">
                	<tr id="devolucao_loading" align="center" style="display: none">
                		<td>
                			<img alt="Carregando..." src="images/loading.gif">
                		</td>
                	</tr>
                	<tr id="devolucao_resultado" align="center">
                		<td>
                        	
                       	</td>
                	</tr>
                </table>
                <?php endIf; ?>
                <div class="separador"></div>
                
    <?php   if($num_rows_resultado_pesquisa > 0){ ?>
                <script type="text/javascript">
                    $(window).load(function(){
                        //constroi regra para ordernar Data - padrão brasileiro
                        $.extend( $.fn.dataTableExt.oSort, {
                            "date-br-pre": function ( a ) {
                                if (a == null || a == "") {
                                    return 0;
                                }
                                var brDatea = a.split('/');
                                return (brDatea[2] + brDatea[1] + brDatea[0]) * 1;
                            },
                            "date-br-asc": function ( a, b ) {
                                return ((a < b) ? -1 : ((a > b) ? 1 : 0));
                            },
                            "date-br-desc": function ( a, b ) {
                                return ((a < b) ? 1 : ((a > b) ? -1 : 0));
                            }
                        } );
                        
                        $('#example').DataTable( {
                            "columnDefs": [
                                {
                                    // The `data` parameter refers to the data for the cell (defined by the
                                    // `data` option, which defaults to the column being worked with, in
                                    // this case `data: 0`.
                                    "render": function ( data, type, row ) {
                                        //return data +' ('+ row[3]+')';
                                        return data;
                                    },
                                    "targets": 0
                                },
                                //{ "visible": true,  "targets": [ 3 ] },
                                { "type": "date-br", "targets": [1,7] } //targets define qual coluna irá receber a nova regra de ordernação, começando do 0 (zero)
                            ]
                        } );
                        
                        $('#example_wrapper').attr('style', 'width:99%; margin: 10px 0 10px 0;');   
                        $('#example').show();
                    });
                </script>
                <div class="bloco_conteudo">
                    <table id="example" class="display" cellspacing="0" style="display:none !important;" width="100%">
                        <thead>
                            <tr>
                                <th colspan="12" align="left">Resultado da Pesquisa</th>
                            </tr>
                            <tr>
                                <th colspan="12" align="left">
                                    <img src='images/icones/exclamationYellowTransparente.gif' align="absmiddle" border="0" />&nbsp;Verifica&ccedil;&atilde;o Manual
                                </th>
                            </tr>
                            <tr>
                                <th></th>
                                <th>Solicita&ccedil;&atilde;o</th>
                                <th>Segurado</th>
                                <th>Placa</th>
                                <th>Chassi</th>
                                <th>Proposta</th>
                                <th>Tipo Contrato</th>
                                <th>Data &Uacute;ltima A&ccedil;&atilde;o</th>
                                <th>&Uacute;ltima A&ccedil;&atilde;o</th>
                                <th>Motivo</th>
                                <th>Usu&aacute;rio</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>            
        <?php           for($i = 0; $i < pg_num_rows($resultado_pesquisa); $i++){ ?>
                            <tr>
                                <td>
                        <?php   $manual = pg_fetch_result($resultado_pesquisa,$i,"prpsverificacao_manual");
                                if($manual == 't'){ ?>
                                    <img src='images/icones/exclamationYellowTransparente.gif' align="absmiddle" border="0" />
                        <?php   } ?>
                                </td>
                                <td><?=pg_fetch_result($resultado_pesquisa, $i, "prpsdt_solicitacao");?></td>
                                <td><?=pg_fetch_result($resultado_pesquisa, $i, "prpssegurado");?></td>
                                <td><?=pg_fetch_result($resultado_pesquisa, $i, "veiplaca");?></td>
                                <td><?=pg_fetch_result($resultado_pesquisa, $i, "prpschassi");?></td>
                                <td>
                                    <?if($_SESSION['usuario']['depoid'] != 56){?>
                                        <a href='?acao=editar&id=<?=pg_fetch_result($resultado_pesquisa,$i,"prpsoid");?>'   target="_blank" ><?=pg_fetch_result($resultado_pesquisa,$i,"prpsproposta");?></a>                                               
                                    <?} else {?>
                                        <a href="javascript:abre_historico(<?=pg_fetch_result($resultado_pesquisa,$i,"prpsoid")?>)"><?=pg_fetch_result($resultado_pesquisa,$i,"prpsproposta");?></a>
                                    <?}?>
                                </td>
                                <td><?=pg_fetch_result($resultado_pesquisa,$i,"tpcdescricao");?></td>
                                <td><?=pg_fetch_result($resultado_pesquisa,$i,"prpsdt_ultima_acao");?></td>                            
                                <td><?=pg_fetch_result($resultado_pesquisa,$i,"prpsadescricao");?></td>
                                <td><?=pg_fetch_result($resultado_pesquisa,$i,"psmtdescricao");?></td>                            
                                <td><?=pg_fetch_result($resultado_pesquisa,$i,"ds_login");?></td>                            
                                <td><?=pg_fetch_result($resultado_pesquisa,$i,"prpssdescricao");?></td>
                            </tr>
    <?php               } ?>
    
                        </tbody>
                    </table>
                </div>
                <div class="separador"></div>
    <?      } ?>
            </td>
        </tr>
    </table>
    </center>
