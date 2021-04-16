<?php

/*
 * Classe para envio de email
 */
include_once _SITEDIR_ . 'lib/phpMailer/class.phpmailer.php';
//require _SITEDIR_ . 'modulos/Financas/DAO/FinCreditoFuturoDAO.php';
 
/**
 * Classe de negócios para o Credito Futuro
 * 
 * @author Renato Teixeira Bueno
 * 
 */

class CreditoFuturo {
	
	/*
	 * objeto DAO
	 */
	private $dao;
	
	/*
	 * Objeto Email
	 */
	private $mail;
	
	/*
	 * Constantes dos status
	 */
	// const STATUS_APROVADO  = 1;
	// const STATUS_CONCLUIDO = 2;
	// const STATUS_PENDENTE  = 3;
	// const STATUS_REPROVADO = 4;

	public $STATUS_APROVADO  = 1;
	public $STATUS_CONCLUIDO = 2;
	public $STATUS_PENDENTE  = 3;
	public $STATUS_REPROVADO = 4;
	
	/*
	 * Constantes para motivo credito
	 */
	// const MOTIVO_CREDITO_OUTROS 				= 0;
	// const MOTIVO_CREDITO_CONTESTACAO 			= 1;
	// const MOTIVO_CREDITO_INDICACAO_AMIGO 		= 2;
	// const MOTIVO_CREDITO_ISENCAO_MONITORAMENTO 	= 3;
	// const MOTIVO_CREDITO_DEBITO_AUTOMATICO 		= 4;
	// const MOTIVO_CREDITO_CARTAO_CREDITO 		= 5;

	public $MOTIVO_CREDITO_OUTROS 				= 0;
	public $MOTIVO_CREDITO_CONTESTACAO 			= 1;
	public $MOTIVO_CREDITO_INDICACAO_AMIGO 		= 2;
	public $MOTIVO_CREDITO_ISENCAO_MONITORAMENTO 	= 3;
	public $MOTIVO_CREDITO_DEBITO_AUTOMATICO 		= 4;
	public $MOTIVO_CREDITO_CARTAO_CREDITO 		= 5;
	
	/*
	 * Constantes de forma de aplicacao
	 */
	// const APLICACAO_INTEGRAL  = 1;
	// const APLICACAO_PARCELADO = 2;

	public $APLICACAO_INTEGRAL  = 1;
	public $APLICACAO_PARCELADO = 2;
	
	/*
	 * Constantes de tipo de desconto
	 */
	// const DESCONTO_PERCENTUAL = 1;
	// const DESCONTO_VALOR = 2;

	public $DESCONTO_PERCENTUAL = 1;
	public $DESCONTO_VALOR = 2;
	
	/*
	 * Constantes de desconto aplicado sobre
	 */
	// const APLICADO_SOBRE_MONITORAMENTO 	= 1;
	// const APLICADO_SOBRE_LOCACAO 		= 2;

	public $APLICADO_SOBRE_MONITORAMENTO 	= 1;
	public $APLICADO_SOBRE_LOCACAO 		= 2;
	

	// const OPERACAO_INCLUSAO_CREDITO 	 = 1;
	// const OPERACAO_ALTERACAO_CREDITO 	 = 2;
	// const OPERACAO_EXCLUSAO_CREDITO 	 = 3;
	// const OPERACAO_ENCERRAMENTO_CREDITO  = 4;
	// const OPERACAO_APLICACAO_DESCONTO 	 = 5;
	// const OPERACAO_CANCELAMENTO_DESCONTO = 6;
	// const OPERACAO_APROVACAO_CREDITO 	 = 7;
	// const OPERACAO_REPROVACAO_DESCONTO 	 = 8;
	// const OPERACAO_DESCARTE_DESCONTO 	 = 9;
	// const OPERACAO_CANCELAMENTO_NF 	 	 = 10;

	public $OPERACAO_INCLUSAO_CREDITO 	 	= 1;
	public $OPERACAO_ALTERACAO_CREDITO 	 	= 2;
	public $OPERACAO_EXCLUSAO_CREDITO 	 	= 3;
	public $OPERACAO_ENCERRAMENTO_CREDITO  	= 4;
	public $OPERACAO_APLICACAO_DESCONTO 	= 5;
	public $OPERACAO_CANCELAMENTO_DESCONTO 	= 6;
	public $OPERACAO_APROVACAO_CREDITO 	 	= 7;
	public $OPERACAO_REPROVACAO_DESCONTO 	= 8;
	public $OPERACAO_DESCARTE_DESCONTO 	 	= 9;
	public $OPERACAO_CANCELAMENTO_NF 	 	= 10;

	 
	
	/**
	 * Metodo para incluir um Credito Fututo
	 * Metodo com as regras de negocio referente a inclusao
	 * 
	 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
	 * @param CreditoFuturoVO $CreditoFuturoVo
	 * @throws Exception
	 */
	public function incluir(CreditoFuturoVO $CreditoFuturoVo) {
		
			
		/*
		 * Valida as informaÃ§Ãµes obrigatorios por motivo de credito
		 */
		$CreditoFuturoVo->saldo = 'NULL';
		if ($CreditoFuturoVo->MotivoCredito->tipo == $this->MOTIVO_CREDITO_CONTESTACAO) {
			
			if (empty($CreditoFuturoVo->protocolo)) {
				throw new Exception('Necessário número do protocolo da contestação.');
			}

			$CreditoFuturoVo->tipoDesconto = $this->DESCONTO_VALOR;
		}
		
		if ($CreditoFuturoVo->MotivoCredito->tipo == $this->MOTIVO_CREDITO_INDICACAO_AMIGO) {
			
			if (empty($CreditoFuturoVo->contratoIndicado)) {
				throw new Exception('Necessário número do contrato indicado pelo cliente.');
			}
		}
		
		if ($CreditoFuturoVo->MotivoCredito->tipo == $this->MOTIVO_CREDITO_ISENCAO_MONITORAMENTO) {

            $CreditoFuturoVo->formaAplicacao = $this->APLICACAO_PARCELADO;
			$CreditoFuturoVo->tipoDesconto = $this->DESCONTO_PERCENTUAL;
            $CreditoFuturoVo->aplicarDescontoSobre = $this->APLICADO_SOBRE_MONITORAMENTO;
			$CreditoFuturoVo->valor = 100;
		}
			
		/*
		 * Aplicacao de forma INTEGRAL -> a quantidade de parcelas Ã© igual a 1
		 * Aplicacao  em PARCELAS -> a quantidade deverÃ¡ ser a  informada no campo  Qtd. Parcelas
		 */
		if ($CreditoFuturoVo->formaAplicacao == $this->APLICACAO_INTEGRAL) {
			$CreditoFuturoVo->qtdParcelas = 1;
		} 
		
		if ($CreditoFuturoVo->formaAplicacao == $this->APLICACAO_PARCELADO) {
			
			if ((int) $CreditoFuturoVo->qtdParcelas == 0) {
				throw new Exception ('Necessário informar o número de parcelas para aplicação Parcelado.');
			}
		}
        
        
        //verificação com a parametrização de credito futuro
        
        /*
		 * Busca os valores parametrizados para o credito futuro
		 */
		$parametrosCreditoFuturo = $this->dao->obterParametrosCreditoFuturo();
        
		if (!$parametrosCreditoFuturo) {
			throw new Exception('Parametros do crédito futuro não encontrados.');
		}
		

		/*
         * Verifica se os valores informados sao maiores que a cota configurada
         */
        $CreditoFuturoVo->status = $this->STATUS_APROVADO;

        //se tipo desconto for porcentagem
        if ($CreditoFuturoVo->tipoDesconto == '1') {
            
            if (floatval($CreditoFuturoVo->valor) > floatval($parametrosCreditoFuturo->porcentagem) ||
                    $CreditoFuturoVo->qtdParcelas > $parametrosCreditoFuturo->numeroparcelas) {

                $CreditoFuturoVo->status = $this->STATUS_PENDENTE;
            }
        } else {
            //se tipo desconto for valor

            $CreditoFuturoVo->saldo = ($CreditoFuturoVo->qtdParcelas * $CreditoFuturoVo->valor);

            if (floatval($CreditoFuturoVo->saldo) > floatval($parametrosCreditoFuturo->valorcredito) ||
                    $CreditoFuturoVo->qtdParcelas > $parametrosCreditoFuturo->numeroparcelas) {

                $CreditoFuturoVo->status = $this->STATUS_PENDENTE;
            }
        }
		
        	/*
			 * Apenas se for contestaÃ§Ã£o calcula o saldo
			 */
			


		/*
		 * ValidaÃ§Ãµes dos campos obrigatÃ³rios da tabela
		 */
		if (empty($CreditoFuturoVo->cliente)) {
			throw new Exception('Necessário informar o cliente.');
		}
		
		if (empty($CreditoFuturoVo->usuarioInclusao)) {
			throw new Exception('Usuário de inclusão não informado.');
		}
		
		if (empty($CreditoFuturoVo->MotivoCredito->id)) {
			throw new Exception('Necessário informar o motivo do crédito.');
		}
		
		if (empty($CreditoFuturoVo->status)) {
			throw new Exception('Status não informado.');
		}
        
		if (empty($CreditoFuturoVo->tipoDesconto)) {
			throw new Exception('Necessário informar tipo de desconto.');
		}
		
		if (empty($CreditoFuturoVo->formaAplicacao)) {
			throw new Exception('Necessário informar forma de aplicação.');
		}
		
		if (empty($CreditoFuturoVo->aplicarDescontoSobre)) {
			throw new Exception('Necessário informar tipo de desconto sobre.');
		}



		
		/*
		 * Salva o credito fututo (persistencia)
		 */
		$creditoFuturoId = $this->dao->salvar($CreditoFuturoVo);
		
		/*
		 * Insere as parcelas
		 * Mesmo que a forma de aplicação seja INTEGRAL haverÃ¡ pelo menos uma parcela
		 */
		for ($i = 1; $i <= $CreditoFuturoVo->qtdParcelas; $i++) {
				
			$this->dao->adicionarParcela($creditoFuturoId, $i , $CreditoFuturoVo->valor);
		}
		
        //se o status for pendente quer dizer que o valores informados são maiores que os parametrizados na configuração de
        //credito futuro.
		if ($CreditoFuturoVo->status == $this->STATUS_PENDENTE) {			
            //chamar método notificar Aprovador
            $this->notificarAprovador($CreditoFuturoVo, $parametrosCreditoFuturo);    
		}

		/*
		 * Historico do credito futuro
		 */
        
        $CreditoFuturoVo->id = $creditoFuturoId;

        $saldoParcelas = $this->dao->buscarParcelasCreditoFuturo($creditoFuturoId, $CreditoFuturoVo->formaAplicacao, $CreditoFuturoVo->tipoDesconto);
        
        $parametros = array (
                'usuarioInclusao' => $CreditoFuturoVo->usuarioInclusao,
                'operacao' => $this->OPERACAO_INCLUSAO_CREDITO,
                'origem' => $CreditoFuturoVo->origem,
                'creditoFuturoId' => $CreditoFuturoVo->id,
                'status' => $CreditoFuturoVo->status,
                'tipoDesconto' => $CreditoFuturoVo->tipoDesconto,
                'formaAplicacao' => $CreditoFuturoVo->formaAplicacao,
                'aplicarDescontoSobre' => $CreditoFuturoVo->aplicarDescontoSobre,
                'qtdParcelas' =>  $CreditoFuturoVo->qtdParcelas,
                'valor' => $CreditoFuturoVo->valor,
                'saldo' => $CreditoFuturoVo->saldo,
                'cfhsaldo_parcelas' => $saldoParcelas,
                'observacao' => $CreditoFuturoVo->observacao,
                'justificativa' => NULL,
                'obrigacaoFinanceiraDesconto' => $CreditoFuturoVo->obrigacaoFinanceiraDesconto,
                
                'nf_numero' => 'NULL',
                'nf_serie' => NULL,
                'dt_emissao_nf' => 'NULL',
                'valor_total_nf' => 'NULL',
                'vl_total_itens_nf' => 'NULL',
                'valor_aplicado_desconto' => 'NULL',
                'num_parcela_aplicada' => 'NULL',
            );
        
		$CreditoFuturoHistoricoVo = $this->prepararHistoricoVo($parametros);
        
		$this->dao->incluirHistoricoCreditoFuturo($CreditoFuturoHistoricoVo);
		
		/*
		 * Historico no cliente
		 * Prepara o texto da observacao
		 */
		$textoHistoticoCliente = $this->prepararTextoHistoricoCliente($CreditoFuturoVo, $this->OPERACAO_INCLUSAO_CREDITO, $creditoFuturoId, array());
		$this->dao->incluirHistoricoCliente($CreditoFuturoVo, $textoHistoticoCliente);
		
		/*
		 * Historico da contestacao
		*/
		if ($CreditoFuturoVo->MotivoCredito->tipo == $this->MOTIVO_CREDITO_CONTESTACAO) {
			
			$protocoloStatus = $this->dao->buscarStatusAnaliseContas($CreditoFuturoVo->protocolo);
		
			$this->dao->incluirHistoricoContestacao($textoHistoticoCliente, $CreditoFuturoVo->protocolo, $protocoloStatus, $CreditoFuturoVo->usuarioInclusao);
		}
		
		/*
		 * Historico no contrato indicador
		 */
		if ($CreditoFuturoVo->MotivoCredito->tipo == $this->MOTIVO_CREDITO_INDICACAO_AMIGO) {
				
			$cliente = $this->dao->obterDadosClientePorId($CreditoFuturoVo->cliente);
			
            
			if (count($cliente) > 0) {
				$numeroDocumento = $cliente['numerodocumento'];
				$nome 			 = $cliente['nome'];
				$textoHistoticoCliente .= "<br/>Cliente Indicador: $numeroDocumento $nome";
			}
			
            
			$this->dao->incluirHistoricoContratoIndicador($CreditoFuturoVo->contratoIndicado, $CreditoFuturoVo->usuarioInclusao, $textoHistoticoCliente);
		}
		
		return true;
		
	}
    
