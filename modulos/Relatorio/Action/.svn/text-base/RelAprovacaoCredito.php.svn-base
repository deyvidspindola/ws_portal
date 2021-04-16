<?php

/**
 * 
 * @author Robson Silva
 * @since 20/05/2013
 * @package modulos/Relatorio/Action
 */

include "modulos/Relatorio/DAO/RelAprovacaoCreditoDAO.php";
include "lib/Components/CsvWriter.php";

class RelAprovacaoCredito{
    
    
    /**
     * Mensagens de retorno
     */
    const MSG_ERRO_PESQUISA    = 'Erro ao realizar a pesquisa.'; //M001
    const MSG_ERRO_ARQUIVO     = 'Erro ao gerar o arquivo.'; //M002
    const MSG_ERRO_VALIDACAO   = 'Existem campos obrigatórios não preenchidos.';//M003
    const MSG_SUCESSO_ARQUIVO  = 'Arquivo gerado com sucesso.';//M005
    const MSG_NENHUM_RESULTADO = 'Nenhum resultado encontrado.';//M004
    const MSG_ERRO_FORA_INVATERVALO_PESQUISA = 'O período máximo para exibição em tela é de 1 mês.';//M006
    const MSG_ERRO_FORA_INVATERVALO_CSV = 'O período máximo para exportação em CSV é de 1 ano.';//M007
    
    /**
    * Acesso a dados do módulo
    * @var RelAprovacaoCreditoDAO
    */
    private $DAO;    
    
    /**
     * Armazena o número de linhas do relatório
     * @var int 
     */
    private $countRelatorio = 0;
    
    /**
     * View a ser carragada 
     */
    private $view = '';
    
    /**
     * Mensagens da view Index
     */
    private $mensagem_erro = '';
    private $mensagem_sucesso = '';
    private $mensagem_alerta = '';
    private $mensagem_info = '';
    
    
    /**
     * Tipos de propostas
     */
    private $tipos_proposta = array();
    
    /**
     * Status das propostas
     */
    private $status_propostas = array();
    
    /**
    * Construtor
    */
    public function __construct() {
        
        global $conn;
        $this->DAO = new RelAprovacaoCreditoDAO($conn);
        
        //Popula o array do tipos de propostas
        $this->tipos_proposta = array(
            "L" => "Locação",
            "D" => "Demonstração",
            "I" => "Duplicação",
            "M" => "Migração de Contrato",
            "U" => "Upgrade",
            "G" => "Downgrade",
            "S" => "Substituição",
            "T" => "Transferência Titularidade",
            "V" => "Transferência Titularidade com Troca Veículo",
            "C" => "Troca de Veiculo",
            "R" => "Revenda"
        );
        
        //Popula o array do status de propostas
        $this->status_propostas = array(
            "P" => "Pendente",
            "R" => "Aguardando Retorno",
            "C" => "Concluído",
            "E" => "Cancelado"
        );
    }
    
    /**
     * Método index do relatório
     * @param Object $filtros Filtros da pesquisa
     * @param string $nome_arquivo Nome do arquivo CSV gerado
     */
    public function index($filtros = null, $nome_arquivo = '') {
        
        
		try {
            //Verifica se os filtros não foram informados e popula o objeto
            if (is_null($filtros) ) {
                $filtros = $this->montarFiltros();
            }
            //Options com os tipos de proposta (usado na view)
			$tipoProposta = $this->carregarTipoProposta($filtros->cb_tipo_proposta);
            //Options com os tipos de contratos (usado na view)
            $tipoContrato = $this->carregarTipoContrato($filtros->cb_tipo_contrato);
            
		} catch (Exception $e) {
            $this->mensagem_erro = $e->getMessage();
            include 'modulos/Relatorio/View/rel_aprovacao_credito/index.php';
		}
                
        /*
         * Chama a view da tela inicial com os filtros
         */
        include 'modulos/Relatorio/View/rel_aprovacao_credito/index.php';
    }
    
