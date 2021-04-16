<?php
require_once _MODULEDIR_ . 'Relatorio/DAO/RelSeriaisLancadosDAO.php';
require_once _SITEDIR_ . 'lib/Components/Paginacao/PaginacaoComponente.php';
require_once _SITEDIR_."lib/Components/CsvWriter.php";

class RelSeriaisLancados {
	/**
     * Objeto DAO da classe.
     * 
     * @var CadExemploDAO
     */
	private $dao;
	/**
     * Contém dados a serem utilizados na View.
     * 
     * @var stdClass 
     */
	private $view;
	/**
	 * Mensagem de alerta para campos obrigatórios não preenchidos
	 * @const String
	 */
	const MENSAGEM_NENHUM_REGISTRO = "Não foram encontrados registros.";
	/**
     * Mensagem de alerta para campos obrigatórios não preenchidos
     * @const String
     */
	const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";
	/**
     * Mensagem de erro para o processamento do arquivo
     * @const String
     */
    const MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO = "Houve um erro no processamento do arquivo.";
    /**
     * Mensagem de erro para o processamentos dos dados
     * @const String
     */
    const MENSAGEM_ERRO_PROCESSAMENTO = "Ocorreu um erro ao gerar o relatório, entre em contato com o suporte.";
    /**
     * Mensagem de erro timeout
     * @const String
     */
    const MENSAGEM_ERRO_TIMEOUT = "Tempo de resposta excedido, entre em contato com o suporte.";
    /**
     * Mensagem de alerta para quando a data inicial é maior que a final
     * @const String
     */
    const MENSAGEM_PERIODO_INVALIDO = "O período selecionado é inválido.";

	/**
	 * [__construct description]
	 */
	public function __construct(){

		global $conn;
		$this->dao = new RelSeriaisLancadosDAO($conn);
		//Cria objeto da view
		$this->view = new stdClass();
		//Mensagens
        $this->view->mensagemErro = '';
        $this->view->mensagemAlerta = '';
        $this->view->mensagemSucesso = '';
        $this->view->mensagemInfo = 'Os campos com * são obrigatórios.';
        //Dados para view
        $this->view->dados = null;
        //Filtros/parametros utlizados na view
        $this->view->parametros = null;
        //Status de uma transação 
        $this->view->status = false;
        //Campos do formulario
        $this->view->campos = null;
        // Paginação
        $this->view->paginacao = null;
        // Ordenação
        $this->view->ordenacao = null;
	}
	
