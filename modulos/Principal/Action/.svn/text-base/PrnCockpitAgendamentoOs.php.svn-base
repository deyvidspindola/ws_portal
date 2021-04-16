<?php
/**
 * Cockpit de Agendamento de OS
 *
 * @package Principal
 * @author  Kleber Goto Kihara <kleber.kihara@meta.com.br>
 */
class PrnCockpitAgendamentoOs {

    /**
     * Objeto DAO.
     *
     * @var PrnCockpitAgendamentoOsDAO
     */
    private $dao;

    /**
     * Objeto Parâmetros.
     *
     * @var stdClass
     */
    private $param;

    /**
     * Objeto View.
     *
     * @var stdClass
     */
    private $view;

    /**
     * Mensagem de erro padrão.
     *
     * @const String
     */
    const MENSAGEM_ALERTA_NENHUM_REGISTRO = "Não existe cadastro de equivalência para os parâmetros desta O.S.";

    /**
     *
     */
    const MENSAGEM_ALERTA_NENHUMA_RESERVA = "Não há produtos reservados.";

    const MENSAGEM_SUCESSO_SALVAR_RESERVA = "Reserva de produtos efetuada com sucesso!";


    const MENTAGEM_ALERTA_ESTOQUE_TRANSITO_INSUFICIENTE = "Quantidade em trânsito insuficiente para atender a reserva.";
    /**
     * Status Pre-reserva
     */
    const STATUS_PRE_RESERVA = 1;
    /**
     * Método construtor.
     *
     * @param PrnCockpitAgendamentoOsDAO $dao Objeto DAO.
     *
     * @return Void
     * @todo Parar a execução e apresentar o erro padrão (caso não receba $dao).
     */
    public function __construct($dao = null) {
        /*
         * Cria o objeto View.
         */
        $this->view = new stdClass();

        $this->view->mostrar_btn_cancelar = true;

        // Dados
        $this->view->dados = null;

        // Filtros/Parâmetros
        $this->view->parametros = null;

        // Mensagens
        $this->view->mensagem->alerta  = '';
        $this->view->mensagem->erro    = '';
        $this->view->mensagem->sucesso = '';

        // Status
        $this->view->status = true;

        //Array com os produtos sem saldo de estoque
        $this->view->prodIndisponivel = array();

        /*
         * Cria o objeto Parâmetros.
         */
        $this->param = new stdClass();

        /*
         * Flag que define se houve erro ao salvar reservas
         */
        $this->view->erroSalvar = false;

        /*
         * Cria o objeto Dao.
         */
        if (is_object($dao)) {
            $this->dao = $dao;
        } else {
            // ToDo
        }
    }

    /**
     * Método padrão da classe.
     *
     * @return void
     */
    public function index() {
     
        try {
          
        	unset($_SESSION['agendamento']);

            $this->inicializarParametros();
            $this->inicializarViewParametros();

            $equipamento = count($this->view->parametros->disponivel->equipamento);
            $outro       = count($this->view->parametros->disponivel->outro);

            if (!$equipamento && !$outro) {
                $this->view->mensagem->alerta = self::MENSAGEM_ALERTA_NENHUM_REGISTRO;
                $this->view->status           = false;
            }

            // Tira a diferença do que esta disponível e do que esta reservado
            foreach ($this->view->parametros->disponivel->equipamento as $produto) {
              $reservado = $this->dao->buscaQuantidadeReservada($this->param->repoid, $produto->oid);
              if ($reservado != null) { 
                $produto->disponivel -= (int) $reservado->disponivel;
                $produto->transito -= (int) $reservado->transito;
              }
            }

            foreach ($this->view->parametros->disponivel->outro as $produto) {
              $reservado = $this->dao->buscaQuantidadeReservada($this->param->repoid, $produto->oid);
              if ($reservado != null) { 
                $produto->disponivel -= (int) $reservado->disponivel;
                $produto->transito -= (int) $reservado->transito;
              }
            }

             /*
            * Busca os produtos reservados e passa para a view os dados
            */
            $this->buscarProdutosReservados($this->param->ordoid, $this->param->repoid);

            //$this->atualizarQuantidadeProdutos();

            if(!$this->dao->pesquisarAgendamentoUsuario($this->param->ordoid, $_SESSION['usuario']['oid'])){
              if(!isset($_SESSION['funcao']['cancelar_reserva_cockpit'])){
                $this->view->mostrar_btn_cancelar = false;
              }
            }

            unset($equipamento);
            unset($outro);
        } catch (ErrorException $e) {
            $this->view->mensagem->erro = $e->getMessage();
            $this->view->status         = false;
        } catch (Exception $e) {
            $this->view->mensagem->alerta = $e->getMessage();
            $this->view->status           = false;
        }

        require_once _MODULEDIR_ . 'Principal/View/prn_cockpit_agendamento_os/index.php';

    }