    /**
     * Monta os filtros de acordo com os parametros informados
     * @return stdClass Filtros
     */
    private function montarFiltros() {
        //Filtros
        $filtros = new stdClass();
        
        //inicializa os filtros
        $filtros->dt_ini            = ( isset( $_POST['dt_ini'] ) && !empty( $_POST['dt_ini'] ) ) ? $_POST['dt_ini'] : '';
        $filtros->dt_fim            = ( isset( $_POST['dt_fim'] ) && !empty( $_POST['dt_fim'] ) ) ? $_POST['dt_fim'] : '';
        $filtros->cb_gestor         = ( isset( $_POST['cb_gestor'] ) && !empty( $_POST['cb_gestor'] ) ) ? $_POST['cb_gestor'] : '';
        $filtros->cb_financeiro     = ( isset( $_POST['cb_financeiro'] ) && !empty( $_POST['cb_financeiro'] ) ) ? $_POST['cb_financeiro'] : '';
        $filtros->cb_tipo_proposta  = ( isset( $_POST['cb_tipo_proposta'] ) && !empty( $_POST['cb_tipo_proposta'] ) ) ? $_POST['cb_tipo_proposta'] : '';
        $filtros->cb_tipo_contrato  = ( isset( $_POST['cb_tipo_contrato'] ) &&  $_POST['cb_tipo_contrato'] != '' ) ? (int) $_POST['cb_tipo_contrato'] : '';
        $filtros->acao              = ( isset( $_POST['acao'] ) && !empty( $_POST['acao'] ) ) ? $_POST['acao'] : '';
        
        return $filtros;
    }
    
    /**
     * Valida os filtros informados
     * @param stdClass $filtros
     * @throws Exception
     */
    private function validarFiltros(stdClass $filtros){
        //Valida os campos obrigatórios, Lança uma exceção com código -1 para 
        //identificação o erro de validação
        if(empty($filtros->dt_ini)){
            throw new Exception(RelAprovacaoCredito::MSG_ERRO_VALIDACAO, -1);
        } 

        if(empty($filtros->dt_fim)){
            throw new Exception(RelAprovacaoCredito::MSG_ERRO_VALIDACAO, -1);
        }

        //Calcula a diferença das data
        $dt_ini = strtotime($this->DAO->dateToDb($filtros->dt_ini) . ' 00:00:00');
        $dt_fim = strtotime($this->DAO->dateToDb($filtros->dt_fim) . ' 23:59:59');
        //Converte a diferença das de segundos para dias
        $direfenca_dias = floor( ($dt_fim - $dt_ini) / 3600 / 24 );

        //Verica o periodo maximo para geração do relatório/csv
        if ($filtros->acao == 'pesquisar'){
            if ($direfenca_dias > 31){
                throw new Exception(RelAprovacaoCredito::MSG_ERRO_FORA_INVATERVALO_PESQUISA, -1);
            }
        } else {
            if ($direfenca_dias > 366){
                throw new Exception(RelAprovacaoCredito::MSG_ERRO_FORA_INVATERVALO_CSV, -1);
            }
        }
        
    }

    /**
     * Método pesquisa, carrega o relatório na tela
     */
    public function pesquisar() {
        $filtros = null;
        try{
            $filtros = $this->montarFiltros();
            $this->validarFiltros($filtros);

            $this->relatorio($filtros);
            
        } catch (Exception $e){
            if ($e->getCode() == -1){
                //Define a mensagem de erro da validação
                $this->mensagem_alerta = $e->getMessage();
            } else {
                //Define a mensagem de erro padrão da pesquisa
                $this->mensagem_erro = RelAprovacaoCredito::MSG_ERRO_PESQUISA;
            }
            //Inclui cabeçalho em caso de erro
            $this->index($filtros);
        }
    }
    
