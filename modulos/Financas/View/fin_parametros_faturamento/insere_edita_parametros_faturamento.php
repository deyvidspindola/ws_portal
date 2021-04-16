<?php 
cabecalho();
include("calendar/calendar.js");
include("lib/funcoes.js");
include("lib/funcoes.php");
// echo "<pre>".print_r($_POST, 1)."</pre>";
?>
<head>

     <!-- CSS -->
    <link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css" />
    <link type="text/css" rel="stylesheet" href="calendar/calendar.css"/>
    <link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.css" />
  
  	<style type="text/css">
		.disabled {
			font-weight: bold;
			color: silver !important;
			background-color: #efefef;
		}
     </style>
  
    <!-- JAVASCRIPT -->
    <script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.maskedinput.js"></script>
    <script type="text/javascript" src="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.js"></script>
    <script type="text/javascript" src="lib/layout/1.1.0/bootstrap.js"></script>
    <script type="text/javascript" src="includes/js/validacoes.js"></script>    
    <script type="text/javascript" src="includes/js/calendar.js"></script>
    <script type="text/javascript" src="modulos/web/js/fin_parametros_faturamento_insere_edita.js?rand=<?=rand(1, 9999);?>"></script>   
    <script type="text/javascript" src="modulos/web/js/lib/jquery.maskMoney.js"></script> 
    <script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js"></script> 

</head>


<body>

    <div class="modulo_titulo">Parâmetros do Faturamento</div>
    
        <div class="modulo_conteudo">
        
            <?php if(!empty($mensagemInformativaNaoIncluido)){
                foreach ($mensagemInformativaNaoIncluido as $msg){
                    echo '<div id="mensagem" class="mensagem alerta"> '. $msg . '</div>';
                }
            }
            ?>
            
        <?php if (!empty($mensagemInformativa) ): 
        
                    $class_msg =  $mensagemInformativa['status'] === "OK" ? 'mensagem sucesso' : 'mensagem alerta' ;  ?>

         	<div id="mensagem" class="<?php echo $class_msg;?>"><?php echo $mensagemInformativa['msg']; ?></div>
        
		<?php else :?>
           
            <div id="mensagem"></div>
            
       <?php endif;?>    
        