    /**
     * Método que inicializa o objeto das informações recebidas.
     *
     * @return Void
     */
    private function inicializarParametros() {

        if (count($_POST) > 0) {
            foreach ($_POST as $key => $value) {
                $this->param->$key = isset($_POST[$key]) ? $value : '';
            }
        }

        unset($_POST);

        if (count($_GET) > 0) {
            foreach ($_GET as $key => $value) {
                if (!isset($this->param->$key)) {
                    $this->param->$key = isset($_GET[$key]) ? $value : '';
                }
            }
        }

        unset($_GET);

        $contrato = $this->dao->buscarContrato($this->param);

        if (isset($contrato->numero)) {
            $this->param->connumero = $contrato->numero;
        }

    }

    /**
     * Método que inicializa os filtros/parâmetros do objeto View.
     *
     * @return void
     */
    private function inicializarViewParametros() {
      
    	$this->view->parametros->disponivel->equipamento = $this->dao->buscarEquipamentoDisponivel($this->param);

    	//DUM 81330 - RS26 – Resumo Representante
    	if(count($this->view->parametros->disponivel->equipamento)==0){
          $this->view->parametros->disponivel->equipamento = $this->dao->buscarEquipamentoDisponivelSemEquivalencia($this->param);
      }
      $this->view->parametros->disponivel->outro = $this->dao->buscarOutroDisponivel($this->param);
      //DUM 81330 - RS26 – Resumo Representante
      if(count($this->view->parametros->disponivel->outro)==0){
          $this->view->parametros->disponivel->outro = $this->dao->buscarOutroDisponivelSemEquivalencia($this->param);
      }

        $this->view->parametros->representante = $this->dao->buscarRepresentante($this->param);

       foreach($this->view->parametros->disponivel as $chave => $produtos) {

            foreach($produtos as $registro){
                $estoqueReservado = $this->dao->sumarizarEstoqueReservado($this->param->repoid, $registro->oid);
                $registro->qtdeInicial = ($registro->disponivel - $estoqueReservado);
                $registro->qtdeInicial = ($registro->qtdeInicial > 0) ? $registro->qtdeInicial : 0;                
                $registro->reservado = $estoqueReservado;
                
            }
        }
    }


    /**
     * Método para buscar os produtos reservados
     *
     * @param $ordoid
     * @return int
     */
    private function buscarProdutosReservados($ordoid, $repoid, $preencherSessaoComBancoDeDados = true){

    	/*
    	 * Inicia a sessao dos itens.
    	 * O metodo verifica se ja existe uma sessao setada.
    	 * Caso nao possuo cria uma nova sessao
    	 */
    	self::iniciarSessaoItens($ordoid,$repoid);

	    $this->view->produtos->reservados = $this->dao->buscarProdutosReservados($ordoid, $repoid);
      
      if(count($this->view->produtos->reservados) == 0){
        $this->view->mostrar_btn_cancelar = false;
      }

	    if($this->view->produtos->reservados) {

	    	foreach ($this->view->produtos->reservados as $produto) {

	    		$this->view->id_reserva_agendamento = $produto['id_reserva_agendamento'];

	    		$params = array(
	    				'idProduto'					=> (int) $produto['id_produto'],
	    				'descricaoProduto' 			=> $produto['descricao_produto'],
	    				'qtdDisponivel' 			=> $produto['quantidade_disponivel'],
	    				'qtdTransito' 				=> $produto['quantidade_transito'],
	    				'qtdDisponivelOriginal' 	=> $produto['quantidade_disponivel'],
	    				'qtdTransitoOriginal' 		=> $produto['quantidade_transito'],
	    				'idReservaAgendamento' 		=> $produto['id_reserva_agendamento'],
	    				'idReservaAgendamentoItem' 	=> $produto['id_reserva_agendamento_item'],
              'btn_cancelar'              => false              
	    		);

	    		if ($preencherSessaoComBancoDeDados) {


	    			self::inserirItemSessao($params, $produto['id_ordem_servico'], $repoid);
	    		}
	    	}
    	}
      
      

    	$this->view->produtos->sessao = self::obterTodosItensSessao($ordoid, $repoid);

    	if (!$this->view->produtos->sessao) {      
    		$this->view->mensagem->alerta = self::MENSAGEM_ALERTA_NENHUMA_RESERVA;
    		$this->view->status           = false;
    	}
    }