    /**
     * Exibe o relatório em Tela
     * @param stdClass $filtros
     * @throws Exception
     */
    private function relatorio(stdClass $filtros ) {
        //Carrega a pesquisa do relatório
        $relatorio = $this->DAO->pesquisa( $filtros );   
        //Lança uma exceção em caso de falha na consulta
        if ( $relatorio->error === true ){
            throw new Exception($relatorio->message);
        } else {
            $this->arrayRelatorio = pg_fetch_all($relatorio->resource);
            $this->countRelatorio = pg_num_rows($relatorio->resource);
        }
        
        if ( $relatorio->error === false ){
            if ($this->countRelatorio > 0){
                $this->view = 'modulos/Relatorio/View/rel_aprovacao_credito/resultado_pesq.php';
            } else {
                $this->mensagem_alerta = RelAprovacaoCredito::MSG_NENHUM_RESULTADO;
            }
            
        }
        $this->index($filtros);
    }
    
    
    /**
    * Método para gerar o CSV
    */
    public function gerar_csv() {
        $filtros = array();
        try{
            
            $filtros = $this->montarFiltros();
            //Excuta a validação dos campos
            $this->validarFiltros($filtros);
            
            $this->csv($filtros);
        }
        catch(Exception $e){
            if ($e->getCode() == -1){
                //Define a mensagem de erro da validação
                $this->mensagem_alerta = $e->getMessage();
            } else {
                //Define a mensagem de erro padrão da geração do arquivo
                $this->mensagem_erro = RelAprovacaoCredito::MSG_ERRO_ARQUIVO;
            }
            //Inclui cabeçalho
            $this->index($filtros);
        }
    }
    
    /**
     * Gera o arquivo CSV
     * @param Object $filtros
     * @throws Exception
     */
    private function csv( $filtros ) {
        //Diretório do Arquivo
        $caminho = '/var/www/docs_temporario/';
        
        //Nome do arquivo
        $nome_arquivo = 'aprovacao_credito_'.date("Ymd").'.csv';
        //Flag para identifica se o arquivo foi gerado
        $arquivo = false;
        
        //Carrega a pesquisa do CSV
        $consulta = $this->DAO->pesquisaCsv($filtros);

        //Lança uma exceção em caso de erro na consulta
        if ( $consulta->error === true ){
            throw new Exception();
        } else{
            //Verifica se o caminho existe
            if ( file_exists($caminho) ){
                // Gera CSV
                $csvWriter = new CsvWriter( $caminho.$nome_arquivo, ';', '', true);

                //Gera o cabeçalho 
                $cabecalho = array(
                    "Contrato",
                    "Cliente",
                    "Tipo de Contrato",
                    "CPF/CNPJ",
                    "Data de Cadastro",
                    "Status",
                    "Tipo de Pessoa",
                    "Tipo de Proposta",
                    "Observação do Financeiro",
                    "Serasa",
                    "Data da Aprovação do Financeiro",
                    "Descrição",
                    "Nome do Usuário",
                    "Login"
                );
                
                //Adiciona o Cabeçalho
                $csvWriter->addLine( $cabecalho ); 
                
                //Total de registros
                $this->countRelatorio = pg_num_rows( $consulta->resource );
                
                //Adiciona os dados ao corpo do CSV
                if ($this->countRelatorio > 0){
                    while ($relatorio = pg_fetch_assoc($consulta->resource)) {
                        //Trata os dados
                        $relatorio["contrato"]              = ( !empty($relatorio["contrato"]) )                ? $relatorio["contrato"] : ' ';
                        $relatorio["cliente"]               = ( !empty($relatorio["cliente"]) )                 ? $relatorio["cliente"] : ' ';
                        $relatorio["tipo_contrato"]         = ( !empty($relatorio["tipo_contrato"]) )           ? $relatorio["tipo_contrato"] : ' ';
                        $relatorio["cnpf"]                  = ( !empty($relatorio["cnpf"]) )                    ? $relatorio["cnpf"] : ' ';
                        $relatorio["data_cadastro"]         = ( !empty($relatorio["data_cadastro"]) )           ? $relatorio["data_cadastro"] : ' ';
                        $relatorio["status_proposta"]       = ( !empty($relatorio["status_proposta"]) )         ? $this->status_propostas[$relatorio["status_proposta"]] : ' ';
                        $relatorio["tipo_pessoa"]           = ( !empty($relatorio["tipo_pessoa"]) )             ? $relatorio["tipo_pessoa"] : ' ';
                        $relatorio["tipo_proposta"]         = ( !empty($relatorio["tipo_proposta"]) )           ? $this->tipos_proposta[$relatorio["tipo_proposta"]] : ' ';
                        $relatorio["observacao_financeiro"] = ( !empty($relatorio["observacao_financeiro"]) )   ? $relatorio["observacao_financeiro"] : ' ';
                        $relatorio["aciap"]                 = ( !empty($relatorio["aciap"]) )                   ? $relatorio["aciap"] : ' ';
                        $relatorio["data_aprovacao"]        = ( !empty($relatorio["data_aprovacao"]) )          ? $relatorio["data_aprovacao"] : ' ';
                        $relatorio["status_financeiro"]     = ( !empty($relatorio["status_financeiro"]) )       ? $relatorio["status_financeiro"] : ' ';
                        $relatorio["usuario"]               = ( !empty($relatorio["usuario"]) )                 ? $relatorio["usuario"] : ' ';
                        $relatorio["login_usuario"]         = ( !empty($relatorio["login_usuario"]) )           ? $relatorio["login_usuario"] : ' ';

                        
                        // Corpo do CSV
                        $csvWriter->addLine(
                            array(
                                $relatorio["contrato"],
                                $relatorio["cliente"],             
                                $relatorio["tipo_contrato"],
                                $relatorio["cnpf"],        
                                $relatorio["data_cadastro"],
                                $relatorio["status_proposta"],
                                $relatorio["tipo_pessoa"],
                                $relatorio["tipo_proposta"],
                                $relatorio["observacao_financeiro"],
                                $relatorio["aciap"],
                                $relatorio["data_aprovacao"],
                                $relatorio["status_financeiro"],
                                $relatorio["usuario"],
                                $relatorio["login_usuario"]         
                            )
                        );
                        
                    } //While
                    
                } //IF Count do Relatório
                
            } //IF File_exists
            
            //Verifica se o arquivo foi gerado
            $arquivo = file_exists( $caminho.$nome_arquivo);
            //Lança uma exceção em caso de erro na geração do arquivo
            if ($arquivo === false){
                throw new Exception();
            } 
            if ( $this->countRelatorio > 0 ){
                //Mensagem do arquivo gerado
                $this->mensagem_sucesso = RelAprovacaoCredito::MSG_SUCESSO_ARQUIVO;
            } else {
                $this->mensagem_alerta = RelAprovacaoCredito::MSG_NENHUM_RESULTADO;
            }
            
        } // ELSE Consulta
        //Se o arquivo foi gerado carrega a view para download do CSV
        if ( $arquivo === true ){
            $this->view = 'modulos/Relatorio/View/rel_aprovacao_credito/csv.php';
        }
        //Invoca a Index com o nome do arquivo csv
        $this->index($filtros, $nome_arquivo);
    }
    
