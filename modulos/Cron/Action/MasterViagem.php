<?php

/**
 * Classe para persistência de dados deste modulo
 */
require _MODULEDIR_ . 'Cron/DAO/MasterViagemDAO.php';

/**
 * Classe padrão para envio de emails
 */
require _SITEDIR_ . 'lib/phpMailer/class.phpmailer.php';

/**
 * @class MasterViagem
 * @author Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
 * @since 31/08/2012
 * Camada de regras de negócio.
 */
class MasterViagem {

    private $dao;
    private $return_adiantamentos;
    private $return_reembolsos;
    
    /*
     * Mensagens de erro
     */
    const M1 = 'WebService indisponível. Solicitações aprovadas serão carregadas na próxima execução do processo agendado.';
    const M2 = 'Nenhum registro retornado na listagem de solicitações de reembolso.';
    const M3 = 'Nenhum registro retornado na listagem de solicitações de adiantamento.';
    const M4 = 'Indisponibilidade da base de dados.';
    const M5 = 'Solicitação ## - CPF vazio ou não encontrado.';
    const M6 = 'Solicitação ## - Valor adiantamento vazio ou não encontrado.';
    const M7 = 'Solicitação ## - Data de aprovação vazia ou não encontrada.';
    const M8 = 'Solicitação ## - Data de pagamento do reembolso vazia ou não encontrada.';
    const M9 = 'Solicitação ## - Centro de custo vazio ou não encontrado.';
    const M10 = 'Solicitação ## - Número da solicitação vazio ou não encontrado.';
    const M11 = 'Solicitação ## - Valor reembolso vazio ou não encontrado.';
    const M12 = 'Solicitação ## - Quantidade vazia ou não encontrada.';
    const M13 = 'Solicitação ## - Conta contábil vazia ou não encontrada.';
    
    /**
     * @author Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * Tráz a data do último registro inserido, caso exista
     */
    public function getLastSolicitationDate() {

        $last_date = $this->dao->getLastSolicitationDate();

        return empty($last_date) ? date('Y-m-d\T00:00:00') : $last_date;
    }
    
    /**
     * @author  Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * @param   $solicitations => XML com as solicitações
     * @param   $tipo => Tipo da solicitação, 'A' para adiantamento e 'R' para 
     *          reembolso.
     * Grava na base de dados as solicitações trazidas pelo método listarSolicitacao
     * do WS da Master Viagem, para futuramente realizar a integração.
     */
    public function saveListSolicitation($solicitations, $tipo, $last) {
        
        /*
         * Se não houver XML provavelmente o WS está fora do ar, então
         * gravamos um log e enviamos um email avisando o grupo responsável.
         */
    	if (!$solicitations) {
            $this->logFix(null, self::M1, 'WI');
            $this->sendEmailLog(self::M1);
            return false;
        }

         $descricao_erro = ($tipo == 'A') ? self::M3 : self::M2;
         $sigla_motivo = ($tipo == 'A') ? 'SA' : 'SR';
        
        $list_solicitations = $solicitations->listarSolicitacaoResponse->listarSolicitacaoResult->SolicitacaoListRS;

        if (!empty($list_solicitations)) {
            
        	if ($tipo == 'A') {
        		$this->return_adiantamentos = 1;
        	}
        	if ($tipo == 'R') {
        		$this->return_reembolsos = 1;
        	}
        	/*
             * Caso seja mais de uma solicitação vinda no XML, esta virá como um
             * array, então percorremos e gravamos cada uma
             */
            if (is_array($list_solicitations)) {
                foreach ($list_solicitations as $solicitation) {
                    $this->saveSolicitation($solicitation, $tipo);
                }
            } else {
                $this->saveSolicitation($list_solicitations, $tipo);
            }
        } else {
            
            /*
             * Caso haja XML, mas não retorne nenhuma solicitação, tbm gravamos log
             * e enviamos email, mas, com outra mensagem.
             * Só ocorre se nenhuma das chamada retornar solicitações
             */
	       	if ($last) {
	       		if ((($tipo == 'A')&&(empty($this->return_adiantamentos)))||(($tipo == 'R')&&(empty($this->return_reembolsos)))) {
	            	$this->logFix(null,$descricao_erro, $sigla_motivo);
	            	$this->sendEmailLog($descricao_erro);
	       		}
	       	}
        }
        //echo "PASSANDO adiantamentos.....".$this->return_adiantamentos;
        //echo "PASSANDO reembolsos.....".$this->return_reembolsos;
        
    }
    