    /**
     * Metodo para reservar os produtos
     *
     * @param int
     * @return void
     */
    public function reservarProdutos(){

    	try {

    		$this->inicializarParametros();

    		$this->inicializarViewParametros();

    		$equipamento = count($this->view->parametros->disponivel->equipamento);
    		$outro       = count($this->view->parametros->disponivel->outro);

    		if (!$equipamento && !$outro) {
    			$this->view->mensagem->alerta = self::MENSAGEM_ALERTA_NENHUM_REGISTRO;
    			$this->view->status           = false;
    		}

    		/*
    		 * Inicia a sessao dos itens.
    		 * O metodo verifica se ja existe uma sessao setada.
    		 * Caso nao possuo cria uma nova sessao
    		 */
    		self::iniciarSessaoItens($this->param->ordoid , $this->param->repoid);

    		if (!empty($this->param->produto) && !empty($this->param->checkbox)) {

    			$check = array();

    			/*
    			 * Valida os ids de produtos que foram checkados
    			 */
    			foreach ($this->param->checkbox as $idProduto => $valueCheck) {

    				if (array_key_exists('checkedDisponivel', $valueCheck)){
    					$check[$idProduto]['disponivel'] = true;
    				}

    				if (array_key_exists('checkedTransito', $valueCheck)) {
    					$check[$idProduto]['transito'] = true;
    				}
    			}

    			/*
    			 * Percorre o array com as quantidades para reserva validando com o checkbox que estao checkados conforme validacao acima
    			 */
    			foreach ($this->param->produto as $idProduto => $valor) {

    				foreach ($check as $id_produto_checked => $value) {

    					$qtdDisponivel = 0;
    					$qtdTransito = 0;

    					if ($idProduto == $id_produto_checked) {

    						$chaves = array_keys($value);

    						foreach ($chaves as $tipo) {

    							foreach ($valor as $tipoCheckado => $quantidade) {

                    if ($tipoCheckado == 'qtdeInicial') {
                        $qtdInicial = intval($quantidade);
                    }

    								if ($tipoCheckado == $tipo) {

    									if ($tipoCheckado == 'disponivel') {

    										$qtdDisponivel = (int) $quantidade;
    									}

                      if ($tipo == 'transito') {
    										$qtdTransito = (int) $quantidade;
                        //$remessas = $this->dao->buscaRemessaPorProduto($idProduto, $this->param->repoid);
    									}
    								}
    							}
    						}

    						/*
    						 * obtem a sessao para futura verificacao da quantidade solicitada em relacao a permissao do usuario
    						 */
    						$itens = self::obterItemSessao($idProduto, $this->param->ordoid, $this->param->repoid);

    						/*
    						 * Metodo para obter a descricao do produto
    						*/
    						$descricaoProduto = $this->dao->obterDescricaoProduto($idProduto);

    						/*
    						 * Parametros para inserir na sessao de itens
    						 */
    						$params = array(
    								'idProduto'                   => (int) $idProduto,
    								'descricaoProduto'            => $descricaoProduto,
    								'qtdDisponivel'               => $qtdDisponivel,
    								'qtdTransito'                 => $qtdTransito,
    								'qtdDisponivelOriginal'       => null,
    								'qtdTransitoOriginal'         => null,
    								'id_reserva_agendamento'      => null,
    								'id_reserva_agendamento_item' => null,
                    'btn_cancelar'                => true
    						);



    						if (count($itens) > 0) {

    							if ($_SESSION['funcao']['solicita_unidades_estoque'] != 0) {

    								/*
    								 * O usuario possui permissao para reservar mais que um produto
    								 */
    								self::inserirItemSessao($params, $this->param->ordoid, $this->param->repoid);

    							} else {

    								/*
    								 * O usuario nao tem permissao para reservar mais que um pruduto
    								 * porem ele pode inserir um de cada estoque, tanto do estoque em transito quanto do estoque disponivel
    								 */
    								if ($itens['quantidade_disponivel'] == 0 && $itens['quantidade_transito'] > 0) {

    									$params['qtdDisponivel'] = $qtdDisponivel;
    									$params['qtdTransito'] = 0;
    									self::inserirItemSessao($params, $this->param->ordoid, $this->param->repoid);

    								} else if ($itens['quantidade_disponivel'] > 0 && $itens['quantidade_transito'] == 0) {

    									$params['qtdDisponivel'] = 0;
    									$params['qtdTransito'] = $qtdTransito;
    									self::inserirItemSessao($params, $this->param->ordoid, $this->param->repoid);
    								}
    							}

    						} else {

    							/*
    							 * Aqui nao importa se o usuario possui ou nao permissao para solicitar varias unidades do produto
    							 * pois nao ha nenhum reservado ainda
    							 */
    							self::inserirItemSessao($params, $this->param->ordoid, $this->param->repoid);
    						}

                            if (isset($_SESSION['agendamento'][$this->param->ordoid][$this->param->repoid][$params['idProduto']]['quantidade_disponivel'])) {
                                $qtdReservada = $_SESSION['agendamento'][$this->param->ordoid][$this->param->repoid][$params['idProduto']]['quantidade_disponivel'];
                            }
                            else {
                                $qtdReservada = 0;
                            }

                            /*
                            * Diminui a quantidade da reserva do produto da quantidade disponível em estoque
                            */
                            foreach($this->view->parametros->disponivel as $chave => $produtos) {
                                 
                                   foreach($produtos as $registro){

                                      if(intval($registro->oid) == intval($idProduto)){

                                          $registro->disponivel = ($registro->qtdeInicial - intval($qtdReservada));
                                          $registro->disponivel = ($registro->disponivel > 0) ? $registro->disponivel : 0;
                                      }
                                   }
                              }

    					}
    				}
    			}
    		}

	    	/*
	    	 * Busca os produtos reservados e passa para a view os dados
	    	 */
	    	$this->buscarProdutosReservados($this->param->ordoid, $this->param->repoid, false);

        //$this->view->mostrar_btn_cancelar = true;

        if(!$this->dao->pesquisarAgendamentoUsuario($this->param->ordoid, $_SESSION['usuario']['oid'])){
          if(!isset($_SESSION['funcao']['cancelar_reserva_cockpit'])){
               $this->view->mostrar_btn_cancelar = false;
          }
        }

     	} catch (ErrorException $e) {
            $this->view->mensagem->erro = $e->getMessage();
            $this->view->status         = false;
        } catch (Exception $e) {
            $this->view->mensagem->alerta = $e->getMessage();
            $this->view->status           = false;
        }

        require_once _MODULEDIR_ . 'Principal/View/prn_cockpit_agendamento_os/index.php';

    }