    /**
     * Metodo para alterar um Credito Fututo
     * Metodo com as regras de negocio referente a alteração
     * 
     * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
     * @param CreditoFuturoVO $CreditoFuturoVo
     * @throws Exception
     */
    public function alterar(CreditoFuturoVO $CreditoFuturoVo) {

        $dadosCreditoFuturo = (object) $this->dao->pesquisarPorID($CreditoFuturoVo->id);

        if (!count($dadosCreditoFuturo)) {
            throw new Exception('Falha ao carregar dados do crédito futuro.');
        }

        //monta o vo de motivo de credito com informações do credito futuro atual
        $CreditoFuturoVo->MotivoCredito->id = $dadosCreditoFuturo->cfocfmcoid;
        $CreditoFuturoVo->MotivoCredito->tipo = $dadosCreditoFuturo->tipo_motivo_credito;
        $CreditoFuturoVo->MotivoCredito->descricao = $dadosCreditoFuturo->motivo_credito_descricao;

        //preencho o objeto com informações já existentes do credito futuro
        $CreditoFuturoVo->cliente = $dadosCreditoFuturo->cfoclioid;
        $CreditoFuturoVo->formaInclusao = $dadosCreditoFuturo->cfoforma_inclusao;
        $CreditoFuturoVo->contratoIndicado = isset($dadosCreditoFuturo->cfoconnum_indicado) && !empty($dadosCreditoFuturo->cfoconnum_indicado) ? trim($dadosCreditoFuturo->cfoconnum_indicado) : 'NULL';
        $CreditoFuturoVo->protocolo = isset($dadosCreditoFuturo->cfoancoid) && !empty($dadosCreditoFuturo->cfoancoid) ? trim($dadosCreditoFuturo->cfoancoid) : 'NULL';

        /*
         * Valida as informaÃ§Ãµes obrigatorios por motivo de credito
         */
        $CreditoFuturoVo->saldo = 'NULL';
        if ($CreditoFuturoVo->MotivoCredito->tipo == $this->MOTIVO_CREDITO_CONTESTACAO) {

            /*
             * Apenas se for contestaÃ§Ã£o calcula o saldo
             */
            $CreditoFuturoVo->saldo = ($CreditoFuturoVo->qtdParcelas * $CreditoFuturoVo->valor);

            $CreditoFuturoVo->tipoDesconto = $this->DESCONTO_VALOR;
        }

        if ($CreditoFuturoVo->MotivoCredito->tipo == $this->MOTIVO_CREDITO_ISENCAO_MONITORAMENTO) {

            $CreditoFuturoVo->formaAplicacao = $this->APLICACAO_PARCELADO;
            $CreditoFuturoVo->tipoDesconto = $this->DESCONTO_PERCENTUAL;
            $CreditoFuturoVo->aplicarDescontoSobre = $this->APLICADO_SOBRE_MONITORAMENTO;
            $CreditoFuturoVo->valor = 100;
        }

        /*
         * Aplicacao de forma INTEGRAL -> a quantidade de parcelas Ã© igual a 1
         * Aplicacao  em PARCELAS -> a quantidade deverÃ¡ ser a  informada no campo  Qtd. Parcelas
         */
        if ($CreditoFuturoVo->formaAplicacao == $this->APLICACAO_INTEGRAL) {
            $CreditoFuturoVo->qtdParcelas = 1;
        }

        if ($CreditoFuturoVo->formaAplicacao == $this->APLICACAO_PARCELADO) {

            if ((int) $CreditoFuturoVo->qtdParcelas == 0) {
                throw new Exception('Necessário informar o número de parcelas para aplicação Parcelado.');
            }
        }


        //verificação com a parametrização de credito futuro

        /*
         * Busca os valores parametrizados para o credito futuro
         */
        $parametrosCreditoFuturo = $this->dao->obterParametrosCreditoFuturo();

        if (!$parametrosCreditoFuturo) {
            throw new Exception('Parametros do crédito futuro não encontrados.');
        }

        /*
         * Verifica se os valores informados sao maiores que a cota configurada
         */
        $CreditoFuturoVo->status = $this->STATUS_APROVADO;

        //se tipo desconto for porcentagem
        if ($CreditoFuturoVo->tipoDesconto == '1') {

            if (floatval($CreditoFuturoVo->valor) > floatval($parametrosCreditoFuturo->porcentagem) ||
                    $CreditoFuturoVo->qtdParcelas > $parametrosCreditoFuturo->numeroparcelas) {

                $CreditoFuturoVo->status = $this->STATUS_PENDENTE;
            }
        } else {
            //se tipo desconto for valor
        	$CreditoFuturoVo->saldo = ($CreditoFuturoVo->qtdParcelas * $CreditoFuturoVo->valor);

            if (floatval($CreditoFuturoVo->saldo) > floatval($parametrosCreditoFuturo->valorcredito) ||
                    $CreditoFuturoVo->qtdParcelas > $parametrosCreditoFuturo->numeroparcelas) {

                $CreditoFuturoVo->status = $this->STATUS_PENDENTE;
            }
        }

        /*
         * ValidaÃ§Ãµes dos campos obrigatÃ³rios da tabela
         */

        if (empty($CreditoFuturoVo->tipoDesconto)) {
            throw new Exception('Necessário informar tipo de desconto.');
        }

        if (empty($CreditoFuturoVo->formaAplicacao)) {
            throw new Exception('Necessário informar forma de aplicação.');
        }

        if (empty($CreditoFuturoVo->aplicarDescontoSobre)) {
            throw new Exception('Necessário informar tipo de desconto sobre.');
        }


        /*
         * Salva o credito fututo (persistencia)
         */
        $creditoFuturoAtualizado = $this->dao->atualizar($CreditoFuturoVo);

        if ($creditoFuturoAtualizado) {

            /*
             * Insere as parcelas
             * Mesmo que a forma de aplicação seja INTEGRAL haverÃ¡ pelo menos uma parcela
             */

            if ($this->dao->deletarParcelas($CreditoFuturoVo->id)) {
                for ($i = 1; $i <= $CreditoFuturoVo->qtdParcelas; $i++) {
                    $this->dao->adicionarParcela($CreditoFuturoVo->id, $i, $CreditoFuturoVo->valor);
                }
            }

            //se o status for pendente quer dizer que o valores informados são maiores que os parametrizados na configuração de
            //credito futuro.

            if ($CreditoFuturoVo->status == $this->STATUS_PENDENTE || ($CreditoFuturoVo->status == $this->STATUS_PENDENTE && $CreditoFuturoVo->valor != $dadosCreditoFuturo->valor)) {
                //chamar método notificar Aprovador
                $this->notificarAprovador($CreditoFuturoVo, $parametrosCreditoFuturo);
            }
                
            /*
             * Historico do credito futuro
             */     

            $saldoParcelas = $this->dao->buscarParcelasCreditoFuturo($CreditoFuturoVo->id, $CreditoFuturoVo->formaAplicacao, $CreditoFuturoVo->tipoDesconto);       
            
            $parametros = array (
                'usuarioInclusao' => $CreditoFuturoVo->usuarioInclusao,
                'operacao' => $this->OPERACAO_ALTERACAO_CREDITO,
                'origem' => $CreditoFuturoVo->origem,
                'creditoFuturoId' => $CreditoFuturoVo->id,
                'status' => $CreditoFuturoVo->status,
                'tipoDesconto' => $CreditoFuturoVo->tipoDesconto,
                'formaAplicacao' => $CreditoFuturoVo->formaAplicacao,
                'aplicarDescontoSobre' => $CreditoFuturoVo->aplicarDescontoSobre,
                'qtdParcelas' =>  $CreditoFuturoVo->qtdParcelas,
                'valor' => $CreditoFuturoVo->valor,
                'cfhsaldo_parcelas' => $saldoParcelas,
                'saldo' => $CreditoFuturoVo->saldo,
                'observacao' => $CreditoFuturoVo->observacao,
                'justificativa' => NULL,
                'obrigacaoFinanceiraDesconto' => $CreditoFuturoVo->obrigacaoFinanceiraDesconto,
                
                'nf_numero' => 'NULL',
                'nf_serie' => NULL,
                'dt_emissao_nf' => 'NULL',
                'valor_total_nf' => 'NULL',
                'vl_total_itens_nf' => 'NULL',
                'valor_aplicado_desconto' => 'NULL',
                'num_parcela_aplicada' => 'NULL',
            );
            
            $CreditoFuturoHistoricoVo = $this->prepararHistoricoVo($parametros);
            $this->dao->incluirHistoricoCreditoFuturo($CreditoFuturoHistoricoVo);


            /*
             * Historico no cliente
             * Prepara o texto da observacao
             */
            $textoHistoticoCliente = $this->prepararTextoHistoricoCliente($CreditoFuturoVo, $this->OPERACAO_ALTERACAO_CREDITO, $CreditoFuturoVo->id, array());
            $this->dao->incluirHistoricoCliente($CreditoFuturoVo, $textoHistoticoCliente);

            /*
             * Historico da contestacao
             */
            if ($CreditoFuturoVo->MotivoCredito->tipo == $this->MOTIVO_CREDITO_CONTESTACAO) {

                $protocoloStatus = $this->dao->buscarStatusAnaliseContas($CreditoFuturoVo->protocolo);

                $this->dao->incluirHistoricoContestacao($textoHistoticoCliente, $CreditoFuturoVo->protocolo, $protocoloStatus, $CreditoFuturoVo->usuarioInclusao);
            }

            /*
             * Historico no contrato indicador
             */
            if ($CreditoFuturoVo->MotivoCredito->tipo == $this->MOTIVO_CREDITO_INDICACAO_AMIGO) {

                $cliente = $this->dao->obterDadosClientePorId($CreditoFuturoVo->cliente);


                if (count($cliente) > 0) {
                    $numeroDocumento = $cliente['numerodocumento'];
                    $nome = $cliente['nome'];
                    $textoHistoticoCliente .= "<br/>Cliente Indicador: $numeroDocumento $nome";
                }


                $this->dao->incluirHistoricoContratoIndicador($CreditoFuturoVo->contratoIndicado, $CreditoFuturoVo->usuarioInclusao, $textoHistoticoCliente);
            }
            
            return true;
        } else {
            throw new Exception('Houve um erro no processamento dos dados.');
        }
    }
    
    
    public function excluir(CreditoFuturoVO $CreditoFuturoVo) {
        
        
        $dadosCreditoFuturo = $this->obterDadosCreditoFuturoPorId($CreditoFuturoVo->id);
        foreach ($dadosCreditoFuturo as $key => $value) {
        	$CreditoFuturoVo->$key = $dadosCreditoFuturo->$key;
        }

        $CreditoFuturoVo->usuarioInclusao = $CreditoFuturoVo->usuarioExclusao;
                
        
        if ($this->dao->excluir($CreditoFuturoVo)) {
            
             /*
             * Historico do credito futuro
             */ 

            $saldoParcelas = $this->dao->buscarParcelasCreditoFuturo($CreditoFuturoVo->id, $CreditoFuturoVo->formaAplicacao, $CreditoFuturoVo->tipoDesconto);       
                        
            $parametros = array (
                'usuarioInclusao' => $CreditoFuturoVo->usuarioExclusao,
                'operacao' => $this->OPERACAO_EXCLUSAO_CREDITO,
                'origem' => $CreditoFuturoVo->origem,
                'creditoFuturoId' => $CreditoFuturoVo->id,
                'status' => $CreditoFuturoVo->status,
                'tipoDesconto' => $CreditoFuturoVo->tipoDesconto,
                'formaAplicacao' => $CreditoFuturoVo->formaAplicacao,
                'aplicarDescontoSobre' => $CreditoFuturoVo->aplicarDescontoSobre,
                'qtdParcelas' =>  $CreditoFuturoVo->qtdParcelas,
                'valor' => $CreditoFuturoVo->valor,
                'saldo' => $CreditoFuturoVo->saldo,
                'cfhsaldo_parcelas' => $saldoParcelas,
                'observacao' => NULL,
                'justificativa' => $CreditoFuturoVo->observacao,
                'obrigacaoFinanceiraDesconto' => $CreditoFuturoVo->obrigacaoFinanceiraDesconto,
                
                'nf_numero' => 'NULL',
                'nf_serie' => NULL,
                'dt_emissao_nf' => 'NULL',
                'valor_total_nf' => 'NULL',
                'vl_total_itens_nf' => 'NULL',
                'valor_aplicado_desconto' => 'NULL',
                'num_parcela_aplicada' => 'NULL',
            );
            
            $CreditoFuturoHistoricoVo = $this->prepararHistoricoVo($parametros);
            $this->dao->incluirHistoricoCreditoFuturo($CreditoFuturoHistoricoVo);
            
            /*
             * Historico no cliente
             * Prepara o texto da observacao
             */
            $textoHistoticoCliente = $this->prepararTextoHistoricoCliente($CreditoFuturoVo, $this->OPERACAO_EXCLUSAO_CREDITO, $CreditoFuturoVo->id, array());
            $this->dao->incluirHistoricoCliente($CreditoFuturoVo, $textoHistoticoCliente);

            /*
             * Historico da contestacao
             */
            if ($CreditoFuturoVo->MotivoCredito->tipo == $this->MOTIVO_CREDITO_CONTESTACAO) {

                $protocoloStatus = $this->dao->buscarStatusAnaliseContas($CreditoFuturoVo->protocolo);

                $this->dao->incluirHistoricoContestacao($textoHistoticoCliente, $CreditoFuturoVo->protocolo, $protocoloStatus, $CreditoFuturoVo->usuarioInclusao);
            }

            /*
             * Historico no contrato indicador
             */
            if ($CreditoFuturoVo->MotivoCredito->tipo == $this->MOTIVO_CREDITO_INDICACAO_AMIGO) {

                $cliente = $this->dao->obterDadosClientePorId($CreditoFuturoVo->cliente);


                if (count($cliente) > 0) {
                    $numeroDocumento = $cliente['numerodocumento'];
                    $nome = $cliente['nome'];
                    $textoHistoticoCliente .= "<br/>Cliente Indicador: $numeroDocumento $nome";
                }


                $this->dao->incluirHistoricoContratoIndicador($CreditoFuturoVo->contratoIndicado, $CreditoFuturoVo->usuarioInclusao, $textoHistoticoCliente);
            }
            
            return true;
            
        } else {
            throw new Exception('Houve um erro no processamento dos dados.');
        }
        
    }
    
    
    public function encerrar(CreditoFuturoVO $CreditoFuturoVo) {
        
        
        $dadosCreditoFuturo = $this->obterDadosCreditoFuturoPorId($CreditoFuturoVo->id);
        foreach ($dadosCreditoFuturo as $key => $value) {
        	$CreditoFuturoVo->$key = $dadosCreditoFuturo->$key;
        }

        $CreditoFuturoVo->usuarioInclusao = $CreditoFuturoVo->usuarioEncerramento;
        
        //encerramento status é dado como concluido
        $CreditoFuturoVo->status = '2';
        
        if ($this->dao->encerrar($CreditoFuturoVo)) {
            
             /*
             * Historico do credito futuro
             */             
            $saldoParcelas = $this->dao->buscarParcelasCreditoFuturo($CreditoFuturoVo->id, $CreditoFuturoVo->formaAplicacao, $CreditoFuturoVo->tipoDesconto);       

            $parametros = array (
                'usuarioInclusao' => $CreditoFuturoVo->usuarioEncerramento,
                'operacao' => $this->OPERACAO_ENCERRAMENTO_CREDITO,
                'origem' => $CreditoFuturoVo->origem,
                'creditoFuturoId' => $CreditoFuturoVo->id,
                'status' => $CreditoFuturoVo->status,
                'tipoDesconto' => $CreditoFuturoVo->tipoDesconto,
                'formaAplicacao' => $CreditoFuturoVo->formaAplicacao,
                'aplicarDescontoSobre' => $CreditoFuturoVo->aplicarDescontoSobre,
                'qtdParcelas' =>  $CreditoFuturoVo->qtdParcelas,
                'valor' => $CreditoFuturoVo->valor,
                'saldo' => $CreditoFuturoVo->saldo,
                'cfhsaldo_parcelas' => $saldoParcelas,
                'observacao' => NULL,
                'justificativa' => $CreditoFuturoVo->observacao,
                'obrigacaoFinanceiraDesconto' => $CreditoFuturoVo->obrigacaoFinanceiraDesconto,
                
                'nf_numero' => 'NULL',
                'nf_serie' => NULL,
                'dt_emissao_nf' => 'NULL',
                'valor_total_nf' => 'NULL',
                'vl_total_itens_nf' => 'NULL',
                'valor_aplicado_desconto' => 'NULL',
                'num_parcela_aplicada' => 'NULL',
            );
            
            
            $CreditoFuturoHistoricoVo = $this->prepararHistoricoVo($parametros);
            $this->dao->incluirHistoricoCreditoFuturo($CreditoFuturoHistoricoVo);


            /*
             * Historico no cliente
             * Prepara o texto da observacao
             */
            $textoHistoticoCliente = $this->prepararTextoHistoricoCliente($CreditoFuturoVo, $this->OPERACAO_ENCERRAMENTO_CREDITO, $CreditoFuturoVo->id, array());
            $this->dao->incluirHistoricoCliente($CreditoFuturoVo, $textoHistoticoCliente);

            /*
             * Historico da contestacao
             */
            if ($CreditoFuturoVo->MotivoCredito->tipo == $this->MOTIVO_CREDITO_CONTESTACAO) {

                $protocoloStatus = $this->dao->buscarStatusAnaliseContas($CreditoFuturoVo->protocolo);

                $this->dao->incluirHistoricoContestacao($textoHistoticoCliente, $CreditoFuturoVo->protocolo, $protocoloStatus, $CreditoFuturoVo->usuarioInclusao);
            }

            /*
             * Historico no contrato indicador
             */
            if ($CreditoFuturoVo->MotivoCredito->tipo == $this->MOTIVO_CREDITO_INDICACAO_AMIGO) {

                $cliente = $this->dao->obterDadosClientePorId($CreditoFuturoVo->cliente);


                if (count($cliente) > 0) {
                    $numeroDocumento = $cliente['numerodocumento'];
                    $nome = $cliente['nome'];
                    $textoHistoticoCliente .= "<br/>Cliente Indicador: $numeroDocumento $nome";
                }


                $this->dao->incluirHistoricoContratoIndicador($CreditoFuturoVo->contratoIndicado, $CreditoFuturoVo->usuarioInclusao, $textoHistoticoCliente);
            }
            
            return true;
            
        } else {
            throw new Exception('Houve um erro no processamento dos dados.');
        }
        
    }
    
    
    //aprovar
    public function aprovar(CreditoFuturoVO $CreditoFuturoVo) {
        
        $dadosCreditoFuturo = $this->obterDadosCreditoFuturoPorId($CreditoFuturoVo->id);
        foreach ($dadosCreditoFuturo as $key => $value) {
        	$CreditoFuturoVo->$key = $dadosCreditoFuturo->$key;
        }

        $CreditoFuturoVo->usuarioInclusao = $CreditoFuturoVo->usuarioAvaliador;
        
        //encerramento status é dado como concluido
        $CreditoFuturoVo->status = '1';

        if ($this->dao->aprovar($CreditoFuturoVo)) {
            
             /*
             * Historico do credito futuro
             */      
            $saldoParcelas = $this->dao->buscarParcelasCreditoFuturo($CreditoFuturoVo->id, $CreditoFuturoVo->formaAplicacao, $CreditoFuturoVo->tipoDesconto);       

            $parametros = array (
                'usuarioInclusao' => $CreditoFuturoVo->usuarioAvaliador,
                'operacao' => $this->OPERACAO_APROVACAO_CREDITO,
                'origem' => $CreditoFuturoVo->origem,
                'creditoFuturoId' => $CreditoFuturoVo->id,
                'status' => $CreditoFuturoVo->status,
                'tipoDesconto' => $CreditoFuturoVo->tipoDesconto,
                'formaAplicacao' => $CreditoFuturoVo->formaAplicacao,
                'aplicarDescontoSobre' => $CreditoFuturoVo->aplicarDescontoSobre,
                'qtdParcelas' =>  $CreditoFuturoVo->qtdParcelas,
                'valor' => $CreditoFuturoVo->valor,
                'saldo' => $CreditoFuturoVo->saldo,
                'cfhsaldo_parcelas' => $saldoParcelas,
                'observacao' => NULL,
                'justificativa' => NULL,
                'obrigacaoFinanceiraDesconto' => $CreditoFuturoVo->obrigacaoFinanceiraDesconto,
                
                'nf_numero' => 'NULL',
                'nf_serie' => NULL,
                'dt_emissao_nf' => 'NULL',
                'valor_total_nf' => 'NULL',
                'vl_total_itens_nf' => 'NULL',
                'valor_aplicado_desconto' => 'NULL',
                'num_parcela_aplicada' => 'NULL',
            );
            
            
            $CreditoFuturoHistoricoVo = $this->prepararHistoricoVo($parametros);
            $this->dao->incluirHistoricoCreditoFuturo($CreditoFuturoHistoricoVo);


            /*
             * Historico no cliente
             * Prepara o texto da observacao
             */
            $textoHistoticoCliente = $this->prepararTextoHistoricoCliente($CreditoFuturoVo, $this->OPERACAO_APROVACAO_CREDITO, $CreditoFuturoVo->id, array());
            $this->dao->incluirHistoricoCliente($CreditoFuturoVo, $textoHistoticoCliente);

            /*
             * Historico da contestacao
             */
            if ($CreditoFuturoVo->MotivoCredito->tipo == $this->MOTIVO_CREDITO_CONTESTACAO) {

                $protocoloStatus = $this->dao->buscarStatusAnaliseContas($CreditoFuturoVo->protocolo);

                $this->dao->incluirHistoricoContestacao($textoHistoticoCliente, $CreditoFuturoVo->protocolo, $protocoloStatus, $CreditoFuturoVo->usuarioInclusao);
            }

            /*
             * Historico no contrato indicador
             */
            if ($CreditoFuturoVo->MotivoCredito->tipo == $this->MOTIVO_CREDITO_INDICACAO_AMIGO) {

                $cliente = $this->dao->obterDadosClientePorId($CreditoFuturoVo->cliente);


                if (count($cliente) > 0) {
                    $numeroDocumento = $cliente['numerodocumento'];
                    $nome = $cliente['nome'];
                    $textoHistoticoCliente .= "<br/>Cliente Indicador: $numeroDocumento $nome";
                }


                $this->dao->incluirHistoricoContratoIndicador($CreditoFuturoVo->contratoIndicado, $CreditoFuturoVo->usuarioInclusao, $textoHistoticoCliente);
            }
            
            return true;
            
        } else {
        	//echo "caiu execessao nao salvo";
           throw new Exception('Houve um erro no processamento dos dados.');
        }
        
    }

