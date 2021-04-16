<?php

include "modulos/Relatorio/DAO/RelCustoMedioProdutosDAO.php";
include "lib/Components/CsvWriter.php";

class RelCustoMedioProdutos{
    
    /**
    * Acesso a dados do módulo
    * @var RelCustoMedioProdutosDAO
    */
    private $DAO;    
    private $arrayRelatorio;   
    private $countRelatorio;
    
    public $mensagem;

    /**
    * Construtor
    */
    public function __construct() {
        
        global $conn;
        $this->DAO = new RelCustoMedioProdutosDAO($conn);
    }
    
    /**
    * Método index
    */
    public function index() {
        
        $this->arrayRepresentantes = array();
        
        /*
        * Busca os registros para carregar a combo "Representante Responsável"
        */
        $representantes = $this->DAO->buscaRepresentantes();        

        if ( $representantes['error'] === true ){
            $mensagem_erro = $representantes['message'];
        }
        else{
            $this->arrayRepresentantes = pg_fetch_all($representantes['resource']);
        }
        
        /*
         * Chama a view da tela inicial com o filtro
         */
        include 'modulos/Relatorio/View/rel_custo_medio_produtos/index.php';        
    }
    
    /**
    * Método pesquisar
    */
    public function pesquisar() {

        $dt_ini                     = ( !empty($_POST['dt_ini']) )                      ? $_POST['dt_ini']                      : '';
        $dt_fim                     = ( !empty($_POST['dt_fim']) )                      ? $_POST['dt_fim']                      : '';
        $tipo_relatorio             = ( !empty($_POST['tipo_relatorio']) )              ? $_POST['tipo_relatorio']              : '';
        $pesquisar_por              = ( !empty($_POST['pesquisar_por']) )               ? $_POST['pesquisar_por']               : '';
        $representante_responsavel  = ( !empty($_POST['representante_responsavel']) )   ? $_POST['representante_responsavel']   : '';
        $tipo_produto               = ( !empty($_POST['tipo_produto']) )                ? $_POST['tipo_produto']                : '';
        
        $filtros                    = array();
        $mensagem_erro              = "";
        $this->countRelatorio       = 0;
        $this->arrayRelatorio       = array();
        $this->arrayRepresentantes  = array();
        
        try{
            
            if ( !empty($dt_ini) && !empty($dt_fim) ){
                $filtros['dt_ini'] = $dt_ini;
                $filtros['dt_fim'] = $dt_fim;
            }
            
            if ( !empty($tipo_relatorio) ){
                $filtros['tipo_relatorio'] = $tipo_relatorio;
            }
            
            if ( !empty($pesquisar_por) ){
                $filtros['pesquisar_por'] = $pesquisar_por;
            }
            
            if ( !empty($representante_responsavel) ){
                $filtros['representante_responsavel'] = $representante_responsavel;
            }
            
            if ( !empty($tipo_produto) ){
                $filtros['tipo_produto'] = $tipo_produto;
            }

            /*
            * Busca os registros para carregar a combo "Representante Responsável"
            */
            $representantes = $this->DAO->buscaRepresentantes();        

            if ( $representantes['error'] === true ){
                $mensagem_erro = $representantes['message'];
            }
            else{
                $this->arrayRepresentantes = pg_fetch_all($representantes['resource']);
            }
      
            if ( $tipo_relatorio == "analitico"){                
                $this->relatorioAnalitico( $filtros );
            }
            else{                
                $this->relatorioSintetico( $filtros );
            }
             
        }
        catch(Exception $e){
            $mensagem_erro = $e->getMessage();
        }
        
    }
    
    
    /**
    * Método pesquisar
    */
    public function gerar_csv() {

        $dt_ini                     = ( !empty($_POST['dt_ini']) )                      ? $_POST['dt_ini']                      : '';
        $dt_fim                     = ( !empty($_POST['dt_fim']) )                      ? $_POST['dt_fim']                      : '';
        $tipo_relatorio             = ( !empty($_POST['tipo_relatorio']) )              ? $_POST['tipo_relatorio']              : '';
        $pesquisar_por              = ( !empty($_POST['pesquisar_por']) )               ? $_POST['pesquisar_por']               : '';
        $representante_responsavel  = ( !empty($_POST['representante_responsavel']) )   ? $_POST['representante_responsavel']   : '';
        $tipo_produto               = ( !empty($_POST['tipo_produto']) )                ? $_POST['tipo_produto']                : '';
        
        $filtros                    = array();
        $mensagem_erro              = "";
        $this->countRelatorio       = 0;
        $this->arrayRelatorio       = array();
        $this->arrayRepresentantes  = array();
                
        try{
            
            if ( !empty($dt_ini) && !empty($dt_fim) ){
                $filtros['dt_ini'] = $dt_ini;
                $filtros['dt_fim'] = $dt_fim;
            }
            
            if ( !empty($tipo_relatorio) ){
                $filtros['tipo_relatorio'] = $tipo_relatorio;
            }
            
            if ( !empty($pesquisar_por) ){
                $filtros['pesquisar_por'] = $pesquisar_por;
            }
            
            if ( !empty($representante_responsavel) ){
                $filtros['representante_responsavel'] = $representante_responsavel;
            }
            
            if ( !empty($tipo_produto) ){
                $filtros['tipo_produto'] = $tipo_produto;
            }

            /*
            * Busca os registros para carregar a combo "Representante Responsável"
            */
            $representantes = $this->DAO->buscaRepresentantes();        

            if ( $representantes['error'] === true ){
                $mensagem_erro = $representantes['message'];
            }
            else{
                $this->arrayRepresentantes = pg_fetch_all($representantes['resource']);
            }

            if ( $tipo_relatorio == "analitico"){                
                $this->csvAnalitico( $filtros );
            }
            else{                
                $this->csvSintetico( $filtros );
            }
            
            
            
        }
        catch(Exception $e){
            $mensagem_erro = $e->getMessage();
        }
        
        
    }
    
    
    /**
     * Método relatorioAnalitico
     */
    public function relatorioAnalitico( $filtros ) {
        
        $relatorio = $this->DAO->pesquisaAnalitica( $filtros );        

        if ( $relatorio['error'] === true ){
            
            $mensagem_erro = $relatorio['message'];
        }
        else{
            
            $this->arrayRelatorio = pg_fetch_all($relatorio['resource']);
            $this->countRelatorio = pg_num_rows($relatorio['resource']);
        }
        include 'modulos/Relatorio/View/rel_custo_medio_produtos/index.php';
        
        if ( $relatorio['error'] === false ){
            
            include 'modulos/Relatorio/View/rel_custo_medio_produtos/pesquisa_analitica.php';
        }
    }
    
    
    /**
     * Método relatorioSintetico
     */
    public function relatorioSintetico( $filtros ) {
        
        $relatorio = $this->DAO->pesquisaSintetica( $filtros );        

        if ( $relatorio['error'] === true ){
            
            $mensagem_erro = $relatorio['message'];
        }
        else{
            
            $this->arrayRelatorio = pg_fetch_all($relatorio['resource']);
            $this->countRelatorio = pg_num_rows($relatorio['resource']);
        }
        
        if( $this->countRelatorio > 0 ){
            
            foreach ( $this->arrayRelatorio as $key => $value){

                $value['entioids'] = str_replace( array('{','}'), '', $value['entioids']);

                if ( !empty($value['produto_id']) && !empty($value['entioids']) ){

                    $calculo = $this->DAO->calculaMediaProduto( $value['produto_id'], $value['entioids'] );                
                }

                $this->arrayRelatorio[$key]['custo_medio_unitario'] = $calculo['value'];
                $this->arrayRelatorio[$key]['total'] = number_format(  $calculo['value'], 2, '.', '' ) * $value['quantidade'];
            }
        }
        include 'modulos/Relatorio/View/rel_custo_medio_produtos/index.php';    
        
        if ( $relatorio['error'] === false ){
            
            include 'modulos/Relatorio/View/rel_custo_medio_produtos/pesquisa_sintetica.php';
        }
    }
    
    
    /**
     * Método csvAnalitico
     */
    public function csvAnalitico( $filtros ) {
                
        $caminho = '/var/www/docs_temporario/';
        $nome_arquivo = 'arquivo_custo_medio_produtos_'.date("Y_m_d").'.csv';
        $arquivo = false;
        
        $relatorio = $this->DAO->pesquisaAnalitica( $filtros );        

        if ( $relatorio['error'] === true ){
            
            $mensagem_erro = $relatorio['message'];
        }
        else{
            
            if ( file_exists($caminho) ){
                
                $this->arrayRelatorio = pg_fetch_all( $relatorio['resource'] );
                $this->countRelatorio = pg_num_rows( $relatorio['resource'] );
            
                ob_start();

                // Gera CSV
                $csvWriter = new CsvWriter( $caminho.$nome_arquivo, ';', '' );

                // Cabeçalho
                $csvWriter->addLine( 
                    array(
                        'Código Produto', 
                        'Descrição', 
                        'Representante',
                        'Data',
                        'Tipo de Movimentação',
                        'E ou S',
                        'Quantidade',
                        'Custo Médio Unit.',
                        'Total',
                        'NF',
                        'Fornecedor',
                        'Cliente',
                        'Contrato',
                        'Tipo Contrato' 
                    )
                ); 

                if ( is_array($this->arrayRelatorio) ){

                    foreach( $this->arrayRelatorio as $relatorio ){

                        $relatorio['produto_id']            = ( !empty($relatorio['produto_id']) )  ? $relatorio['produto_id']  : ' ';
                        $relatorio['produto_descricao']     = ( !empty($relatorio['produto_descricao']) )  ? $relatorio['produto_descricao']  : ' ';
                        $relatorio['representante_nome']    = ( !empty($relatorio['representante_nome']) )  ? $relatorio['representante_nome']  : ' ';
                        $relatorio['data']                  = ( !empty($relatorio['data']) )  ? $relatorio['data']  : ' ';
                        $relatorio['movimentacao_tipo']     = ( !empty($relatorio['movimentacao_tipo']) )  ? $relatorio['movimentacao_tipo']  : ' ';
                        $relatorio['emvtipo']               = ( !empty($relatorio['emvtipo']) )  ? $relatorio['emvtipo']  : ' ';
                        $relatorio['quantidade']            = ( !empty($relatorio['quantidade']) )  ? $relatorio['quantidade']  : ' ';
                        $relatorio['custo_medio_unitario']  = ( !empty($relatorio['custo_medio_unitario']) )  ? number_format( $relatorio['custo_medio_unitario'], 2, ',', '.' )  : ' ';
                        $relatorio['total']                 = ( !empty($relatorio['total']) )  ? number_format( $relatorio['total'], 2, ',', '.' )  : ' ';
                        $relatorio['nota']                  = ( !empty($relatorio['nota']) )  ? $relatorio['nota']  : ' ';   
                        $relatorio['serie']                 = ( !empty($relatorio['serie']) )  ? trim($relatorio['serie'])  : ' '; 
                        $relatorio['fornecedor_nome']       = ( !empty($relatorio['fornecedor_nome']) )  ? $relatorio['fornecedor_nome']  : ' ';   
                        $relatorio['cliente_nome']          = ( !empty($relatorio['cliente_nome']) )  ? $relatorio['cliente_nome']  : ' ';   
                        $relatorio['contrato_numero']       = ( !empty($relatorio['contrato_numero']) )  ? $relatorio['contrato_numero']  : ' ';   
                        $relatorio['contrato_tipo']         = ( !empty($relatorio['contrato_tipo']) )  ? $relatorio['contrato_tipo']  : ' ';                   
                        $relatorio['nf'] = $relatorio['nota'] . "-" . $relatorio['serie'];

                        // Corpo
                        $csvWriter->addLine(
                            array(
                                $relatorio['produto_id'],
                                $relatorio['produto_descricao'],
                                $relatorio['representante_nome'],
                                $relatorio['data'],
                                $relatorio['movimentacao_tipo'],
                                $relatorio['emvtipo'],
                                $relatorio['quantidade'],
                                $relatorio['custo_medio_unitario'],
                                $relatorio['total'],
                                $relatorio['nf'],
                                $relatorio['fornecedor_nome'],
                                $relatorio['cliente_nome'],
                                $relatorio['contrato_numero'],
                                $relatorio['contrato_tipo']
                            )
                        );
                    }
                }

                $arquivo = $csvWriter->writeToFile( $caminho.$nome_arquivo );
                ob_end_clean();
            }
            if ($arquivo === false){
                $mensagem_erro = "Houve um erro ao gerar o arquivo.";
            }
            elseif ( $this->countRelatorio > 0 ){
                $mensagem_sucesso = "Arquivo gerado com sucesso.";
            }
        }
        include 'modulos/Relatorio/View/rel_custo_medio_produtos/index.php';
        
        if ( $arquivo === true ){
            include 'modulos/Relatorio/View/rel_custo_medio_produtos/csv.php';
        }
    }
    
    
    /**
     * Método csvAnalitico
     */
    public function csvSintetico( $filtros ) {
        
        $caminho = '/var/www/docs_temporario/';
        $nome_arquivo = 'arquivo_custo_medio_produtos_'.date("Y_m_d").'.csv';
        $arquivo = false;
        
        $relatorio = $this->DAO->pesquisaSintetica( $filtros );        

        if ( $relatorio['error'] === true ){
            
            $mensagem_erro = $relatorio['message'];
        }
        else{
            
            if ( file_exists($caminho) ){
            
                $this->arrayRelatorio = pg_fetch_all($relatorio['resource']);
                $this->countRelatorio = pg_num_rows($relatorio['resource']);

                if( $this->countRelatorio > 0 ){

                    foreach ( $this->arrayRelatorio as $key => $value){

                        $value['entioids'] = str_replace( array('{','}'), '', $value['entioids']);

                        if ( !empty($value['produto_id']) && !empty($value['entioids']) ){

                            $calculo = $this->DAO->calculaMediaProduto( $value['produto_id'], $value['entioids'] );                
                        }

                        $this->arrayRelatorio[$key]['custo_medio_unitario'] = $calculo['value'];
                        $this->arrayRelatorio[$key]['total'] =  number_format(  $calculo['value'], 2, '.', '' ) * $value['quantidade'];
                    }
                }
                ob_start();
                // Gera CSV
                $csvWriter = new CsvWriter( $caminho.$nome_arquivo, ';', '' );

                // Cabeçalho
                $csvWriter->addLine( 
                    array(
                        'Código Produto', 
                        'Descrição', 
                        'Quantidade',
                        'Custo Médio Unit.',
                        'Total'               
                    )
                ); 

                if ( is_array($this->arrayRelatorio) ){

                    foreach( $this->arrayRelatorio as $relatorio ){

                        $relatorio['produto_id']            = ( !empty($relatorio['produto_id']) )  ? $relatorio['produto_id']  : ' ';
                        $relatorio['produto_descricao']     = ( !empty($relatorio['produto_descricao']) )  ? $relatorio['produto_descricao']  : ' ';
                        $relatorio['quantidade']            = ( !empty($relatorio['quantidade']) )  ? $relatorio['quantidade']  : ' ';
                        $relatorio['custo_medio_unitario']  = ( !empty($relatorio['custo_medio_unitario']) )  ? number_format( $relatorio['custo_medio_unitario'], 2, ',', '.' )  : ' ';
                        $relatorio['total']                 = ( !empty($relatorio['total']) )  ? number_format( $relatorio['total'], 2, ',', '.' )  : ' ';

                        // Corpo
                        $csvWriter->addLine(
                            array(
                                $relatorio['produto_id'],
                                $relatorio['produto_descricao'],
                                $relatorio['quantidade'],
                                $relatorio['custo_medio_unitario'],
                                $relatorio['total']                    
                            )
                        );
                    }
                }

                $arquivo = $csvWriter->writeToFile( $caminho.$nome_arquivo );
                ob_end_clean();
                            
            }
            
            if ($arquivo === false){
                $mensagem_erro = "Houve um erro ao gerar o arquivo.";
            }
            elseif ( $this->countRelatorio > 0 ){
                $mensagem_sucesso = "Arquivo gerado com sucesso.";
            }
            
        }
        
        include 'modulos/Relatorio/View/rel_custo_medio_produtos/index.php';
        
        if ( $arquivo === true ){
         
            include 'modulos/Relatorio/View/rel_custo_medio_produtos/csv.php';
        }
    }
    
}

?>