<?php
/*
* 1 - Incluindo classes utilizadas
*/
include_once("includes/classes/BarraProgressoPHP.php");
/**
* Importação de itens do documento via arquivo CVS
*
* @author Rafel Aguiar <rafael.aguiar@gateware.com.br>
* @package Finanças
* @since 10/04/2016
*/
class FinImportarItensNF
{
  /*
  * Constantes de Mensagens
  */
  const MENSAGEM_ERRO_TIPO_ARQUIVO      = "Arquivo com formato inválido. São aceitos apenas arquivos (.CSV).";
  const MENSAGEM_ERRO_UPLOAD_ARQUIVO    = "Ocorreu um erro inesperado ao enviar o arquivo.";
  const MENSAGEM_ERRO_DOCUMENTO_GRUPO   = "Grupo de documento informando não permitido ou não foi selecionado.";
  const MENSAGEM_ERRO_LAYOUT_LINHAS     = "Arquivo não possue minimos de linhas necessárias.";
  const MENSAGEM_ERRO_LAYOUT_COLUNAS    = "Arquivo CSV com layout incompatível.";


  const MENSAGEM_ERRO_FORNECEDOR_NAO_SELECIONADO = "Favor selecionar o fornecedor.";
  const MENSAGEM_ERRO_ESTABELECIMENTO_NAO_SELECIONADO = "Favor selecionar o estabelecimento.";

  const MENSAGEM_ERRO_CODIGO_PRODUTO    = "Código Produto inválido.";
  const MENSAGEM_ERRO_NCM               = "Código NCM inválido.";
  const MENSAGEM_ERRO_CONTA_CONTABIL    = "Conta Contábil inválida.";
  const MENSAGEM_ERRO_CENTRO_CUSTO      = "Centro de Custo inválido.";
  const MENSAGEM_ERRO_QUANTIDADE        = "Quantidade inválida. (Inteiro válido e maior que zero.)";
  const MENSAGEM_ERRO_VALOR_UNITARIO    = "Valor unitário inválido. (valor monetário válido e positivo.)";
  const MENSAGEM_ERRO_CFOP              = "CFOP inválido. (Inteiro Válido.)";
  const MENSAGEM_ERRO_ORIGEM_ST         = "Origem ST inválida. (Inteiro Válido.)";
  const MENSAGEM_ERRO_ST_ICMS           = "ST ICMS inválido. (Inteiro Válido.)";
  const MENSAGEM_ERRO_CST_IPI           = "Inteiro Válido. (CST IPI inválido.)";


  const MENSAGEM_ERRO_CFOP_DEVE_SER_VAZIO       = "O campo CFOP não pode estar preenchido para esse grupo de documento.";
  const MENSAGEM_ERRO_ORIGEM_ST_DEVE_SER_VAZIO  = "O campo Origem ST não pode estar preenchido para esse grupo de documento.";
  const MENSAGEM_ERRO_ST_ICMS_DEVE_SER_VAZIO    = "O campo ST ICMS não pode estar preenchido para esse grupo de documento.";
  const MENSAGEM_ERRO_CST_IPI_DEVE_SER_VAZIO    = "O campo CST IPI não pode estar preenchido para esse grupo de documento.";

  /*
  * Constantes de configurações
  */
  const PASTA_RETORNO_REMESSA         = "/var/www/docs_temporario";
  const PRE_NOME_ARQUIVO              = "arquivo_ressalva_";
  const TOTAL_COLUNAS                 = 10;


  private $dao;
  private $arquivo;
  private $cabecalho;
  private $documentoGrupo;
  private $empresa;
  private $entiitmoid;
  private $nomeDoArquivo;
  private $conteudo = array();
  private $erros = array();
  private $sucessos = array();
  private $ajax = 1;
  private $valorTotal=0;
  private $cabecalhoValido = "Código Produto ;NCM;Conta Contábil ;Centro de Custo ;Quantidade ;Valor Unitário ;CFOP ;Origem ST;ST ICMS ;CST IPI";
  private $fornecedorid;
  private $estabelecimento;
  /**
  * Construtor
  *
  * @param $dao Objeto DAO da classe
  * @param string  $arquivo  Arquivo do upload
  * @param int  $documentoGrupo  is do tipo de documento
  * @return void
  */
  function __construct($dao, $arquivo ='', $documentoGrupo='' )
  {
    $this->dao  = is_object($dao) ? $dao : NULL;

    if (!empty($arquivo))
      $this->setArquivo($arquivo);

    if (!empty($documentoGrupo))
      $this->setdocumentoGrupo($documentoGrupo);

  }