    public function reprovar(CreditoFuturoVO $CreditoFuturoVo) {

    	$dadosCreditoFuturo = $this->obterDadosCreditoFuturoPorId($CreditoFuturoVo->id);
        foreach ($dadosCreditoFuturo as $key => $value) {
        	$CreditoFuturoVo->$key = $dadosCreditoFuturo->$key;
        }

        $CreditoFuturoVo->usuarioInclusao = $CreditoFuturoVo->usuarioAvaliador;
        
        $CreditoFuturoVo->status = '4';
        
        if ($this->dao->reprovar($CreditoFuturoVo)) {
            
             /*
             * Historico do credito futuro
             */
            $saldoParcelas = $this->dao->buscarParcelasCreditoFuturo($CreditoFuturoVo->id, $CreditoFuturoVo->formaAplicacao, $CreditoFuturoVo->tipoDesconto);       
                           
            $parametros = array (
                'usuarioInclusao' => $CreditoFuturoVo->usuarioAvaliador,
                'operacao' => $this->OPERACAO_REPROVACAO_DESCONTO,
                'origem' => $CreditoFuturoVo->origem,
                'creditoFuturoId' => $CreditoFuturoVo->id,
                'status' => $CreditoFuturoVo->status,
                'tipoDesconto' => $CreditoFuturoVo->tipoDesconto,
                'formaAplicacao' => $CreditoFuturoVo->formaAplicacao,
                'aplicarDescontoSobre' => $CreditoFuturoVo->aplicarDescontoSobre,
                'qtdParcelas' =>  $CreditoFuturoVo->qtdParcelas,
                'valor' => $CreditoFuturoVo->valor,
                'saldo' => $CreditoFuturoVo->saldo,
                'cfhsaldo_parcelas' => $saldoParcelas,
                'observacao' => NULL,
                'justificativa' => $CreditoFuturoVo->observacao,
                'obrigacaoFinanceiraDesconto' => $CreditoFuturoVo->obrigacaoFinanceiraDesconto,
                
                'nf_numero' => 'NULL',
                'nf_serie' => NULL,
                'dt_emissao_nf' => 'NULL',
                'valor_total_nf' => 'NULL',
                'vl_total_itens_nf' => 'NULL',
                'valor_aplicado_desconto' => 'NULL',
                'num_parcela_aplicada' => 'NULL',
            );
            
            
            $CreditoFuturoHistoricoVo = $this->prepararHistoricoVo($parametros);
            $this->dao->incluirHistoricoCreditoFuturo($CreditoFuturoHistoricoVo);


            /*
             * Historico no cliente
             * Prepara o texto da observacao
             */
            $textoHistoticoCliente = $this->prepararTextoHistoricoCliente($CreditoFuturoVo, $this->OPERACAO_REPROVACAO_DESCONTO, $CreditoFuturoVo->id, array());
            $this->dao->incluirHistoricoCliente($CreditoFuturoVo, $textoHistoticoCliente);

            /*
             * Historico da contestacao
             */
            if ($CreditoFuturoVo->MotivoCredito->tipo == $this->MOTIVO_CREDITO_CONTESTACAO) {

                $protocoloStatus = $this->dao->buscarStatusAnaliseContas($CreditoFuturoVo->protocolo);

                $this->dao->incluirHistoricoContestacao($textoHistoticoCliente, $CreditoFuturoVo->protocolo, $protocoloStatus, $CreditoFuturoVo->usuarioInclusao);
            }

            /*
             * Historico no contrato indicador
             */
            if ($CreditoFuturoVo->MotivoCredito->tipo == $this->MOTIVO_CREDITO_INDICACAO_AMIGO) {

                $cliente = $this->dao->obterDadosClientePorId($CreditoFuturoVo->cliente);


                if (count($cliente) > 0) {
                    $numeroDocumento = $cliente['numerodocumento'];
                    $nome = $cliente['nome'];
                    $textoHistoticoCliente .= "<br/>Cliente Indicador: $numeroDocumento $nome";
                }


                $this->dao->incluirHistoricoContratoIndicador($CreditoFuturoVo->contratoIndicado, $CreditoFuturoVo->usuarioInclusao, $textoHistoticoCliente);
            }

            $this->notificarReprovacao($CreditoFuturoVo->id, $CreditoFuturoVo->observacao);
            
            return true;
            
        } else {
            throw new Exception('Houve um erro no processamento dos dados.');
        }
        
    }

	
	/**
	 * Metodo para preencher o VO do historico
	 * 
	 * @param array cujas chaves sÃ£o os atributos do VO de histórico
	 * @return object CreditoFuturoHistoricoVO
	 */
	public function prepararHistoricoVo($params) {
		
		$CreditoFututoHistoricoVO = new CreditoFuturoHistoricoVO();
        
		foreach ($params as $key => $value) {
			
			if ($key == 'usuarioInclusao') {
				
				if (empty($params[$key])) {
					throw new Exception('Necessário informar usuário de inclusão par ao histórico.');
				}
			}
            
            if ($key == 'status') {
				
				if (empty($params[$key])) {
					throw new Exception('Necessário informar status do crédito futuro.');
				}
			}
            
            if ($key == 'tipoDesconto') {
				
				if (empty($params[$key])) {
					throw new Exception('Necessário informar tipo de desconto do crédito futuro.');
				}
			}
            
            if ($key == 'formaAplicacao') {
				
				if (empty($params[$key])) {
					throw new Exception('Necessário informar forma de aplicação do crédito futuro.');
				}
			}
            
            if ($key == 'aplicarDescontoSobre') {
				
				if (empty($params[$key])) {
					throw new Exception('Necessário informar aplicação de desconto do crédito futuro.');
				}
			}
            
   //          if ($key == 'qtdParcelas') {
				
			// 	if (empty($params[$key])) {
			// 		throw new Exception('Necessário informar quantidade de parcelas do crédito futuro.');
			// 	}
			// }
            
			if ($key == 'operacao') {
			
				if (empty($params[$key])) {
					throw new Exception('Necessário informar operação para o histórico.');
				}
			}
			
			if ($key == 'origem') {
					
				if (empty($params[$key])) {
					throw new Exception('Necessário informar a origem da funcionalidade para o histórico.');
				}
			}
			
            
            if ($key == 'valor') {
					
				if (empty($params[$key])) {
					throw new Exception('Necessário informar o valor do crédito futuro para o histórico.');
				}
			}
            
			if ($key == 'cliente') {
					
				if (empty($params[$key])) {
					throw new Exception('Necessário informar o cliente para o histórico.');
				}
			}
			
			if ($key == 'creditoFuturoId') {
					
				if (empty($params[$key])) {
					throw new Exception('Necessário informar o crédito futuro inserido para o histórico.');
				}
			}
			
			$CreditoFututoHistoricoVO->$key = isset($params[$key]) ? $params[$key] : '';
		}
		
		return $CreditoFututoHistoricoVO;
	}
	
