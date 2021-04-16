<head>
    <!-- CSS -->
    <link type="text/css" rel="stylesheet" href="calendar/calendar.css"/>
    <link type="text/css" rel="stylesheet" href="includes/css/base_form.css">
    <link type="text/css" rel="stylesheet" href="modulos/web/js/lib/jQueryUI/themes/base/jquery.ui.all.css" > 
    <link type="text/css" rel="stylesheet" href="modulos/web/css/cad_controle_falhas.css">

    <!-- JAVASCRIPT -->
    <script type="text/javascript" src="includes/js/calendar.js"></script>
    <script type="text/javascript" src="includes/js/mascaras.js"></script>
    <script type="text/javascript" src="includes/js/auxiliares.js"></script>
    <script type="text/javascript" src="includes/js/validacoes.js"></script>
    <script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>
    <script type="text/javascript" src="modulos/web/js/lib/jQueryUI/js/jquery-ui-1.8.24.custom.min.js"></script>
    <script type="text/javascript" src="modulos/web/js/cad_controle_falhas.js"></script>
</head>

<form name="form" id="form" method="POST" action="cad_controle_falhas.php">
    <input type="hidden" name="acao" id="acao" />

    <center>
        
        <table class="tableMoldura">
            
            <tr class="tableTitulo">
                <td><h1>Controle de Falhas</h1></td>
            </tr>
            
            <tr>
                <td>
                    <span id="msg" class="msg"></span>
                </td>
            </tr>	
            
            <tr>
                <td align="center">
                    
                    <table class="tableMoldura">
                        
                        <tr class="tableSubTitulo">
                            <td colspan="2"><h2>Dados para pesquisa</h2></td>
                        </tr> 
                        
                        <tr>
                            <td width="10%">
                                <label>Serial: *</label>
                            </td>
                            <td width="90%">
                                <input type="text" name="equno_serie" id="equno_serie" maxlength="15" size="15"><input type="text" style="display:none" />
                            </td>
                        </tr> 
                        
                        <tr class="tableRodapeModelo1">
                            <td colspan="2" align="center">
                                <input type="button" value="Pesquisar" name="btn_pesquisar_historico_falhas" id="btn_pesquisar_historico_falhas" class="botao">
                                <input type="button" value="Novo" name="btn_inserir_falhas" id="btn_inserir_falhas" class="botao">
                            </td>
                        </tr> 
                        
                    </table>
                    
                </td>
            </tr> 
                        
        </table>
        
    </center>
</form>