    /**
     * @author  Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * @param   $solicitation => XML com a solicitação
     * @param   $tipo => Tipo da solicitação, 'A' para adiantamento e 'R' para 
     *          reembolso.
     * Método de apoio ao princial saveListSolicitation, houve a necessidade deste
     * pois, podemos ter um array ou não.
     */
    public function saveSolicitation($solicitation, $tipo) {
        
        /*
         * Verifica se a solicitação já não está inserida, o Cron pode ser chamado
         * mais uma vez e listar a mesma, então não podemos gravar novamente
         */
        $qtde_duplicados = $this->dao->verifyDuplicSolicitation($solicitation, $tipo);

        if ((int) $qtde_duplicados == 0) {

            $is_ok = $this->dao->saveListSolicitation($solicitation, $tipo);
            
            /*
             * Caso haja falha ao gravar, enviamos email e gravamos log.
             */
            if (!$is_ok) {

                $message = self::M4;
				$motivo = 'OT';
				
                $this->logFix(null, $message, $motivo);
                $this->sendEmailLog($message);
            }
        }
    }
    
    /**
     * @author  Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * Lista as solicitações a serem integradas na base da sascar
     */
    public function getSolicitationsByNoProcess() {
        return $this->dao->getSolicitationsByNoProcess();
    }
    