	/**
	 * Metodo para preparar o texto que sera incluido no historico do cliente
	 * 
	 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
	 * @param object CreditoFuturoVO
	 * @param int operacao [1 - Inclusao, 2 - Alteracao, 3 - Exclusao, 4 - Encerramento]
	 * @return string texto
	 */
	public function prepararTextoHistoricoCliente($CreditoFuturo, $operacao, $creditoFuturoId, $dadosNotaFiscal) {
		

		switch ($operacao) {
            case $this->OPERACAO_INCLUSAO_CREDITO: 
            		$acao = 'Inclusão de crédito';
                break;
            case $this->OPERACAO_ALTERACAO_CREDITO: 
            		$acao = 'Alteração do crédito';
                break;
            case $this->OPERACAO_EXCLUSAO_CREDITO: 
            		$acao = 'Exclusão do crédito';
                break;
            case $this->OPERACAO_ENCERRAMENTO_CREDITO: 
            		$acao = 'Encerramento do crédito';
                break;
            case $this->OPERACAO_APLICACAO_DESCONTO: 
            		$acao = 'Desconto concedido';
                break;
            case $this->OPERACAO_CANCELAMENTO_DESCONTO: 
            		$acao = 'Desconto cancelado';
                break;
            case $this->OPERACAO_APROVACAO_CREDITO: 
            		$acao = 'Credito Aprovado';
                break;
            case $this->OPERACAO_REPROVACAO_DESCONTO: 
            		$acao = 'Credito Reprovado';
                break;
            case $this->OPERACAO_DESCARTE_DESCONTO: 
            		$acao = 'Desconto descartado';
                break;
            case $this->OPERACAO_CANCELAMENTO_NF: 
            		$acao = 'Cancelamento de NF';
                break;
        }

        switch ($CreditoFuturo->status) {
            case $this->STATUS_APROVADO: $status = 'Aprovado';
                break;
            case $this->STATUS_CONCLUIDO: $status = 'Concluído';
                break;
            case $this->STATUS_PENDENTE: $status = 'Pendente';
                break;
            case $this->STATUS_REPROVADO: $status = 'Reprovado';
                break;
        }

       //exit;

		$tipoDesconto = ($CreditoFuturo->tipoDesconto == $this->DESCONTO_PERCENTUAL) ? 'Percentual' : 'Valor';
		
		$aplicacao = ($CreditoFuturo->formaAplicacao == $this->APLICACAO_INTEGRAL) ? 'Integral' : 'Parcelas' . ' - Qtde. Parcelas: ' . $CreditoFuturo->qtdParcelas . '';
		
		$sobre = ($CreditoFuturo->aplicarDescontoSobre == $this->APLICADO_SOBRE_LOCACAO) ? 'Locação' : 'Monitoramento';
		
		$descricaoObrigacaoFinanceira = $this->dao->obterDescricaoObrigacaoFinanceiraPorId($CreditoFuturo->obrigacaoFinanceiraDesconto);
	
        $valor =  '';
        if (trim($CreditoFuturo->valor) != '') {

        	if ($CreditoFuturo->tipoDesconto == $this->DESCONTO_PERCENTUAL) {
        		$valor = number_format($CreditoFuturo->valor, 2, ',', '.') .  ' %';
        	} else {
        		$valor = 'R$ ' . number_format($CreditoFuturo->valor, 2, ',', '.');
        	}
            
        } else {
            $valor =  '';
        }

       if ($operacao == $this->OPERACAO_APROVACAO_CREDITO) {
       		$texto = "Crédito futuro com Cód. de Identificação " . $creditoFuturoId . " foi aprovado.";

       		$campanha = $this->dao->retornaTipoCampanhaPromocional($CreditoFuturo->CampanhaPromocional->cfcpoid);
       			
       		if (strlen($campanha) > 0) {
       			$texto .= "Campanha: " . $campanha . "<br/>";
       		}
       		$texto .= "Motivo do Crédito: " . $CreditoFuturo->MotivoCredito->descricao . "<br/>";
       		$texto .= "Tipo Desconto: " . $tipoDesconto . "<br/>";
       		$texto .= "Valor: " . $valor . "<br/>";
       		$texto .= "Aplicação : " . $aplicacao . "<br/>";
       		$texto .= "Aplicado Sobre: " . $sobre . "<br/>";
       		$texto .= "Obrig. Financ. Descto: " . $descricaoObrigacaoFinanceira .  "<br/>";
       		$texto .= "Status: " . $status . "<br/>";
       		
       } elseif ($operacao == $this->OPERACAO_REPROVACAO_DESCONTO) {
       		$texto = "Crédito futuro com Cód. de Identificação " . $creditoFuturoId . " foi recusado porque " . $CreditoFuturo->observacao . ".";

       		$campanha = $this->dao->retornaTipoCampanhaPromocional($CreditoFuturo->CampanhaPromocional->cfcpoid);
       			
       		if (strlen($campanha) > 0) {
       			$texto .= "Campanha: " . $campanha . "<br/>";
       		}
       		$texto .= "Motivo do Crédito: " . $CreditoFuturo->MotivoCredito->descricao . "<br/>";
       		$texto .= "Tipo Desconto: " . $tipoDesconto . "<br/>";
       		$texto .= "Valor: " . $valor . "<br/>";
       		$texto .= "Aplicação : " . $aplicacao . "<br/>";
       		$texto .= "Aplicado Sobre: " . $sobre . "<br/>";
       		$texto .= "Obrig. Financ. Descto: " . $descricaoObrigacaoFinanceira .  "<br/>";
       		$texto .= "Status: " . $status . "<br/>";

       } elseif ($operacao == $this->OPERACAO_APLICACAO_DESCONTO) {   		


       		if (count($dadosNotaFiscal)>0) {
				$texto = $acao . ": <br/>Nota Fiscal " . $dadosNotaFiscal['nflno_numero'] . "/" . $dadosNotaFiscal['nflserie'] . " emitida em " . date('d/m/Y') . "<br/>";
				$texto .= "Crédito Futuro  - Cód. Identif.: " . $creditoFuturoId . "<br/>";
			} else {
				$texto = $acao . ": Crédito Futuro <br/>";
				$texto .= "Cód. Identif.: " . $creditoFuturoId . "<br/>";
			}

			if ( isset($CreditoFuturo->protocolo) && trim($CreditoFuturo->protocolo) != '' && $CreditoFuturo->MotivoCredito->tipo == '1' ) {
				$texto .= "Protocolo: " . $CreditoFuturo->protocolo . "<br/>";
			}

			if ( isset($CreditoFuturo->contratoIndicado) && trim($CreditoFuturo->contratoIndicado) != '' && $CreditoFuturo->MotivoCredito->tipo == '2' ) {
				$texto .= "Contrato Indicado: " . $CreditoFuturo->contratoIndicado . "<br/>";
			}
			
       		$campanha = $this->dao->retornaTipoCampanhaPromocional($CreditoFuturo->CampanhaPromocional->cfcpoid);       		
			
			if (strlen($campanha) > 0) {
				$texto .= "Campanha: " . $campanha . "<br/>";
			}
			$texto .= "Motivo do Crédito: " . $CreditoFuturo->MotivoCredito->descricao . "<br/>";
			$texto .= "Tipo Desconto: " . $tipoDesconto . "<br/>";
			$texto .= "Valor: " . $valor . "<br/>";
			$texto .= "Aplicação : " . $aplicacao . "<br/>";
			$texto .= "Aplicado Sobre: " . $sobre . "<br/>";
			$texto .= "Obrig. Financ. Descto: " . $descricaoObrigacaoFinanceira .  "<br/>";
			$texto .= "Status: " . $status . "<br/>";
			$texto .= "Valor do Desconto Concedido: R$ " . number_format($CreditoFuturo->valor_do_desconto, 2 , ',', '.');		
			

       } else {
			$texto = $acao . ": Crédito Futuro <br/>";
			$texto .= "Cód. Identif.: " . $creditoFuturoId . "<br/>";

			if ( isset($CreditoFuturo->protocolo) && trim($CreditoFuturo->protocolo) != '' && $CreditoFuturo->MotivoCredito->tipo == '1' ) {
				$texto .= "Protocolo: " . $CreditoFuturo->protocolo . "<br/>";
			}

			if ( isset($CreditoFuturo->contratoIndicado) && trim($CreditoFuturo->contratoIndicado) != '' && $CreditoFuturo->MotivoCredito->tipo == '2' ) {
				$texto .= "Contrato Indicado: " . $CreditoFuturo->contratoIndicado . "<br/>";
			}
			
			$campanha = $this->dao->retornaTipoCampanhaPromocional($CreditoFuturo->CampanhaPromocional->cfcpoid);			
			
			if (strlen($campanha) > 0) {
				$texto .= "Campanha: " . $campanha . "<br/>";
			}
			
			$texto .= "Motivo do Crédito: " . $CreditoFuturo->MotivoCredito->descricao . "<br/>";
			$texto .= "Tipo Desconto: " . $tipoDesconto . "<br/>";
			$texto .= "Valor: " . $valor . "<br/>";
			$texto .= "Aplicação : " . $aplicacao . "<br/>";
			$texto .= "Aplicado Sobre: " . $sobre . "<br/>";
			$texto .= "Obrig. Financ. Descto: " . $descricaoObrigacaoFinanceira .  "<br/>";
			$texto .= "Status: " . $status . "";

       } 
       
       return $texto;
	}
    