  /**
  * Seta fornecedorid
  *
  * importante na validacao do cpof
  *
  * @param int $fornecedorid codigo do fornecedor
  * @return void
  */
  public function setFornecedorId($fornecedorid)
  {
      # verifica se passado o fornecedorid
      try {
        if (empty($fornecedorid))
          throw new Exception(self::MENSAGEM_ERRO_FORNECEDOR_NAO_SELECIONADO);

      } catch (Exception $e) {
        if ($this->ajax)
          header('statusText: '.$e->getMessage(), true, 500);
        else {
          echo $e->getMessage();
        }
        exit;
      }
    $this->fornecedorid = $fornecedorid;
  }

  /**
  * Seta estabelecimento
  *
  * importante na validacao do cpof
  *
  * @param int $estabelecimento codigo do estabelecimento
  * @return void
  */
  public function setEstabelecimento($estabelecimento)
  {
      # verifica se passado o estabelecimento
      try {
        if (empty($estabelecimento))
          throw new Exception(self::MENSAGEM_ERRO_ESTABELECIMENTO_NAO_SELECIONADO);

      } catch (Exception $e) {
        if ($this->ajax)
          header('statusText: '.$e->getMessage(), true, 500);
        else {
          echo $e->getMessage();
        }
        exit;
      }
    $this->estabelecimento = $estabelecimento;
  }


  /**
  * Seta entiitmoid
  *
  * importante na criação do item na sessão e usado no index do array
  *
  * @param int $entiitmoid codigo entiitmoid
  * @return void
  */
  public function setEntradaId($entiitmoid)
  {
    $this->entiitmoid = $entiitmoid;
  }
  /**
  * Seta codigo da empresa selecionada
  *
  * importante pq alguns metodos depende dessa imformação para validar seus campos
  *
  * @param int $empresa codigo da empresa selecionada
  * @return void
  */
  public function setEmpresa($empresa)
  {
      $this->empresa = $empresa;
  }

  /**
  * Seta arquivo do upload
  *
  * Valida formatos aceitos são arquivos .csv do tipo text/csv sem limite de tamanho
  * em codigo, porem deve ser respeitada o tamanho maximo configurado no php.ini
  *
  * @param string $arquivo ponteiro para o arquivo do upload $_FILE
  * @return void
  */
  public function setArquivo($arquivo)
  {
    # validando arquivo e valido
    try {
      if (strtolower(substr($arquivo['arquivo']['name'], strrpos($arquivo['arquivo']['name'], ".")+1, 3)) != 'csv')
        throw new Exception(self::MENSAGEM_ERRO_TIPO_ARQUIVO);

      if ($arquivo['arquivo']['error'] != 0)
        throw new Exception(self::MENSAGEM_ERRO_UPLOAD_ARQUIVO);

    } catch (Exception $e) {
      if ($this->ajax)
      {
          header('statusText: '.$e->getMessage(), true, 500);
      }
      else
        echo $e->getMessage();
      exit;
    }

    $this->arquivo= $arquivo;
  }

  /**
  * Seta tipo do documento
  *
  * O parametro tipo do documento será utilizado na validação dos compos
  *
  * @param int $documentoGrupo codigo do tipo de documento
  * @return void
  */
  public function setDocumentoGrupo($documentoGrupo)
  {
    # verifica se existe tipo do documento
    try {
      if (!$this->dao->validarTipoDocumento($documentoGrupo))
        throw new Exception(self::MENSAGEM_ERRO_DOCUMENTO_GRUPO);

    } catch (Exception $e) {
      if ($this->ajax)
        header('statusText: '.$e->getMessage(), true, 500);
      else {
        echo $e->getMessage();
      }
      exit;
    }

    $this->documentoGrupo = $documentoGrupo;
  }