    /**
     * @author  Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * @param   $xml => XML recebido pelo WS da Master Viagem
     * @param   $tipoData => Indica se é 'A' (adiantamento) ou 'R' (reembolso)    
     * Método que faz a integração, lendo o XML e gravando na base sascar
     */
    public function saveRecupSolicitation($xml, $tipoData) {
       
        try {

            pg_query($this->dao->conn, 'BEGIN');
            
            //TAG pai onde vem todo XML
            $solicitacao_result = $xml->recuperarSolicitacaoResponse->recuperarSolicitacaoResult;
            
            /*
             * Validações para garantir que o PHP não dê WARNING caso a tag não seja 
             * retornada
             */
            $cpf = !empty($solicitacao_result->Viajante->CPF) ? $solicitacao_result->Viajante->CPF : '';
            $codCentroCusto = !empty($solicitacao_result->SolRateio->RateioRS->CodCentroCusto) ? $solicitacao_result->SolRateio->RateioRS->CodCentroCusto : '';
            $numero_solicitacao = !empty($solicitacao_result->NroSolic) ? $solicitacao_result->NroSolic : 0;
            $valor_adiantamento = !empty($solicitacao_result->SolAdiantamento->AdiantamentoRS->Valor) ? $solicitacao_result->SolAdiantamento->AdiantamentoRS->Valor : 0;
            $quantidade_adiantamento = !empty($solicitacao_result->SolAdiantamento->AdiantamentoRS->Quantidade) ? $solicitacao_result->SolAdiantamento->AdiantamentoRS->Quantidade : 1;
			$valor_adiantamento = $valor_adiantamento * $quantidade_adiantamento;
            
            if (!empty($cpf)) {
                
                /*
                 * O XML retorna o CPF, mas precisamos gravar o id do fornecedor
                 * então buscamos o id através do cpf
                 */
                $ret_cpf = $this->dao->getIdCpf($cpf);

				if (empty($ret_cpf)) {
                    //CPF não existe na tabela fornecedor
                    throw new Exception(self::M5);
                }
            } else {
                //CPF Vazio                
                throw new Exception(self::M5);
            }

            if (empty($codCentroCusto)) {
                //Centro de Custo vazio                
                throw new Exception(self::M9);
            }

            if (empty($numero_solicitacao)) {
                //Numero Solicitacao vazio               
                throw new Exception(self::M10);
            }
            
            /*
             * Caso haja 2 aprovadores a TAG AprovacaoRS virá em forma
            * de array, caso contrario virá apenas um objeto
            */
            if(is_array($solicitacao_result->SolAprovacao->AprovacaoRS)) {
            
            	$aprovador1 = $solicitacao_result->SolAprovacao->AprovacaoRS[0]->AprovadorReal->NomeCompleto;
            	$emailAprovador1 = $solicitacao_result->SolAprovacao->AprovacaoRS[0]->AprovadorReal->Email;
            	$aprovador2 = $solicitacao_result->SolAprovacao->AprovacaoRS[1]->AprovadorReal->NomeCompleto;
            	$emailAprovador2 = $solicitacao_result->SolAprovacao->AprovacaoRS[1]->AprovadorReal->Email;
            
            	$obs = "Aprovado por $aprovador1 <$emailAprovador1> e $aprovador2 <$emailAprovador2>";
            
            } else {
            	$aprovador = $solicitacao_result->SolAprovacao->AprovacaoRS->AprovadorReal->NomeCompleto;
            	$emailAprovador = $solicitacao_result->SolAprovacao->AprovacaoRS->AprovadorReal->Email;
            
            	$obs = "Aprovado por $aprovador <$emailAprovador>";
            }
            
            /*
             * Adiantamento
             */
            if ($tipoData == 'A') {

	            //Valor Adiantamento vazio
	            if (empty($valor_adiantamento)) {
	            	throw new Exception(self::M6);
	            }
	            
            	$data_aprovacao = !empty($solicitacao_result->DataAprovacaoCusto) ? trim($solicitacao_result->DataAprovacaoCusto) : '';
	            
	            if(empty($data_aprovacao)){
	            	throw new Exception(self::M7);
	            }

	            $data_vencimento = $this->getDataVencimento($data_aprovacao);

                $params = array(
                    'viajante_cpf'          => $ret_cpf,
                    'data_vencimento'       => $data_vencimento,
                    'centro_custo'          => $codCentroCusto,
                	'tipo_ct_pagar'         => 31,
                	'tipo_documento'        => 34,
                    'numero_solicitacao'    => $numero_solicitacao,
                    'solAdiantamento_valor' => $valor_adiantamento,
                    'observacao'            => $obs
                );

                /*
                 * método para gravar dados de Solicitação de Adiantamento na 
                 * Tabela apagar
                 */
                $insert_ok = $this->dao->saveAdvanceSolicitation($params);
                
                if(!$insert_ok) {
                    throw new Exception(self::M4);
                }
                
                /*
                 * Método que Atualiza campos da tabela solicitacao_viagem confirmando os lançamentos de adiantamento
                */
                $chave_solicitacao = $solicitacao_result->SolicitacaoId;
                 
                $is_update = $this->dao->updateConfirmaLanctoSolViagem($chave_solicitacao, $numero_solicitacao, $tipoData);
                
                /*
                 * Caso haja falha na atualização, gravamos log e enviamos email
                */
                if(!$is_update) {
                	throw new Exception(self::M4);
                }
                
            }
            
            /*
             * Reembolso
             */
            if ($tipoData == 'R') {

            	$reembolsoRS = $solicitacao_result->SolReembolso->ReembolsoRS;
            	
            	if (is_array($reembolsoRS)) {
            		$data_pagamento_reembolso = trim($solicitacao_result->SolReembolso->ReembolsoRS[0]->DataPagamento);
            	} else {
            		$data_pagamento_reembolso = trim($solicitacao_result->SolReembolso->ReembolsoRS->DataPagamento);
            	}
            	//$data_pagamento_reembolso=trim($solicitacao_result->DataPagtoReembolso);
            	
            	if(empty($data_pagamento_reembolso)){
            		throw new Exception(self::M8);
	           	}
            	
                $contra_partida = (!empty($valor_adiantamento)) ? 1965 : 2289;

                $params = array(
                    'viajante_cpf' => $ret_cpf,
                    'numero_solicitacao' => $numero_solicitacao,                    
                    'contra_partida' => $contra_partida,
                );
                
                /*
                 * método para gravar dados de Solicitação de Reembolso na 
                 * tabela entrada
                 */
                $entoid = $this->dao->saveRepaymentSolicitation($params);
                
                //Entoid tabela Entrada Vazio
                if (empty($entoid)) {
                    throw new Exception(self::M4);
                }

                
                /*
                 * O valor total será a soma dos itens, por isso iniciamos com 0
                 */
                $valor_total = 0;
                
                /*
                 * Quando se tem vários itens a TAG ReembolsoRS os retorna em 
                 * formato de array
                 */
                if (is_array($reembolsoRS)) {
                    foreach ($reembolsoRS as $reembolso) {
                        
                        /*
                         * Para cada posição do array inserimos o item na tabela
                         * entrada_item
                         */
                        $retorno = $this->prepareItem($reembolso, $solicitacao_result->SolicitacaoId, $entoid, $codCentroCusto);
                        
                        if($retorno['error']) {
                            throw new Exception($retorno['message']);
                        }
                        
                        /*
                         * Aqui somamos o valor unitário de cada item para
                         * posteriormente atualizar a tabela entrada
                         */
                        $valor_total += ($reembolso->Valor*$reembolso->Quantidade);
                        
                    }
                } else {
                    
                    /*
                     * Cairá aqui caso haja apenas um item, logo não virá um array
                     */
                    $this->prepareItem($reembolsoRS, $solicitacao_result->SolicitacaoId, $entoid, $codCentroCusto);
                    $valor_total += ($reembolsoRS->Valor*$reembolsoRS->Quantidade);
                }
                
                /*
                 * Cálculo do número de títulos para o pagamento do documento
                 */
                if (empty($valor_adiantamento)) {
                	$no_parcela = 1;
                } else {
                	if ( $valor_total > $valor_adiantamento) {
                		$no_parcela = 2;
                	} else {
                		$no_parcela = 0;
                	}
                }
                
                /*
                 * Método que atualiza o valor total da entrada com a soma dos itens, e o numero de títulos para o pagamento do documento
                 */
                $is_update = $this->dao->updateValorTotal($entoid, $valor_total, $no_parcela );
            
                /*
                 * Caso haja falha na atualização, gravamos log e enviamos email
                 */
                if(!$is_update) {
                    throw new Exception(self::M4);
                }
                
                /*
                 * Geração de títulos de acordo com numero de títulos calculados acima
                 *
                 */
                if ($no_parcela == 1) {
                	
                   /*
                	* Inserção parcela 1 de 1
                	*
                	*/
                	$data_vencimento = $data_pagamento_reembolso;
                	$valor = $valor_total;
                	
                	$params = array(
                			'viajante_cpf'          => $ret_cpf,
                			'data_vencimento'       => $data_vencimento,
                			'centro_custo'          => $codCentroCusto,
                			'tipo_ct_pagar'         => 75,
                			'tipo_documento'        => 36,
                			'numero_solicitacao'    => $numero_solicitacao,
                			'solAdiantamento_valor' => $valor,
                			'observacao'            => $obs
                	);
                	
                	/*
                	 * método para gravar dados de Solicitação de Adiantamento na
                	 * Tabela apagar
                	 */
                	$insert_ok = $this->dao->saveAdvanceSolicitation($params, $entoid);
                	
                	if(!$insert_ok) {
                		throw new Exception(self::M4);
                	}
                	 
                } 
                
                if ($no_parcela == 2) {
                	
                   /*
                 	* Inserção parcela 1 de 2
                 	* 
                	*/
                	
                	$data_vencimento = "NOW()"; //$data_pagamento_reembolso;
                	$valor = $valor_adiantamento;
                	
                	$params = array(
                			'viajante_cpf'          => $ret_cpf,
                			'data_vencimento'       => $data_vencimento,
                			'centro_custo'          => $codCentroCusto,
                			'tipo_ct_pagar'         => 75,
                			'tipo_documento'        => 36,
                			'numero_solicitacao'    => $numero_solicitacao,
                			'solAdiantamento_valor'	=> $valor,
                			'observacao'            => $obs
                	);
                	
                	/*
                	 *  método para gravar dados de Solicitação de Adiantamento na
                	 *  Tabela apagar
                	 */
                	$insert_ok = $this->dao->saveAdvanceSolicitation($params, $entoid);
                	
                	if(!$insert_ok) {
                		throw new Exception(self::M4);
                	}
                	
                	
                	/*
                	 * Inserção parcela 2 de 2
                	 */
                	$data_vencimento = $data_pagamento_reembolso ;//$this->soma30dias($data_pagamento_reembolso);
                	$valor = $valor_total - $valor;
                	
                	$params = array(
                			'viajante_cpf'          => $ret_cpf,
                			'data_vencimento'       => $data_vencimento,
                			'centro_custo'          => $codCentroCusto,
                			'tipo_ct_pagar'         => 75,
                			'tipo_documento'        => 36,
                			'numero_solicitacao'    => $numero_solicitacao,
                			'solAdiantamento_valor' => $valor,
                			'observacao'            => $obs
                	);
                	
                	/*
                	 * método para gravar dados de Solicitação de Adiantamento na
                	 * Tabela apagar
                	 */
                	$insert_ok = $this->dao->saveAdvanceSolicitation($params, $entoid);
                	
                	if(!$insert_ok) {
                		throw new Exception(self::M4);
                	}
                	 
                }

                /*
                 * Método que atualiza campos da tabela entrada confirmando os lançamentos de reembolso
                */
                $is_update = $this->dao->updateConfirmaLanctoEntrada($entoid);
                
                /*
                 * Caso haja falha na atualização, gravamos log e enviamos email
                */
                if(!$is_update) {
                	throw new Exception(self::M4);
                }
                
                if ($no_parcela !== 0){
	                /*
	                 * Método que atualiza campos da tabela apagar confirmando os lançamentos de reembolso
	                */
	                $is_update = $this->dao->updateConfirmaLanctoApagar($entoid);
	                
	                /*
	                 * Caso haja falha na atualização, gravamos log e enviamos email
	                */
	                if(!$is_update) {
	                	throw new Exception(self::M4);
	                }
                }
                /*
                 * Método que Atualiza campos da tabela solicitacao_viagem confirmando os lançamentos de reembolso
                */
                $chave_solicitacao = $solicitacao_result->SolicitacaoId;
                 
                $is_update = $this->dao->updateConfirmaLanctoSolViagem($chave_solicitacao, $numero_solicitacao, $tipoData);
                
                /*
                 * Caso haja falha na atualização, gravamos log e enviamos email
                */
                if(!$is_update) {
                	throw new Exception(self::M4);
                }
            }

            pg_query($this->dao->conn, 'COMMIT');
        } catch (Exception $e) {

            pg_query($this->dao->conn, 'ROLLBACK');
            
            $motivo = 'OT';
            $mensagem = $e->getMessage();
            
            $mensagem = str_replace("##", $numero_solicitacao, $mensagem);
            
            $this->logFix($xml, $mensagem, $motivo);
            $this->sendEmailLog($e->getMessage());
        }
    }
    