	public function notificarReprovacao($CreditoFuturoID,$justificativa) {

		$dadosCreditoFuturo = (object) $this->dao->pesquisarPorID($CreditoFuturoID);
		$usuarioAvaliador = $this->dao->buscarUsuarioPorId($dadosCreditoFuturo->cfousuoid_avaliador);
		$usuarioInclusao = $this->dao->buscarUsuarioPorId($dadosCreditoFuturo->cfousuoid_inclusao);

		$emailNotificacao->assuntoemail = "Crédito futuro recusado.";
		$emailNotificacao->corpoemail = "Prezado,<br/>";
		$emailNotificacao->corpoemail .= "Favor verificar o crédito futuro incluído em " . date('d/m/Y', strtotime($dadosCreditoFuturo->cfodt_inclusao)) . " com cód.identif.: número " . $dadosCreditoFuturo->cfooid . " pois foi recusado em " . date('d/m/Y', strtotime($dadosCreditoFuturo->cfodt_avaliacao)) . "  ás " . date('H:i:s', strtotime($dadosCreditoFuturo->cfodt_avaliacao)) . " pelo motivo " . $justificativa . ".<br/>";
		$emailNotificacao->corpoemail .= "Att.<br/>";
		$emailNotificacao->corpoemail .= $usuarioAvaliador->usuario_nome;


		$destinatario = $usuarioInclusao->usuario_email;

		if (trim($destinatario) ==  '') {
			$mensagem['tipo'] = 'alerta';
			$mensagem['mensagem'] = 'Usuário não possui e-mail cadastrado, favor entrar em contato com o responsável pelo departamento ' . $usuarioInclusao->usuario_departamento . '.';
			$_SESSION['flash_message']['multiplo'][] = $mensagem;
			return false;
		}

		$notificado = $this->enviarEmail($destinatario, $emailNotificacao);

		if ($notificado === false) {
			$mensagem['tipo'] = 'erro';
			$mensagem['mensagem'] = 'Houve um erro no envio do e-mail.';
			$_SESSION['flash_message']['multiplo'][] = $mensagem;
			return false;
		}

		return true;
	}