	/**
     * Método padrão da classe. 
     * 
     * Reponsável também por realizar a pesquisa invocando o método privado
     * 
     * @return void
     */
	public function index(){

		try {
           
            $this->view->parametros = $this->tratarParametros();

            //Inicializa os dados
            $this->inicializarParametros();

            //Verificar se a ação pesquisar e executa pesquisa
            if (isset($this->view->parametros->acao) && $this->view->parametros->acao != '') {

                if($this->view->parametros->acao === 'listar-cidades') {
                    $this->listarCidadesEstado($this->view->parametros->uf);
                }

                // Validacao
                $this->validarCamposPesquisa($this->view->parametros);

            	if($this->view->parametros->acao === 'pesquisar') {

                    if($this->view->parametros->tipo_relatorio == 'geral') {

        	           $this->view->dados = $this->pesquisar($this->view->parametros);

                    } else if($this->view->parametros->tipo_relatorio == 'duplicado') {

                        $seriaisDuplicados = $this->pesquisarSeriaisRepresentantes($this->view->parametros);
                        
                        if(is_null($seriaisDuplicados)) {
                            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
                        } else {

                            $this->view->parametros->seriaisDuplicados = $seriaisDuplicados;
                            $this->view->dados = $this->pesquisar($this->view->parametros);
                        }
                    }

                } else if($this->view->parametros->acao === 'gerar-csv') {

                    if($this->view->parametros->tipo_relatorio == 'geral') {

                        $this->view->arquivoCSV = $this->gerarCSV($this->view->parametros);

                    } else if($this->view->parametros->tipo_relatorio == 'duplicado') {

                        $seriaisDuplicados = $this->pesquisarSeriaisRepresentantes($this->view->parametros);

                        if(is_null($seriaisDuplicados)) {
                            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
                        } else {
                            $this->view->parametros->seriaisDuplicados = $seriaisDuplicados;
                            $this->view->arquivoCSV = $this->gerarCSV($this->view->parametros);
                        }
                    }

                }
            }

        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {
            $this->view->mensagemAlerta = $e->getMessage();
        }

        //Inclui a view padrão
		require_once _MODULEDIR_ . 'Relatorio/View/rel_seriais_lancados/index.php';
	}

	/**
     * Responsável por tratar e retornar o resultado da pesquisa. 
     * 
     * @param stdClass $filtros Filtros da pesquisa
     * 
     * @return array
     */
    private function pesquisar(stdClass $filtros) {

        try {
            $parametros = clone $filtros;
            $paginacao = new PaginacaoComponente();

            if(isset($parametros->data_inicial) && $parametros->data_inicial != '' ) {
                $parametros->data_inicial = $this->transformaData($parametros->data_inicial);
            }

            if(isset($parametros->data_final) && $parametros->data_final != '' ) {
                $parametros->data_final = $this->transformaData($parametros->data_final);
            }

            $totalRegistros = $this->dao->pesquisar($parametros);

            $this->view->totalResultados = $totalRegistros[0]->total_registros;
            
            // Valida se houve resultado na pesquisa
            if (intval($totalRegistros[0]->total_registros) == 0) {
                throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
            }

            // Seta quantidades de registros por página
            $paginacao->setQuantidadesArray(array(10, 25, 50, 100, 200));
            // Desabilita combo de classificacao
            $paginacao->desabilitarComboClassificacao();
            $this->view->ordenacao = $paginacao->gerarOrdenacao();
            $this->view->paginacao = $paginacao->gerarPaginacao($this->view->totalResultados);

            $resultadoPesquisa = $this->dao->pesquisar($parametros, $paginacao->buscarPaginacao());

            $this->view->status = TRUE;
            
            return $resultadoPesquisa;
        } catch (Exception $e) {
            $this->view->mensagemAlerta = $e->getMessage();
            //throw new Exception ($this->view->mensagemAlerta);
        }
    }

    /**
     * [Pesquisa seriais que estao com mais de 1 representante]
     * @param  stdClass $filtros [description]
     * @return [type]            [description]
     */
    private function pesquisarSeriaisRepresentantes(stdClass $filtros) {

        $parametros = clone $filtros;
        $arraySeriais = array();
        $arrayDuplicados = array();
        $retorno = null;


        if(isset($parametros->data_inicial) && $parametros->data_inicial != '' ) {
            $parametros->data_inicial = $this->transformaData($parametros->data_inicial);
        }

        if(isset($parametros->data_final) && $parametros->data_final != '' ) {
            $parametros->data_final = $this->transformaData($parametros->data_final);
        }

        $dados = $this->dao->pesquisar($parametros,false);

        if(is_array($dados)) {
            foreach($dados as $resultado) {
                
                if(!isset($arraySeriais[$resultado->imobserial])) {

                    $arraySeriais[$resultado->imobserial]['repoid'] = $resultado->repoid;
                    $arraySeriais[$resultado->imobserial]['serial'] = $resultado->imobserial;

                } else if(isset($arraySeriais[$resultado->imobserial])) {

                    // Se eu já possuir o serial setado e o representante for diferente 
                    if($arraySeriais[$resultado->imobserial]['serial'] == $resultado->imobserial && 
                        $arraySeriais[$resultado->imobserial]['repoid'] != $resultado->repoid) {
                        $arrayDuplicados[] = "'".$resultado->imobserial."'";
                    }

                } 

            }
        }

        if(count($arrayDuplicados) > 0) {
            $retorno = implode(',', $arrayDuplicados);
        }

        return $retorno;
    }

    /**
     * Método usado para gerar o arquivo csv
     * @param  stdClass $filtros [description]
     * @return array            
     */
    private function gerarCSV(stdClass $filtros) {

        $arquivo = 'relatorio_ser_'.time().'.csv';
        $diretorio = '/var/www/docs_temporario/';

        $parametros = clone $filtros;

        // Validacao
        $this->validarCamposPesquisa($parametros);

        if(isset($parametros->data_inicial) && $parametros->data_inicial != '' ) {
            $parametros->data_inicial = $this->transformaData($parametros->data_inicial);
        }

        if(isset($parametros->data_final) && $parametros->data_final != '' ) {
            $parametros->data_final = $this->transformaData($parametros->data_final);
        }

        try {
            if (is_dir($diretorio) && is_writable($diretorio)) {

                $dados = $this->dao->pesquisar($parametros,false);
                
                if(count($dados) > 0) {

                    $csvWriter = new CsvWriter($diretorio.$arquivo, ';', '', true);

                    // Colunas
                    $csvWriter->addLine(array(
                        'Data Ajuste',
                        'Inventario',
                        'Representante',
                        'Código item',
                        'Produto',
                        'Modelo',
                        'Serial',
                        'Estoque atual',
                        'Valor unitário'
                    ));

                    foreach($dados as $resultado) {

                        $linha[0] = date("d/m/Y",strtotime($resultado->invdt_ajuste));
                        $linha[1] = $resultado->invoid;
                        $linha[2] = $resultado->repnome;
                        $linha[3] = $resultado->prdoid;
                        $linha[4] = $resultado->prdproduto;
                        $linha[5] = $this->retornaModeloImobilizado($resultado);
                        $linha[6] = $resultado->imobserial;
                        $linha[7] = $this->retornaRepresentanteEquipamento($resultado);

                        if(!is_null($resultado->pcmcusto_medio)) {
                            $linha[8] = number_format($resultado->pcmcusto_medio, 2, ',', '.');
                        } else {
                            $linha[8] = '';
                        }

                        $csvWriter->addLine($linha);
                    }

                    //Verifica se o arquivo foi gerado
                    $arquivoGerado = file_exists($diretorio.$arquivo);

                    if ($arquivoGerado === false) {
                        throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO);
                    }

                    return array('caminho' => $diretorio . $arquivo , 'arquivo' => $arquivo);
                } else {
                    throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
                }
            } 

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

	/**
	* Valida campos da pesquisa
	* @param  stdClass $dados [description]
	* @return [type]          [description]
	*/
    private function validarCamposPesquisa(stdClass $dados) {

        $camposDestaques = array();
        $camposObrigatorios = array('data_inicial','data_final','tipo_relatorio');

        foreach ($camposObrigatorios as $value) {
        	if(trim($dados->$value) == '') {
        		$camposDestaques[] = array('campo' => $value);
        	}
        }

        // Caso os campos obrigatórios não estejam preenchidos
        if(count($camposDestaques) > 0) {
        	$this->view->campos = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        } else {

	        // Trata data final menor do que a data inicial
            $flagPeriodo = $this->validarPeriodo($this->transformaData($dados->data_inicial),$this->transformaData($dados->data_final));
            // Trata o período máximo (6 meses)
            $flagPeriodoMaximo = $this->validaPeriodoDataMaximo($dados->data_inicial,$dados->data_final,6);

            if ($flagPeriodo == false || $flagPeriodoMaximo == false) {

                $camposDestaques[] = array('campo' => 'data_inicial');
                $camposDestaques[] = array('campo' => 'data_final');

                $this->view->campos = $camposDestaques;
                throw new Exception(self::MENSAGEM_PERIODO_INVALIDO);
            }
        }
    }

    /**
     * Valida periodo entre datas
     * @param  [type] $dataInicial [description]
     * @param  [type] $dataFinal   [description]
     * @return boolean
     */
    private function validarPeriodo($dataInicial, $dataFinal){

        $dataIni = explode('-',$dataInicial);
        $dataFim = explode('-',$dataFinal);

        $dataIni = mktime(0,0,0,$dataIni[1],$dataIni[2],$dataIni[0]);
        $dataFim = mktime(0,0,0,$dataFim[1],$dataFim[2],$dataFim[0]);

        if($dataIni <= $dataFim) {
            return true;
        }

        return false;
    }

    /**
     * [Valida o maximo de meses permitidos para a pesquisa]
     * @param  [type] $dataInicial [description]
     * @param  [type] $dataFinal   [description]
     * @param  [type] $qtdMeses    [description]
     * @return [type]              [description]
     */
    private function validaPeriodoDataMaximo($dataInicial,$dataFinal,$qtdMeses) {

        $dtIni = $this->transformaData($dataInicial);
        $dtFim = strtotime($this->transformaData($dataFinal));
        $qtdMeses = (int) $qtdMeses;

        if($qtdMeses > 0) {
            $periodoMaximo = strtotime($dtIni." + ".$qtdMeses." month");
            
            if($dtFim <= $periodoMaximo) {
                return true;
            }
        }

        return false;
    }

	/**
	* Trata os parametros do POST/GET. Preenche um objeto com os parametros
	* do POST e/ou GET.
	* 
	* @return stdClass Parametros tradados
	* 
	* @retrun stdClass
	*/
    private function tratarParametros() {
        $retorno = new stdClass();

        if (count($_POST) > 0) {
            
            $this->view->paginacao = null;

            foreach ($_POST as $key => $value) {
                $retorno->$key = isset($_POST[$key]) ? $value : '';
            }
        } 

        if (count($_GET) > 0) {

            foreach ($_GET as $key => $value) {
                
                //Verifica se atributo já existe e não sobrescreve.
                if (!isset($retorno->$key)) {
                     $retorno->$key = isset($_GET[$key]) ? $value : '';
                }
            }

        }
        
        return $retorno;
    }

    /**
     * Transforma data
     * @param  [type] $data [description]
     * @return string
     */
    private function transformaData($data){
        return implode('-', array_reverse(explode('/', substr($data, 0, 10)))).substr($data, 10);
    }

	/**
     * Inicializa parametros
     * 
     * @return void
     */
    private function inicializarParametros() {
        $this->view->parametros->data_inicial = isset($this->view->parametros->data_inicial) ? $this->view->parametros->data_inicial : "" ; 		
        $this->view->parametros->data_final = isset($this->view->parametros->data_final) ? $this->view->parametros->data_final : "" ; 		
        $this->view->parametros->estado = isset($this->view->parametros->estado) ? $this->view->parametros->estado : "" ;
        $this->view->parametros->representante = isset($this->view->parametros->representante) ? $this->view->parametros->representante : "" ;
        $this->view->parametros->cidade = isset($this->view->parametros->cidade) ? $this->view->parametros->cidade : "" ;
        $this->view->parametros->tipo_relatorio = isset($this->view->parametros->tipo_relatorio) ? $this->view->parametros->tipo_relatorio : "" ;
    }
	
	/**
	 * [Lista todas as cidades passando a sigla do estado]
	 * @param  [type] $uf [description]
	 * @return [type]     [description]
	 */
	public function listarCidadesEstado($uf){
	
		$listaCidades = $this->dao->buscaCidadesSiglaEstado($uf);
		
		if(!is_null($listaCidades)){

			array_walk_recursive(
                $listaCidades, function (&$value) {
                   $value = utf8_encode($value);
                }
            );

			echo json_encode($listaCidades);
		} 
		exit;
	}
	
	/**
	 * [Retorna estados]
	 * @return [type] [description]
	 */
	public function retornaEstados() {
		return $this->dao->estados();
	}
	
	/**
	 * [Retorna representantes]
	 * @return [type] [description]
	 */
	public function retornaRepresentantes(){
		return $this->dao->representantes();
	}
	
    /**
     * [Retorna modelo imobilizado]
     * @param  object  $parametros [description]
     * @return [type]             [description]
     */
	public function retornaModeloImobilizado($parametros = null){

        if( trim($parametros->imotcampo_modelo) != '' &&
            trim($parametros->imottabela_secundaria) != '' &&
            trim($parametros->imottabela_modelo) != '' &&
            trim($parametros->imotmodelo) != '' &&
            trim($parametros->imotcampo_serial) != '' &&
            trim($parametros->imobserial) != '') {
            
            $modelo = $this->dao->modeloImobilizado($parametros);

            if(!is_null($modelo) && isset($modelo->modelo_imob)) {
                return $modelo->modelo_imob;
            }
        }

        return '';
    }


    /**
     * [Retorna o representante que está com o equipamento atualmente]
     * @param  object  $parametros [description]
     * @return [string]             [description]
     */
    public function retornaRepresentanteEquipamento($parametros = null) {

        if( trim($parametros->imottabela_secundaria) != '' &&
            trim($parametros->imotprefixo_tabela_secundaria) != '' &&
            trim($parametros->relroid) != '' &&
            trim($parametros->repoid) != '' &&
            trim($parametros->imobserial) != '') {
            
            $repEstoque = $this->dao->estoqueAtual($parametros);

            if(!is_null($repEstoque) && isset($repEstoque->repnome)) {
                return $repEstoque->repnome;
            }
        }
        else if ( trim($parametros->relroid) != '' &&
            trim($parametros->repoid) != '' &&
            trim($parametros->imobserial) != ''){
        	
        	$repEstoque = $this->dao->estoqueAtualImobilizado($parametros);
        	if(!is_null($repEstoque) && isset($repEstoque->repnome)) {
        		return $repEstoque->repnome;
        	}
        }

        return '';
    }

}