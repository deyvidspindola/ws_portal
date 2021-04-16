
<ul class="bloco_opcoes">

    <li class="<?php echo ($this->view->parametros->acao == 'cotacao' || trim($this->view->parametros->acao) == '') ?  'ativo' : '' ?>" id="aba_cotacao" 
        <?php echo ($this->view->parametros->acao == 'cotacao' || trim($this->view->parametros->acao) == '') ?  '' : 'style="background: url(images/fundo.gif);"' ?>>
        <a href="#">Gerar cotação</a>
    </li>

    <li class="<?php echo ($this->view->parametros->acao == 'proposta') ?  'ativo' : '' ?>" id="aba_proposta" 
        <?php echo ($this->view->parametros->acao == 'proposta') ?  '' : 'style="background: url(images/fundo.gif);"' ?>>
        <a href="#">Gerar proposta</a>
    </li>

    <li class="<?php echo ($this->view->parametros->acao == 'apolice') ?  'ativo' : '' ?>" id="aba_apolice" 
        <?php echo ($this->view->parametros->acao == 'apolice') ?  '' : 'style="background: url(images/fundo.gif);"' ?>>
        <a href="#">Gerar apólice</a>
    </li>

</ul>

<div id="cotacao" <?php echo ($this->view->parametros->acao == 'cotacao' || trim($this->view->parametros->acao) == '' ) ?  '' : 'style="display:none;"' ?>>
    <div class="bloco_titulo">Cotação</div>
    <div class="bloco_conteudo">

        <div class="formulario">
            <form id="form_cotacao"  method="post" action="">
                <input type="hidden" id="acao" name="acao" value="cotacao"/>
                <input type="hidden" id="cotacao" name="cotacao" value="cotacao"/>
                <div class="campo menor">
                    <label for="tipo_pessoa_cotacao">Tipo Pessoa *</label>
                    <select id="tipo_pessoa_cotacao" name="tipo_pessoa_cotacao">
                        <option value="">Escolha</option>
                        <option value="1"<?php echo ($this->view->parametros->tipo_pessoa_cotacao == '1') ?  'selected="true"' : ''?>>Física</option>
                        <option value="2"<?php echo ($this->view->parametros->tipo_pessoa_cotacao == '2') ?  'selected="true"' : ''?>>Jurídica</option>
                    </select>
                </div>

                <div class="clear"></div>

                <div class="campo medio">
                    <label for="cpf_cnpj">CPF / CNPJ *</label>
                    <input id="cpf_cnpj" name="cpf_cnpj" value="<?php echo $this->view->parametros->cpf_cnpj?>" maxlength="22" class="campo" type="text">
                </div>
                
                <div class="clear"></div>

                <div class="campo medio">
                    <label for="cep">CEP *</label>
                    <input id="cep" name="cep" value="<?php echo $this->view->parametros->cep?>" class="campo direita" maxlength="10" type="text">
                </div>

                <div class="clear"></div>

                <div class="campo medio">
                    <label for="cod_fipe">Código FIPE *</label>
                    <input id="cod_fipe" name="cod_fipe" value="<?php echo $this->view->parametros->cod_fipe?>" class="campo" maxlength="15" type="text">
                </div>

                <div class="clear"></div>

                <div class="campo menor">
                    <label for="ano_modelo">Ano do Modelo *</label>
                    <input id="ano_modelo" name="ano_modelo" value="<?php echo $this->view->parametros->ano_modelo?>" class="campo numeric" maxlength="9" type="text">
                </div>

                <div class="clear"></div>

                <div class="campo menor">
                    <label for="novo_usado">Novo/Usado *</label>
                    <select id="novo_usado" name="novo_usado">
                        <option value="">Escolha</option>
                        <option value="1" <?php echo ($this->view->parametros->novo_usado == '1') ?  'selected="true"' : ''?>>Novo</option>
                        <option value="0"<?php echo ($this->view->parametros->novo_usado == '0') ?  'selected="true"' : ''?>>Usado</option>
                    </select>
                </div>

                <div class="clear"></div>

                <div class="campo menor">
                    <label for="combustivel">Tipo Combustivel *</label>
                    <select id="combustivel" name="combustivel">
                        <option value="">Escolha</option>
                        <?php foreach ($this->view->comboCombustivel as $dados): ?>
                            <option value="<?php echo $dados->psccombid; ?>" <?php echo ($this->view->parametros->combustivel == $dados->psccombid) ? 'selected="true"' : '' ; ?>>
                                <?php echo $dados->psccombdesc; ?>
                            </option>
                        <?php endForeach; ?>
                    </select>
                </div>

                <div class="clear"></div>

                <div class="campo menor">
                    <label for="uso_veiculo">Uso Veículo *</label>
                    <select id="uso_veiculo" name="uso_veiculo">
                        <option value="">Escolha</option>
                        <?php foreach ($this->view->comboUsoVeiculo as $dados): ?>
                            <option value="<?php echo $dados->psuvutilid; ?>" <?php echo ($this->view->parametros->uso_veiculo == $dados->psuvutilid) ? 'selected="true"' : '' ; ?>>
                                <?php echo $dados->psuvutildesc; ?>
                            </option>
                        <?php endForeach; ?>
                    </select>
                </div>

                <div class="campo menor">
                    <label for="finalidade_uso">Finalidade Uso </label>
                    <input id="finalidade_uso" name="finalidade_uso" value="<?php echo $this->view->parametros->finalidade_uso?>" maxlength="9" class="campo numeric" type="text">
                </div>

                <div class="clear"></div>

                <div class="campo menor">
                    <label for="classe_produto">Classe produto *</label>
                    <input id="classe_produto" name="classe_produto" value="<?php echo $this->view->parametros->classe_produto?>" class="campo numeric" maxlength="9" type="text">
                </div>

                <div class="clear"></div>

                <div class="campo menor">
                    <label for="id_revenda">Id revenda *</label>
                    <!--<input id="id_revenda" name="id_revenda" value="<?php echo $this->view->parametros->id_revenda?>" class="campo numeric" maxlength="10" type="text">-->
                    <select id="id_revenda" name="id_revenda">
                        <option value="">Escolha</option>
                        <?php foreach ($this->view->comboIdRevenda as $dados): ?>
                            <option value="<?php echo $dados->psccodseg; ?>" <?php echo ($this->view->parametros->id_revenda == $dados->psccodseg) ? 'selected="true"' : '' ; ?>>
                                <?php echo $dados->psccodseg; ?>
                            </option>
                        <?php endForeach; ?>
                    </select>
                </div>
                <div class="clear"></div>
            </form>
        </div>

    </div>
    <div class="bloco_acoes">
        <button style="cursor: default;" type="button" id="enviar_cotacao">Enviar cotação</button>
    </div>