    /**
     * @author  Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * @param   $reembolsoRS => Item a ser inserido
     * @param   $solicitacao_id => Chave da solicitação no padrão do WS da Master 
     * @param   $entoid => ID da tabela entrada, como já inserimos nela antes, já temos o id 
     * @param   $codCentroCusto => Código do centro de custo retornado pelo WS      
     */
    public function prepareItem($reembolsoRS, $solicitacao_id, $entoid, $codCentroCusto) {
        
        $valor_reembolso = !empty($reembolsoRS->Valor) ? $reembolsoRS->Valor : null;
        $quantidade_reembolso = !empty($reembolsoRS->Quantidade) ? $reembolsoRS->Quantidade : 0;
        $despesa = !empty($reembolsoRS->Despesa) ? $reembolsoRS->Despesa : 0;

        //Valor Reembolso vazio
        if (is_null($valor_reembolso)) {
            return array('error' => true, 'message' => self::M11);
            
        }

        //Qtde Reembolso vazio
        if (empty($quantidade_reembolso)) {
            return array('error' => true, 'message' => self::M12);            
        }

        //Conta contabil vazia
        if (empty($despesa)) {
            return array('error' => true, 'message' => self::M13);            
        }
                
        $entiprdoid = $this->getProduto($despesa);
        $entiplcoid = $this->getPlanoContabil($despesa);
        
        $params = array(
            'entoid' => $entoid,
            'produto_id' => $entiprdoid,
            'plano_contabil_id' => $entiplcoid,
            'centro_custo' => $codCentroCusto,
            'reembolso_quantidade' => $quantidade_reembolso,
            'reembolso_valor' => $valor_reembolso
        );

        /*
         * método para gravar dados de Solicitação de Reembolso na Tabela entrada_item
         */
        $this->dao->saveRepaymentSolicitationItem($params);
        
        return array('error' => false);
    }
    