    /**
     * Metodo para fechar a janela
     * Clique do botao Fechar Janela
     */
    public function fecharJanela() {

    	$ordoid = ($_POST['ordoid']) ? $_POST['ordoid'] : '';
    	$repoid = ($_POST['repoid']) ? $_POST['repoid'] : '';

    	if (!empty($ordoid)) {

    		self::encerrarSessaoItens($ordoid, $repoid);

    	}

    	echo true;
    	exit;

    }

    /**
     * Metodo para salvar o agendamento
     */
    public function salvarReservas(){

        $contIndisponil = 0;
        $totalItens = 0;
        $saldo = 0;
        $parametros = new stdClass();
        $dadosProduto = new stdClass();


     	if (count($_POST) > 0) {
            foreach ($_POST as $key => $value) {
                $this->parametrosSalvarReservas->$key = isset($_POST[$key]) ? $value : '';
            }
        }
        //print_r($this->parametrosSalvarReservas); exit;
        $id_agendamento = null;

        try{

            $this->dao->begin();

            /*
             * Recupera parametros para pesquisa
             */
            $contrato = $this->dao->buscarContrato($this->parametrosSalvarReservas);
            $parametros->connumero = $contrato->numero;
            $parametros->ordoid = $this->parametrosSalvarReservas->ordoid;
            $parametros->repoid = $this->parametrosSalvarReservas->repoid;

            if (empty($this->parametrosSalvarReservas->id_agendamento)) {
                $ordoid = $this->parametrosSalvarReservas->ordoid;
                $repoid = $this->parametrosSalvarReservas->repoid;

                /*
                 * Insere um agendamento relacionando com a OS
                 */
                $id_agendamento = $this->dao->inserirAgendamento($ordoid, $repoid, self::STATUS_PRE_RESERVA,$_SESSION['usuario']['oid']);
            }

            $totalItens = count($this->parametrosSalvarReservas->reserva);
           
            foreach ($this->parametrosSalvarReservas->reserva as $idProduto => $dadosReserva) {

                $dadosProduto->equipamento = $this->dao->buscarEquipamentoDisponivel($parametros);

                if(count($dadosProduto->equipamento)==0){
                    $dadosProduto->equipamento = $this->dao->buscarEquipamentoDisponivelSemEquivalencia($parametros);
                }

                $dadosProduto->outro = $this->dao->buscarOutroDisponivel($parametros);

                if(count($dadosProduto->outro)==0){
                    $dadosProduto->outro = $this->dao->buscarOutroDisponivelSemEquivalencia($parametros);
                }

                 /*
                 * Valida o saldo do produto reservado alocado ao representante
                 */
                foreach($dadosProduto as $chave => $produtos) {

                    foreach($produtos as $produto) {
                        if($idProduto == intval($produto->oid)) {
                            $saldo = $this->dao->sumarizarEstoqueReservado($parametros->repoid, $idProduto);
                            $saldo = ($produto->disponivel - $saldo);
                            break;
                        }
                    }
                }

                if($saldo < 0) {
                    $contIndisponil++;
                    $this->view->prodIndisponivel[$idProduto][] = $produto->disponivel;
                    continue;
                }

                # Parametro de id do agendamento do formulario
                $pIdAgendamento = $this->parametrosSalvarReservas->id_agendamento;

                /*
                * Verifica se existe quantidade suficiente para as remessas em trânsito
                */
                /*$remesas = array();

                if ( $dadosReserva['transito'] >  0 ) {
                   
                    $quantidadeTransito = 0;
                    $remessas = $this->dao->buscaRemessaPorProduto($idProduto, $parametros->repoid, $dadosReserva['transito']);    
                   
                    foreach ($remessas as $key => $remessa) {
                      $quantidadeTransito += (int) $remessa->quantidade;
                    }

                    if ($quantidadeTransito < $dadosReserva['transito']) {
                      throw new Exception(self::MENTAGEM_ALERTA_ESTOQUE_TRANSITO_INSUFICIENTE);
                    } else {

                    }
                }*/
                

                /*
                 * Nao existe ainda agendamento para a ordem de serviço
                 */
                if (empty($pIdAgendamento)) {

                    /*
                     * Insere um novo item em um novo agendamento
                     */
                    $item_agendamento = $this->dao->inserirItemAgendamento($idProduto, $dadosReserva['disponivel'], $dadosReserva['transito'], $id_agendamento);
                    $this->dao->inserirLog($item_agendamento, 'I', $_SESSION['usuario']['oid']);

                } else {

                    # Parametro id do item do agendamento do formulario
                    $id_item_agendamento = $dadosReserva['dbitem'];

                    if (empty($id_item_agendamento)) {

                        /*
                         * Insere um item no agendamento ja existente
                         */
                        $item_agendamento = $this->dao->inserirItemAgendamento($idProduto, $dadosReserva['disponivel'], $dadosReserva['transito'], $pIdAgendamento);
                        $this->dao->inserirLog($item_agendamento, 'I', $_SESSION['usuario']['oid']);

                    } else {
                        /*
                         * Atualiza o item ja existente no agendamento com as novas quantidades
                         */
                        if ($dadosReserva['disponivel'] != $dadosReserva['disponivelOriginal'] || $dadosReserva['transito'] != $dadosReserva['transitoOriginal']) {
                            $this->dao->atualizarItemAgendamento($idProduto, $dadosReserva['disponivel'], $dadosReserva['transito'], $id_item_agendamento);
                            $this->dao->inserirLog($id_item_agendamento, 'A', $_SESSION['usuario']['oid']);
                        }
                    }
                }

            }

            if ($contIndisponil > 0) {
               $this->view->erroSalvar = true;
            }

            //Se todos produtos estão indisponíveis no estoque não comita
            if($contIndisponil != $totalItens ){
                $this->dao->commit();
            }
            else {
                throw new Exception('');
            }


        }catch(Exception $e){
            $this->dao->rollback();
            $this->view->erroSalvar = true;
            echo json_encode($this->view->prodIndisponivel);
            exit;
        }

        unset($_SESSION['agendamento']);

        echo "OK";
        exit;

     	if (count($_POST) > 0) {
            foreach ($_POST as $key => $value) {
                $this->savarReservasParametros->$key = isset($_POST[$key]) ? $value : '';
            }
        }
        
        $id_agendamento = null;
        
        if (empty($this->parametrosSalvarReservas->id_agendamento)) {
        	
        	/*
        	 * Insere um agendamento relacionando com a OS
        	 */
        	$id_agendamento = $this->dao->inserirAgendamento($this->parametrosSalvarReservas->ordoid, self::STATUS_PRE_RESERVA);
        } 
        
        foreach ($this->parametrosSalvarReservas->reserva as $idProduto => $dadosReserva) { 
        	
        	/*
        	 * Nao existe ainda agendamento para a ordem de serviço
        	 */
        	if (empty($this->parametrosSalvarReservas->id_agendamento)) { 
        	
        		/*
        		 * Insere um novo item em um novo agendamento
        		 */
        		$item_agendamento = $this->dao->inserirItemAgendamento($idProduto, $dadosReserva['disponivel'], $dadosReserva['transito'], $id_agendamento);
        		$this->dao->inserirLog($item_agendamento, 'I', $_SESSION['usuario']['oid']);
        		
        	} else {
        		
        		# Parametro id do item do agendamento do formulario
	        	$id_item_agendamento = $dadosReserva['dbitem'];
	        	
	        	if (empty($id_item_agendamento)) {
	        		
	        		/*
	        		 * Insere um item no agendamento ja existente
	        		 */
		        	$item_agendamento = $this->dao->inserirItemAgendamento($idProduto, $dadosReserva['disponivel'], $dadosReserva['transito'], $pIdAgendamento);
		        	$this->dao->inserirLog($item_agendamento, 'I', $_SESSION['usuario']['oid']);
	        		
	        	} else {
	        			        		
	        		/*
	        		 * Atualiza o item ja existente no agendamento com as novas quantidades
	        		 */
	        		if ($dadosReserva['disponivel'] != $dadosReserva['disponivelOriginal'] || $dadosReserva['transito'] != $dadosReserva['transitoOriginal']) {
		        		$this->dao->atualizarItemAgendamento($idProduto, $dadosReserva['disponivel'], $dadosReserva['transito'], $id_item_agendamento);
		        		$this->dao->inserirLog($id_item_agendamento, 'A', $_SESSION['usuario']['oid']);
	        		}
	        		
	        	}
        	}
        	
        }
        
        unset($_SESSION['agendamento']);
       
    }