</div>

<div id="proposta" <?php echo ($this->view->parametros->acao == 'proposta') ?  '' : 'style="display:none;"' ?>>

    <div class="bloco_titulo">Proposta</div>
    <div class="bloco_conteudo">

        <div class="formulario">
            <form id="form_proposta"  method="post" action="">
                <input type="hidden" id="acao" name="acao" value="proposta"/>
                <input type="hidden" id="origem" name="origem" value="proposta"/>
                <div class="campo menor">
                    <label for="texto">Número Cotação *</label>
                    <input id="num_cotacao" name="num_cotacao" value="<?php echo $this->view->parametros->num_cotacao?>" class="campo numeric" maxlength="10" type="text">
                </div>

                <div class="campo medio">
                    <label for="texto">Número Contrato *</label>
                    <input id="num_contrato" name="num_contrato" value="<?php echo $this->view->parametros->num_contrato?>" class="campo numeric" maxlength="10" type="text">
                </div>
                
                <div class="clear"></div>

                <div class="campo medio">
                    <label for="texto">Nome Cliente *</label>
                    <input id="nome_cliente" name="nome_cliente" value="<?php echo $this->view->parametros->nome_cliente?>" class="campo" type="text">
                </div>

                <div class="campo menor">
                    <label for="sexo">Sexo *</label>
                    <select id="sexo" name="sexo">
                        <option value="">Escolha</option>
                        <option value="2" <?php echo ($this->view->parametros->sexo == '2') ?  'selected="true"' : ''?>>Feminino</option>
                        <option value="1" <?php echo ($this->view->parametros->sexo == '1') ?  'selected="true"' : ''?>>Masculino</option>
                    </select>
                </div>
                
                <div class="clear"></div>

                <div class="campo menor">
                    <label for="estado_civil">Estado Civil *</label>
                    <select id="estado_civil" name="estado_civil">
                        <option value="">Escolha</option>
                        <option value="5" <?php echo ($this->view->parametros->estado_civil == '5') ?  'selected="true"' : ''?>>Amasiado</option>
                        <option value="2" <?php echo ($this->view->parametros->estado_civil == '2') ?  'selected="true"' : ''?>>Casado</option>
                        <option value="4" <?php echo ($this->view->parametros->estado_civil == '4') ?  'selected="true"' : ''?>>Divorciado</option>
                        <option value="3" <?php echo ($this->view->parametros->estado_civil == '3') ?  'selected="true"' : ''?>>Outros</option>
                        <option value="1" <?php echo ($this->view->parametros->estado_civil == '1') ?  'selected="true"' : ''?>>Solteiro</option>
                        <option value="3" <?php echo ($this->view->parametros->estado_civil == '3') ?  'selected="true"' : ''?>>Viúvo</option>
                    </select>
                </div>

                <div class="campo medio">
                    <label for="profissa">Profissão *</label>
                    <select id="profissa" name="profissa">
                        <option value="">Escolha</option>
                        <?php foreach ($this->view->comboProfissoes as $dados): ?>
                            <option value="<?php echo $dados->pspsprofid; ?>" <?php echo ($this->view->parametros->profissa == $dados->pspsprofid) ? 'selected="true"' : '' ; ?>>
                                <?php echo $dados->pspsprofdesc; ?>
                            </option>
                        <?php endForeach; ?>
                    </select>
                </div>

                <div class="clear"></div>

                <div class="campo data">
                    <label for="dt_nasc">Data Nascimento *</label>
                    <input id="dt_nasc" name="dt_nasc" value="<?php echo $this->view->parametros->dt_nasc?>" class="campo" type="text">
                </div>

                <div class="clear"></div>

                <div class="campo menor">
                    <label for="pep1">Cliente Pep1 *</label>
                    <input id="pep1" name="pep1" value="<?php echo $this->view->parametros->pep1?>" class="campo" maxlength="80" type="text">
                </div>

                <div class="campo menor">
                    <label for="pep2">Cliente Pep2 *</label>
                    <input id="pep2" name="pep2" value="<?php echo $this->view->parametros->pep2?>" class="campo" maxlength="80" type="text">
                </div>

                <div class="clear"></div>

                <div class="campo menor">
                    <label for="ddd_res">DDD Fone Fixo *</label>
                    <input id="ddd_res" name="ddd_res" value="<?php echo $this->view->parametros->ddd_res?>" maxlength="3" class="campo numeric" type="text">
                </div>


                <div class="campo menor">
                    <label for="fone_res">Fone Fixo *</label>
                    <input id="fone_res" name="fone_res" value="<?php echo $this->view->parametros->fone_res?>" maxlength="11" class="campo numeric" type="text">
                </div>

                <div class="clear"></div>

                <div class="campo menor">
                    <label for="ddd_cel">DDD Celular *</label>
                    <input id="ddd_cel" name="ddd_cel" value="<?php echo $this->view->parametros->ddd_cel?>" maxlength="3" class="campo numeric" type="text">
                </div>

                <div class="campo menor">
                    <label for="num_cel">Número Celular *</label>
                    <input id="num_cel" name="num_cel" value="<?php echo $this->view->parametros->num_cel?>" maxlength="11" class="campo numeric" type="text">
                </div>

                <div class="clear"></div>

                <div class="campo medio">
                    <label for="email">E-mail *</label>
                    <input id="email" name="email" value="<?php echo $this->view->parametros->email?>" maxlength="40" class="campo" type="text">
                </div>

                <div class="clear"></div>

                <div class="campo medio">
                    <label for="endereco">Endereço *</label>
                    <input id="endereco" name="endereco" value="<?php echo $this->view->parametros->endereco?>" maxlength="80" class="campo" type="text">
                </div>

                <div class="campo menor">
                    <label for="endereco_num">Nº *</label>
                    <input id="endereco_num" name="endereco_num" value="<?php echo $this->view->parametros->endereco_num?>" maxlength="9" class="campo numeric" type="text">
                </div>

                <div class="clear"></div>

                <div class="campo medio">
                    <label for="complemento">Complemento</label>
                    <input id="complemento" name="complemento" value="<?php echo $this->view->parametros->complemento?>" maxlength="60" class="campo" type="text">
                </div>

                <div class="clear"></div>

                <div class="campo medio">
                    <label for="cidade">Cidade *</label>
                    <input id="cidade" name="cidade" value="<?php echo $this->view->parametros->cidade?>" class="campo" maxlength="50" type="text">
                </div>

                <div class="campo menor">
                    <label for="uf">UF *</label>
                    <input id="uf" name="uf" value="<?php echo $this->view->parametros->uf?>" maxlength="2" class="campo" type="text">
                </div>

                <div class="clear"></div>

                <div class="campo menor">
                    <label for="placa">Placa Veículo *</label>
                    <input id="placa" name="placa" value="<?php echo $this->view->parametros->placa?>" class="campo" maxlength="10" type="text">
                </div>

                <div class="campo medio">
                    <label for="chassi">Chassi Veículo *</label>
                    <input id="chassi" name="chassi" value="<?php echo $this->view->parametros->chassi?>" class="campo" maxlength="20" type="text">
                </div>

                <div class="clear"></div>

                <div class="campo menor">
                    <label for="uti_vei">Utilização Veículo *</label>
                    <select id="uti_vei" name="uti_vei">
                        <option value="">Escolha</option>
                        <?php foreach ($this->view->comboUsoVeiculo as $dados): ?>
                            <option value="<?php echo $dados->psuvutilid; ?>" <?php echo ($this->view->parametros->uti_vei == $dados->psuvutilid) ? 'selected="true"' : '' ; ?>>
                                <?php echo $dados->psuvutildesc; ?>
                            </option>
                        <?php endForeach; ?>
                    </select>
                </div>

                <div class="campo menor">
                    <!-- auto_utilizacao -->
                    <label for="tipo_seguro">Tipo Seguro *</label>
                    <select id="tipo_seguro" name="tipo_seguro">
                        <option value="">Escolha</option>
                        <option value="2" <?php echo ($this->view->parametros->tipo_seguro == '2') ?  'selected="true"' : ''?>>Comercial</option>
                        <option value="1" <?php echo ($this->view->parametros->tipo_seguro == '1') ?  'selected="true"' : ''?>>Particular</option>
                    </select>
                </div>

                <div class="clear"></div>

                <div class="campo medio">
                    <!--dados_cobranca_taxa_instalacao -> formapagamentoid-->
                    <label for="forma_pag">Forma de Pagamento * <!--24 - Cartão de Crédito Visa / 25 - Cartão de Crédito Master / 3 - Débito Automático HSBC--></label>
                     <select id="forma_pag" name="forma_pag">
                        <option value="">Escolha</option>
                        <?php foreach ($this->view->comboFormaPagamento as $dados): ?>
                            <option value="<?php echo $dados->psfcsascarcod; ?>" <?php echo ($this->view->parametros->forma_pag == $dados->psfcsascarcod) ? 'selected="true"' : '' ; ?>>
                                <?php echo $dados->psfcsascardesc; ?>
                            </option>
                        <?php endForeach; ?>
                    </select>
                </div>
                <div class="clear"></div>

                <div class="campo menor">
                    <label for="classe_produto_prop">Classe produto *</label>
                    <input id="classe_produto_prop" name="classe_produto_prop" value="<?php echo $this->view->parametros->classe_produto_prop?>" class="campo numeric" maxlength="9" type="text">
                </div>

                <div class="clear"></div>

                <div class="campo medio">
                    <label for="id_corretor_intranet">Corretor *</label>

                    <select id="id_corretor_intranet" name="id_corretor_intranet">
                        <option value="">Escolha</option>
                        <?php foreach ($this->view->comboCorretor as $dados): ?>
                            <option value="<?php echo $dados->pscoid; ?>" <?php echo ($this->view->parametros->id_corretor_intranet == $dados->pscoid) ? 'selected="true"' : '' ; ?>>
                                <?php echo $dados->corrnome; ?>
                            </option>
                        <?php endForeach; ?>
                    </select>
                </div>

                <div class="clear"></div>

            </form>
        </div>

    </div>
    <div class="bloco_acoes">
        <button style="cursor: default;" type="button" id="enviar_proposta">Enviar proposta</button>
    </div>