    /**
     * @author  Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * Regra: A data do vencimento cai sempre na quinta-feira da próxima semana
     * caso a requisição seja feita no domingo, o vencimento será na quinta
     * da mesma semana.
     */
    private function getDataVencimento($data_aprovacao) {
        
        /*
         * Como o date('w') retorna 0 para domingo até 6 para sábado
         * então se hoje for:
         * 0 => Domingo, pegamos a quinta da mesma semana
         * 1, 2, 3 => segunda, terça ou quarta pegamos a quinta da semana que vem
         * 4 => hoje é quinta então pegamos a próxima, ou seja, da semana que vem
         * 5, 6 => sexta ou sábado, pega quinta semana que vem
         * 
         */
        $self_week = array(0, 4, 5, 6);

        if(in_array(date('w', strtotime($data_aprovacao)), $self_week)){
            $data_vencimento = date('Y-m-d', strtotime(date('d F Y', strtotime($data_aprovacao)) . " next Thursday"));
        }else{    
            $data_vencimento = date('Y-m-d', strtotime(date('d F Y', strtotime($data_aprovacao)) . " +1 week next Thursday"));
        }
        
        return $data_vencimento;
        
    }
    
    /**
     * @author  Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * @param   $xml => XML retornado pelo WS
     * @param   $mensagem => Descrição do motivo de gravação do log
     * @param   $motivo => Sigla do motivo de gravação do log
     * Método que grava na tabela log_solicita_viagem caso algo impeça o "caminho feliz"
     * da demanda
     */
    private function logFix($xml, $mensagem, $motivo) {

        if (!empty($xml)) {
            $solicitacao_result = $xml->recuperarSolicitacaoResponse->recuperarSolicitacaoResult;
        }

        $cpf = !empty($solicitacao_result->Viajante->CPF) ? $solicitacao_result->Viajante->CPF : '';
        $ret_cpf = $this->dao->getIdCpf($cpf);
        $valor_adiantamento = !empty($solicitacao_result->SolAdiantamento->AdiantamentoRS->Valor) ? $solicitacao_result->SolAdiantamento->AdiantamentoRS->Valor : 0;
        $valor_reembolso = !empty($solicitacao_result->SolReembolso->ReembolsoRS->Valor) ? $solicitacao_result->SolReembolso->ReembolsoRS->Valor : 0;
        $quantidade_reembolso = !empty($solicitacao_result->SolReembolso->ReembolsoRS->Quantidade) ? $solicitacao_result->SolReembolso->ReembolsoRS->Quantidade : 0;
        $codCentroCusto = !empty($solicitacao_result->SolRateio->RateioRS->CodCentroCusto) ? $solicitacao_result->SolRateio->RateioRS->CodCentroCusto : 0;
        $despesa = !empty($solicitacao_result->SolReembolso->ReembolsoRS->Despesa) ? $solicitacao_result->SolReembolso->ReembolsoRS->Despesa : 0;

        $lsvtipo_conta_pagar = empty($valor_reembolso) ? 31 : 'NULL';
        // 36 - Relatório de Viagem, 34 - Adiantamento para Viagem
        $lsvtipo_documento = empty($valor_reembolso) ? 34 : 36;
        $lsvvalor = empty($valor_reembolso) ? $valor_adiantamento : $valor_reembolso;
        $lsvvalor = empty($lsvvalor) ? 'NULL' : $lsvvalor;
        $lsvestabelecimento = !empty($solicitacao_result->SolReembolso->ReembolsoRS->Valor) ? 6 : 'NULL';
        $lsvgrupo_documento = !empty($solicitacao_result->SolReembolso->ReembolsoRS->Valor) ? 3 : 'NULL';
        $lsvtipo_movimentacao = !empty($solicitacao_result->SolReembolso->ReembolsoRS->Valor) ? 1 : 'NULL';
        
        if(!empty($solicitacao_result->SolReembolso->ReembolsoRS->Valor)) {
            $lsvparcela = !empty($solicitacao_result->SolAdiantamento->AdiantamentoRS->Valor) ? 2 : 1;
        } else {
            $lsvparcela = 'NULL';
        }
       
        $lsvcontra_partida = !empty($solicitacao_result->SolAdiantamento->AdiantamentoRS->Valor) ? 1965 : 2289;
        $cpf = empty($ret_cpf) ? 'NULL' : $ret_cpf;
        $codCentroCusto = empty($codCentroCusto) ? 'NULL' : $codCentroCusto;
        $quantidade_reembolso = empty($quantidade_reembolso) ? 'NULL' : $quantidade_reembolso;


        $lsvproduto = $this->getProduto($despesa);
        $lsvconta_contabil = $this->getPlanoContabil($despesa);

        $solicitacao_id = empty($solicitacao_result->SolicitacaoId) ? 0 : $solicitacao_result->SolicitacaoId;
        $numero_solic = empty($solicitacao_result->NroSolic) ? 0 : $solicitacao_result->NroSolic;

        $params = array(
            'id_solicitacao' => $solicitacao_id,
            'numero_solicitacao' => $numero_solic,
            'viajante_cpf' => $cpf,
            'centro_custo' => $codCentroCusto,
            'lsvtipo_conta_pagar' => $lsvtipo_conta_pagar,
            'lsvtipo_documento' => $lsvtipo_documento,
            'lsvvalor' => $lsvvalor,
            'lsvestabelecimento' => $lsvestabelecimento,
            'lsvgrupo_documento' => $lsvgrupo_documento,
            'lsvtipo_movimentacao' => $lsvtipo_movimentacao,
            'lsvparcela' => $lsvparcela,
            'lsvcontra_partida' => $lsvcontra_partida,
            'lsvproduto' => $lsvproduto,
            'lsvconta_contabil' => $lsvconta_contabil,
            'solReembolso_qtde' => $quantidade_reembolso
        );

        
        /*
         * Grava o log na base
         */
        $this->dao->saveLog($params, $mensagem, $motivo);
    }
    