    public function excluirReserva(){
      
      try{

        $this->inicializarParametros();
        $this->inicializarViewParametros();

        if(!empty($this->param->id_agendamento) AND !empty($this->param->cancelamentoJustificativa)){
          $this->dao->cancelarAgendamento($this->param->id_agendamento,$this->param->cancelamentoJustificativa);
          $this->view->mensagem->sucesso = 'Reserva cancelada com sucesso!';
        }

      } catch (ErrorException $e) {
        $this->view->mensagem->erro = $e->getMessage();
        $this->view->status         = false;
      } catch (Exception $e) {
        $this->view->mensagem->alerta = $e->getMessage();
        $this->view->status           = false;
      }
      require_once _MODULEDIR_ . 'Principal/View/prn_cockpit_agendamento_os/index.php';
    }

    /**
     * Metodo para exclusao de itens de agendamento
     */
    public function excluirItemReserva(){

    	try {

    		$this->inicializarParametros();
    		$this->inicializarViewParametros();
        // echo "<pre>";
        //   print_r($this->view->parametros->disponivel);
        //   echo "</pre>";
        //   die();
        

    		$equipamento = count($this->view->parametros->disponivel->equipamento);
    		$outro       = count($this->view->parametros->disponivel->outro);

    		if (!$equipamento && !$outro) {
    			$this->view->mensagem->alerta = self::MENSAGEM_ALERTA_NENHUM_REGISTRO;
    			$this->view->status           = false;
    		}


    		if ($this->param->idProdutoExcluir && $this->param->ordoid) {
          /*
    			if (!empty($this->param->cancelamentoJustificativa) && !empty($this->param->id_agendamento) && !empty($this->param->cancelamentoIdItem)) {

    				$this->dao->excluirItemAgendamento($this->param->idProdutoExcluir, $this->param->cancelamentoJustificativa, $this->param->id_agendamento, $this->param->cancelamentoIdItem);
    				$this->dao->inserirLog($this->param->cancelamentoIdItem, 'E', $_SESSION['usuario']['oid']);
    			}
          */
    			self::excluirItemSessao($this->param->idProdutoExcluir, $this->param->ordoid, $this->param->repoid);
    		}

    		/*
    		 * Busca os produtos reservados e passa para a view os dados
    		*/
    		$this->buscarProdutosReservados($this->param->ordoid, $this->param->repoid, false);

        $this->atualizarQuantidadeProdutos();


    		unset($equipamento);
    		unset($outro);
    	} catch (ErrorException $e) {
    		$this->view->mensagem->erro = $e->getMessage();
    		$this->view->status         = false;
    	} catch (Exception $e) {
    		$this->view->mensagem->alerta = $e->getMessage();
    		$this->view->status           = false;
    	}

    	require_once _MODULEDIR_ . 'Principal/View/prn_cockpit_agendamento_os/index.php';

    }