</div>

<div id="apolice" <?php echo ($this->view->parametros->acao == 'apolice') ?  '' : 'style="display:none;"' ?> >
    <div class="bloco_titulo">Apólice</div>
        <div class="bloco_conteudo">

            <div class="formulario">
                <form id="form_apolice"  method="post" action="">

                    <input type="hidden" id="acao" name="acao" value="apolice"/>
                    <input type="hidden" id="origem" name="origem" value="apolice"/>
                    <!--<div class="campo menor">
                        <label for="texto">Código Representante *</label>
                        <input id="cod_rep" name="cod_rep" value="<?php echo $this->view->parametros->cod_rep?>" class="campo" type="text">
                    </div>-->
                    
                    <div class="clear"></div>

                    <div class="campo menor">
                        <label for="texto">Número Ordem de Serviço *</label>
                        <input id="num_ord" maxlength="10" name="num_ord" value="<?php echo $this->view->parametros->num_ord?>" class="campo numeric" maxlength="10" type="text">
                    </div>

                    <div class="clear"></div>

                    <div class="campo menor">
                        <label for="texto">Número Contrato *</label>
                        <input id="num_contrato_apo" name="num_contrato_apo" value="<?php echo $this->view->parametros->num_contrato_apo?>" class="campo numeric" maxlength="10" type="text">
                    </div>

                    <div class="clear"></div>

                    <div class="campo data">
                        <label for="dt_insta_equipa">Data instalação equipamento *</label>
                        <input id="dt_insta_equipa" name="dt_insta_equipa" maxlength="10" value="<?php echo $this->view->parametros->dt_insta_equipa?>" class="campo" type="text">
                    </div>

                    <div class="campo data">
                        <label for="dt_ativa_equipa">Data ativação equipamento *</label>
                        <input id="dt_ativa_equipa" name="dt_ativa_equipa" maxlength="10" value="<?php echo $this->view->parametros->dt_ativa_equipa?>" class="campo" type="text">
                    </div>

                    <div class="clear"></div>

                    <div class="campo menor">
                        <label for="combo">Classe Produto *</label>
                        <input id="classe_produto_apo" name="classe_produto_apo" maxlength="9" value="<?php echo $this->view->parametros->classe_produto_apo?>" class="campo numeric" type="text">
                    </div>

                    <div class="campo">
                        <label for="renovacao_siggo">Renovação Siggo Seguro *</label>
                         <select id="renovacao_siggo" name="renovacao_siggo">
                            <option value="">Escolha</option>
                            <option value="2" <?php echo ($this->view->parametros->renovacao_siggo == '2') ? 'selected="true"' : '' ; ?>>Não</option>
                            <option value="1" <?php echo ($this->view->parametros->renovacao_siggo == '1') ? 'selected="true"' : '' ; ?>>Sim</option>
                        </select>
                    </div>

                 <div class="clear"></div>
                </form>
            </div>

        </div>
    <div class="bloco_acoes">
        <button style="cursor: default;" type="button" id="enviar_apolice">Enviar apólice</button>
    </div>
</div>
<?php if (count($this->view->dados) > 0) : ?>
    <!--  Caso contenha erros, exibe os campos destacados  -->
    <script type="text/javascript" >jQuery(document).ready(function() {
        showFormErros(<?php echo json_encode($this->view->dados); ?>); 
    });
    </script>
<?php endif; ?>