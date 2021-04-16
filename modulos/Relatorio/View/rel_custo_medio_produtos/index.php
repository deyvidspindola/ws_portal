<?php
    
    cabecalho();
    require_once ("lib/funcoes.js");
    
?>

<html>
    
<head>

    <meta charset="ISO-8859-1">

    <!-- CSS -->
    <link type="text/css" rel="stylesheet" href="css/style.css"/>
    <link type="text/css" rel="stylesheet" href="lib/css/style.css"/>
    <link type="text/css" rel="stylesheet" href="lib/css/cupertino/jquery-ui-1.10.0.custom.min.css"/>

    <!-- JAVASCRIPT -->    
    <script type="text/javascript" src="includes/js/mascaras.js"></script>
    <script type="text/javascript" src="includes/js/auxiliares.js"></script>
    <script type="text/javascript" src="includes/js/validacoes.js"></script>    
    <script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>
    <script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js"></script>
    <script type="text/javascript" src="lib/js/jquery-ui-1.10.0.custom.min.js"></script>
    <script type="text/javascript" src="lib/js/bootstrap.js"></script>  
    <script type="text/javascript" src="modulos/web/js/rel_custo_medio_produtos.js"></script>

</head>

<body>
    
    <div class="mensagem info <?php if (empty($mensagem_info)){echo "invisivel"; }?>"><? echo $mensagem_info; ?></div>
    <div class="mensagem sucesso <?php if (empty($mensagem_sucesso)){echo "invisivel"; }?>"><? echo $mensagem_sucesso; ?></div>
    <div class="mensagem alerta <?php if (empty($mensagem_alerta)){echo "invisivel"; }?>"><? echo $mensagem_alerta; ?></div>
    <div class="mensagem erro <?php if (empty($mensagem_erro)){echo "invisivel"; }?>"><? echo $mensagem_erro; ?></div>
    
    <div class="modulo_titulo">Custo Médio de Produtos</div>
    
    <div class="modulo_conteudo">
    
    <form id="form" method="POST">
        
        <input type="hidden" name="acao" id="acao">
        
        <div class="bloco_titulo">Dados para Pesquisa</div>
        
        <div class="bloco_conteudo">
            
            <div class="conteudo">
                    
                <div class="formulario">
                    
                    <div class="campo data">
                        <label>Período: (*)</label> 	      
                        <input type="text" value="<?php echo $filtros['dt_ini']; ?>" maxlength="10" id="dt_ini" name="dt_ini" class="campo">
                    </div>
                    
                    <div class="campo label-periodo">a</div>
                    
                    <div class="campo data">
                        <label>&nbsp;</label>            
                        <input type="text" value="<?php echo $filtros['dt_fim']; ?>" maxlength="10" id="dt_fim" name="dt_fim" class="campo">
                   
                    </div>
                    
                    <div class="clear"></div>
                    
                    <div class="campo maior">
                        
                        <label for="tipo_relatorio">Tipo de Relatório:</label>
                        <select name="tipo_relatorio" id="tipo_relatorio">
                            <option value="analitico" <?php if ( $filtros['tipo_relatorio'] == "analitico" ): echo " selected "; endif;  ?> >Analítico</option>
                            <option value="sintetico" <?php if ( $filtros['tipo_relatorio'] == "sintetico" ): echo " selected "; endif;  ?> >Sintético</option>
                        </select>
                            
                    </div>
                    
                    <div class="clear"></div>
                    
                    <div class="campo maior">
                        
                        <label for="pesquisar_por">Pesquisar por:</label>
                        <select name="pesquisar_por" id="pesquisar_por">
                            <option value="E" <?php if ( $filtros['pesquisar_por'] == "E" ): echo " selected "; endif;  ?> >Dt. Entrada</option>
                            <option value="S" <?php if ( $filtros['pesquisar_por'] == "S" ): echo " selected "; endif;  ?> >Dt. Saída</option>
                        </select>
                            
                    </div>
                    
                    <div class="clear"></div>
                    
                    <div class="campo maior">
                        
                        <label for="representante_responsavel">Representante Responsável:</label>
                        <select name="representante_responsavel" id="representante_responsavel">
                            <option value="">Selecione</option>
                            
                            <?php if ( is_array($this->arrayRepresentantes) ): ?>
                            
                                <?php foreach($this->arrayRepresentantes as $representante ): ?>
                                    <option value='<?php echo $representante['representante_id'] ?>' <?php if ( $representante['representante_id'] == $filtros['representante_responsavel'] ): echo " selected "; endif; ?> >
                                        <?php echo $representante['representante_nome'] ?>
                                    </option>
                                <?php endforeach; ?>

                            <?php endif; ?>
                            
                        </select>
                            
                    </div>
                    
                    <div class="clear"></div>
                    
                    <div class="campo maior">
                        
                        <label for="tipo_produto">Tipo de Produto:</label>
                        <select name="tipo_produto" id="tipo_produto">
                            <option value="">Selecione</option>
                            <option value="L" <?php if ( $filtros['tipo_produto'] == "L" ): echo " selected "; endif;  ?> >Locação</option>
                            <option value="R" <?php if ( $filtros['tipo_produto'] == "R" ): echo " selected "; endif;  ?> >Revenda</option>
                        </select>
                            
                    </div>
                    
                </div>
                
            </div>
            
            <div class="separador"></div>
            
        </div>
        
        
        
        <div class="bloco_acoes">
            <button type="button" id="pesquisar">Pesquisar</button>
            <button type="button" id="gerar_csv">Gerar CSV</button>
        </div>
        
        <div class="bloco_rodape"></div>
        
        