    private function atualizarQuantidadeProdutos(){
      if(isset($this->view->produtos->sessao)) {
              
                foreach($this->view->produtos->sessao as $codProduto => $produto) {
                    
                    foreach($this->view->parametros->disponivel as $chave => $produtos) {

                        foreach($produtos as $registro) {                           
                            if(intval($registro->oid) == $codProduto){

                                $registro->disponivel = ($registro->disponivel - intval($produto['quantidade_disponivel']));
                                $registro->disponivel = ($registro->disponivel > 0) ? $registro->disponivel : 0;
                            }
                        }
                    }
                }
            }
            $produtosRepresentantes = $this->dao->buscarProdutosReservadosRepresentante($this->param->repoid, $this->param->ordoid);
         
            if(!empty($produtosRepresentantes)){

                foreach($produtosRepresentantes as $produtoRep){

                  foreach($this->view->parametros->disponivel as $chave => $produtos) {

                    foreach($produtos as $registro){

                      if(intval($registro->oid) == $produtoRep['id_produto']){
                        
                          $registro->disponivel = ($registro->disponivel - intval($produtoRep['quantidade_disponivel']));
                          $registro->disponivel = ($registro->disponivel > 0) ? $registro->disponivel : 0;
                          $registro->reservado = intval($produtoRep['quantidade_disponivel']);
                      }
                    }                      
                  }
                }  
            }
    }


    /**
     * Método solicitarAgendamento()
     * Ação do botão solicitar produtos.
     *
     * @return void
     */
    public function solicitarAgendamento() {

        $this->dao->begin();

        if (count($_POST) > 0) {
            foreach ($_POST as $key => $value) {
                $this->solicitarParametros->$key = isset($_POST[$key]) ? $value : '';
            }
        }

        $this->solicitarParametros->sagusuoid = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

        if (isset($this->solicitarParametros->sagordoid) && count($this->solicitarParametros->itens)) {
           $solicitacaoAgendamento = $this->dao->gravarSolicitacaoAgendamento($this->solicitarParametros);

           if ($solicitacaoAgendamento !== false) {

               if ($this->gravarItemSolicitacaoAgendamento($solicitacaoAgendamento,$this->solicitarParametros->itens) ) {
                   try {
                      $msg = "Solicitação de produtos a distribuição. Observação: " . utf8_decode($this->solicitarParametros->sagobservacao);
                      $ordoid = $this->solicitarParametros->sagordoid;
                      $this->dao->registraHistoricoOS($ordoid, $msg);
                   } catch (Exception $e) {
                     $this->dao->rollback();
                     echo "0";
                     exit;   
                   }
                   $this->dao->commit();
                   echo "1";
                   exit;
               } else {
                   $this->dao->rollback();
                   echo "0";
                   exit;
               }
           } else  {
               echo "0";
               exit;
           }
        }
        echo "0";
        exit;
    }

