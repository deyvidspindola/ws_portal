<?php 

require_once _MODULEDIR_ . 'Gestao/DAO/GesLayoutDAO.php';

class GesLayout {

	private $dao;

	public $habilitaInserir = false;
	public $habilitaUsuarios = false;
	public $habilitaArvore = false;
	public $habilitaMetas = false;
	public $habilitaIndicadores = false;
	public $habilitaImportacao = false;
	public $navegacao = array();
	public $superUsuario;
    public $moduloTitulo = '';
    public $moduloScripts = array();

	public function __construct($conn) {
		$this->dao = new GesLayoutDAO($conn);
		$_SESSION['navegacao'][0] = '';
		$this->superUsuario  = $this->dao->verificarPermissao('usuarios');
		$this->atualizarPermissoes();
	}

    public function renderizarCabecalho($moduloTitulo, array $scripts = array()){
        
        $this->moduloTitulo = $moduloTitulo;
        
        if (count($scripts) > 0){
            $this->moduloScripts = $scripts;
        }
        
        require_once _MODULEDIR_ . 'Gestao/View/ges_layout/cabecalho_padrao.php';
    }
    
    
	public function renderizarMenu() {

		$this->atualizarPermissoes();

		require_once _MODULEDIR_ . 'Gestao/View/ges_layout/menu.php';
	}

	private function atualizarPermissoes(){
		$this->habilitaInserir     = $this->dao->verificarPermissao('inserir');
		$this->habilitaUsuarios    = $this->dao->verificarPermissao('usuarios');
		$this->habilitaArvore      = $this->dao->verificarPermissao('arvores');
		$this->habilitaMetas       = $this->dao->verificarPermissao('metas');
		$this->habilitaIndicadores = $this->dao->verificarPermissao('indicadores');
		$this->habilitaImportacao  = $this->dao->verificarPermissao('importacao');
	}
    
    private function carregarDadosUmaArvore($ano, $funcionario){
		
        $this->metasPlanos = $this->dao->buscarMetasPlanos($ano, $funcionario);
        
        $arvoresMontadas = array();
       
        $nomeArvore = $this->dao->buscarArvoreNome($funcionario, $ano);
        
		$arvoresMontadas[0]['arvore_nome']  = $nomeArvore;
        
		$arvoresMontadas[0]['arvore'] 		= $this->dao->montarDadosArvore($funcionario, $ano);        

        $this->todasArvores = $arvoresMontadas;

		unset($arvoresMontadas);        
    }


	private function carregarDadosMultiplasArvores($ano = 2014){


		$usuarioLogado = $_SESSION['usuario']['oid'];

		$funcionarioId = $this->dao->buscarFuncionarioId($usuarioLogado);		

		$this->listarAnos = $this->listaAnos();

		$this->superUsuario  = $this->dao->verificarPermissao('usuarios');
        

        if ($this->superUsuario)  {

			$arvores = $this->dao->buscarArvoresCadastradas($ano);
            
            $arvoresMontadas = array();


            $i = 0;

            foreach ($arvores as $arvore) {

                $arvoreEstrutura = $this->dao->montarDadosArvorePorId($arvore->gmaoid, $ano);

                if (!empty($arvoreEstrutura)) {
                    $arvoresMontadas[$i]['arvore_nome'] = $arvore->gmanome;
                    $arvoresMontadas[$i]['arvore'] = $arvoreEstrutura;
                }

                $i++;
            }

            $this->todasArvores = $arvoresMontadas;
            $this->metasPlanos = $this->dao->buscarMetasPlanos($ano);
            unset($arvoresMontadas);
			
		} else {
			 $this->carregarDadosUmaArvore($ano, $funcionarioId);
        }
	}

	public function renderizarSidebar() {

		unset($_SESSION['gestao']['funcionario_arvore']);

		/* Mantis 7233 - Passar o ano corrente para o método que monta a árvore.
		   Se não passar nada, como era feito, sempre carrega primeiro 2014. */
		$this->carregarDadosMultiplasArvores(date('Y'));


		require_once _MODULEDIR_ . 'Gestao/View/ges_layout/sidebar.php';	
	}

	public function renderizarWrapper() {
		require_once _MODULEDIR_ . 'Gestao/View/ges_layout/wrapper.php';
	}

	public function renderizarFooter() {
		require_once _MODULEDIR_ . 'Gestao/View/ges_layout/footer.php';
	}

