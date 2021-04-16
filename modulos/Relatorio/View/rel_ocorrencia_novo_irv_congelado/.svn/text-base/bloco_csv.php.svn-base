<div id="loader_xls"
     class="carregando invisivel"></div>
    <div id="baixarXls" class="<?php
                            if (!isset($this->view->parametros->arquivo_csv) || empty($this->view->parametros->arquivo_csv)){
                                echo 'invisivel';
                            }
                        ?>">
         <div class="separador"></div>
         <div class="bloco_titulo">Download</div>
         <div class="bloco_conteudo">
             <div class="conteudo centro">
                 <a target="_blank" href="<?php
                            if (isset($this->view->parametros->arquivo_csv) || !empty($this->view->parametros->arquivo_csv)){
                                echo 'download.php?arquivo=' . $this->view->parametros->arquivo_csv;
                            }
                        ?>">
                     <img src="images/icones/t3/caixa2.jpg">
                 <br />
                 <span><?php
                            if (isset($this->view->parametros->arquivo_csv) || !empty($this->view->parametros->arquivo_csv)){
                                echo substr($this->view->parametros->arquivo_csv,  strrpos($this->view->parametros->arquivo_csv, '/') + 1);
                            }
                        ?></span>
                  </a>
             </div>
         </div>
     </div>