    /**
     * Método gravarItemSolicitacaoAgendamento()
     * Ação que é chamada após o sucesso do método solicitarAgendamento(), para salvar os itens
     * pertencentes a ela.
     *
     * @param string $solicitacaoAgenId
     * @param array  $itens
     *
     * @return boolean
     */
    private function gravarItemSolicitacaoAgendamento($solicitacaoAgenId,$itens) {

        $this->dao->begin();

        $retorno = true;

        $this->parametroItem->saisagoid = $solicitacaoAgenId;

        foreach ($itens as $item) {

            $this->parametroItem->saiprdoid = $item[0]; //produto id
            $this->parametroItem->saiqtde_solicitacao = $item[1]; //produto qtd
            $this->parametroItem->saisaisoid = 1; //pendente default

            $status = $this->dao->gravarItemSolicitacaoAgendamento($this->parametroItem);
            if ( $status === false  ){
                $retorno = false;
            }

        }

        if (!$retorno) {
            $this->dao->rollback();
            return $retorno;
        }

        $this->dao->commit();
        return $retorno;

    }

    /**
     * Inicializa a sessao dos itens
     */
    public static function iniciarSessaoItens($ordoid,$repoid){
    	if(!isset($_SESSION['agendamento'][$ordoid][$repoid])) {
    		$_SESSION['agendamento'][$ordoid][$repoid] = array();
    	}

    }

    /**
     * Encerra a sessao dos itens
     */
    public static function encerrarSessaoItens($ordoid,$repoid){

    	if(isset($_SESSION['agendamento'][$ordoid][$repoid])) {
    		unset($_SESSION['agendamento'][$ordoid][$repoid]);
    	}
    }

    /**
     * Metodo para incluir produtos da sessao
     *
     */
    public static function inserirItemSessao($params, $ordoid, $repoid){

    	if (isset($_SESSION['agendamento'][$ordoid][$repoid])) {

    		if (!$_SESSION['agendamento'][$ordoid][$repoid][$params['idProduto']]) {

	    		$_SESSION['agendamento'][$ordoid][$repoid][$params['idProduto']] = array(
	    			'id_produto' 						=> (int) $params['idProduto'],
	    			'descricao_produto' 				=> $params['descricaoProduto'],
	    			'quantidade_disponivel' 			=> $params['qtdDisponivel'],
	    			'quantidade_transito' 				=> $params['qtdTransito'],
	    			'quantidade_disponivel_original' 	=> $params['qtdDisponivelOriginal'],
	    			'quantidade_transito_original' 		=> $params['qtdTransitoOriginal'],
	    			'id_reserva_agendamento' 			=> $params['idReservaAgendamento'],
	    			'id_reserva_agendamento_item' 		=> $params['idReservaAgendamentoItem'],
            'btn_cancelar'                    => $params['btn_cancelar']           
	    		);

    		} else {

    			$_SESSION['agendamento'][$ordoid][$repoid][$params['idProduto']]['quantidade_disponivel'] += $params['qtdDisponivel'];
    			$_SESSION['agendamento'][$ordoid][$repoid][$params['idProduto']]['quantidade_transito'] += $params['qtdTransito'];
    		}
    	}

    }


    /**
     * Metodo para excluir item sessao
     */
    public static function excluirItemSessao($idProduto, $ordoid, $repoid) {

    	if(isset($_SESSION['agendamento'][$ordoid][$repoid])) {

    		if($_SESSION['agendamento'][$ordoid][$repoid][$idProduto]) {
    			unset($_SESSION['agendamento'][$ordoid][$repoid][$idProduto]);
    		}
    	}
    }

    /**
     * Metodo para obter a sessao completa dos itens
     */
    public static function obterTodosItensSessao($ordoid,$repoid){

    	if(isset($_SESSION['agendamento'][$ordoid][$repoid])) {

    		if(count($_SESSION['agendamento'][$ordoid][$repoid]) > 0) {
    			return $_SESSION['agendamento'][$ordoid][$repoid];
    		}
    	}
    }

    /**
     * Metodo para obter a sessao
     */
    public static function obterItemSessao($idProduto, $ordoid, $repoid) {

    	if(isset($_SESSION['agendamento'])) {

    		if($_SESSION['agendamento'][$ordoid][$repoid][$idProduto]) {
    			return $_SESSION['agendamento'][$ordoid][$repoid][$idProduto];
    		}
    	}

    	return array();
    }