	public function carregarArvore() {


		$ano = isset($_POST['ano']) ? trim($_POST['ano']) : '2014';
		$voltar_inicio = isset($_POST['nivel_navegacao']) ? trim($_POST['nivel_navegacao']) : 0;

		$_SESSION['navegacao'][] = array(
				"funcionario"=> $_POST['funcionario'],
				"arvore"=> $this->dao->buscarArvoreNome($_POST['funcionario'],$ano)
			);

		

		$funcionarioRetornoArvore = "";
		$funcionarioRetornoArvoreNome= "";

		if (isset($_POST['funcionario']) && !empty($_POST['funcionario'])) {		

			$funcionario = $_POST['funcionario'];

			if (!empty($_SESSION['navegacao'][$voltar_inicio - 1])) {

				$funcionarioRetornoArvore = $_SESSION['navegacao'][$voltar_inicio - 1]['funcionario'];
				$funcionarioRetornoArvoreNome = $_SESSION['navegacao'][$voltar_inicio - 1]['arvore'];

			}

		} else {
			unset($_SESSION['gestao']['funcionario_arvore']);
			$usuarioLogado = $_SESSION['usuario']['oid'];
			$funcionario = $this->dao->buscarFuncionarioId($usuarioLogado);

		}
        
        //Carrega os dados da arvore para a função "gerarHtmlArvore"
        $this->carregarDadosUmaArvore($ano, $funcionario);


		$retorno = array(
			'html'                   => utf8_encode($this->gerarHtmlArvore()),
			'funcionario_retorno'    => $funcionarioRetornoArvoreNome,
			'funcionario_retorno_id' => $funcionarioRetornoArvore,
			'voltar_inicio'          => $voltar_inicio
		);


		echo json_encode($retorno);

	}


	public function buscarArvoreMultiplaAno() {
		$ano = isset($_POST['ano']) ? trim($_POST['ano']) : '2014';
        $this->carregarDadosMultiplasArvores($ano);
		$retorno = array(
			'html' => utf8_encode($this->gerarHtmlArvore())
		);
		echo json_encode($retorno);
	}


	public function carregarArvoreMultiplo() {

		$ano = isset($_POST['ano']) ? trim($_POST['ano']) : '2014';
		$voltar_inicio = isset($_POST['nivel_navegacao']) ? trim($_POST['nivel_navegacao']) : 0;

		$nomeArvore = $this->dao->buscarArvoreNome($_POST['funcionario'],$ano);

		$_SESSION['navegacao'][] = array(
				"funcionario"=> $_POST['funcionario'],
				"arvore"=> $nomeArvore
		);

		$funcionarioRetornoArvore     = "";
		$funcionarioRetornoArvoreNome = "";

		if (isset($_POST['funcionario'])) {
			$funcionario = $_POST['funcionario'];

			if (!empty($_SESSION['navegacao'][$voltar_inicio - 1])) {

				$funcionarioRetornoArvore = $_SESSION['navegacao'][$voltar_inicio - 1]['funcionario'];
				$funcionarioRetornoArvoreNome = $_SESSION['navegacao'][$voltar_inicio - 1]['arvore'];

			}

		} else {
			unset($_SESSION['gestao']['funcionario_arvore']);
			$usuarioLogado = $_SESSION['usuario']['oid'];
			$funcionario = $this->dao->buscarFuncionarioId($usuarioLogado);
		}

        
        //Carrega os dados da arvore para a função "gerarHtmlArvore"
        $this->carregarDadosUmaArvore($ano, $funcionario);

		$retorno = array(
			'html'                   => utf8_encode($this->gerarHtmlArvore()),
			'funcionario_retorno'    => $funcionarioRetornoArvoreNome,
			'funcionario_retorno_id' => $funcionarioRetornoArvore,
			'voltar_inicio'          => $voltar_inicio
		);

		echo json_encode($retorno);

	}
    
    private function gerarHtmlArvore(){
		ob_start();
		require_once _MODULEDIR_ . 'Gestao/View/ges_layout/arvore_ajax.php';
		$html = ob_get_contents();
		ob_end_clean();	
        return $html;
        
    }