    /**
     * Gera os options do combo Tipo Proposta
     * @param string $selecionado
     * @return string
     */
	private function carregarTipoProposta($selecionado="") {
		$html = "";
		$html .= "<option value=''>Escolha</option>";
		foreach ($this->tipos_proposta as $key => $tipo) {
			if ($key == $selecionado) 
				$html .= "<option selected value='$key'>$tipo</option>";	
			else 
				$html .= "<option value='$key'>$tipo</option>";
		}
		return $html;
	}
	
    /**
     * Gera os options do combo Tipo Contrato
     * @param string $selecionado
     * @return string
     * @throws Exception
     */
	private function carregarTipoContrato($selecionado="") {
		$consulta_tipos = $this->DAO->buscarTiposContratos();
        
        $tipos = $consulta_tipos->dados;
        
        $html = "";
        if ($consulta_tipos->error == false){
            $html .= "<option value=''>Escolha</option>";
            foreach ($tipos as $tipo) {
                if ($tipo->tpcoid == $selecionado && $selecionado != "")
                    $html .= "<option selected value='$tipo->tpcoid'>$tipo->tpcdescricao</option>";
                else 
                    $html .= "<option value='$tipo->tpcoid'>$tipo->tpcdescricao</option>";
            }
        } else {
            throw new Exception($consulta_tipos->message);
        }
		return $html;
	}
    
    
}

?>