    /**
     * @author  Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * @param   String  $solicitation_key => chave da solicitacao
     * @param   Integer $solicitation_number => numero da solicitacao     
     * Insere a solicitação que falhou na tabela log_solicita_viagem
     */
    public function saveLogWSRepuraSolicitacao($solicitation_key, $solicitation_number) {
        $this->dao->saveLogWSRepuraSolicitacao($solicitation_key, $solicitation_number, self::M1, 'WI');
        $this->sendEmailLog(self::M1);
    }


    /**
     * @author  Angelo Frizzo Júnior <angelo.frizzo@meta.com.br>
     * @param   $Produto => String contendo o tipo do produto     * 
     * Retorna os IDs de acordo com o tipo de produto
     */
    private function getProduto($Produto) {
        switch (trim(strtolower(strtr((utf8_decode($Produto)),"ÁÉÍÓÚÂÊÔÃÕÀÈÌÒÙÇ", "áéíóúâêôãõàèìòùç")))) {
            /*
        	case 'Hospedagem':
                $entiprdoid = 4440;
                break;
            case 'Lavanderia':
                $entiprdoid = 3638;
                break;
            case 'Passagens':
                $entiprdoid = 5719;
                break;
            case 'Kilometragem':
                $entiprdoid = 5842;
                break;
            */
            case 'taxi':
                $entiprdoid = 5683;
                break;
            case 'café da manhã':
                $entiprdoid = 1538;
                break;
            case 'almoço':
                $entiprdoid = 1538;
                break;
            case 'jantar':
                $entiprdoid = 1538;
                break;
            case 'estacionamento':
                $entiprdoid = 1535;
                break;
            case 'pedágio':
                $entiprdoid = 5670;
                break;
            case 'outras despesas':
                $entiprdoid = 2081;
                break;
            default:
                $entiprdoid = 2081;
        }
        return $entiprdoid;
    }
    