  /**
  * Carrega arquivo
  *
  * Carrega os dados do arquivo do upload e cria um array baseando-se em 'ENTER 13'
  * para a quebra das linhas e ';' para as quebras das colunas
  *
  * Exemplo:
  * --------
  * CABEÇALHO 1; CABEÇALHO 2; CABEÇALHO... 'ENTER 13'
  * col1; col2; coln... 'ENTER 13'
  * col1; col2; coln... 'ENTER 13'
  *
  * @return void
  */
  private function carregarArquivo(){
    #carregando conteudo do arquivo
    $csvString = file_get_contents($this->arquivo['arquivo']['tmp_name']);

    # covertendo 'ENTER 13' para linhas linhas
    $csvLinhas = explode(chr(13), $csvString);

    # covertendo ',' para linhas linhas
    if (is_array($csvLinhas))
    {
      foreach($csvLinhas as $indexLinha =>  $linha)
      {
        $csvTmp = explode(";", $linha);
        # pulando cabeçalho e iginorando linha vazia
        if ($indexLinha > 0 && strlen($linha) > 10)
        {
          $this->conteudo[$indexLinha] = array(
            'codigo_produto'  =>  preg_replace('/\s/','',$csvTmp[0])
            ,'ncm'            =>  $csvTmp[1]
            ,'conta_contabil' =>  $csvTmp[2]
            ,'centro_custo'   =>  $csvTmp[3]
            ,'quantidade'     =>  $csvTmp[4]
            ,'valor_unitario' =>  $csvTmp[5]
            ,'cfop'           =>  $csvTmp[6]
            ,'origem_st'      =>  $csvTmp[7]
            ,'st_icms'        =>  $csvTmp[8]
            ,'cst_ipi'        =>  $csvTmp[9]
          );
        }else if($indexLinha == 0 ){
          $this->cabecalho = $linha;
        }
      }
    }
  }