    public function voltarArvore() {

		$ano = isset($_POST['ano']) ? trim($_POST['ano']) : '2014';
		$voltar_inicio = isset($_POST['nivel_navegacao']) ? trim($_POST['nivel_navegacao']) : 0;

		$navegacao = $voltar_inicio;

		$desabilitaVoltar = 0;		
		if ((empty($_SESSION['navegacao'][$voltar_inicio - 1]['funcionario']))) {
			$desabilitaVoltar = 1;
		}

		if (isset($_POST['funcionario']) && !empty($_POST['funcionario'])) {		

			$funcionario = $_POST['funcionario'];

			if (!empty($_SESSION['navegacao'][$voltar_inicio - 1])) {

				$funcionarioRetornoArvore = $_SESSION['navegacao'][$voltar_inicio - 1]['funcionario'];
				$funcionarioRetornoArvoreNome = $_SESSION['navegacao'][$voltar_inicio - 1]['arvore'];

			} else {

				unset($_SESSION['navegacao']);
				$_SESSION['navegacao'][0] = "";

			}

			// $funcionarioRetornoArvore = $_POST['superior'];

			// $funcionarioRetornoArvoreNome = $this->dao->buscarFuncionarioNom01

		} else {
			unset($_SESSION['gestao']['funcionario_arvore']);
			$usuarioLogado = $_SESSION['usuario']['oid'];
			$funcionario = $this->dao->buscarFuncionarioId($usuarioLogado);

		}

		$this->arvoreNome = $this->dao->buscarArvoreNome($funcionario, $ano);
		$this->dadosArvore = $this->dao->montarDadosArvore($funcionario, $ano);




		$navegacao = $voltar_inicio - 1;

		$voltar_tela_inicial = $navegacao == 0 ? 1 : 0;

		ob_start();
		require_once _MODULEDIR_ . 'Gestao/View/ges_layout/arvore_ajax.php';
		$html = ob_get_contents();
		ob_end_clean();		

		$retorno = array(
			'html' => utf8_encode($html),
			'funcionario_retorno' => $funcionarioRetornoArvoreNome,
			'funcionario_retorno_id' => $funcionarioRetornoArvore,
			'desabilita_voltar' => $desabilitaVoltar,
			'navegacao' => $navegacao,
			'voltar_inicio' => $voltar_tela_inicial
		);

		echo json_encode($retorno);

	}


	public function voltarArvoreMultiplo() {

		$ano = isset($_POST['ano']) ? trim($_POST['ano']) : '2014';
		$voltar_inicio = isset($_POST['nivel_navegacao']) ? trim($_POST['nivel_navegacao']) : 0;
		
		$navegacao = $voltar_inicio;

		$desabilitaVoltar = 0;		
		if ((empty($_SESSION['navegacao'][$voltar_inicio - 1]['funcionario']))) {
			$desabilitaVoltar = 1;
		}

		$funcionario = $_POST['funcionario'];		

		$funcionarioRetornoArvore = $_SESSION['navegacao'][$voltar_inicio - 1]['funcionario'];
		$funcionarioRetornoArvoreNome = $_SESSION['navegacao'][$voltar_inicio - 1]['arvore'];

		
		$this->superUsuario  = $this->dao->verificarPermissao('usuarios');

		$this->telaInicial = $navegacao -1 == 0 ? true : false;

		if (!empty($_SESSION['navegacao'][$voltar_inicio - 1]['funcionario']))  {
                        
            //Carrega os dados da arvore para a função "gerarHtmlArvore"
            $this->carregarDadosUmaArvore($ano, $funcionario);
            
		} else {
            
            $this->carregarDadosMultiplasArvores($ano);
            
			unset($_SESSION['navegacao']);
            
			$_SESSION['navegacao'][0] = "";
		}


		$navegacao = $voltar_inicio - 1;

		$voltar_tela_inicial = $navegacao == 1 ? 1 : 0;

		$retorno = array(
			'html'                   => utf8_encode($this->gerarHtmlArvore()),
			'funcionario_retorno'    => $funcionarioRetornoArvoreNome,
			'funcionario_retorno_id' => $funcionarioRetornoArvore,
			'desabilita_voltar'      => $desabilitaVoltar,
			'navegacao'              => $navegacao,
			'voltar_inicio'          => $voltar_tela_inicial
		);

		echo json_encode($retorno);

	}



	


	public function listaAnos(){
        $anos = array();
        $anos[] = 2014;
        $anoAtual = date('Y');
        for($ano = 2014; $ano <= $anoAtual; $ano++){
            $anos[] = $ano+1;
        }
        if($anoAtual == 2013){
            $anos[] = 2015;
        }
        arsort($anos);
        return $anos;
    }

}