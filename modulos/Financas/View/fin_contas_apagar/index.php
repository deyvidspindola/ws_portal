

<?php require_once _MODULEDIR_ . "Financas/View/fin_contas_apagar/cabecalho.php"; ?>    

    <form id="frm_pesquisar"  method="post" action="">
        <div class="bloco_titulo">Empresa</div>
        <div class="bloco_conteudo">
            <div class="formulario">
                
                <div class="campo maior">
                    <select class="campo empresa" id="tecoid" name="tecoid">
                        <option value="">Selecione</option>
                        <?php foreach ($this->view->empresas as $empresaId => $empresa) :?>
                        <option value="<?php echo $empresa->tecoid; ?>" <?php echo ($this->view->filtros->tecoid == $empresa->tecoid ? 'selected="selected"' : "" );?> ><?php echo $empresa->tecrazao; ?></option>
                        <?php endforeach;?>
                    </select>
                    <input type="hidden" name="limpaTecoid" id="limpaTecoid" value="">
                </div>
                <div class="clear"></div>

            </div>
        </div>

        <div class="separador"></div>

        <ul class="bloco_opcoes">
            <li id="gerar_arquivo"      ><a title="Gerar Arquivo"      href="fin_arq_apagar.php?aba=gerar_arquivo"       >Gerar Arquivo</a></li>
            <li id="envio_arquivos"     ><a title="Envio de Arquivos"  href="fin_arq_apagar.php?aba=envio_arquivos"      >Envio de Arquivos</a></li>
            <li id="titulos_processados"><a title="Títulos Processados" href="fin_arq_apagar.php?aba=titulos_processados">Títulos Processados</a></li>
            <li id="logs"               ><a title="Logs"     href="fin_arq_apagar.php?aba=logs"               >Logs</a></li>
        </ul>

    
        <?php 
            switch ($this->view->parametros->aba) {
                case 'gerar_arquivo':
                        require_once _MODULEDIR_ . "Financas/View/fin_contas_apagar/aba_gerar_arquivo.php";     
                    break;
                case 'envio_arquivos':
                        require_once _MODULEDIR_ . "Financas/View/fin_contas_apagar/aba_envio_arquivos.php";     
                    break;
                case 'titulos_processados':
                        require_once _MODULEDIR_ . "Financas/View/fin_contas_apagar/aba_titulos_processados.php";     
                    break;
                case 'logs':
                        require_once _MODULEDIR_ . "Financas/View/fin_contas_apagar/aba_logs.php";     
                    break;
                default:
                    break;
            }
                
        ?>
    
        
    </form>
    
<?php require_once _MODULEDIR_ . "Financas/View/fin_contas_apagar/rodape.php"; ?>