    public function notificarAprovador($CreditoFuturoVo, $parametrosCreditoFuturo) {

        $percentual = number_format($parametrosCreditoFuturo->porcentagem, 2, ',', '.');
        $valorMoeda = number_format($parametrosCreditoFuturo->valorcredito, 2, ',', '.');

        $parametrosCreditoFuturo->corpoemail = str_replace('[VALOR]', $valorMoeda, $parametrosCreditoFuturo->corpoemail);
        $parametrosCreditoFuturo->corpoemail = str_replace('[PERCENTUAL]', $percentual, $parametrosCreditoFuturo->corpoemail);
        $parametrosCreditoFuturo->corpoemail = str_replace('[QTD.PARCELAS]', $parametrosCreditoFuturo->numeroparcelas, $parametrosCreditoFuturo->corpoemail);

        $usuariosAprovadores = $this->dao->obterUsuariosAprovacao($CreditoFuturoVo->MotivoCredito->tipo);
        
        foreach ($usuariosAprovadores as $usuarioAprovador) {

            if (!empty($usuarioAprovador['email'])) {

                $this->enviarEmail($usuarioAprovador['email'], $parametrosCreditoFuturo);
            }
        }
    }
	
	/**
	 * Metodo para preparar o email que sera enviado para os aprovadores
	 * 
	 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
	 * @param ParametrosCreditoFuturo
	 */
	public function enviarEmail($emailAprovador, $parametrosCreditoFuturo) {
		
		$this->mail->IsSMTP();
		$this->mail->From = "sascar@sascar.com.br";
		$this->mail->FromName = "SASCAR";
		$this->mail->Subject = $parametrosCreditoFuturo->assuntoemail;
		$this->mail->MsgHTML($parametrosCreditoFuturo->corpoemail);
		$this->mail->ClearAllRecipients();
		
		if($_SESSION['servidor_teste'] == 1) {
            $this->mail->AddAddress(_EMAIL_TESTE_);
		} else {
			$this->mail->AddAddress($emailAprovador);
		}

		if ($this->mail->Send()){
			$this->mail->ClearAllRecipients();
			$this->mail->ClearAttachments();
			return true;
		}
		return false;
		
	}

	public function obterDadosCreditoFuturoPorId($creditoFuturoId) {

		$dadosCreditoFuturo = (object) $this->dao->pesquisarPorID($creditoFuturoId);

        if (!count($dadosCreditoFuturo)) {
            throw new Exception('Falha ao carregar dados do crédito futuro.');
        }
        

        //monta o vo de motivo de credito com informações do credito futuro atual
        $CreditoFuturoVo->MotivoCredito->id = $dadosCreditoFuturo->cfocfmcoid;
        $CreditoFuturoVo->MotivoCredito->tipo = $dadosCreditoFuturo->tipo_motivo_credito;
        $CreditoFuturoVo->MotivoCredito->descricao = $dadosCreditoFuturo->motivo_credito_descricao;

        //preencho o objeto com informações já existentes do credito futuro
        $CreditoFuturoVo->cliente = $dadosCreditoFuturo->cfoclioid;
        $CreditoFuturoVo->formaInclusao = $dadosCreditoFuturo->cfoforma_inclusao;
        $CreditoFuturoVo->contratoIndicado = isset($dadosCreditoFuturo->cfoconnum_indicado) && !empty($dadosCreditoFuturo->cfoconnum_indicado) ? trim($dadosCreditoFuturo->cfoconnum_indicado) : 'NULL';
        $CreditoFuturoVo->protocolo = isset($dadosCreditoFuturo->cfoancoid) && !empty($dadosCreditoFuturo->cfoancoid) ? trim($dadosCreditoFuturo->cfoancoid) : 'NULL';
        $CreditoFuturoVo->tipoDesconto = $dadosCreditoFuturo->cfotipo_desconto;
        $CreditoFuturoVo->valor = $dadosCreditoFuturo->cfovalor;
        $CreditoFuturoVo->formaAplicacao = $dadosCreditoFuturo->cfoforma_aplicacao;
        $CreditoFuturoVo->aplicarDescontoSobre = $dadosCreditoFuturo->cfoaplicar_desconto;
        $CreditoFuturoVo->obrigacaoFinanceiraDesconto = $dadosCreditoFuturo->cfoobroid_desconto;
        $CreditoFuturoVo->qtdParcelas = $dadosCreditoFuturo->cfoqtde_parcelas;
        $CreditoFuturoVo->saldo = $dadosCreditoFuturo->cfosaldo;
        $CreditoFuturoVo->status = $dadosCreditoFuturo->cfostatus;

        if ($CreditoFuturoVo->valor != '') {
            $CreditoFuturoVo->valor = str_replace('.', '', $CreditoFuturoVo->valor);
            $CreditoFuturoVo->valor = str_replace(',', '.', $CreditoFuturoVo->valor);
        }
        
        
        if ($CreditoFuturoVo->saldo != '') {
            $CreditoFuturoVo->saldo = str_replace('.', '', $CreditoFuturoVo->saldo);
            $CreditoFuturoVo->saldo = str_replace(',', '.', $CreditoFuturoVo->saldo);
        } else {
            $CreditoFuturoVo->saldo  = 'NULL';
        }

        return $CreditoFuturoVo;
	}