  /**
  * Validando dados
  *
  * Valida dados do arquivo importado respeitando as regras de layout e valores
  * e fazendo consultas no banco de dados para validar alguns dos compos
  *
  * Estrutura:
  *
  * Código Produto ;NCM;Conta Contábil ;Centro de Custo ;Quantidade ;Valor Unitário ;CFOP ;Origem ST;ST ICMS ;CST IPI
  *
  * Regras:
  *
  * Código Produto *: validar se o código do produto existe na base e é válido;
  * NCM: Se informado, validar se existe;
  * Conta contábil *: validar se existe;
  * Centro de Custo *: validar se existe;
  * Quantidade *: Inteiro válido e maior que zero;
  * Valor unitário *: Valor monetário válido, positivo;
  * CFOP *: Inteiro Válido;
  * Origem ST *:  Inteiro válido;
  * ST ICMS *:  Inteiro válido;
  * CST IPI *: Inteiro válido;
  *
  * OBS1: Para o tipo SEM NF (3), o campo CFOP não é obrigatório, portanto a coluna virá vazia;
  * OBS2: Para os tipos NF Municipais e Sem NF (1,2), os campos Origem ST, ST ICMS, CST IPI, não são obrigatórios, portanto, não virão preenchidos.
  * @return void
  */
  public function processarArquivo(){
    #carregando array do CSV
    $this->carregarArquivo();

    # Validando layout do aqruivo
    try
    {
      # validando se existe linhas
      if (count($this->conteudo) == 0)
        throw new Exception(self::MENSAGEM_ERRO_LAYOUT_LINHAS);

      # validando layout do arquivo
      if ( strtoupper(str_replace(' ','',$this->cabecalho)) != strtoupper(str_replace(' ','',$this->cabecalhoValido)) )
        throw new Exception(self::MENSAGEM_ERRO_LAYOUT_COLUNAS);

      # validando se existe colunas
      if (count($this->conteudo[1]) != self::TOTAL_COLUNAS)
        throw new Exception(self::MENSAGEM_ERRO_LAYOUT_COLUNAS);

    } catch (Exception $e) {
      if ($this->ajax)
        header('statusText: '.$e->getMessage(), true, 500);
      else {
        echo $e->getMessage();
      }
      exit;
    }

    # iniciando classe para controlar barra de progresso
    $progresso = new BarraProgressoPHP(count($this->conteudo)-1);

    # setando informação de nome da fase
    $progresso->setFase('Importando CSV');

    # validando campos do arquivo
    foreach($this->conteudo as $linhaIndex => $registro)
    {

      //usleep(400000);
      # Código Produto *: validar se o código do produto existe na base e é válido;
      ## validar no banco de dados
      if (!$result = $this->dao->validarCodigoProduto($registro['codigo_produto']))
      {
        $this->erros[$linhaIndex][] = array(
          'campo'  =>  "codigo_produto"
          ,'erro'   =>  self::MENSAGEM_ERRO_CODIGO_PRODUTO
        );
      }else{
        $registro['nome_produto'] = $result['prdproduto'];
        $registro['prdptioid_produto'] = $result['prdptioid'];
        $registro['prdtp_cadastro_produto'] = $result['prdtp_cadastro'];
      }

      # NCM: Se informado, validar se existe;
      ## validar no banco de dados
      if (!$this->dao->validarNCM($registro['ncm']))
      {
        $this->erros[$linhaIndex][] = array(
          'campo'  =>  "ncm"
          ,'erro'   =>  self::MENSAGEM_ERRO_NCM
        );
      }

      # Conta contábil *: validar se existe;
      ## validar no banco de dados
      if (!$result = $this->dao->validarContaContabil($registro['conta_contabil'], $this->empresa))
      {
        $this->erros[$linhaIndex][] = array(
          'campo'  =>  "conta_contabil"
          ,'erro'   =>  self::MENSAGEM_ERRO_CONTA_CONTABIL
        );
      }else{
        $registro['conta_contabil_nome'] = $result['plcdescricao'];
        $registro['conta_contabil_conta'] = $result['plcconta'];
        $registro['plcmovimentacao'] = $result['plcmovimentacao'];
      }

      # Centro de Custo *: validar se existe;
      ## validar no banco de dados
      if (!$result = $this->dao->validarCentroCusto($registro['centro_custo'], $this->empresa))
      {
        $this->erros[$linhaIndex][] = array(
          'campo'  =>  "centro_custo"
          ,'erro'   =>  self::MENSAGEM_ERRO_CENTRO_CUSTO
        );
      }else{
        $registro['centro_custo_conta'] = $result['cntno_centro'];
        $registro['centro_custo_nome'] = $result['cntconta'];
      }

      # Quantidade *: Inteiro válido e maior que zero;
      if ($registro['quantidade'] <= 0 || !is_numeric($registro['quantidade']))
      {
        $this->erros[$linhaIndex][] = array(
          'campo'  =>  "quantidade"
          ,'erro'   =>  self::MENSAGEM_ERRO_QUANTIDADE
        );
      }

      # Valor unitário *: Valor monetário válido, positivo;
      if (str_replace(',', '', str_replace('.', '', $registro['valor_unitario'])) < 0 || !is_numeric(str_replace(',', '', str_replace('.', '', $registro['valor_unitario']))))
      {
        $this->erros[$linhaIndex][] = array(
          'campo'  =>  'valor_unitario'
          ,'erro'   =>  self::MENSAGEM_ERRO_VALOR_UNITARIO
        );
      }else{
        # calculando o valor total do item
        $registro['valor_unitario'] = str_replace('R$', '',  strtoupper(str_replace(',','.',str_replace('.','',$registro['valor_unitario']))));
        $registro['valor_total'] = number_format($registro['valor_unitario'] * $registro['quantidade'], 4);
      }

      # CFOP *: Inteiro Válido;
      if (!$registro['cfop'] > 0 && !in_array($this->documentoGrupo, array(3)) )
      {
        $this->erros[$linhaIndex][] = array(
          'campo'  =>  'cfop'
          ,'erro'   =>  self::MENSAGEM_ERRO_CFOP
        );
    }elseif(!in_array($this->documentoGrupo, array(3))){
        if ($erroCFOP = $this->dao->validarCFOP($registro['cfop'],$this->estabelecimento, $this->fornecedorid))
        {
            $this->erros[$linhaIndex][] = array(
              'campo'  =>  'cfop'
              ,'erro'   => $erroCFOP
            );
        }

    }

      # CFOP *:  deve estar vazio quando documento selecionado é "sem nf" = 3
      if ($registro['cfop'] != "" && in_array($this->documentoGrupo, array(3)))
      {
        $this->erros[$linhaIndex][] = array(
          'campo'  =>  'cfop'
          ,'erro'   =>  self::MENSAGEM_ERRO_CFOP_DEVE_SER_VAZIO
        );
      }

      # Origem ST *:  Inteiro válido; ou 0
      if (( !in_array($registro['origem_st'], array(0,1,2) ) )  && !in_array($this->documentoGrupo, array(2,3)) )
      {
        $this->erros[$linhaIndex][] = array(
          'campo'  =>  'origem_st'
          ,'erro'   =>  self::MENSAGEM_ERRO_ORIGEM_ST
        );
    }elseif( !in_array($this->documentoGrupo, array(2,3))){
        switch ($registro['origem_st']) {
            case 0:
                $registro['origem_st'] = 1;
            break;
            case 1:
                $registro['origem_st'] = 2;
            break;
            case 2:
                $registro['origem_st'] = 3;
            break;
        }
    }

      #  Origem ST *:  deve estar vazio quando documento selecionado é "NF municial ou Sem NF" = 2,3
      if ($registro['origem_st'] != "" && in_array($this->documentoGrupo, array(2,3)))
      {
        $this->erros[$linhaIndex][] = array(
          'campo'  =>  'origem_st'
          ,'erro'   =>  self::MENSAGEM_ERRO_ORIGEM_ST_DEVE_SER_VAZIO
        );
      }


      # ST ICMS *:  Inteiro válido;
      if (!$this->dao->validarSTICMS($registro['st_icms'])  && !in_array($this->documentoGrupo, array(2,3)))
      {
        $this->erros[$linhaIndex][] = array(
          'campo'  =>  'st_icms'
          ,'erro'   =>  self::MENSAGEM_ERRO_ST_ICMS
        );
      }

      # ST ICMS *:  deve estar vazio quando documento selecionado é "NF municial ou Sem NF" = 2,3
      if ($registro['st_icms'] != "" && in_array($this->documentoGrupo, array(2,3)))
      {
        $this->erros[$linhaIndex][] = array(
          'campo'  =>  'st_icms'
          ,'erro'   =>  self::MENSAGEM_ERRO_ST_ICMS_DEVE_SER_VAZIO
        );
      }


      # CST IPI *: Inteiro válido;
      if (!$this->dao->validarCSTIPI($registro['cst_ipi'])  && !in_array($this->documentoGrupo, array(2,3)))
      {
        $this->erros[$linhaIndex][] = array(
          'campo'  =>  'cst_ipi'
          ,'erro'   =>  self::MENSAGEM_ERRO_CST_IPI
        );
      }

      # CST IPI *:  deve estar vazio quando documento selecionado é "NF municial ou Sem NF" = 2,3
      if ($registro['cst_ipi'] != "" && in_array($this->documentoGrupo, array(2,3)))
      {
        $this->erros[$linhaIndex][] = array(
          'campo'  =>  'cst_ipi'
          ,'erro'   =>  self::MENSAGEM_ERRO_CST_IPI_DEVE_SER_VAZIO
        );
      }

      # verifica se ocorreu erro nessa linha, caso sim remove ela do array de importação
      if ( !isset($this->erros[$linhaIndex]) )
      {
        #motando array de itens importado com sucesso
        $this->sucessos[$linhaIndex] = $registro;
        # colocando dados na sessão


        # montando indice do array da sessao
        $indice = $registro['codigo_produto'].'-'.$registro['conta_contabil'].'-'.$registro['centro_custo'].'-'.str_replace(".","",$registro['valor_total'])."-".$this->entiitmoid;

        #montando array do novo item da sessão
        $novoItem = array(
          'oid'                => $registro['codigo_produto']
          ,'nome'              => $registro['nome_produto']
          ,'contacontabiloid'  => $registro['conta_contabil']
          ,'plcconta'          => $registro['conta_contabil_conta']
          ,'entiitmoid'        => ''
          ,'contacontabilnome' => $registro['conta_contabil_nome']
          ,'prdtp_cadastro'    => $registro['prdtp_cadastro_produto']
          ,'prdptioid'         => $registro['prdptioid_produto']
          ,'centrocustooid'    => $registro['centro_custo']
          ,'cntno_centro'      => $registro['centro_custo_conta']
          ,'centrocustonome'   => $registro['centro_custo_nome']
          ,'quantidade'        => $registro['quantidade']
          ,'valor'             => $registro['valor_unitario']
          ,'vl_ipi'            => 0
          ,'vl_total'          => $registro['valor_total']
          ,'plcmovimentacao'   => $registro['plcmovimentacao']
          ,'plcperc_pis'       => ''
          ,'plcperc_cofins'    => ''
          ,'rproid'            => ''
          ,'rprnome'           => ''
          ,'cfop'              => $registro['cfop']
          ,'aliq_dif'          => 0
          ,'bordero'           => ''
          ,'entistrioid'       => $registro['origem_st']
          ,'entisticmsoid'     => $registro['st_icms']
          ,'entistipioid'      => $registro['cst_ipi']
          ,"entiicms_proprio"  => 0
          ,"entisubst_tributaria" => 0
          ,"entidiferencial_aliq" => 0
          ,"entipis_cofins"       => 0
          ,"entiipi"              => 0
          ,"entivl_base"          => NULL
          ,"entiperc_icms"        => NULL
          ,"entivlr_icms"         => NULL
          ,"entivl_base_icms_st"  => NULL
          ,"entiperc_icms_st"     => NULL
          ,"entivl_icms_st"       => NULL
          ,"entivl_base_dif_aliq" => NULL
          ,"entiperc_dif_aliq"    => NULL
          ,"entivl_dif_aliq"      => NULL
          ,"entivl_base_ipi"      => NULL
          ,"entiperc_ipi"         => NULL
          ,"ipi"                  => NULL
          ,"entistcpoid"          => ''
          ,"entivl_base_pis"      => NULL
          ,"entiperc_pis"         => NULL
          ,"entipis"              => NULL
          ,"entistccoid"          => ''
          ,"entivl_base_cofins"   => NULL
          ,"entiperc_cofins"      => NULL
          ,"enticofins"           => NULL
          ,"vl_desconto"          => 0
        );
        # adicionando novo item na sessao
        $corretos[$indice] = $novoItem;

        $this->valorTotal += $registro['valor_total'];
      }

      # incrementa a barra de progresso
      $progresso->proximoPasso();
    }

    # gera arquivo de ressalvas caso exista erros na importação
    if (count($this->erros) > 0)
    {
        $this->gerarArquivoressalvas();
    }else{
        if (is_array($_SESSION['produtos_nf']))
            $_SESSION['produtos_nf'] = array_merge($corretos, $_SESSION['produtos_nf']);
        else
            $_SESSION['produtos_nf'] = $corretos;
    }

    # retorna json do resumo da importação
    echo $this->retornarResumoImportacao();
  }


