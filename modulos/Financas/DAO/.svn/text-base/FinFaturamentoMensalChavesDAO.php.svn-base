<?php

// Inclui a classe de funções
include_once "{$libdir}/funcoes.php";

/**
 * Classe responsável pela persistencia de dados.
 *
 * @author Allan Helfstein <allan.helfstein.ext@sascar.com.br>
 * @since 06/11/2017
 * @category Class
 * @package FinFaturamentoMensalChavesDAO
 */

class FinFaturamentoMensalChavesDAO 
{ 
	
	 /* Ajustes de horário para  inserir na query */
    const HORAINICIO = "00:00:00";
    const HORAFINAL  = "23:59:59";

    /*Mensagens de erro*/
    const MENSAGEM_ERRO_0001 = "Empresa inexistente";
    const MENSAGEM_ERRO_0002 = "O campo de periodo pode não está  preenchido corretamente, por favor verifique.";

    /** Conexão com o banco de dados */
    private $conn;
    /** Usuario logado */
    private $usuarioLogado;
    /*dados da tela(input)*/
    private $empresa;
    private $dataInicial;
	private $dataFinal;

    public function __construct($conn) {

        //Seta a conexao na classe
        $this->conn 		= $conn;
        $this->usuarioLogado = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';
        $this->dataInicial 	= null;
        $this->dataFinal 	= null;

        // Se nao tiver nada na sessao assume usuario AUTOMATICO
        if(empty($this->usuarioLogado)) {
            $this->usuarioLogado = 2750;
        }
    }


	public function getEmpresa()
    {
    	return $this->empresa;
    }

    public function setEmpresa($empresa)
    {
    	$this->empresa = $empresa;
    }

    public function getDataInicial()
    {
    	return $this->dataInicial;
    }

    public function setDataInicial($data)
    {
    	$this->dataInicial = $data;
    }

    public function getDataFinal()
    {
    	return $this->dataFinal;
    }

    public function setDataFinal($data)
    {
    	$this->dataFinal = $data;
    }
	
    /**
     * 
     * Retorna as informações da empresa de endereco, agencia de banco, para criar o header do arquivo
     * @param  $empresa
     */

    public function getInformacoesEmpresa($empresa = null)
    {

		$sqlInformacoesEmpresa = "SELECT 
                        				tecoid, 
                        				teccnpj,
                                        tecinscr,
                        				UPPER(formata_str(tecrazao)) AS tecrazao
                        			FROM 
                        				tectran ";
		if($empresa != "")
		{
        	$sqlInformacoesEmpresa.= " WHERE tecoid = ".$empresa;
		}

        $sqlInformacoesEmpresa.= " ORDER BY 
                        				tecrazao";

		if (! $resuInfEmpr = pg_query ( $this->conn, $sqlInformacoesEmpresa )) {
			throw new Exception ( "Erro ao efetuar a consulta de busca das informações da empresa" );
		}

		return pg_fetch_all($resuInfEmpr);
	
    }

     /**
     * Retorna o resultado com as notas fiscais;
     */
    public function getRelatorio()
    {
    	$validaDados =  $this->validaDados();
    	if(!isset($validaDados['erro']))
		{
			
			try
			{
				$sql = "
					SELECT entoid AS cod_entrada,
					       to_ascii(tecrazao) AS empresa,
					       to_ascii(etbdescricao) AS estabelecimento,
					       to_ascii(gdocdescricao) AS grupo_dcto,
					       to_ascii(forfornecedor) AS nome_fornecedor,
					       to_char(entdt_entrada,'dd/mm/yyyy') AS data_entrada,
					       entnota AS nro_nota,
					       enttotal AS valor_total,
					       cast(entchave_acesso_nfe as text) AS chave_acesso 

					FROM entrada
					JOIN fornecedores ON foroid = entforoid
					JOIN tectran ON enttecoid = tecoid
					JOIN estabelecimento ON etboid = entetboid
					JOIN tp_nota_fiscal ON enttnfoid = tnfoid
					JOIN grupo_documento ON gdocoid = tnfgdocoid
					WHERE entforoid=foroid
					  AND entexclusao IS NULL
					  AND entdt_entrada BETWEEN '". implode("-",array_reverse(explode("/",$this->dataInicial)))." ".self::HORAINICIO."' AND '". implode("-",array_reverse(explode("/",$this->dataFinal)))." ".self::HORAFINAL."'
					  ";
			    //valida se a empresa foi selecionada
				if($this->empresa != "")
				{
					$sql.= " AND enttecoid = ".$this->empresa;
				}

			    $sql.= "	
			    	ORDER BY 1
		    	";
		    	if (! $resultado = pg_query ( $this->conn, $sql )) {
					throw new Exception ( "Erro ao efetuar a consulta de busca das informações para o relatório" );
				}

				return pg_fetch_all($resultado);
			}
			catch(\Exception $e)
			{
				return $e;
			}
		}
		else
		{
			return $validaDados;
		}

    }  

    /**
     * Retorna o resultado com as notas fiscais em csv;
     */
    public function getRelatorioCsv()
    {
			$relatorio = $this->getRelatorio(array($this->dataInicial,$this->dataFinal), $this->empresa);
			$this->exportCsvDownload($relatorio, "faturamento_entrada_");
    }  

    /**
     *Validação de inputs
     */
    public function validaDados()
    {
    	$data = array();

    	$data['data_inicial']	= $this->validacaoData($this->dataInicial);
    	$data['data_final']		= $this->validacaoData($this->dataFinal);

    	// valida se a empresa existe;
		if(!is_array($this->getInformacoesEmpresa($this->empresa)))
    	{
    		return array("erro" => self::MENSAGEM_ERRO_0001);
    	}
    	// valida se a data é passada pelo usuário é valida;
    	if($data['data_inicial'] == false && $data['data_final'] == false)
    	{
    		return array("erro" => self::MENSAGEM_ERRO_0002);
    	}
    	
    	return true;
    } 

    function validacaoData($data)
    {
    	if(strpos($data, "/"))
    	{
			$data = explode("/","$data"); // fatia a string $dat em pedados, usando / como referência
			$d = $data[0];
			$m = $data[1];
			$y = $data[2];
		 
			// verifica se a data é válida!
			// 1 = true (válida)
			// 0 = false (inválida)
			$res = checkdate($m,$d,$y);
			if ($res == 1)
			{
			  return true;
			} 
			else 
			{
			  return false;
			}
		}
	}
 
    /*
	* exporta informações para csv
    */
    public function exportCsvDownload($array, $filename = null, $delimiter=";") 
    {
	    

	    header('Content-Type: application/csv; charset=UTF-8');
	    header('Content-Disposition: attachment; filename="'.$filename.date("dmY-His").'.csv";');

	    $f = fopen('php://output', 'w');

	    foreach ($array as $line) {
	        fputcsv($f, $line, $delimiter);
	    }
	    exit;
	    
	}   

		

}