<!--             <div class="mensagem info">(*) Campos de preenchimento obrigatório.</div>  -->
            
            <div class="bloco_titulo">Inserir Parâmetros do Faturamento</div>
      
                <div class="bloco_conteudo">
                            
                     <div class="formulario">
                            
                           <form name="frm" id="frm" method="post" action="" enctype="multipart/form-data"> 
                            
                            <input type="hidden" name="acao" id="acao" />
                            
                            <!-- Caso o parfoid seja diferente de vazio, o parâmetro será atualizado, senão, o parâmetro será inserido.-->
							<input type="hidden" id="parfoid" name="parfoid" value="<?php echo $_POST['parfoid']; ?>" />
							
                            <fieldset style="width:1000px; background: none; border: 1px solid silver; padding: 6px; margin-bottom: 10px">
					         	<legend>Nível *</legend>		
						         	                                    
								<label>
									<input <?php echo (isset($_POST['parfoid']) &&  !empty($_POST['parfoid'])) ? 'disabled="disabled"':'' ; ?> type="radio" name="nivel"  value="1"  class="radio" <?php echo $_POST['nivel'] == '1'  || !isset($_POST['nivel']) ? 'checked="checked"' : '' ; ?> />                                  
										Contrato 
								</label>

                                <label>
                                    <input <?php echo (isset($_POST['parfoid']) &&  !empty($_POST['parfoid'])) ? 'disabled="disabled"':'' ; ?> " type="radio" name="nivel"  value="2" class="radio" <?php echo ($_POST['nivel'] == '2') ? 'checked="checked"' : '' ; ?> />
                                    Cliente
                                </label>

								<label>
								    <input <?php echo (isset($_POST['parfoid']) &&  !empty($_POST['parfoid'])) ? 'disabled="disabled"':'' ; ?>  type="radio" name="nivel"  value="3" class="radio" <?php echo ($_POST['nivel'] == '3') ? 'checked="checked"' : '' ; ?> />
								     Tipo Contrato
								</label>
							</fieldset>
                            
                             <div class="clear"></div>
                            
                            <div class="campo menor dados_contrato" style="<?php echo $_POST['nivel'] != '1' && !empty($_POST['parfoid']) ? 'display:none;' : 'display:block;' ?>  " >
							    <label for="num_contrato">Contrato </label> 
							    <?php if(empty($_POST['parfoid'])) : ?>
										<input id="contrato" class="campo" name="contrato" size="12" <?php echo (isset($_POST['nivel']) && $_POST['nivel'] != '1') ? 'disabled="disabled"' : ''; ?> value="<?php echo $_POST['contrato']; ?>" />
								<?php else : ?>
										<input type="hidden" class="campo" id="contrato" name="contrato" value="<?php echo $_POST['contrato']; ?>" />
										<input class="campo" id="mostra_contrato" name="mostra_contrato" size="12" disabled="disabled" value="<?php echo $_POST['contrato']; ?>" />
								<?php endif; ?>
						   </div>  
             
                           <div class="clear"></div>
                            
                          <?php if(empty($_POST['parfoid'])) : ?>
		
								 <div class="dados_cliente" style="display:none; ">
									 <?php $this->comp_cliente->render() ?>
	                            	 <div class="clear"></div>
								 </div>
							 
							 <?php else : ?>
								
								<div class="dados_cliente" style=" <?php echo (isset($_POST['nivel']) && ($_POST['nivel'] != '2' ) || !isset($_POST['nivel'])) ? 'display:none;' : '' ?> ">	
									 <div class="campo maior">
										<label for="nome_cliente">Nome do Cliente</label>
									
										<input type="hidden" id="clioid" name="clioid" value="<?php echo $_POST['clioid']; ?>" />
										<input class="campo disabled"  <?php echo (isset($_POST['nivel']) && ($_POST['nivel'] != '2' ) || !isset($_POST['nivel'])) ? 'disabled="disabled"' : '' ?> type="text" id="nome_cliente" name="nome_cliente" size="50" maxlength="50" value="<?php echo $_POST['nome_cliente']; ?>" />
		                             </div>
		                       
		                            <div>
			                            <fieldset style="width:180px; background: none; border: 1px solid silver;float:left; margin-top: 5px; margin-bottom: 5px;">
											<legend>Tipo Pessoa</legend>
											  <label>
												<input type="radio" name="tipo_pessoa"  value="F" <?php echo (isset($_POST['tipo_pessoa_literal']) && $_POST['tipo_pessoa_literal'] == 'F' ? 'checked="checked"' : '') ;?>   <?php echo (isset($_POST['nivel']) && ($_POST['nivel'] != '2') || isset($_POST['parfoid']) || !isset($_POST['nivel'])) ? 'disabled ' : '' ?> />                                  
													Física
											  </label>
											  <label>
												  <input type="radio" name="tipo_pessoa"  value="J" <?php echo (!isset($_POST['tipo_pessoa_literal']) || $_POST['tipo_pessoa_literal'] == 'J') ? 'checked="checked"' : '' ;?>  <?php echo (isset($_POST['nivel']) && ($_POST['nivel'] != '2') || isset($_POST['parfoid']) || !isset($_POST['nivel'])) ? 'disabled ' : '' ?> />                                  
												  Jurídica
											  </label>
									     </fieldset>
									</div>			
		
								     <div class="campo medio">
										<input type="hidden" name="tipo_pessoa_literal"  value="<?php echo isset($_POST['tipo_pessoa_literal']) ? $_POST['tipo_pessoa_literal'] : 'J'; ?>" />
										 <!-- Campo CPF/CNPJ -->
										 <label for="cpf_cnpj">
										   	<?php if ($_POST['tipo_pessoa'] == 'F'):?>
										   		CPF
										  	<?php else:?>
										   		CNPJ
										   	<?php endif;?>
										  </label>
									    	<input class="campo disabled" type="text" id="cpf_cnpj" name="cpf_cnpj" value="<?php echo empty($_POST['cpf_cnpj_cliente']) ? $_POST['cpf_cnpj'] : formata_cgc_cpf($_POST['cpf_cnpj_cliente']); ?>" <?php echo (isset($_POST['nivel']) && ($_POST['nivel'] != '2') || isset($_POST['parfoid']) || !isset($_POST['nivel']) ) ? 'disabled="disabled"' : '' ?> />
											<input type="hidden" id="cpf_cnpj_cliente" name="cpf_cnpj_cliente" value="<?php echo $_POST['cpf_cnpj_cliente'] ;?>"/>
									 
									 </div>
								 </div>
							 
							 <?php endif; ?>
                            
                            <div class="clear"></div>
                             
                            <div id="resultado_relatorio" style="display: none"></div>
                            
                            <div id="resultado_progress" align="center" style="display:none">
								<img src="modulos/web/images/loading.gif" alt="Carregando..." />
							</div>
                            
                            <div class="clear"></div>
	                       
	                        <div class="campo medio dados_tipo_contrato" style=" <?php echo (isset($_POST['nivel']) && ($_POST['nivel'] != '3' ) || !isset($_POST['nivel'])) ? 'display:none;' : '' ?> ">
								<label for="tipo_contrato">Tipo de Contrato </label>
								
								<?php if(empty($_POST['parfoid'])) : ?>
									<select  class="campo disabled" <?php echo ($_POST['nivel'] != '3' ) ? 'disabled="disabled"' : '' ?>  name="tipo_contrato" style="width: 570px">
								<?php else : ?>
									<input type="hidden" id="tipo_contrato" name="tipo_contrato" value="<?php echo $_POST['tipo_contrato']; ?>" />
									<select disabled="disabled" name="mostra_tipo_contrato" style="width: 570px">
								<?php endif; ?>
									<option value=""></option>
									<?php foreach ($listaTipoContrato as $tipoContrato): ?>
										<option <?php echo ($_POST['tipo_contrato'] == $tipoContrato['id_tipo_contrato']) ? 'selected' : ''; ?> value="<?php echo $tipoContrato['id_tipo_contrato'];?>"><?php echo $tipoContrato['descricao'];?></option>
									<?php endforeach;?>
								</select>
							</div>
							
                            
                            <div class="clear"></div>
                            
                            <fieldset class="maior opcoes-display-block" style="width: 550px !important;"> 
                            	<legend>Isenção e Desconto</legend>
                            
	                             <div class="clear"></div>
	                            
	                             <div class="campo medio" >
									<label for="isento_cobranca">Isento Cobrança </label>
									<input type="checkbox" id="isento_cobranca" name="isento_cobranca" <?php echo $_POST['isento_cobranca'] == 'on' ? 'checked' : '' ;?> />
							     </div>
							   
							   
							   <div class="campo data periodo">
		                            <div class="inicial">
		                                <label for="data_ini">Data Inicial </label>
		                                <input class="campo" type="text" <?php echo $_POST['isento_cobranca'] != 'on' ? 'disabled="disabled"' : '' ;?> class="campo" value="<?php echo $_POST['isento_cobranca_dt_ini'];?>"  id="isento_cobranca_dt_ini" name="isento_cobranca_dt_ini" />
	
		                            </div>
		                            <div class="campo label-periodo">a</div>
		                            <div class="final">
		                                <label for="data_fim">Data Final </label>
		                                <input class="campo" type="text" <?php echo $_POST['isento_cobranca'] != 'on' ? 'disabled="disabled"' : '' ;?> class="campo" value="<?php echo $_POST['isento_cobranca_dt_fim'];?>" id="isento_cobranca_dt_fim" name="isento_cobranca_dt_fim" />
		                            </div>
	                            </div>
							   
							   <div class="clear"></div>
								
								<div class="campo medio">
									<label for="perc_desconto">%  de Desconto</label>
								    <input style="border: solid 1px #999;" type="text" <?php echo (!empty($_POST['perc_desconto'])) ? 'checked' : '' ;?> id="perc_desconto" name="perc_desconto" size="12" maxlength="6" value="<?php echo $_POST['perc_desconto']; ?>" />
						        </div>
							
								<div class="campo data periodo">
		                            <div class="inicial">
		                               <label for="data_ini">Data Inicial </label>
		                               <input  class="campo" type="text" <?php echo empty($_POST['perc_desconto']) ? 'disabled="disabled"' : '' ;?> class="campo" value="<?php echo $_POST['perc_desconto_dt_ini'];?>"  id="perc_desconto_dt_ini" name="perc_desconto_dt_ini" />
	
		                            </div>
		                            <div class="campo label-periodo">a</div>
		                            <div class="final">
		                                <label for="data_fim">Data Final </label>
		                                <input class="campo" type="text" <?php echo empty($_POST['perc_desconto']) ? 'disabled="disabled"' : '' ;?> class="campo" value="<?php echo $_POST['perc_desconto_dt_fim'];?>" id="perc_desconto_dt_fim" name="perc_desconto_dt_fim" />
		                            </div>
	                            </div>
                            
                                 <div class="clear"></div>
                                                        
                            	 <div class="campo medio">
		                          	<label for="obrigacao_financeira">Valor </label> 
									<input style="border: solid 1px #999;" type="text" <?php echo (!empty($_POST['valor'])) ? 'checked' : '' ;?> id="valor" name="valor" size="12" maxlength="9" value="<?php echo $_POST['valor']; ?>" />
								</div>

                                <div class="campo data periodo">
                                    <div class="inicial">
                                        <label for="data_ini">Data Inicial </label>
                                        <input  class="campo" type="text" <?php echo empty($_POST['valor']) ? 'disabled="disabled"' : '' ;?> class="campo" value="<?php echo $_POST['valor_dt_ini'];?>"  id="valor_dt_ini" name="valor_dt_ini" />

                                    </div>
                                    <div class="campo label-periodo">a</div>
                                    <div class="final">
                                        <label for="data_fim">Data Final </label>
                                        <input class="campo" type="text" <?php echo empty($_POST['valor']) ? 'disabled="disabled"' : '' ;?> class="campo" value="<?php echo $_POST['valor_dt_fim'];?>" id="valor_dt_fim" name="valor_dt_fim" />
                                    </div>
                                </div>

								<div class="campo medio" >
								    <label for="periodicidade_reajuste">Periodicidade de Reajuste (meses)</label>
									<input  style="border: solid 1px #999;" type="text" name="periodicidade_reajuste" size="12" maxlength="6" value="<?php echo $_POST['periodicidade_reajuste']; ?>" />
								</div>

                                <div class="clear"></div>

                                <div class="dados_cliente" style=" <?php echo (isset($_POST['nivel']) && ($_POST['nivel'] != '2' ) || !isset($_POST['nivel'])) ? 'display:none;' : '' ?> ">
                                    <div class="campo maior">
                                        <label for="prazo_vencimento">Prazo de Vencimento (Dias)</label>
                                        <input style="border: solid 1px #999;" type="text" name="prazo_vencimento" size="12" maxlength="3" value="<?php echo $_POST['prazo_vencimento']; ?>" />
                                    </div>
                                </div>
                             </fieldset>


							<div class="clear"></div>

                               <fieldset class="maior opcoes-display-block" style="width: 550px !important;">
                                   <legend>Macro Motivo(s)</legend>
                                   <div id="obr_table" style="overflow: scroll; overflow-x: hidden; width: 550px; height: 91px; ">
                                       <div class="listagem">
                                           <table>
                                               <thead>
                                               <tr>
                                                   <th class="selecao"></th>
                                                   <th class="medio">Macro Motivo</th>
                                               </tr>
                                               </thead>
                                               <tbody>
                                               <?php foreach($listaMacroMotivo as $index => $macro) :
                                                   $check = '';
                                                   if($macro['id'] == $_POST['radio_macro']){
                                                       $check = 'checked="checked"';
                                                   }
                                                   ?>

                                                   <tr class="<?php echo $index % 2 == 0 ? "impar" : "par"; ?>">
                                                       <td class="centro">
                                                           <input type="radio" id="radio_macro_<?php echo $macro['id']; ?>" name="radio_macro" value="<?php echo $macro['id']; ?>"
                                                               <?php echo $check; ?>
                                                                  class="radio_macro" />
                                                       </td>
                                                       <td class="left"><label for="radio_macro_<?php echo $macro['tipo']; ?>"><?php echo $macro['tipo'].' - '. $macro['motivo']; ?></label></td>
                                                   </tr>
                                               <?php endforeach; ?>
                                               </tbody>
                                           </table>
                                       </div>
                                   </div>
                               </fieldset>
                               <fieldset class="maior opcoes-display-block" style="width: 418px !important;">
                                   <legend>Micro Motivo(s)</legend>
                                   <div id="mic_motivo" style="overflow: scroll; overflow-x: hidden; width: 418px; height: 90px; ">
                                       <div class="listagem">
                                           <table>
                                               <thead>
                                               <tr>
                                                   <th class="selecao"></th>
                                                   <th class="medio">Micro Motivo</th>
                                               </tr>
                                               </thead>
                                               <tbody>
                                               <?php foreach($listaMicroMotivo as $index => $micro) :
                                                   $checka = '';
                                                   if($micro['id'] == $_POST['radio_micro']){
                                                       $checka = 'checked="checked"';
                                                   }
                                                   ?>
                                                   <tr class="<?php echo $index % 2 == 0 ? "impar" : "par"; ?>">
                                                       <td class="centro">
                                                           <input type="radio" id="radio_micro_<?php echo $micro['id']; ?>" name="radio_micro" value="<?php echo $micro['id']; ?>"
                                                               <?php echo $checka; ?>
                                                                  class="radio_micro" />
                                                       </td>
                                                       <td class="left"><label for="radio_micro_<?php echo $micro['tipo']; ?>"><?php echo $micro['tipo'].' - '. $micro['motivo']; ?></label></td>
                                                   </tr>
                                               <?php endforeach; ?>
                                               </tbody>
                                           </table>
                                       </div>
                                   </div>
                               </fieldset>


                               <fieldset class="maior opcoes-display-block" style="width: 1000px !important; ">
                                   <legend>Observa&ccedil;&atilde;o</legend>
                                   <textarea style=" border:0; width: 1000px !important; height: 90px !important;"  id="obs_param" name="obs_param" rows="8" cols="60"><?php echo trim($_POST['obs_param']);?></textarea>
                               </fieldset>

                               <div class="clear"></div>

                               <fieldset  class="opcoes-display-block" style="width:1000px !important;">
					        <legend>Seleção da(s) Obriga&ccedil;&atilde;o(&otilde;es) Financeira(s)</legend>
								<div id="obr_table" style="overflow: scroll; overflow-x: hidden; width: 1000px; height: 232px; ">
								     <div class="listagem">
								          <table>
								            <thead>
												<tr>
				                                    <th class="selecao"><input type="checkbox" id="checkbox_obrigacao_financeira_todos" name="checkbox_obrigacao_financeira_todos" value="" /></th>
				                                    <th class="medio">Obrigação Financeira</th>
				                                    <th class="menor">Grupo</th>
												</tr>
	                                         </thead>
											 <tbody>
											<?php foreach($listaObrigacaoFinanceira as $index => $obrigacao_financeira) : ?>
									
												<tr class="<?php echo $index % 2 == 0 ? "impar" : "par"; ?>">
													<td class="centro">
														<input type="checkbox" id="checkbox_obrigacao_financeira_<?php echo $obrigacao_financeira['id']; ?>" name="checkbox_obrigacao_financeira[]" value="<?php echo $obrigacao_financeira['id']; ?>"
															<?php if(isset($_POST['checkbox_obrigacao_financeira']) && in_array($obrigacao_financeira['id'], $_POST['checkbox_obrigacao_financeira'])) : ?>
															     checked="checked"
															<?php endif; ?>
														         class="checkbox_obrigacao_financeira" />
													</td>
													<td class="left"><label for="checkbox_obrigacao_financeira_<?php echo $obrigacao_financeira['id']; ?>"><?php echo $obrigacao_financeira['id'].' - '. $obrigacao_financeira['descricao']; ?></label></td>
													<td class="left"><?php echo $obrigacao_financeira['grupo']; ?></td>
												</tr>
											<?php endforeach; ?>
											</tbody>
										</table>
									 </div>
								</div>
						</fieldset>


                        <div class="clear"></div>
                        <div class="separador"></div>

                            
                        <div id="div_importacao_massivo">   
                            <fieldset style="width:1000px; background: none; border: 1px solid silver; padding: 6px; margin-bottom: 10px">
                                <legend>Replicar parâmetro de forma massiva?</legend>		

                                <label>
                                    <input type="radio" name="param_massivo" id="param_massivo"  value="1"  class="radio"/>                                  
                                    Sim 
                                </label>
                                </br>
                                <label>
                                    <input checked type="radio" name="param_massivo" id="param_massivo"  value="0"  class="radio"/>                                  
                                    Não 
                                </label>
                            </fieldset>

                            <div class="clear"></div>
                            <div id="div_arquivo_massivo"> 
                                <fieldset class="maior opcoes-display-block" style="width: 550px !important;"> 
                                    <legend>Arquivo *</legend>
                                    <input type="file" name="arqcontratos" id="arqcontratos" accept=".csv">
                                </fieldset>
                            </div>
                            <div class="clear"></div>
                            <div class="separador"></div>
                        </div>
                      </form>
                    </div>
                </div>  
           
      <div id="loader_1" class="carregando" style="display:none;"></div>     
           
      <div  class="bloco_acoes">
         <button type="button" id="btn_confirmar" name="btn_confirmar">Confirmar</button>
         <button type="button" id="btn_retornar" name="btn_retornar">Retornar</button>
		<!-- Botão excluir somente ficará visível quando o usuário estiver editando -->
		<?php if (isset($_POST['parfoid']) && !empty($_POST['parfoid'])):?>
		    <button type="button" id="btn_excluir" name="btn_excluir">Excluir</button>
		<?php endif;?>
      </div>                  

      
      
	</div>
</body>

<?php 
include ("lib/rodape.php");