  /**
  * Gerando arquivo de ressalvas
  *
  * Gera um arquivo com os itens que nao passaram pela validacao e cria uma coluna
  * nova com os erros encontrados
  *
  * @return string link do arquivo de remessa
  */
  private function gerarArquivoressalvas()
  {
    if (count($this->erros) > 0 )
    {
      # Criando cabecalho do arquivo de ressalvas
      $errosStringRetorno =  $this->cabecalho . ";Erros ( linha, coluna, erro )" . chr(13);
      # correndo lista de erros existentes
      foreach($this->erros as $linhaIndex => $erros)
      {
        # montando string de erros
        foreach($erros as $i => $erro)
        {
          $erroString .=  ($erroString!=''?'     ':'') . ($i+1). ") " . $erro['campo'] . ': ' .$erro['erro'];
        }
        # montando corpo do  arquivo de ressalvas
        $errosStringRetorno .= implode(';',$this->conteudo[$linhaIndex]) . "; Linha:" . ($linhaIndex+1) . " - Erro(s): " . $erroString . chr(13);
        $erroString="";
      }

      # gravando o arquivo de ressalvas
      $this->nomeDoArquivo = self::PRE_NOME_ARQUIVO .date('d_m_Y_H_i_s').'.csv';
      file_put_contents(self::PASTA_RETORNO_REMESSA.'/'.$this->nomeDoArquivo,$errosStringRetorno );

      return self::PASTA_RETORNO_REMESSA.'/'.$this->nomeDoArquivo;
    }
  }

  /**
  * Criando json de retorno
  *
  * Gera string de retorno com informacoes do processamento do arquivo e erros
  * e caminho para donwload do arquivo do arquivo de ressalvas
  *
  * @return string json do resumo da importacao
  */
  private function retornarResumoImportacao(){

    $retorno = array(
      'total_erros'       =>  count($this->erros)
      ,'total_importados' =>  count($this->sucessos)
      ,'arquivo_retorno'  =>  count($this->erros) > 0 ?self::PASTA_RETORNO_REMESSA.'/'.$this->nomeDoArquivo:''
      ,'total_valor' =>  number_format($this->valorTotal,4,",",".")
    );


    return '|'.json_encode($retorno);
  }
}
?>
