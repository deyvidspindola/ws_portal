<?php
cabecalho();
include("calendar/calendar.js");
include("lib/funcoes.js");
include("lib/funcoes.php");
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
    <script type="text/javascript" src="modulos/web/js/fin_parametros_faturamento_excluir_massivo.js?rand=<?= rand(1, 9999); ?>"></script>   


</head>


<body>

    <div class="modulo_titulo">Parâmetros do Faturamento</div>

    <div class="modulo_conteudo">

        <?php
        if (!empty($mensagemInformativaNaoIncluido)) {
            foreach ($mensagemInformativaNaoIncluido as $msg) {
                echo '<div id="mensagem" class="mensagem alerta"> ' . $msg . '</div>';
            }
        }
        ?>

        <?php
        if (!empty($mensagemInformativa)):

            $class_msg = $mensagemInformativa['status'] === "OK" ? 'mensagem sucesso' : 'mensagem alerta';
            ?>

            <div id="mensagem" class="<?php echo $class_msg; ?>"><?php echo $mensagemInformativa['msg']; ?></div>

        <?php else : ?>

            <div id="mensagem"></div>

        <?php endif; ?>    
        <form name="frm-excluir-massivo" id="frm-excluir-massivo" method="post" action="" enctype="multipart/form-data"> 

            <div class="bloco_titulo">Dados para a pesquisa</div>

            <div class="bloco_conteudo">

                <div class="formulario">

                    <input type="hidden" name="acao" id="acao" />

                    <fieldset style="width:1000px; background: none; border: 1px solid silver; padding: 6px; margin-bottom: 10px">
                        <legend>Nível *</legend>		

                        <label>
                            <input type="checkbox" name="nivel_excluir" id='nivel_excluir' value="1"  class="checkbox" checked=""/>                                  
                            Contrato 
                        </label>

                    </fieldset>

                    <fieldset style="width:1000px; background: none; border: 1px solid silver; padding: 6px; margin-bottom: 10px">
                        <legend>Isenção </legend>		

                        <label>
                            <input type="checkbox" name="isento_excluir"  id="isento_excluir" value="1"  class="checkbox"/>                                  
                            Isenção 
                        </label>

                        <label>
                            <input type="checkbox" name="valor_excluir" id="valor_excluir" value="1"  class="checkbox" />                                  
                            Valor 
                        </label>

                        <label>
                            <input type="checkbox" name="desconto_excluir" id="desconto_excluir" value="1"  class="checkbox" />                                  
                            % de Desconto 
                        </label>

                    </fieldset>

                    <div id="div_arquivo_massivo_excluir"> 
                        <label  style="padding-top: 15px">Arquivo *</label>
                        <input type="file" name="arqcontratos_excluir" id="arqcontratos_excluir" accept=".csv">
                    </div>
                    <div class="clear"></div>


                    <div id="resultado_progress" align="center" style="display:none">
                        <img src="modulos/web/images/loading.gif" alt="Carregando..." />
                    </div>

                    <div class="clear"></div>
                    <div class="separador"></div>

                </div>
            </div>  

            <div id="loader_1" class="carregando" style="display:none;"></div>     

            <div  class="bloco_acoes">
                <button type="button" id="btn_confirmar_massivo" name="btn_confirmar_massivo">Confirmar</button>
                <button type="button" id="btn_retornar_massivo" name="btn_retornar_massivo">Retornar</button>
            </div>
        </form>                  
    </div>
</body>

<?php
include ("lib/rodape.php");