	/**
	 * Description
	 * 
	 * $creditosFuturos = array (
	 *      [0] => array(
	 * 				
	 * 					credito_id,ok
            			connumero,ok
            			tipo_motivo_credito,
            			cfoaplicar_desconto,ok
            			valor,
            			parcela_id
	 * 
	 * 				)
	 * )
	 * 
	 * $totais = array(
	 * 		M => array(
	 * 				valor_total
	 * 			),
	 * 		L => array(
	 * 				valor_total
	 * 			)
	 * )
	 * 
	 * $nota_fiscal_id
	 * 
	 * @param type $creditosFuturos 
	 * @param type $totais 
	 * @param type $nota_fiscal_id 
	 * @param type $persistencia 
	 * @return type
	 */
	public function processarDesconto($creditosFuturos, $totais, $notaFiscal, $persistencia = true) {

		
		$retorno = array();

		$creditoTipoContestacao = false;
            foreach ($creditosFuturos as $key => $item) { 
          	    //1 - "Contestação"
                if ($item->MotivoCredito->tipo == '1') {
                    $creditoTipoContestacao = true;
                    break;
                }
            }

            $vl_total_itens_nf = floatval($totais['M']['valor_total']) + floatval($totais['L']['valor_total']);

            $historicos = array();
            $itens = array();

            //se for motivos de tipo contestação, calculo os descontos sobre o total dos valores
            //sendo eles Monitoramento ou Locação
            if ($creditoTipoContestacao) {
                //$valor_total_descontar = $valor_a_descontar = floatval($totais['M']['valor_total']) + floatval($totais['L']['valor_total']);
                
                //05/12/2014 - 16:20
                //De acordo com a Luciana Noronha, o desconto sobre o total dos valores não é validado pela prefeitura 
                //e ela disse que se o desconto for feito em cima do monitoramento, está ok.
                //apesar de estar especificado em documentação para o desconto ser sobre o total dos valores.
                
                //12/12/2014 - 10:17
                //Em conversa com Dani Leite estou fazendo uma tentativa de criar 2 descontos, desmembrando o que for locação e o que for monitoramento.
                //Mas o prometido ainda é o acordado no dia 05/12/14
                
            	$total_itens_m = floatval($totais['M']['valor_total']);
            	$total_itens_l = floatval($totais['L']['valor_total']);
            	$total_nota = $total_itens_m + $total_itens_l;
            	
            	$creditos_processados = array();
            	
            	$desconto_m = $total_itens_m;
            	$desconto_l = $total_itens_l;

                foreach ($creditosFuturos as $key => $item) {

                    $item->item_desconto = true;           
                    
                    //considerar o valor do crédito, para não acontecer de aplicar mais desconto do que o crédito que o cliente tem
                    if ($desconto_m > $item->valor){
                    	$desconto_m = $item->valor;
                    	$desconto_l = 0;
                    } else {
                    	if ($desconto_l > ($item->valor - $desconto_m)){
                    		$desconto_l = ($item->valor - $desconto_m);
                    	}
                    	if ($desconto_l <= 0){
                    		$desconto_l = 0;
                    	}
                    }
                    
                    //aplicar descontos
                    if ($total_itens_m > 0){
                    	if ($total_itens_l > 0){
                    		$total_aux = $total_nota + $item->valor;
                    	} else {
                    		$total_aux = $total_nota;
                    	}
                    	$creditos_processados[] = $this->aplicarDesconto($desconto_m, $total_aux, $item, $notaFiscal, $persistencia);
                    }
                    /*if ($total_itens_l > 0){
                    	$creditos_processados[] = $this->aplicarDesconto($desconto_l, $total_nota, $item, $notaFiscal, $persistencia);
                    }*/

                    foreach ($creditos_processados AS $credito_processado){
                    
	                    $historicos[] = $credito_processado['historicos'];
	
	                    if ($credito_processado['aplicado']) {
	                        $credito_processado['desconto'] = str_replace('-', '', $credito_processado['desconto']);
	                        $item->desconto_aplicado = $credito_processado['desconto'];
	                        $totais['valor_total'] += '-' . $item->desconto_aplicado;
	                        $itens[] = $item;
	                    }
                    }

                }

            } else {


                $valor_total_monitoramento = floatval($totais['M']['valor_total']);
                $valor_total_locacao = floatval($totais['L']['valor_total']);

                $valor_total_descontar = floatval($totais['M']['valor_total']) + floatval($totais['L']['valor_total']);


                foreach ($creditosFuturos as $key => $item) {


                        $item->item_desconto = true;

                            //1 - Monitoramento 
                            if ($item->aplicarDescontoSobre == '1') {                        

                                $valor_total_monitoramento_retorno = $this->aplicarDesconto($valor_total_monitoramento, $valor_total_descontar, $item, $notaFiscal, $persistencia);
                                $valor_total_monitoramento = $valor_total_monitoramento_retorno['valor_com_desconto'];
                                $valor_total_descontar = $valor_total_monitoramento_retorno['total'];
                                $credito_processado = $valor_total_monitoramento_retorno;
                                $historicos[] = $credito_processado['historicos'];
                            }

                            //2 - Locação
                            if ($item->aplicarDescontoSobre == '2') { 

                                $valor_total_locacao_retorno = $this->aplicarDesconto($valor_total_locacao, $valor_total_descontar, $item, $notaFiscal, $persistencia);
                                $valor_total_locacao = $valor_total_locacao_retorno['valor_com_desconto'];
                                $valor_total_descontar = $valor_total_locacao_retorno['total'];
                                $credito_processado = $valor_total_locacao_retorno;
                                $historicos[] = $credito_processado['historicos'];

                            }
                        

                        if ($credito_processado['aplicado']) {
                            $credito_processado['desconto'] = str_replace('-', '', $credito_processado['desconto']);
                            $item->desconto_aplicado = number_format($credito_processado['desconto'], 2, '.','');
                            $totais['valor_total'] += '-' . $item->desconto_aplicado;
                            $itens[] = $item;
                        }
                }                
            }

            $retorno['creditos'] = $itens;
            $retorno['total'] = $totais['valor_total'];


            if ($persistencia && isset($historicos) && count($historicos)) {
            	 //REALIZO O INSERT DOS HISTORICOS

	        	foreach ($historicos as $key => $item_historicos) {	        		

	        		if (count($item_historicos)) {
	        			$item_historicos['historico_credito_futuro']->valor_total_nf = $totais['valor_total'];
	        			$item_historicos['historico_credito_futuro']->vl_total_itens_nf = $vl_total_itens_nf;

	        			$this->salvarHistoricosAplicacao($item_historicos);

	        			$credito_id = $item_historicos['historico_credito_futuro']->creditoFuturoId;
	        			$creditoFuturo = $item_historicos['historico_cliente']['credito_futuro_vo'];

						//verifico se há parcelas ativas do credito futuro, se não houver encerro o credito futuro em questão.
						if (!$this->dao->verificarParcelasAtivasCreditoFuturo($creditoFuturo->id)) {

							//realizo o encerramento do credito futuro
							if ($this->encerrarCredito($creditoFuturo->id)) {
								//se concluido, realizo um registro no na tabela credito_futuro_historico
								$historicos = $this->prepararHistoricosAplicacao($creditoFuturo, $notaFiscal, $this->OPERACAO_ENCERRAMENTO_CREDITO);
								$this->salvarHistoricosAplicacao($historicos);
							}


						}

	        		}

	        		
	        	}
            }

            return $retorno;
	}

	/**
	 *
	 * Método aplicarDesconto()
	 * Responsável por aplicar o credito futuro no valor da Nota.
	 * 
	 * @param float   $valor - Valor que sofrerá o desconto.
	 * @param array   $creditoFuturo - Dados do crédito futuro que esta sendo aplicado.
	 * @param boolean $persistencia - Default true, indica se deve ser realizado persistencia dos dados.
	 * 
	 * @return array $retorno - com indices 'desconto' e 'valor'.
	 * 
	 */
	public function aplicarDesconto($valor, $total, $creditoFuturo, $notaFiscal, $persistencia = true) {

		//////////////////////Lógica de teste//////////////////////////////

		$retorno = array();

		$total = (string)$total;
		$total = (float)$total;

		$valorComDesconto = 0;
		$desconto = 0;
		$desconto_cheio = 0;


		if ( floatval($valor) <= 0.01 || floatval($total) <= 0.01) {

        	//lança o credito e inativa a parcela (da como usado o credito em questao)
			$valorComDesconto = floatval($valor);
			$desconto 			= floatval($creditoFuturo->valor);
			$creditoFuturo->valor_do_desconto = 0;

			$retorno['valor_com_desconto'] = floatval($valorComDesconto);
			$retorno['desconto']           = floatval($desconto);
			$retorno['desconto_cheio']     = floatval($desconto);
			$retorno['aplicado']           = false;

			if ( floatval($total) == 0.01 ) {
				$retorno['total']          = 0;
			} else if (floatval($total) < 0.01) {
				$retorno['total']          = 0.01;
			} else {
				$retorno['total'] = floatval($total);
			}

			

			if ($persistencia) {

				//se for diferente de constetação realiza a persistencia de descarte do credito futuro em questao
				if ($creditoFuturo->MotivoCredito->tipo != '1') {


					$salvoMovimentacao = $this->registrarMovimentacao($creditoFuturo->id, $notaFiscal['nfloid'], $creditoFuturo->Parcelas->id, 0);

					if ($salvoMovimentacao) {

						$this->inativarParcelaCredito($creditoFuturo->id, $creditoFuturo->Parcelas->id);

						if ($creditoFuturo->tipoDesconto == '2') {

							//busco o saldo atualizado do credito futuro.
							$saldo_atual = $this->dao->buscarSaldoAtual($creditoFuturo->id);

							//atualizo o saldo deo crdito fururo.
							$this->dao->atualizarSaldo($creditoFuturo->id, $saldo_atual);

						}

					} 

					$retorno['historicos'] = $this->prepararHistoricosAplicacao($creditoFuturo, $notaFiscal, $this->OPERACAO_DESCARTE_DESCONTO);

				} 

			}
			

			return $retorno;

		}



		$valorComDesconto = floatval($valor) - floatval($creditoFuturo->valor);


		if ($valorComDesconto > 0) {
			

			$valor_com_desconto = $valorComDesconto;
			$desconto 			= $creditoFuturo->valor;
			$desconto_cheio     = $creditoFuturo->valor;
			$total = floatval($total) - floatval($creditoFuturo->valor);

		} else if ($valorComDesconto <= 0 && (floatval($total) - floatval($creditoFuturo->valor))  <= 0) {
			
			
			$valor_com_desconto = floatval($valor) - 0.01;
			$desconto 			= floatval($valor) - 0.01 ;
			$desconto_cheio     = floatval($valor);
			$total = 0.01;


		} else {


			$total = floatval($total) - floatval($creditoFuturo->valor);

			$valor_com_desconto = $valorComDesconto;

			if (floatval($valorComDesconto) <= 0) {
				$desconto = floatval($valor);
				$desconto_cheio  = floatval($valor);
			}	

		}

		$retorno['valor_com_desconto'] = $valor_com_desconto;
		$retorno['desconto']           = $desconto;
		$retorno['desconto_cheio']     = $desconto_cheio;
		$retorno['aplicado']           = true;
		$retorno['total']              = $total;


		$creditoFuturo->valor_do_desconto = $desconto;



		if ($persistencia) {

			//prepara dados para realizar persistencia
			$movimentacao_credito_id = $creditoFuturo->id;
			$movimentacao_nota_fiscal_id = $notaFiscal['nfloid'];
			$movimentacao_parcela_id = $creditoFuturo->Parcelas->id;


			//se for contestação
			// ASM-5333 - Modulo Credito Futuro - Motivo INDICAÇÃO AMIGO
			// Correção: Adicionado Motivo indicação amigo, pois quando o desconto era maior que o valor da nota o desconto estava sendo encerrado erradamente. O restante do desconto deve ficar como saldo até zerar
			if ($creditoFuturo->MotivoCredito->tipo == '1' || $creditoFuturo->MotivoCredito->tipo == '2') {

				

				$movimentacao_valor = $retorno['desconto_cheio'];
				

				//registro a movimentação do credito futuro.
				$this->registrarMovimentacao($movimentacao_credito_id, $movimentacao_nota_fiscal_id, $movimentacao_parcela_id, $movimentacao_valor);

			} else {

				$movimentacao_valor = $creditoFuturo->valor;

				$this->registrarMovimentacao($movimentacao_credito_id, $movimentacao_nota_fiscal_id, $movimentacao_parcela_id, $movimentacao_valor);

			}

			//inativo a parcela se for necessário conforme as condições:
			// Condição 1 - Motivos diferentes de (1-Contestação e 2-Indicação)
			// Ou Condição 2 - Motivos igual à (1-Contestação e 2-Indicação) e valor do desconto igual ao valor a ser faturado
			if ( ($creditoFuturo->MotivoCredito->tipo != '1' && $creditoFuturo->MotivoCredito->tipo != '2') || 
				( ($creditoFuturo->MotivoCredito->tipo == '1' || $creditoFuturo->MotivoCredito->tipo == '2') && (floatval($creditoFuturo->valor) - floatval($retorno['desconto_cheio'])) == 0  ) ) {				

				$this->inativarParcelaCredito($creditoFuturo->id, $creditoFuturo->Parcelas->id);
			}

			//realizo atualzção de saldo
			if ($creditoFuturo->tipoDesconto == '2') {

				//busco o saldo atualizado do credito futuro.
				$saldo_atual = $this->dao->buscarSaldoAtual($creditoFuturo->id);

				//atualizo o saldo deo crdito fururo.
				$this->dao->atualizarSaldo($creditoFuturo->id, $saldo_atual);

			}

			$retorno['historicos'] = $this->prepararHistoricosAplicacao($creditoFuturo, $notaFiscal, $this->OPERACAO_APLICACAO_DESCONTO);

		}

		return $retorno;



	}