    /**
    * Salva as reservas no banco de dados
    *
    */
    public function efetuarReserva() {
      
      $this->inicializarParametros();
      $this->inicializarViewParametros();

      $equipamento = count($this->view->parametros->disponivel->equipamento);
      $outro       = count($this->view->parametros->disponivel->outro);
      $idAgendamento = null;

      if (!$equipamento && !$outro) {
        $this->view->mensagem->alerta = self::MENSAGEM_ALERTA_NENHUM_REGISTRO;
        $this->view->status           = false;
      }
      
      try {

        $this->dao->begin();
        
        $parametros = new stdClass();
        $parametros->repoid = $this->param->repoid;
        $parametros->connumero = $this->param->connumero;

        $produtosReservar = array();
        $produtosEstoque = array_merge($this->view->parametros->disponivel->equipamento, $this->view->parametros->disponivel->outro);

      /*  echo "<pre>";
        print_r($produtosEstoque);
        echo "</pre>";
        exit;*/

        // Tira a diferença do que esta disponível e do que esta reservado
        foreach ($produtosEstoque as $produto) {
          $reservado = $this->dao->buscaQuantidadeReservada($this->param->repoid, $produto->oid);
          if ($reservado != null) { 
            $produto->disponivel -= (int) $reservado->disponivel;
            $produto->transito -= (int) $reservado->transito;
          }
        }
       
        // Verifica quais produtos serão reservados e se todos tem quantidade suficiente
        if (count($this->param->produto_reservar_estoque) > 0) {
          foreach ($this->param->produto_reservar_estoque as $idProduto => $quantidade) {
            $produtoEstoque = null;
            foreach ($produtosEstoque as $produto) {
              if ($produto->oid == $idProduto) {
                $produtoEstoque = $produto;
                break 1;
              }
            }
            
            if ($produtoEstoque->disponivel >= $quantidade) {
              $produtosReservar[$idProduto]['estoque'] = $quantidade;
              $produtosReservar[$idProduto]['transito'] = 0;
            } else {
              throw new Exception("Quantidade insuficiente em estoque");
            }
          }
        }
        if (count($this->param->produto_reservar_transito) > 0) {
          foreach ($this->param->produto_reservar_transito as $idProduto => $quantidade) {
            $produtoEstoque = null;
            foreach ($produtosEstoque as $produto) {
              if ($produto->oid == $idProduto) {
                $produtoEstoque = $produto;
                break 1;
              }
            }
            
            if ($produtoEstoque->transito >= $quantidade) {

              if (!isset($produtosReservar[$idProduto]['estoque'])) {
                $produtosReservar[$idProduto]['estoque'] = 0;
              }

              $produtosReservar[$idProduto]['transito'] = $quantidade;

            } else {
              throw new Exception("Quantidade insuficiente em remessas");
            }
          }
        } 
        
        // Caso tenha produtos a reservar efetua o registro
        if (count($produtosReservar) > 0) {
          $remessas = array();
          if (!empty($this->param->id_agendamento)) {
            $idAgendamento =$this->param->id_agendamento;
          } else {
            $idAgendamento = $this->dao->inserirAgendamento($this->param->ordoid, $this->param->repoid, self::STATUS_PRE_RESERVA, $_SESSION['usuario']['oid']);
          }
          foreach ($produtosReservar as $idProduto => $quantidade) {

            // Efetua a reserva de produtos disponíveis em estoque
            if ($quantidade['estoque'] > 0) {
              $item_agendamento = $this->dao->inserirItemAgendamento($idProduto, $quantidade['estoque'], 0, $idAgendamento);
              $this->dao->inserirLog($item_agendamento, 'I', $_SESSION['usuario']['oid']);
            }

            // Efetua a reserva de produtos em trânsito de acordo com as remessas
            if ($quantidade['transito'] > 0) {
              $quantidadeReservar = $quantidade['transito'];
              $quantidadeReservada = null;
              $remessas = $this->dao->buscaRemessaPorProduto($idProduto, $this->param->repoid, $quantidade['transito']);
              if ($remessas != false) {
                foreach ($remessas as $remessa) {
                  if ($quantidadeReservar > 0) {
                    $quantidadeReservada = 0;
                    if ($remessa->quantidade <= $quantidadeReservar) {
                      $quantidadeReservada = $remessa->quantidade;
                    } else {
                      $quantidadeReservada = $quantidadeReservar;
                    }
                    $item_agendamento = $this->dao->inserirItemAgendamento($idProduto, 0, $quantidadeReservada, $idAgendamento, $remessa->esroid);
                    $this->dao->inserirLog($item_agendamento, 'I', $_SESSION['usuario']['oid']);
                    $quantidadeReservar -= $quantidadeReservada;
                  }
                }
              } else {
                throw new Exception("Quantidade insuficiente em remessas");
              }
            }
          }
        }

        $this->view->mensagem->sucesso = self::MENSAGEM_SUCESSO_SALVAR_RESERVA;

        $this->dao->commit();
        
        $this->index(); // Chama a tela novamente
        
      } catch (ErrorException $e) {
        $this->dao->rollback();
        $this->view->mensagem->erro = $e->getMessage();
        $this->view->status         = false;
        $this->index();
      } catch (Exception $e) {
        $this->dao->rollback();
        $this->view->mensagem->alerta = $e->getMessage();
        $this->view->status           = false;
        $this->index();
      }
      require_once _MODULEDIR_ . 'Principal/View/prn_cockpit_agendamento_os/index.php';
    }

}