    /**
     * @author  Angelo Frizzo <angelo.frizzo@meta.com.br>
     * @param   $PlanoContabil => String contendo o plano contábil     * 
     * Retorna os IDs de acordo com o plano contábil
     */
    private function getPlanoContabil($PlanoContabil) {
        switch (trim(strtolower(strtr((utf8_decode($PlanoContabil)),"ÁÉÍÓÚÂÊÔÃÕÀÈÌÒÙÇ", "áéíóúâêôãõàèìòùç")))) {
        	/*
        	case 'Hospedagem':
                $entiplcoid = 2483;
                break;
            case 'Locomoção':
                $entiplcoid = 2484;
                break;
            case 'Passagens':
                $entiplcoid = 2485;
                break;
            case 'Refeições em viagem':
                $entiplcoid = 2486;
                break;
            */
            case 'taxi':
                $entiplcoid = 2484;
                break;
            case 'café da manhã':
                $entiplcoid = 2486;
                break;
            case 'almoço':
                $entiplcoid = 2486;
                break;
            case 'jantar':
                $entiplcoid = 2486;
                break;
            case 'estacionamento':
                $entiplcoid = 3362;
                break;
            case 'pedágio':
                $entiplcoid = 3363;
                break;
            case 'outras despesas':
                $entiplcoid = 2487;
                break;
            default:
                $entiplcoid = 2487;
        }
        return $entiplcoid;
    }
    
    /**
     * @author  Angelo Frizzo <angelo.frizzo@meta.com.br>
     * @param   String  $data => Data vencimento do primeiro titulo de reembolso 
     * Acrescenta 30 dias na data de vencimento do primeiro titulo de reembolso para definir data segundo título
     */
    private function soma30dias($data) {
		$data = explode("T",$data);
		$data = $data[0];
		$data = explode ("-",$data);
		var_dump($data);
		$dia = $data[2];
		$mes = $data[1];
		$ano = $data[0];
		$inicial = mktime(0,0,0,$mes,$dia,$ano);
		$final = $inicial + (30 * 86400);
		$final = strftime("%Y-%m-%d",$final);
    	return $final;
    }
    