	public function inativarParcelaCredito($credito_id, $parcela_id) {

		if ( trim($credito_id) == '' || trim($parcela_id) == '' ) {
			throw new Exception('Há campos obrigatórios não informados para o encerramento da parcela de crédito.');
		}

		$this->dao->inativarParcelaCredito($credito_id, $parcela_id);
	}

	public function encerrarCredito($credito_id) {

		if (trim($credito_id) == '') {
			throw new Exception('O código do crédito futuro é necessário para o encerramento do mesmo.');
		}

		//usuario automatico 2750 Default
		$usuarioEncerramento = isset($_SESSION['usuario']['oid']) ? trim($_SESSION['usuario']['oid']) : 2750;

		//usuario de encerramento é o mesmo que está lançando os creditos.
		return $this->dao->encerrarCredito($credito_id, $usuarioEncerramento);

	}

	public function registrarMovimentacao($credito_id, $nota_fiscal_id, $parcela_id, $valor) {

		if ( trim($credito_id) == '' || trim($nota_fiscal_id) == '' || trim($parcela_id) == '' || trim($valor) == '') {
			throw new Exception('Há campos obrigatórios não informados para o registro de movimentação.');
		}

		return $this->dao->salvarMovimentacao($credito_id, $nota_fiscal_id, $parcela_id, $valor);

	}


	public function salvarHistoricosAplicacao($historicos) {

		$this->dao->incluirHistoricoCreditoFuturo($historicos['historico_credito_futuro']);

		$this->dao->incluirHistoricoCliente($historicos['historico_cliente']['credito_futuro_vo'], $historicos['historico_cliente']['texto_historico_cliente']);

		if (isset($historicos['historico_por_motivo'])) {

			if ($historicos['historico_por_motivo']['tipo_motivo_credito'] == $this->MOTIVO_CREDITO_CONTESTACAO) {

				$this->dao->incluirHistoricoContestacao($historicos['historico_por_motivo']['texto_historico_cliente'], $historicos['historico_por_motivo']['protocolo'], $historicos['historico_por_motivo']['protocolo_status'], $historicos['historico_por_motivo']['usuario_inclusao']);
			}

			if ($historicos['historico_por_motivo']['tipo_motivo_credito'] == $this->MOTIVO_CREDITO_INDICACAO_AMIGO) {
				$this->dao->incluirHistoricoContratoIndicador($historicos['historico_por_motivo']['contrato_indicado'], $historicos['historico_por_motivo']['usuario_inclusao'], $historicos['historico_por_motivo']['texto_historico_cliente']);
			}

		}

	}

	public function prepararHistoricosAplicacao($creditoFuturo, $notaFiscal, $operacao) {

		

		$dadosCreditoFuturo = $this->obterDadosCreditoFuturoPorId($creditoFuturo->id);
        foreach ($dadosCreditoFuturo as $key => $value) {
        	$creditoFuturo->$key = $dadosCreditoFuturo->$key;
        }

        if ($operacao == $this->OPERACAO_ENCERRAMENTO_CREDITO || $operacao == $this->OPERACAO_CANCELAMENTO_DESCONTO) {
        	$creditoFuturo->valor_do_desconto = 'NULL';
        	$creditoFuturo->Parcelas->numero  = 'NULL';
        }

		/*
		 * Historico do credito futuro
		 */
		$saldoParcelas = $this->dao->buscarParcelasCreditoFuturo($creditoFuturo->id, $creditoFuturo->formaAplicacao, $creditoFuturo->tipoDesconto);       
		$creditoFuturo->usuarioInclusao = isset($_SESSION['usuario']['oid']) ? trim($_SESSION['usuario']['oid']) : $creditoFuturo->usuarioInclusao;

		$parametros = array (
		    'usuarioInclusao' => $creditoFuturo->usuarioInclusao,
		    'operacao' => $operacao,
		    'origem' => $creditoFuturo->origem,
		    'creditoFuturoId' => $creditoFuturo->id,
		    'status' => $creditoFuturo->status,
		    'tipoDesconto' => $creditoFuturo->tipoDesconto,
		    'formaAplicacao' => $creditoFuturo->formaAplicacao,
		    'aplicarDescontoSobre' => $creditoFuturo->aplicarDescontoSobre,
		    'qtdParcelas' =>  $creditoFuturo->qtdParcelas,
		    'valor' => $creditoFuturo->valor,
		    'saldo' => $creditoFuturo->saldo,
		    'cfhsaldo_parcelas' => $saldoParcelas,
		    'observacao' => NULL,
		    'justificativa' => NULL,
		    'obrigacaoFinanceiraDesconto' => $creditoFuturo->obrigacaoFinanceiraDesconto,
		    
		    'nf_numero' => isset($notaFiscal['nflno_numero']) ? $notaFiscal['nflno_numero'] : 'NULL',
		    'nf_serie' => isset($notaFiscal['nflserie']) ? $notaFiscal['nflserie'] : NULL,
		    'dt_emissao_nf' => isset($notaFiscal['nflno_numero']) ? 'NOW()' : 'NULL',
		    'valor_total_nf' => 'NULL',
		    'vl_total_itens_nf' => 'NULL',
		    'valor_aplicado_desconto' => isset($notaFiscal['nflno_numero']) ? $creditoFuturo->valor_do_desconto : 'NULL',
		    'num_parcela_aplicada' => isset($notaFiscal['nflno_numero']) ? $creditoFuturo->Parcelas->numero : 'NULL',
		);


		$CreditoFuturoHistoricoVo = $this->prepararHistoricoVo($parametros);
		//$this->dao->incluirHistoricoCreditoFuturo($CreditoFuturoHistoricoVo);

		$creditoFuturoHistoricos['historico_credito_futuro'] = $CreditoFuturoHistoricoVo;

		/*
		 * Historico no cliente
		 * Prepara o texto da observacao
		 */
		$textoHistoticoCliente = $this->prepararTextoHistoricoCliente($creditoFuturo, $operacao, $creditoFuturo->id, $notaFiscal);
		//$this->dao->incluirHistoricoCliente($creditoFuturo, $textoHistoticoCliente);

		$creditoFuturoHistoricos['historico_cliente']['credito_futuro_vo'] = $creditoFuturo;
		$creditoFuturoHistoricos['historico_cliente']['texto_historico_cliente'] = $textoHistoticoCliente;

		/*
		 * Historico da contestacao
		 */            

		if ($creditoFuturo->MotivoCredito->tipo == $this->MOTIVO_CREDITO_CONTESTACAO) {
			
			$creditoFuturoHistoricos['historico_por_motivo']['tipo_motivo_credito'] = $creditoFuturo->MotivoCredito->tipo;

		    $protocoloStatus = $this->dao->buscarStatusAnaliseContas($creditoFuturo->protocolo);

		    $creditoFuturoHistoricos['historico_por_motivo']['texto_historico_cliente'] = $textoHistoticoCliente;
		    $creditoFuturoHistoricos['historico_por_motivo']['protocolo'] = $creditoFuturo->protocolo;
		    $creditoFuturoHistoricos['historico_por_motivo']['protocolo_status'] = $protocoloStatus;
		    $creditoFuturoHistoricos['historico_por_motivo']['usuario_inclusao'] = $creditoFuturo->usuarioInclusao;
		    
		}

		/*
		 * Historico no contrato indicador
		 */
		if ($creditoFuturo->MotivoCredito->tipo == $this->MOTIVO_CREDITO_INDICACAO_AMIGO) {

			

			$creditoFuturoHistoricos['historico_por_motivo']['tipo_motivo_credito'] = $creditoFuturo->MotivoCredito->tipo;

		    $cliente = $this->dao->obterDadosClientePorId($creditoFuturo->cliente);


		    if (count($cliente) > 0) {
		        $numeroDocumento = $cliente['numerodocumento'];
		        $nome = $cliente['nome'];
		        $textoHistoticoCliente .= "<br/>Cliente Indicador: $numeroDocumento $nome";
		    }

		    $creditoFuturoHistoricos['historico_por_motivo']['texto_historico_cliente'] = $textoHistoticoCliente;
		    $creditoFuturoHistoricos['historico_por_motivo']['contrato_indicado'] = $creditoFuturo->contratoIndicado;
		    $creditoFuturoHistoricos['historico_por_motivo']['usuario_inclusao'] = $creditoFuturo->usuarioInclusao;
		    
		}

		return $creditoFuturoHistoricos;
	}
	
	
	public function __construct($FinCreditoFuturoDAO) {
		
		$this->dao = $FinCreditoFuturoDAO;
		
		$this->mail = new PHPMailer();
		
	}
	
	
}