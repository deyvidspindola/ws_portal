<?php require_once '_header.php';?> 
    <script type="text/javascript" src="modulos/web/js/fin_transferencia_titularidade.js" charset="utf-8"></script>

    <div class="mensagem alerta" id="msgalerta1" style="display: none;">
    </div>

    <form name="frm_pesquisar" id="frm_pesquisar" method="POST" action="">
        <input type="hidden" name="acao" id="acao" value="pesquisa" />

        <div class="modulo_titulo">
            Cadastro de Proposta para Transferência
        </div>

        <div class="modulo_conteudo">
            <?php require_once '_abas.php';?>
            <div class="bloco_titulo">
                Transferência de Titularidade
            </div>

            <div class="bloco_conteudo">
                <div class="formulario">
                    <div class="campo maior">
                        <div class="input_container">
                            <label for="numero_cpf_cnpj" style="margin-left: 1px; margin-top: -3px; margin-bottom: 3px;">Atual Titular (*)</label>
                            <input  type="text" id="cliente" name="cliente" value="" size="37"/>
                            <input type="hidden" id="id_cliente" name="id_cliente" />
                            <ul id="cliente_id">

                            </ul>
                        </div>
                    </div>
                    <div class="campo maior">
                        <label style="margin-left: -50px !important;" for="atual_titular">CPF/CNPJ (*)</label>
                        <input  disabled  style="margin-left: -50px !important; width:244px; height: 21px; margin-top: 3px;" type="text" name="cpfcnpj" id="cpfcnpj" value="<?php echo !empty($this->numeroCpfCnpj) ? $this->numeroCpfCnpj : null; ?>" class="campo" />
                    </div>
                    <div class="campo menor">
                        <label style="margin-left: -175px !important;" for="numero_termo_contrato">Nº Termo/Contrato</label>
                        <input disabled style="margin-left: -175px !important; height: 21px; margin-top: 3px;" type="text" name="contrato" id="contrato" value="<?php echo !empty($this->numeroTermoContrato) ? $this->numeroTermoContrato : null; ?>" class="campo" onKeyup="formatar(this, '@');" onBlur="revalidar(this, '@', '');" maxlength="10" />
                    </div>
                    <div class="campo menor">
                        <label style="margin-left: -178px !important;" for="numero_placa">Placa</label>
                        <input  disabled  style="margin-left: -178px !important; height: 21px; margin-top: 3px;" type="text" name="placa" id="placa" value="<?php echo !empty($this->numeroPlaca) ? $this->numeroPlaca : null; ?>" class="campo" maxlength="7" />
                    </div>

                    <div class="clear"></div>

                    <div class="campo maior">
                        <label for="classecontrato" style="margin-left: 3px;">Classe do Contrato:</label>
                        <select name="classecontrato" id="classecontrato" class="form_field" style="width:300px;  margin-left: 3px;">
                            <option value="">Escolha</option>
                            <?
                            for($i=0;$i<$_SESSION['snequipamento_classe'];$i++){
                                $selected = "";
                                if($_POST['classe_busca']==$_SESSION["sequipamento_classe"][$i]['eqcoid']){$selected = ' selected="selected"';}
                                ?>
                                <option value="<?=$_SESSION["sequipamento_classe"][$i]['eqcoid']?>" <?=$selected?>><?=$_SESSION["sequipamento_classe"][$i]['eqcdescricao']?></option>
                                <?
                            }
                            ?>
                        </select>
                    </div>
                    <div class="campo menor">
                        <label for="numeroresultados" style="margin-left: -70px;">Mostrar:</label>
                        <select name="numeroresultados" id="numeroresultados" style="margin-left: -70px; width:100px;">
                            <option value="all">Escolha</option>
                            <option value="10" <?php echo !empty($this->numeroResultados) && $this->numeroResultados == "10" ? 'selected' : null; ?>>10 registros</option>
                            <option value="25" <?php echo !empty($this->numeroResultados) && $this->numeroResultados == "25" ? 'selected' : null; ?>>25 registros</option>
                            <option value="50" <?php echo !empty($this->numeroResultados) && $this->numeroResultados == "50" ? 'selected' : null; ?>>50 registros</option>
                            <option value="100" <?php echo !empty($this->numeroResultados) && $this->numeroResultados == "100" ? 'selected' : null; ?>>100 registros</option>
                            <option value="300" <?php echo !empty($this->numeroResultados) && $this->numeroResultados == "300" ? 'selected' : null; ?>>300 registros</option>
                            <option value="500" <?php echo !empty($this->numeroResultados) && $this->numeroResultados == "500" ? 'selected' : null; ?>>500 registros</option>
                        </select>
                    </div>
                    <div class="campo menor">
                        <label for="ordenaresultados" style="margin-left: -80px;">Ordenar:</label>
                        <select name="ordenaresultados" id="ordenaresultados" style="margin-left: -80px; width:146px;">
                            <option value="connumero">Escolha</option>
                            <option value="inicio_vigencia" <?php echo !empty($this->ordenaResultados) && $this->ordenaResultados == "vigencia" ? 'selected' : null; ?>>Data de Vigência</option>
                            <option value="connumero" <?php echo !empty($this->ordenaResultados) && $this->ordenaResultados == "contrato" ? 'selected' : null; ?>>Nº Termo/Contrato</option>
                            <option value="veiplaca" <?php echo !empty($this->ordenaResultados) && $this->ordenaResultados == "placa" ? 'selected' : null; ?>>Placa</option>
                        </select>
                    </div>
                    <div class="campo menor">
                        <label for="classificaresultados" style="margin-left: -44px;">Classificar:</label>
                        <select name="classificaresultados" id="classificaresultados" style="margin-left: -45px; width:100px;">
                            <option value="asc">Escolha</option>
                            <option value="asc" <?php echo !empty($this->situacaoRps) && $this->situacaoRps == "asc" ? 'selected' : null; ?>>Ascendente</option>
                            <option value="desc" <?php echo !empty($this->situacaoRps) && $this->situacaoRps == "desc" ? 'selected' : null; ?>>Descendente</option>
                        </select>
                    </div>
                    
                     <div class="campo menor">
                        <label for="statusId" style="margin-left: -44px;">Status:</label>
                        <select name="statusId" id="statusId" style="margin-left: -45px; width:110px;">
                             <option value="all">Todos</option>
                            <?php
                            foreach($statusLista as $status){
                                $selected = "";
                               if($status['csioid'] == 13){
                                   $selected = ' selected ';
                               }?>
                             
                                <option value="<?=$status['csioid']; ?>" <?php echo $selected;?> > <?php echo $status['csidescricao']; ?> </option>
                            <?php
                            }
                            ?>
                            
                        </select>
                    </div>
                     

                    <div class="clear"></div>
                </div>
            </div>
            <div class="bloco_acoes">
                <button type="button" id="bt_pesquisar">Pesquisar</button>
                <button type="button" href="fin_transferencia_titularidade.php" id="limparCliente" >Limpar</button>
            </div>

            <div class="separador"></div>
            <div id="frame01"></div>

            <div class="separador"></div>
            <div id="frame04"></div>

            <div class="separador"></div>
            <div id="process" title="Mensagem"></div>
        </div>
    </form>
    <div class="separador"></div>
<?php include "lib/rodape.php"; ?>
<div class="separador"></div>