    /**
     * @author  Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
     * @param   $message => String contendo a mensagem a set enviada aos responsáveis     
     */
    public function sendEmailLog($message) {

        //array com os destinatarios do email
        if ($_SESSION['servidor_teste'] == 1) {
            $lista_email = array("ricardo.mota@meta.com.br", "angelo.frizzo@meta.com.br","lucky.vigario@meta.com.br");
        } else {
            $lista_email = array("integracao_itm@sascar.com.br");
        }

        $mail = new PHPMailer();
        $mail->ClearAllRecipients();

        $mail->IsSMTP();
        $mail->From = "sistema@sascar.com.br";
        $mail->FromName = "Intranet SASCAR - E-mail automático";
        $mail->Subject = "[Alerta] Integração Master Viagem";

        $mail->MsgHTML("
                Data e hora da chamada - " . date('d/m/Y H:i:s') . "<br /><br />
                    
                <b>Descrição:</b><br />
                $message
                ");

        //adiciona os destinatarios
        foreach ($lista_email as $destinatarios) {
            $mail->AddAddress($destinatarios);
        }

        $mail->Send();
    }
    
    /**
     * @author  Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
     * @param   $options => Array com os dados de acesso e filros para o WS
     * Método que faz a chama para o WS da master viagem, através da extensão CURL
     * ele lista as solicitações a serem integradas posteriormente por outro método
     */
    public function listarSolicitacao(array $options) {

        $url = "https://www2.itm.tur.br/master/ws/interface2.asmx?wsdl";
        
        $statusViagem = !empty($options['statusViagem']) ? "<statusViagem>{$options['statusViagem']}</statusViagem>" : "";
        $dataFin = date('Y-m-d\TH:i:s');        
        
        echo "****** XML ENVIADO AO WS ******\n";        
        $body = '
           <soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:arg="http://www.argoit.com.br/">
            <soap:Header/>
            <soap:Body>
               <arg:listarSolicitacao>
                  <!--Optional:-->
                  <arg:login>' . _MASTERV_LOGIN_ . '</arg:login>
                  <!--Optional:-->
                  <arg:senha>' . _MASTERV_SENHA_ . '</arg:senha>
                  <!--Optional:-->
                  <arg:urlCliente>' . _MASTERV_URL_ . '</arg:urlCliente>
                  <!--Optional:-->
                  <arg:xml>
                     <xml>
                        <dataIni>' . $options['last_date'] . '</dataIni>
                        <dataFin>' . $dataFin . '</dataFin>
                        <tipoData>' . $options['filter'] . '</tipoData>
                        '.$statusViagem.'    
                    </xml>
                  </arg:xml>
               </arg:listarSolicitacao>
            </soap:Body>
         </soap:Envelope>';

        $headers = array(
            'Content-Type: text/xml; charset="utf-8"',
            'Content-Length: ' . strlen($body),
            'Accept: text/xml',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'SOAPAction: "http://www.argoit.com.br/listarSolicitacao"'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Stuff I have added
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        //curl_setopt($ch, CURLOPT_USERPWD, $credentials);

        $data = curl_exec($ch);
        /* header('Content-type: text/xml');
          echo $data;
          exit; */

        if (!$data) {
            echo curl_error($ch);
            return false;
        }
        
        curl_close($ch);

        $xml_obj = new SimpleXMLElement($data);

        $envelope = $xml_obj->xpath('//soap:Envelope');
        $envelope = reset($envelope);

        $body = $envelope->xpath('soap:Body');
        $body = reset($body);

        return json_decode(json_encode($body));
    }
    
    /**
     * @author  Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
     * @param   $options => Array com os dados de acesso e filros para o WS
     * Método que faz a chama para o WS da master viagem, através da extensão CURL
     * ele traz todo os dados a serem gravados da solicitação
     */
    public function recuperarSolicitacao(array $options) {
        $url = "https://www2.itm.tur.br/master/ws/interface2.asmx?wsdl";

        $body = '
           <soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:arg="http://www.argoit.com.br/">
            <soap:Header/>
            <soap:Body>
               <arg:recuperarSolicitacao>
                  <!--Optional:-->
                  <arg:login>' . _MASTERV_LOGIN_ . '</arg:login>
                  <!--Optional:-->
                  <arg:senha>' . _MASTERV_SENHA_ . '</arg:senha>
                  <!--Optional:-->
                  <arg:urlCliente>' . _MASTERV_URL_ . '</arg:urlCliente>
                  <!--Optional:-->
                  <!---Id da Solicitação-->
                  <arg:solicitacaoId>' . $options['solicitacaoId'] . '</arg:solicitacaoId> 
                  <!---Número da Solicitação-->
                  <arg:nroSolic>' . $options['nroSolic'] . '</arg:nroSolic> 
               </arg:recuperarSolicitacao>
            </soap:Body>
         </soap:Envelope>';

        $headers = array(
            'Content-Type: text/xml; charset="utf-8"',
            'Content-Length: ' . strlen($body),
            'Accept: text/xml',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'SOAPAction: "http://www.argoit.com.br/recuperarSolicitacao"'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Stuff I have added
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        //curl_setopt($ch, CURLOPT_USERPWD, $credentials);

        $data = curl_exec($ch);

        if (!$data) {
            return false;
        }

        $xml_obj = new SimpleXMLElement($data);

        $envelope = $xml_obj->xpath('//soap:Envelope');
        $envelope = reset($envelope);

        $body = $envelope->xpath('soap:Body');
        $body = reset($body);

        return json_decode(json_encode($body));
    }

    public function __construct() {
        $this->dao = new MasterViagemDAO();
    }

}