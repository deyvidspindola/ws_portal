

<?php require_once _MODULEDIR_ . "Financas/View/fin_faturamento_nf_chaves/cabecalho.php"; ?>    

<style type="text/css">
            <!--
            @import url("includes/css/base_form.css");
            @import url("includes/css/calendar.css");
            -->            
        </style>
        <script language="javascript" type="text/javascript" src="includes/js/calendar.js"></script>

    <!-- Mensagens-->
    <?php
        if(isset($this->view['mensagem']) )
        {
            echo "<div class='mensagem erro'>". $this->view['mensagem'] . "</div>";
        }
        if(isset($this->view['resultado']['erro']))
        {
            echo "<div class='mensagem erro'>". $this->view['resultado']['erro'] . "</div>";
        }
    ?>

    <form id="frm_pesquisar" name="frm_pesquisar"  method="post" action="">
        <div class="bloco_titulo">Filtros</div>
        <div class="bloco_conteudo">
            <div class="formulario">
                <div class="campo maior">
                    <span>Empresa</span>
                    <select class="campo empresa" id="tecoid" name="tecoid">
                        <option value="">TODAS</option>
                        <?php foreach ($this->view['empresas'] as $empresaId => $empresa) : ?>
                            <option value="<?php echo $empresa['tecoid']; ?>" 
                                <?php echo (isset($_POST['tecoid']) && $_POST['tecoid'] == $empresa['tecoid'])? "selected":"" ?>>
                                    <?php echo $empresa['tecoid'].' - '.$empresa['tecrazao']; ?>
                                </option>
                        <?php endforeach;?>
                    </select>
                </div>
                <div class="clear"></div>
                <div class="campo maior">
                    <span>Periodo</span>
                    <input type="text" required="required" name="data_ini_pesquisa" id="data_ini_pesquisa" size="10" maxLength="10" 
                    value="<?php echo (isset($_POST['data_ini_pesquisa']))? $_POST['data_ini_pesquisa']:"" ?>" onkeyup="formatar(this, '@@/@@/@@@@')" onBlur="revalidar(this,'@@/@@/@@@@/','data');" readonly="readonly">
                                        <img src="images/calendar_cal.gif" align="middle" border="0" alt="Calendário"  onclick="displayCalendar(document.frm_pesquisar.data_ini_pesquisa,'dd/mm/yyyy',this)">
                                        &agrave;
                    <input type="text" name="data_fim_pesquisa" required="required" id="data_fim_pesquisa" size="10" maxLength="10" 
                                        value="<?php echo (isset($_POST['data_fim_pesquisa']))? $_POST['data_fim_pesquisa']:"" ?>" onkeyup="formatar(this, '@@/@@/@@@@')" onBlur="revalidar(this,'@@/@@/@@@@/','data');" readonly="readonly">
                                        <img src="images/calendar_cal.gif" align="middle" border="0" alt="Calendário"  onclick="displayCalendar(document.frm_pesquisar.data_fim_pesquisa,'dd/mm/yyyy',this)">
                </div>
                <div class="clear"></div>
                <div class="campo maior">
                    <button name="consultar" value="consultar"><i class="ui-icon ui-icon-search" style="float: left; color: #333;"></i>Consultar</button>
                    <button name="exportar" value="exportar"><i class="ui-icon ui-icon-script" style="float: left; color: #333;"></i>Exportar consulta</button>
                </div>
                <div class="clear"></div>

            </div>
        </div>

    </form>

    <br class="clear">
    <br class="clear">

    <?php if(isset($this->view['resultado']) && !isset($this->view['resultado']['erro'])){?>
        <div class="bloco_titulo">Resultado da pesquisa</div>
            <div class="bloco_conteudo">
                <table width="100%" cellspacing="1" cellpadding="1" border="0">
                    <thead>
                        <tr>
                            <th class="tab_registro_azescuro" style="font-weight: bold;">C&Oacute;D.ENT.</th>
                            <th class="tab_registro_azescuro" style="font-weight: bold;">EMPRESA</th>
                            <!-- <th class="tab_registro_azescuro" style="font-weight: bold;">ESTABELECIMENTO</th> -->
                            <!-- <th class="tab_registro_azescuro" style="font-weight: bold;">GRUPO DOC</th> -->
                            <th class="tab_registro_azescuro" style="font-weight: bold;">NOME FORNECEDOR</th>
                            <th class="tab_registro_azescuro" style="font-weight: bold;">DATA ENTRADA</th>
                            <th class="tab_registro_azescuro" style="font-weight: bold;">NRO NOTA</th>
                            <th class="tab_registro_azescuro" style="font-weight: bold;">VALOR TOTAL</th>
                            <th class="tab_registro_azescuro" style="font-weight: bold;">CHAVE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $count = 0; 
                        foreach($this->view['resultado'] as $key => $value)
                        {
                            ?>
                        <tr class="checkNotas <?php echo ($key % 2 == 0)?'tab_registro_destaque': 'tab_registro' ?>">
                            <td style="text-align: center;"><?php echo $value['cod_entrada'];?></td>
                            <td><?php echo $value['empresa'];?></td>
                            <!-- <td><?php echo $value['estabelecimento'];?></td> -->
                            <!-- <td><?php echo $value['grupo_dcto'];?></td> -->
                            <td><?php echo $value['nome_fornecedor'];?></td>
                            <td style="text-align: right;"><?php echo $value['data_entrada'];?></td>
                            <td style="text-align: right;"><?php echo $value['nro_nota'];?></td>
                            <td style="text-align: right;"><?php echo number_format($value['valor_total'],2,",",".");?></td>
                            <td style="text-align: center;"><?php echo $value['chave_acesso'];?></td>
                        </tr>
                        <?php 
                            $count++;
                        } 
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7" class="tab_registro_azescuro" style="text-align: center;"> 
                                Numero de resultados apresentado nesta consulta: <?php echo $count;?> 
                            </td>
                        </tr>
                    </tfoot>
                </table>
        </div>
    <?php } ?>
    
<?php require_once _MODULEDIR_ . "Financas/View/fin_faturamento_nf_chaves/rodape.php"; ?>