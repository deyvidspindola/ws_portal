  <?php require_once '_msgPrincipal.php'; ?> 

                <div class="bloco_titulo"><?=utf8_encode('Transferencia de Titularidade e Alteração - Massivo')?></div>
                <form id="form" name="form" action="fin_transferencia_titularidade.php?acao=novo" method="post" enctype="multipart/form-data">

                    <input type="hidden" name="acao" id="acao" value="pesquisa" />
                    <div class="bloco_conteudo" style="padding-bottom: 20px">
                        <div class="formulario">
                            <?php if($this->acao == 'pesquisar'): ?>
                                <?php if(!empty($this->msgInfo)): ?><div class="mensagem info"><?php echo $this->msgInfo; ?></div><?php endif; ?>
                                <?php if(!empty($this->msgAlerta)): ?><div class="mensagem alerta"><?php echo $this->msgAlerta; ?></div><?php endif; ?>
                                <?php if(!empty($this->msgSucesso)): ?><div class="mensagem sucesso"><?php echo $this->msgSucesso; ?></div><?php endif; ?>
                                <?php if(!empty($this->msgErro)): ?><div class="mensagem erro"><?php echo $this->msgErro; ?></div><?php endif; ?>
                            <?php endif; ?>

                            <div class="campo maior">
                                <label style="margin-left: 1px;" for="numero_cpf_cnpj">CPF/CNPJ (*)</label>
                                <input style="width:360px; margin-left: 1px;" type="text" name="numero_cpf_cnpj" id="numero_cpf_cnpj" value="<?php echo !empty($rsRow ['retornocnpj']) && empty($rsRow ['retornocpf']) ? $resultadoCPFCNPJ : $resultadoCPFCNPJ; ?>" class="campo" disabled/>
                            </div>

                            <div class="campo maior">
                                <label style="!important;" for="atual_titular">Atual Titular (*)</label>
                                <input style=" !important;" type="text" name="atual_titular" id="atual_titular" value="<?php echo $_POST["cliente"]; ?>" class="campo" disabled/>
                            </div>

                            <div class="clear"></div>

                            <div class="campo maior">
                                <label style=" margin-left: 1px;" for="tipo_proposta">Tipo de Proposta:</label>
                                <select style="margin-left: 1px; width: 360px; !important;" name="tipo_proposta" id="tipo_proposta" disabled>
                                    <option value=""><?=utf8_encode('Transferência de Títularidade - Massivo') ?></option>
                                    <option value="alteracao" <?php echo !empty($this->tipoProposta) && $this->tipoProposta == "alteracao" ? 'selected' : null; ?>>Alteração - Massivo</option>
                                    <option value="transferencia" <?php echo !empty($this->tipoProposta) && $this->tipoProposta == "transferencia" ? 'selected' : null; ?>><?=utf8_encode('Transferência de Títularidade - Massivo') ?></option>
                                </select>
                            </div>

                            <div class="campo menor">
                                <label  for="tipo_contrato">Contratos:</label>
                                <select style="margin-top: 2px; width: 90px;  !important;" name="tipo_contrato" id="tipo_contrato" disabled>
                                    <option value="todos">Ativos</option>
                                    <option value="todos" <?php echo !empty($this->tipoContrato) && $this->tipoContrato == "1" ? 'selected' : null; ?>>Todos</option>
                                    <option value="ativos" <?php echo !empty($this->tipoContrato) && $this->tipoContrato == "1" ? 'selected' : null; ?>>Ativos</option>
                                    <option value="pendentes" <?php echo !empty($this->tipoContrato) && $this->tipoContrato == "1" ? 'selected' : null; ?>>Pendentes</option>
                                </select>
                            </div>

                            <div class="campo maior">
                                <label for="classe_contrato">Classe do Contrato:</label>
                                <select style="margin-top: 2px; width: 150px; !important;" name="classe_contrato" id="classe_contrato" disabled>
                                    <option value="todos">Todos</option>
                                    <option value="todos" <?php echo !empty($this->classeContrato) && $this->classeContrato == "1" ? 'selected' : null; ?>>Todos</option>
                                    <option value="sascar_full" <?php echo !empty($this->classeContrato) && $this->classeContrato == "1" ? 'selected' : null; ?>>Sascar Full</option>
                                    <option value="sascar_full_sat" <?php echo !empty($this->classeContrato) && $this->classeContrato == "1" ? 'selected' : null; ?>>Sascar Full SAT 1000</option>
                                </select>
                            </div>

                            <div class="custom-control custom-radio" style="padding-top: 10px; margin-left: -190px;">
                                <label>
                                    <input type="radio" class="custom-control-input" id="semtaxa" name="refidelizar" value="sim" style="margin-left: -190px;" checked>
                                    <?php echo utf8_encode('Isencao na Taxa de Adesão');?> (*refideliza o cliente)
                                    &nbsp;&nbsp;
                                    <select name="prazoIsencao" id="prazoIsencao" required="true">
                                        <option value="-">Escolha</option>
                                        <?php
                                        for ($i = 0; $i <= 60; $i++) {
                                            ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php }
                                        ?>
                                    </select>
                                </label>
                            </div>
                            <div class="custom-control custom-radio">
                                <label>
                                    <input type="radio" class="custom-control-input" id="comtaxa" name="refidelizar" value="nao" style="margin-left: -190px;">
                                    <?php echo utf8_encode('Cobrar Taxa de Adesão');?>&nbsp;
                                    <!--<input id="vlr_taxa" name="taxa-transferencia" type="text" maxlength="7" onkeyup="jQuery(this).maskMoney({thousands:'.', decimal:','});" value="0,00" size="4">-->
                                </label>
                               
                            </div>
                            
                            
                            <fieldset style="margin-right: -600px">
                                <legend>Informe o Novo Titular</legend>
                                <div class="campo maior">
                                    <div class="input_container">
                                        <label for="novo_titular" style="margin-left: 1px; margin-top: -6px;">Novo Titular (*)</label>
                                        <input style="margin-top: 3px;" type="text" id="clientenovo" name="clientenovo" value="" size="37"/>
                                        <input type="hidden" id="id_clientenovo" name="id_clientenovo" />
                                        <ul id="clientenovo_id">

                                        </ul>
                                    </div>
                                </div>
                                <div class="campo menor">
                                    <label style="margin-left: -50px; margin-top: 15px;" for="novonumero_cpf_cnpj">CPF/CNPJ (*)</label>
                                    <input style="margin-left: -50px; !important; width:300px; height: 21px; margin-top: 6px;" type="text" name="novocpfcnpj" id="novocpfcnpj" value="<?php echo !empty($this->numeroCpfCnpj) ? $this->numeroCpfCnpj : null; ?>" class="campo" />
                                </div>

                                <div class="campo menor"></div>
                            </fieldset>

                                <!-- <div class="campo maior">
                                    <label for="situacao_rps">Situação da RPS</label>
                                    <select name="situacao_rps" id="situacao_rps">
                                        <option value="">Selecione</option>
                                        <option value="1" <?php echo !empty($this->situacaoRps) && $this->situacaoRps == "1" ? 'selected' : null; ?>>NF sem erro</option>
                                        <option value="2" <?php echo !empty($this->situacaoRps) && $this->situacaoRps == "2" ? 'selected' : null; ?>>NF com erro</option>
                                        <option value="3" <?php echo !empty($this->situacaoRps) && $this->situacaoRps == "3" ? 'selected' : null; ?>>NF gerada</option>
                                    </select>
                                </div> -->

                            <div class="clear"></div>

                            <div class="campo menor">
                                <label for="numero_resultados" style="margin-top: -45px; margin-left: 784px;">Mostrar:</label>
                                <select name="numero_resultados" id="numero_resultados" style="margin-left: 784px;  width:100px;">
                                    <option value="all">Escolha</option>
                                    <option value="10" <?php echo !empty($this->numeroResultados) && $this->numeroResultados == "10" ? 'selected' : null; ?>>10 registros</option>
                                    <option value="25" <?php echo !empty($this->numeroResultados) && $this->numeroResultados == "25" ? 'selected' : null; ?>>25 registros</option>
                                    <option value="50" <?php echo !empty($this->numeroResultados) && $this->numeroResultados == "50" ? 'selected' : null; ?>>50 registros</option>
                                </select>
                            </div>
                            <div class="campo menor">
                                <label for="ordena_resultados" style="margin-top: -45px; margin-left: 769px;">Ordenar:</label>
                                <select name="ordena_resultados" id="ordena_resultados" style="margin-left: 769px;  width:126px;">
                                    <option value="contrato">Escolha</option>
                                    <option value="inicio_vigencia" <?php echo !empty($this->ordenaResultados) && $this->ordenaResultados == "vigencia" ? 'selected' : null; ?>>Data de Vigência</option>
                                    <option value="contrato" <?php echo !empty($this->ordenaResultados) && $this->ordenaResultados == "contrato" ? 'selected' : null; ?>>Nº Termo/Contrato</option>
                                    <option value="placa" <?php echo !empty($this->ordenaResultados) && $this->ordenaResultados == "placa" ? 'selected' : null; ?>>Placa</option>
                                </select>
                            </div>
                            <div class="campo menor">
                                <label for="classifica_resultados" style="margin-top: -45px; margin-left: 782px;">Classificar:</label>
                                <select name="classifica_resultados" id="classifica_resultados" style="margin-left: 782px;  width:100px;">
                                    <option value="asc">Escolha</option>
                                    <option value="asc" <?php echo !empty($this->situacaoRps) && $this->situacaoRps == "asc" ? 'selected' : null; ?>>Ascendente</option>
                                    <option value="desc" <?php echo !empty($this->situacaoRps) && $this->situacaoRps == "desc" ? 'selected' : null; ?>>Descendente</option>
                                </select>
                            </div>
                            
                            </form>
                        </div>
                        
                        <div class="separador"></div>
                        <div>
                        	<?php 
                        	
                        	   include (_MODULEDIR_ . 'Financas/View/fin_transferencia_titularidade/contratos_transferencias_selecionados_grid.php');
                        	   
                        	?>
                        </div>
                        
                        <div class="bloco_acoes" >
                            <input type="button" id="confirmar_solicitacao"
                                   value="<?php echo utf8_encode('Confirmar Transferência');?>" />
                            <input type="button" id="btvoltar" value="Voltar" />
                            <div class="botoes_acoes"  ></div>
                        </div>
                        
                        
                        <div id="montalog" >
                            
                        </div>    
                                            
                    </div>

        
