<?php include "head.php"; ?>

    <style>
        .periodo img{
            margin-right: 18px !important;
        }
        .data, .mes_ano{
            width: 125px !important;
        }
        
        fieldset .menor, fieldset .menor input {
            width: 88px !important;
        }
        fieldset label{
            display: block;
        }
        .none{
            display: none;
        }
        .percento-label{
            font-size: 14px;
            position: absolute;
            right: -19px;
            top: 16px;
            border: 0px !important;
            background: none !important;
            color: #000 !important;
        }
        div.campo{
            position: relative;
        }
        
        fieldset{
            position: relative;
        }
        
        fieldset .desconto_percentual_left{
            margin-left: 21px;
             margin-left: 27px\9 !important; /* IE8+9  */
        }
        
        fieldset.tipo_desconto_box{
            width: 223px !important;
        }
        
        #cfcpdt_inicio_vigencia{
            width: 88px\9 !important; /* IE8+9  */
        }
        
         #cfcpdt_fim_vigencia{
            width: 88px\9 !important; /* IE8+9  */
        }
        
        .periodo img{
            margin-right: 7px\9 !important; /* IE8+9  */
        }
        
        #cfcpdesconto_percentual_de{
            width: 80px\9 !important; /* IE8+9  */
        }
        
        #cfcpdesconto_percentual_ate{
            width: 80px\9 !important; /* IE8+9  */
        }
        
        #tipo_desconto_percentual{
            width: 250px\9 !important; /* IE8+9  */
        }
        
        #cfcpdesconto_valor_de{
            width: 80px\9 !important; /* IE8+9  */
        }
        
        #cfcpdesconto_valor_ate{
            width: 80px\9 !important; /* IE8+9  */
        }
        
        #tipo_desconto_valor{
            width: 250px\9 !important; /* IE8+9  */
        }
        
        #tipo_desconto_percentual .campo span, #tipo_desconto_valor .campo span{
             width: auto\9 !important; /* IE8+9  */
        }
        
        fieldset .forIe{
            width: 80px\9 !important; /* IE8+9  */
        }
        
        .tipo_desconto_box{
            margin-top: 10px\9 !important; /* IE8+9  */
        }
        
        .box_percentual_desconto{
            position: absolute\9 !important; /* IE8+9  */
        }
        
        .box_percentual_desconto_de{
            left: 10px\9 !important; /* IE8+9  */
        }
        
        .box_percentual_desconto_ate{
            right: 20px\9 !important; /* IE8+9  */
        }
        
        
        .tipo_desconto_box{
            height: 83px !important; /* IE8+9  */
            height: 108px\9 !important; /* IE8+9  */
        }
        
    </style>  

    <div id="info_principal" class="mensagem info">Campos com * são obrigatórios.</div>
    <div id="msg_responsavel" class="mensagem invisivel"></div>
	
	<?php echo $this->exibirMensagens('mensagens'); ?>
        
    <script>
        var existeParametroEmail = <?php echo $this->existeParametroEmail ? 1 : 0; ?>
    </script>
    
    <div class="bloco_titulo">Dados da Pesquisa</div>
    <div class="bloco_conteudo">
    <div class="formulario">
        <form method="POST" name="form" id="form" action="fin_credito_futuro_parametrizacao_campanha.php">
            <input id="acao" type="hidden" value="pesquisar" name="acao">
            
            <div class="campo data">
                <label for="cfcpdt_inicio_vigencia">Período de Vigência *</label>
                <input id="cfcpdt_inicio_vigencia" tabindex="1" type="text" name="cfcpdt_inicio_vigencia" maxlength="10" value="<?php echo  isset($this->parametroPesquisa->cfcpdt_inicio_vigencia) && !empty($this->parametroPesquisa->cfcpdt_inicio_vigencia) ? trim($this->parametroPesquisa->cfcpdt_inicio_vigencia) : ''; ?>" class="campo" />
            </div>
            <p class="campo label-periodo">a</p>
            <div class="campo data">
                <label for="cfcpdt_fim_vigencia">&nbsp;</label>
		<input id="cfcpdt_fim_vigencia" type="text" tabindex="2" name="cfcpdt_fim_vigencia" maxlength="10" value="<?php echo  isset($this->parametroPesquisa->cfcpdt_fim_vigencia) && !empty($this->parametroPesquisa->cfcpdt_fim_vigencia) ? trim($this->parametroPesquisa->cfcpdt_fim_vigencia) : ''; ?>" class="campo" />
            </div>
            
            <div class="clear"></div>
            
            <div class="campo medio">
                <label for="cfcpcftpoid">Tipo de Campanha Promocional</label>
                <select id="cfcpcftpoid" name="cfcpcftpoid" tabindex="3">
                    <option value="">Selecione</option>
                    <?php foreach ($this->listarTipoCampanha as $item) : ?>
                    <?php $selected =  isset($this->parametroPesquisa->cfcpcftpoid) && $this->parametroPesquisa->cfcpcftpoid != '' && $this->parametroPesquisa->cfcpcftpoid == $item->cftpoid ? 'selected="selected"' : ''; ?>
                        <option <?php echo $selected; ?> value="<?php echo $item->cftpoid; ?>"><?php echo $item->cftpdescricao; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            
            <div class="campo medio">
                <label for="cfcpcfmccoid">Motivo do Crédito</label>
                <select id="cfcpcfmccoid" name="cfcpcfmccoid" tabindex="4">
                    <option value="">Selecione</option>
                    <?php foreach ($this->listarMotivo as $item) : ?>
                    <?php $selected =  isset($this->parametroPesquisa->cfcpcfmccoid) && $this->parametroPesquisa->cfcpcfmccoid != '' && $this->parametroPesquisa->cfcpcfmccoid == $item->cfmcoid ? 'selected="selected"' : ''; ?>
                        <option <?php echo $selected ?> value="<?php echo $item->cfmcoid; ?>"><?php echo $item->cfmcdescricao; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="clear"></div>
            
            <fieldset class="medio opcoes-inline">
                <legend>Tipo do Desconto *</legend>
                <?php
                
                //print_r($this->parametroPesquisa->cfcptipo_desconto);exit;
                if (isset($this->parametroPesquisa->cfcptipo_desconto) && !empty($this->parametroPesquisa->cfcptipo_desconto)) {
                    
                    if ($this->parametroPesquisa->cfcptipo_desconto == 'P') {
                        $percentual_ckecked = 'checked="checked"';
                        $valor_ckecked = '';
                        $todos_checked = '';
                        
                        $valorPorcentagemDe = isset($this->parametroPesquisa->cfcpdesconto_percentual_de) && !empty($this->parametroPesquisa->cfcpdesconto_percentual_de) ? number_format($this->parametroPesquisa->cfcpdesconto_percentual_de, '2', ',', '.') : '';
                        $valorPorcentagemAte = isset($this->parametroPesquisa->cfcpdesconto_percentual_ate) && !empty($this->parametroPesquisa->cfcpdesconto_percentual_ate) ? number_format($this->parametroPesquisa->cfcpdesconto_percentual_ate, '2', ',', '.') : '';
                        
                        
                    } else if ($this->parametroPesquisa->cfcptipo_desconto == 'V'){
                        $percentual_ckecked = '';
                        $valor_ckecked = 'checked="checked"';
                        $todos_checked = '';
                        
                        $valorValorDe = isset($this->parametroPesquisa->cfcpdesconto_valor_de) && !empty($this->parametroPesquisa->cfcpdesconto_valor_de) ? number_format($this->parametroPesquisa->cfcpdesconto_valor_de, '2', ',', '.') : '';
                        $valorValorAte = isset($this->parametroPesquisa->cfcpdesconto_valor_ate) && !empty($this->parametroPesquisa->cfcpdesconto_valor_ate) ? number_format($this->parametroPesquisa->cfcpdesconto_valor_ate, '2', ',', '.') : '';
                    }else{
                        $percentual_ckecked = '';
                        $valor_ckecked = '';
                        $todos_checked = 'checked="checked"';
                    }
                    
                }
                
                
                ?>
                <input id="cfcptipo_desconto_1" class="tipo_desconto" type="radio" <?php echo isset($percentual_ckecked) && !empty($percentual_ckecked) ? $percentual_ckecked : '' ?> tabindex="5" value="P" name="cfcptipo_desconto">
                <label for="cfcptipo_desconto_1">Percentual</label>
                <input id="cfcptipo_desconto_2" class="tipo_desconto" type="radio" <?php echo isset($valor_ckecked) && !empty($valor_ckecked) ? $valor_ckecked : '' ?> value="V" tabindex="6" name="cfcptipo_desconto">
                <label for="cfcptipo_desconto_2">Valor</label>
                <input id="cfcptipo_desconto_3" class="tipo_desconto" type="radio" <?php echo isset($todos_checked) && !empty($todos_checked) ? $todos_checked : (!isset($percentual_ckecked) && !isset($valor_ckecked)) ? 'checked="checked"':''; ?> tabindex="7" value="" name="cfcptipo_desconto">
                <label for="cfcptipo_desconto_3">Todos</label>
            </fieldset>
            
            <fieldset id="tipo_desconto_percentual" class="medio none tipo_desconto_box">
                <legend>Percentual do Desconto</legend>
                <div class="campo menor forIe box_percentual_desconto box_percentual_desconto_de">
                    <label for="cfcpdesconto_de">De *</label>
                    <input id="cfcpdesconto_percentual_de" maxlength='6' name="cfcpdesconto_percentual_de" class="campo desconto_percentual" type="text" value="<?php echo isset($valorPorcentagemDe) && !empty($valorPorcentagemDe) ? $valorPorcentagemDe : '' ?>">
                    <div class="percento-label">%</div>
                </div>
                
                <div class="campo menor desconto_percentual_left forIe box_percentual_desconto box_percentual_desconto_ate">
                    <label for="cfcpdesconto_ate">Até *</label>
                    <input id="cfcpdesconto_percentual_ate" maxlength='6' name="cfcpdesconto_percentual_ate" class="campo desconto_percentual " type="text" value="<?php echo isset($valorPorcentagemAte) && !empty($valorPorcentagemAte) ? $valorPorcentagemAte : '' ?>">
                     <div class="percento-label">%</div>
                </div>
            </fieldset>
            
            <fieldset id="tipo_desconto_valor" class="medio none tipo_desconto_box ">
                <legend>Valor do Desconto</legend>
                <div class="campo menor">
                    <label for="campo_1">De *</label>
                    <input id="cfcpdesconto_valor_de" maxlength='9' class="campo desconto_valor " name="cfcpdesconto_valor_de" type="text" value="<?php echo isset($valorValorDe) && !empty($valorValorDe) ? $valorValorDe : ''; ?>">
                </div>
                
                <div class="campo menor desconto_percentual_left">
                    <label for="campo_1">Até *</label>
                    <input id="cfcpdesconto_valor_ate" maxlength='9' class="campo desconto_valor " name="cfcpdesconto_valor_ate" type="text" value="<?php echo isset($valorValorAte) && !empty($valorValorAte) ? $valorValorAte : ''; ?>">
                </div>
            </fieldset>
            
            <div class="clear"></div>
            
            <fieldset class="medio opcoes-inline ">
                <legend>Forma de Aplicação *</legend>
                <?php
                
                //print_r($this->parametroPesquisa->cfcptipo_desconto);exit;
                if (isset($this->parametroPesquisa->cfcpaplicacao) && !empty($this->parametroPesquisa->cfcpaplicacao)) {
                    
                    if ($this->parametroPesquisa->cfcpaplicacao == 'I') {
                        $integral_ckecked = 'checked="checked"';
                        $parcela_ckecked = '';
                        $todos_checked = '';
                    } else if ($this->parametroPesquisa->cfcpaplicacao == 'P'){
                        $integral_ckecked = '';
                        $parcela_ckecked = 'checked="checked"';
                        $todos_checked = '';
                    }else{
                        $integral_ckecked = '';
                        $parcela_ckecked = '';
                        $todos_checked = 'checked="checked"';
                    }
                    
                }
                ?>
                <input id="cfcpaplicacao_1" type="radio" class="forma_aplicacao" <?php echo isset($integral_ckecked) && !empty($integral_ckecked) ? $integral_ckecked : '' ?>  value="I" tabindex="9" name="cfcpaplicacao">
                <label for="cfcpaplicacao_1">Integral</label>
                <input id="cfcpaplicacao_2" type="radio" class="forma_aplicacao" <?php echo isset($parcela_ckecked) && !empty($parcela_ckecked) ? $parcela_ckecked : '' ?> value="P" tabindex="10" name="cfcpaplicacao">
                <label for="cfcpaplicacao_2">Parcela</label>
                <input id="cfcpaplicacao_3" type="radio" class="forma_aplicacao" <?php echo isset($todos_checked) && !empty($todos_checked) ? $todos_checked : (!isset($integral_ckecked) && !isset($parcela_ckecked)) ? 'checked="checked"':''; ?> tabindex="11" value="" name="cfcpaplicacao">
                <label for="cfcpaplicacao_3">Todos</label>
            </fieldset>
            
            <div class="clear"></div>
        </form>
    </div>
    </div>
    
    <div class="bloco_acoes">
        <button tabindex="12"  id="bt_pesquisar">Pesquisar</button>
        <button tabindex="13" type="submit" id="novo">Novo</button>
    </div>
    
     <!--Grid Pesquisa -->
     <?php include('resultado_pesquisa.php'); ?>   
     <!--Fim grid Pesquisa -->
     
 
    
    <script>
        
    
    </script>
    
<?php include "footer.php"; ?>    