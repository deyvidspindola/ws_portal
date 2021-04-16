<?php
    
    cabecalho();
    require_once ("lib/funcoes.js");
    
?>

<html>
    <head>
        <meta charset="ISO-8859-1">

    <!-- CSS -->
        <link type="text/css" rel="stylesheet" href="lib/css/style.css" />
        <link type="text/css" rel="stylesheet" href="lib/css/cupertino/jquery-ui-1.10.0.custom.min.css" />

        <!-- JAVASCRIPT -->    
        <script type="text/javascript" src="includes/js/mascaras.js"></script>
        <script type="text/javascript" src="includes/js/auxiliares.js"></script>
        <script type="text/javascript" src="includes/js/validacoes.js"></script>    
        <script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>
        <script type="text/javascript" src="lib/js/jquery-ui-1.10.0.custom.min.js"></script>
        <script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js"></script>
        <script type="text/javascript" src="lib/js/bootstrap.js"></script>  
        <script type="text/javascript" src="modulos/web/js/rel_aprovacao_credito.js"></script>
        
    </head>

    
    <body>

        <div class="modulo_titulo">Relatório de Aprovação de Crédito</div>
        <div class="modulo_conteudo">
            
            <div class="mensagem sucesso <?php if (empty($this->mensagem_sucesso)){echo "invisivel"; }?>"><? echo $this->mensagem_sucesso; ?></div>
            <div class="mensagem alerta <?php if (empty($this->mensagem_alerta)){echo "invisivel"; }?>"><? echo $this->mensagem_alerta; ?></div>
            <div class="mensagem erro <?php if (empty($this->mensagem_erro)){echo "invisivel"; }?>"><? echo $this->mensagem_erro; ?></div>
            <div class="mensagem info">Os campos com * são obrigatórios.</div>
            
            <form id="form_rel_aprovacao_credito" method="post">
                <input type="hidden" name="acao" id="acao" value=" " />
                <div class="bloco_titulo">Dados para Pesquisa</div>
                <div class="bloco_conteudo">
                    <div class="formulario"> 
                        <div class="campo data">
                            <label>Período *</label> 	      
                            <input type="text" maxlength="10" id="dt_ini" name="dt_ini" class="campo" value="<?php echo $filtros->dt_ini ?>" style="width: 82px;"/>
                        </div>
                        <div class="campo label-periodo">a</div>
                        <div class="campo data">
                            <label>&nbsp;</label>
                            <input type="text" maxlength="10" id="dt_fim" name="dt_fim" class="campo"  value="<?php echo $filtros->dt_fim ?>" style="width: 82px;" />
                        </div>           
                        <div class="clear"></div>
                        <div class="campo medio">
                            <label>Aprovação Gestor </label> 	      
                            <select id="cb_gestor" name="cb_gestor">
                            	<option value="" <?php if ( $filtros->cb_gestor == "null" ): echo " selected "; endif;  ?> >Escolha</option>
                                <option value="aguardando" <?php if ( $filtros->cb_gestor == "aguardando" ): echo " selected "; endif;  ?> >Aguardando Análise</option>
                                <option value="aprovado" <?php if ( $filtros->cb_gestor == "aprovado" ): echo " selected "; endif;  ?> >Crédito Aprovado</option>
                                <option value="reprovado" <?php if ( $filtros->cb_gestor == "reprovado" ): echo " selected "; endif;  ?>>Crédito Não Aprovado</option>
                            </select>
                        </div>
                        
                        <div class="campo medio">
                            <label>Aprovação Financeiro </label> 	      
                            <select id="cb_financeiro" name="cb_financeiro">
								<option value="" <?php if  ( $filtros->cb_financeiro == "null" ): echo " selected "; endif;  ?> >Escolha</option>
                                <option value="aguardando" <?php if ( $filtros->cb_financeiro == "aguardando" ): echo " selected "; endif;  ?> >Aguardando Análise</option>
                                <option value="aprovado" <?php if  ( $filtros->cb_financeiro == "aprovado" ): echo " selected "; endif;  ?> >Crédito Aprovado</option>
                                <option value="reprovado" <?php if ( $filtros->cb_financeiro == "reprovado" ): echo " selected "; endif;  ?>>Crédito Não Aprovado</option>
                            </select>
                        </div>

                        <div class="campo menor">
                            <label>Tipo de Proposta </label> 	      
                            <select name="cb_tipo_proposta" id="cb_tipo_proposta">
                                <?php echo $tipoProposta; ?>
                            </select>
                        </div>
                        
                        <div class="campo medio">
                            <label>Tipo de Contrato </label> 	      
                            <select name="cb_tipo_contrato" id="cb_tipo_contrato">
								<?php echo $tipoContrato; ?>
                            </select>
                        </div>
                        
                        <div class="clear"></div>
                    </div>
                </div>

                 <div class="bloco_acoes">
                    <button type="button" id="pesquisar">Pesquisar</button>
                    <button type="button" id="gerar_csv">Gerar CSV</button>
                </div>		
            </form>
            <?php if (!empty($this->view)) { include $this->view; } ?>
        </div>
        <div class="separador"></div>
        <?php require_once 'lib/rodape.php'; ?>
    </body>
</html>