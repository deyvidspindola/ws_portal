<?php
ini_set('display_errors', 0);
error_reporting(~E_WARNING);
/**
 * PrnDebitoAutomaticoDAO.php
 * 
 * Classe de persistência dos dados
 * 
 * @author Renato Teixeira Bueno
 * @email renato.bueno@meta.com.br
 * @since 20/09/2012
 * @package Principal
 *
 */
class PrnDebitoAutomaticoDAO {

    private $conn;
    
    /*
     * Insere historico do contrato
     * @params
     * 		prphprpoid integer -- Id da proposta
     * 		prphusuoid integer -- Usuário que gravou o histórico
     * 		prphobs text - Observação
     *
     * @autor Renato Teixeira Bueno
     * @email renato.bueno@meta.com.br
     */
    public function inserirHistoricoProposta($params){
    	
    	$sql = "
    			INSERT INTO proposta_historico
    				(
    					prphprpoid,
    					prphusuoid,
    					prphobs
    				)
    			VALUES (
    				{$params['id_proposta']},
			    	{$params['id_usuario']},
			    	'{$params['texto_alteracao']}'
    			)";
    	if (!$rs = pg_query($this->conn, $sql)) {
    		throw new Exception('001');
    	}
    	
    	
    }
    
    
   /*
    * Insere historico do contrato
    * Através da funcao existente no banco historico_termo_i
    * @params
    * 		connumero integer
    * 		usuoid integer
    * 		obs text
    * 		protocolo text
    *
    * @autor Renato Teixeira Bueno
    * @email renato.bueno@meta.com.br
    */
    public function inserirHistoricoContrato($params){
    	
    	$sql = "SELECT
		    	historico_termo_i(
		    		{$params['numero_contrato']},
			    	{$params['id_usuario']},
			    	'{$params['texto_alteracao']}',
			    	'{$params['protocolo']}'
		    	);";
        if (!$rs = pg_query($this->conn, $sql)) {
    		throw new Exception('001');
    	}
    	
    }
    
    

    /*
     * @author	Willian Ouchi
     * @email	willian.ouchi@meta.com.br
     * @return	Array com os itens filtrados pela pesquisa
     * */

    public function getClientes() {

        $clinome = (isset($_POST['clinome'])) ? utf8_decode($_POST['clinome']) : null;
        $clitipo = (isset($_POST['clitipo'])) ? $_POST['clitipo'] : null;
        $clino_documento = (isset($_POST['clino_documento'])) ? $_POST['clino_documento'] : null;

        $clino_documento = preg_replace('/[^\d]/', '', $clino_documento);


        $sql_where = " WHERE clidt_exclusao IS NULL AND clicexclusao IS NULL ";

        if (!empty($clinome)) {
            $sql_where .= " AND clinome ILIKE '%$clinome%' ";
        }

        if (!empty($clitipo)) {
            $sql_where .= " AND clitipo = '$clitipo' ";
        }

        if (!empty($clino_documento)) {
            if ($clitipo == "F") {
                $sql_where .= " AND clino_cpf = $clino_documento ";
            } elseif ($clitipo == "J") {
                $sql_where .= " AND clino_cgc = $clino_documento ";
            }
        }

        $sql = "
                SELECT
                    clioid,
                    clinome,
                    clitipo,
                    CASE WHEN clitipo = 'F' THEN 
                        clino_cpf
                    ELSE 
                        clino_cgc
                    END AS clino_documento,
                    forcnome,
                    bannome,                    
                    clicagencia,
                    clicconta,
                    forcdebito_conta                   
                FROM 
                    clientes
                    LEFT JOIN cliente_cobranca ON clicclioid = clioid AND clicexclusao IS NULL
                    LEFT JOIN forma_cobranca ON forcoid  = clicformacobranca
                    LEFT JOIN banco ON bancodigo = forccfbbanco
                $sql_where 
            ";

        $rs = pg_query($this->conn, $sql);

       return $rs;
    }

    public function getDadosCliente() {

        $clioid = (isset($_POST['clioid'])) ? $_POST['clioid'] : null;
        $sql = "
                SELECT
                    clioid,
                    clinome,  
                    clitipo,
                    CASE WHEN clitipo = 'F' THEN 
                        clino_cpf
                    ELSE 
                        clino_cgc
                    END AS clino_documento,
                    forcoid,
                    clidia_vcto,
                    bancodigo,
                    clicagencia,
                    clicconta,
                    cliemail,
                    cliemail_nfe,
                    endddd,
                    endfone,
                    endno_cep, 
                    CASE
                        WHEN endpaisoid is not null THEN endpaisoid
                        ELSE 1
                    END AS endpaisoid,

		    CASE
                        WHEN endestoid is not null THEN endestoid
                        ELSE (SELECT estoid FROM estado WHERE estuf = enduf)
                    END AS endestoid,                                       
                    endcidade,
                    endbairro,
                    endlogradouro,
                    endno_numero,
                    endcomplemento,
                    forcdebito_conta
                FROM 
                    clientes
                    LEFT JOIN endereco ON endoid = cliend_cobr
                    LEFT JOIN cliente_cobranca ON clicclioid = clioid
                    LEFT JOIN forma_cobranca ON forcoid  = clicformacobranca
                    LEFT JOIN banco ON bancodigo = forccfbbanco
                WHERE
                    clicexclusao IS NULL AND
                    clioid = " . $clioid . "
                ORDER BY 
                    clicoid DESC
                LIMIT 1
        ";
        
        $rs = pg_query($this->conn, $sql);
        
        $resultado = array();
        $resultado['cliente'] = array();

        $rcliente = pg_fetch_assoc($rs);

        $resultado['clioid'] = utf8_encode($rcliente['clioid']);
        $resultado['clinome'] = utf8_encode($rcliente['clinome']);
        $resultado['clitipo'] = $rcliente['clitipo'];
        $resultado['clino_documento'] = $rcliente['clino_documento'];
        $resultado['forcoid'] = $rcliente['forcoid'];
        $resultado['clidia_vcto'] = $rcliente['clidia_vcto'];
        $resultado['bancodigo'] = ($rcliente['forcdebito_conta'] == 'f') ? '' : $rcliente['bancodigo'];
        $resultado['clicagencia'] = $rcliente['clicagencia'];
        $resultado['clicconta'] = $rcliente['clicconta'];
        $resultado['cliemail'] = utf8_encode($rcliente['cliemail']);
        $resultado['cliemail_nfe'] = utf8_encode($rcliente['cliemail_nfe']);
        $resultado['endddd'] = $rcliente['endddd'];
        $resultado['endfone'] = $rcliente['endfone'];
        $resultado['endno_cep'] = $rcliente['endno_cep'];
        $resultado['endpaisoid'] = $rcliente['endpaisoid'];
        $resultado['endestoid'] = $rcliente['endestoid'];
        $resultado['endcidade'] = utf8_encode($rcliente['endcidade']);
        $resultado['endbairro'] = utf8_encode($rcliente['endbairro']);
        $resultado['endlogradouro'] = utf8_encode($rcliente['endlogradouro']);
        $resultado['endno_numero'] = $rcliente['endno_numero'];
        $resultado['endcomplemento'] = utf8_encode($rcliente['endcomplemento']);
        $resultado['forcdebito_conta'] = $rcliente['forcdebito_conta'];
        

        return $resultado;
    }

    /*
     * Método que busca e popula a combo de motivos
     * 
     * @autor Willian Ouchi
     */

    public function getDadosMotivos() {

        $sql = "
            SELECT
                msdaoid,
                msdadescricao
            FROM 
                motivo_susp_debito_automatico
            WHERE
                msdadt_exclusao IS NULL
        ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('ERRO: <b>Falha ao consultar motivos.</b>');
        }

        $resultado = array();

        $cont = 0;
        while ($rmotivos = pg_fetch_assoc($rs)) {

            $resultado[$cont]['msdaoid'] = $rmotivos['msdaoid'];
            $resultado[$cont]['msdadescricao'] = utf8_encode($rmotivos['msdadescricao']);
            $cont++;
        }

        return $resultado;
    }

    /*
     * Método que busca e popula a combo de países
     * 
     * @autor Willian Ouchi
     */

    public function getDadosPaises() {

        $sql = "
            SELECT
                paisoid,
                paisnome
            FROM 
                paises
            WHERE
                paisexclusao IS NULL
        ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('ERRO: <b>Falha ao consultar país.</b>');
        }

        $resultado = array();

        $cont = 0;
        while ($rpaises = pg_fetch_assoc($rs)) {

            $resultado[$cont]['paisoid'] = $rpaises['paisoid'];
            $resultado[$cont]['paisnome'] = utf8_encode($rpaises['paisnome']);
            $cont++;
        }

        return $resultado;
    }

    /**
     * Método que retorna um estado ou uma listagem de estados
     * 
     * @autor Willian Ouchi
     * */
    public function getDadosEstados($pais = null, $estado = null) {
        $resultado = "";
        $where = "";
        
        if ($pais || $estado) {
            
            if ($estado){
                $where .= " AND estoid = $estado";
            }
            
            
            $sql = "
                SELECT
                    estoid,
                    estuf
                FROM 
                    estado
                WHERE
                    estpaisoid = $pais
                    $where
            ";

            if (!$rs = pg_query($this->conn, $sql)) {
                throw new Exception('ERRO: <b>Falha ao consultar estados.</b>');
            }

            $resultado = array();

            $cont = 0;
            while ($rEstados = pg_fetch_assoc($rs)) {

                $resultado[$cont]['estoid'] = $rEstados['estoid'];
                $resultado[$cont]['estuf']  = utf8_encode($rEstados['estuf']);
                $cont++;
            }
        }

        return $resultado;
    }
    
    
    /**
     * Método que retorna uma cidade ou uma listagem de cidades
     * @autor Willian Ouchi
     * */
    public function getDadosCidades($estado = null){
        
        $resultado = "";
        $where = "";
            
        if ($estado){
            $where .= " AND clcestoid = $estado";
        }

        $sql = "
            SELECT
                clcnome
            FROM 
                correios_localidades
            WHERE
                clcnome IS NOT NULL
                $where
            ORDER BY 
                clcnome
        ";
        
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('ERRO: <b>Falha ao consultar cidades.</b>');
        }

        $resultado = array();

        $cont = 0;
        while ($rCidades = pg_fetch_assoc($rs)) {

            $resultado[$cont]['clcnome']  = utf8_encode($rCidades['clcnome']);
            $cont++;
        }

        return $resultado;
    }
    
    
    /**
     * Método que retorna uma cidade ou uma listagem de cidades
     * @autor Willian Ouchi
     * */
    public function getDadosBairros($estado = null, $cidade = null){
        
        $resultado = "";
        $where = "";
            
        if ($estado){
            $where .= " AND clcestoid = $estado";
        }
        
        if ($cidade){
            $where .= " AND clcnome = '$cidade'";
        }

        $sql = "
            SELECT 
                cbaoid,
                cbanome
            FROM 
                correios_localidades
                INNER JOIN correios_bairros ON clcoid = cbaclcoid
            WHERE 
                cbanome IS NOT NULL
                $where
            ORDER BY
                cbanome
        ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('ERRO: <b>Falha ao consultar cidades.</b>');
        }

        $resultado = array();

        $cont = 0;
        while ($rBairros = pg_fetch_assoc($rs)) {

            $resultado[$cont]['cbaoid'] = $rBairros['cbaoid'];
            $resultado[$cont]['cbanome']  = utf8_encode($rBairros['cbanome']);
            $cont++;
        }

        return $resultado;
    }
    
    
    /**
     * Método que retorna uma cidade ou uma listagem de cidades
     * @autor Willian Ouchi
     * */
    public function getDadosEndereco($cep){
        
        unset($resultado);
        unset($where);
            
        if ($cep){            

            $sql = "
                SELECT 
                    clcestoid AS uf, 
                    clcnome AS cidade, 
                    clgnome AS logradouro,
                    clgcbaoid_ini AS bairro_ini, 
                    clgcbaoid_fim AS bairro_fim
                FROM
                    correios_logradouros 
                    INNER JOIN correios_localidades ON clgclcoid = clcoid
                WHERE 
                    clgcep = '$cep'
            ";

            if (!$rs = pg_query($this->conn, $sql)) {
                throw new Exception('ERRO: <b>Falha ao consultar endereço.</b>');
            }
            
            if (pg_num_rows($rs) > 0){
               
                $resultado = array();

                $rBairros = pg_fetch_assoc($rs);

                $resultado['uf'] = $rBairros['uf'];
                $resultado['cidade']  = utf8_encode(strtoupper($rBairros['cidade']));
                $resultado['bairro_ini'] = utf8_encode(strtoupper($this->getDadosBairro($rBairros['bairro_ini'])));
                $resultado['bairro_fim']  = utf8_encode(strtoupper($this->getDadosBairro($rBairros['bairro_fim'])));
                $resultado['logradouro']  = utf8_encode(strtoupper($rBairros['logradouro']));
            
            }
            else{
                $resultado = false;
                
            }
        }
        return $resultado;
    }    
    
    
    /**
     * Método que retorna uma cidade ou uma listagem de cidades
     * @autor Willian Ouchi
     * */
    public function getDadosBairro($bairro){

        if ($bairro){            

            $sql = "
                SELECT
                    cbanome
                FROM
                    correios_bairros
                WHERE
                    cbaoid = $bairro
            ";
            if (!$rs = pg_query($this->conn, $sql)) {
                throw new Exception('ERRO: <b>Falha ao consultar bairro.</b>');
            }
            $rBairro = pg_fetch_assoc($rs);
            
        }
        return $rBairro['cbanome'];
    }
    
    
    /*
     * Método que efetua a busca a forma de cobranca, banco, agencia e conta corrente anteriores do cliente
     * 
     * @autor Renato Teixeira Bueno
     * @email renato.bueno@meta.com.br
     */

    public function getFormaCobrancaAnterior($id_cliente) {

       $sql = "SELECT
                    clicformacobranca as forma_cobranca,
                    forcnome as descricao_forma_cobranca,
                    forccfbbanco as banco,
                    bannome as nome_banco,
                    clicagencia as agencia,
                    clicconta as conta_corrente,
                    forcdebito_conta as debito_em_conta
                FROM
                    cliente_cobranca
                INNER JOIN
                    forma_cobranca ON forcoid = clicformacobranca
                LEFT JOIN
                    banco ON bancodigo = forccfbbanco
                WHERE
                    clicexclusao IS NULL AND 
                    clicclioid = $id_cliente
                ORDER BY 
                    clicoid DESC 
		LIMIT 1
        ";
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('ERRO: <b>Falha ao consultar forma de cobranças do cliente.</b>');
        }

        if (pg_num_rows($rs) > 0) {
            return pg_fetch_object($rs);
        }

        return false;
    }

    /*
     * Método que insere o historico de débito automático
     * 
     * @autor Renato Teixeira Bueno
     * @email renato.bueno@meta.com.br
     */

    public function inserirHistoricoDebAutomatico($arrParams) {

    	/*
    	 * Dados  anteriores
    	 */
        $forma_cobranca_anterior = (!empty($arrParams['forma_cobranca_anterior'])) ? $arrParams['forma_cobranca_anterior'] : 'null';
        $banco_anterior = (!empty($arrParams['banco_anterior'])) ? $arrParams['banco_anterior'] : 'null';
        $agencia_anterior = (!empty($arrParams['agencia_anterior'])) ? "'" . $arrParams['agencia_anterior'] . "'" : 'null';
        $conta_corrente_anterior = (!empty($arrParams['conta_corrente_anterior'])) ? "'" . $arrParams['conta_corrente_anterior'] . "'" : 'null';
        
        /*
         * Dados posteriores
         */
        $banco_posterior = (!empty($arrParams['banco_posterior'])) ? $arrParams['banco_posterior'] : 'null';
        $agencia_posterior = (!empty($arrParams['agencia_posterior'])) ? $arrParams['agencia_posterior'] : 'null';
        $conta_corrente_posterior = (!empty($arrParams['conta_corrente_posterior'])) ? $arrParams['conta_corrente_posterior'] : 'null';
        

       $sql = "INSERT INTO 
                    historico_debito_automatico
                            (
                                    hdaclioid, 
                                    hdausuoid_cadastro, 
                                    hdamsdaoid, 
                                    hdaprotocolo, 
                                    hdadt_cadastro, 
                                    hdaentrada, 
                                    hdatipo_operacao, 
                                    hdaforcoid_posterior, 
                                    hdabanoid_posterior,
                                    hdaagencia_posterior, 
                                    hdacc_posterior, 
                                    hdaforcoid_anterior, 
                                    hdabanoid_anterior, 
                                    hdaagencia_anterior, 
                                    hdacc_anterior
                            )
                    VALUES (
                                    {$arrParams['id_cliente']},
                                    {$arrParams['id_usuario']},
                                    {$arrParams['motivo']},
                                    '{$arrParams['protocolo']}',
                                    NOW(),
                                    '{$arrParams['entrada']}',
                                    UPPER('{$arrParams['tipo_operacao']}'),
                                    {$arrParams['forma_cobranca_posterior']},
                                    $banco_posterior,
                                    $agencia_posterior,
                                    $conta_corrente_posterior,
                                    $forma_cobranca_anterior,
                                    $banco_anterior,
                                    $agencia_anterior,
                                    $conta_corrente_anterior
                              )";
                                    

        if (!$rs = pg_query($this->conn, $sql)) {
           throw new Exception('001');
        }
    }

    /*
     * Atualiza dados das propostas, relacionadas aos contratos ativos do cliente
     * 
     * @autor Renato Teixeira Bueno
     * @email renato.bueno@meta.com.br
     */

    public function atualizarPropostas($id_cliente, $forma_cobranca, $agencia, $conta_corrente) {

    	$agencia = (!empty($agencia)) ? $agencia : 'null';
    	$conta_corrente = (!empty($conta_corrente)) ? $conta_corrente : 'null';
    	
        $sql = "UPDATE
                        proposta
                SET
                        prpforcoid 		  = $forma_cobranca,
                        prpdebito_agencia = $agencia,
                        prpdebito_cc 	  = $conta_corrente				
                WHERE
                        prptermo in (
                                        SELECT
                                                connumero
                                        FROM
                                                contrato
                                        INNER JOIN
                                                proposta ON prptermo = connumero
                                        WHERE
                                                concsioid = 1
                                        AND
                                                conclioid = $id_cliente
                                )";
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('001');
        }
    }

    /*
     * Atualizar dados das propostas de pagamento relacionadas as propostas, relacionadas aos contratos ativos do cliente
     * 
     * @autor Renato Teixeira Bueno
     * @email renato.bueno@meta.com.br
     */

    public function atualizarPropostasPagamento($id_cliente, $forma_cobranca, $banco, $agencia, $conta_corrente) {

    	$banco = (!empty($banco)) ? $banco : 'null';
    	$agencia = (!empty($agencia)) ? $agencia : 'null';
    	$conta_corrente = (!empty($conta_corrente)) ? $conta_corrente : 'null';
    	
        $sql = "UPDATE
                        proposta_pagamento
                SET
                        ppagforcoid 	   = $forma_cobranca,
                        ppagbancodigo	   = $banco,
                        ppagdebito_agencia = $agencia,
                        ppagdebito_cc 	   = $conta_corrente				
                WHERE
                        ppagprpoid in (
                                        SELECT
                                                prpoid
                                        FROM
                                                proposta
                                        INNER JOIN
                                                contrato ON prptermo = connumero
                                        WHERE
                                                concsioid = 1
                                        AND
                                                conclioid = $id_cliente
                                )";
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('001');
        }
    }

    /*
     * Atualiza dados dos contratos de pagamentos relacionados ao cliente
     * 
     * @autor Renato Teixeira Bueno
     * @email renato.bueno@meta.com.br
     */

    public function atualizarContratosPagamento($id_cliente, $forma_cobranca, $banco, $agencia, $conta_corrente) {

    	$banco = (!empty($banco)) ? $banco : 'null';
    	$agencia = (!empty($agencia)) ? $agencia : 'null';
    	$conta_corrente = (!empty($conta_corrente)) ? $conta_corrente : 'null';
    	
       $sql = "UPDATE
                        contrato_pagamento
                SET
                        cpagforcoid 		= $forma_cobranca,
                        cpagbancodigo 		= $banco,
                        cpagdebito_agencia  = $agencia,
                        cpagdebito_cc 		= $conta_corrente
                WHERE
                        cpagconoid IN (
                                SELECT
                                        connumero
                                FROM
                                        contrato
                                WHERE
                                        concsioid = 1
                                AND
                                        conclioid = $id_cliente
                        )";
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('001');
        }
    }

    /*
     * Atualiza dados do cliente
     * :cliemail => se vier preechido
     * :cliemail_nfe => se vier preechido
     * :cliformacobranca => sempre
     * 
     * @params
     * $id_cliente => clioid
     * $campos = array(
     * 		'cliformacobranca' => $valor, 
     * 		'cliemail_nfe' => $valor, 
     * 		'cliemail' => $valor 
     * )
     *
     * @autor Renato Teixeira Bueno
     * @email renato.bueno@meta.com.br
     */

    public function atualizarCliente($id_cliente, $campos) {

        $sql = "UPDATE
                        clientes
                SET
                        $campos
                WHERE
                        clioid = " . $id_cliente;
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('001');
        }
    }

    /*
     *
     * Atualiza o endereço de cobranca relacionado ao cliente
     * Insere no update apenas os campos cujas variaveis estão preenchidas
     * 
     * Nenhum campo obrigatório. 
     * 
     * @params
     * $id_cliente => clioid
     * $campos = array(
     * 		'endno_cep' 	 => $valor,
     * 		'endpaisoid' 	 => $valor,
     * 		'endestoid' 	 => $valor,
     * 		'endbairro' 	 => $valor,
     * 		'endlogradouro'  => $valor,
     * 		'endno_numero'   => $valor,
     * 		'endcomplemento' => $valor,
     * 		'endddd' 		 => $valor,
     * 		'endfone'		 => $valor
     * )
     * 
     * 
     * @autor Renato Teixeira Bueno
     * @email renato.bueno@meta.com.br
     */

    public function atualizarEnderecoCobranca($id_cliente, $campos) {

        $sql = "UPDATE
                        endereco
                SET
                        $campos
                WHERE
                        endoid  = (
                                SELECT
                                        cliend_cobr
                                FROM
                                        endereco 
                                INNER JOIN
                                        clientes on endoid = cliend_cobr
                                WHERE
                                        clioid = $id_cliente
					)";
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('001');
        }
    }

    /*
     * Atualiza dados da cobrança relacionada ao cliente
     * 
     * @autor Renato Teixeira Bueno
     * @email renato.bueno@meta.com.br
     */

    public function atualizarCobranca($id_cliente, $forma_cobranca, $agencia, $conta_corrente) {
    	
    	$agencia = (!empty($agencia)) ? $agencia : 'null';
    	$conta_corrente = (!empty($conta_corrente)) ? $conta_corrente : 'null';

       $sql = "UPDATE
                        cliente_cobranca
                SET
                        clicformacobranca = $forma_cobranca,
                        clicagencia = $agencia,
                        clicconta = $conta_corrente
                WHERE
                    clicexclusao IS NULL AND    
                        clicclioid = $id_cliente";
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('001');
        }
        
    }

    /*
     * Atualiza os títulos futuros que não tenham gerado arquivo para o banco
     * E cuja forma de cobrança seja Cobrança Registrada
     * Ou Boleto
     * Ou Débito Automático
     * relacionados ao cliente
     *
     * @autor Renato Teixeira Bueno
     * @email renato.bueno@meta.com.br
     */

    public function atualizarTitulos($id_cliente, $forma_cobranca, $banco, $conta_corrente) {
        
        $banco = (!empty($banco)) ? $banco : 'null';
    	$conta_corrente = (!empty($conta_corrente)) ? $conta_corrente : 'null';
        
        $sql = "UPDATE
                        titulo
                SET
                        titformacobranca = $forma_cobranca,
                        titcfbbanco = $banco,
                        titconta_corrente = $conta_corrente
                WHERE
                	titdt_vencimento > NOW()
                AND
                	titdt_credito IS NULL
                AND
                (
                     titnumero_registro_banco IS NULL
                    OR (titemissao IS NULL AND titno_remessa IS NULL) 
                )
                AND 
                        titclioid = $id_cliente
                AND
                        titformacobranca IN (
                                SELECT 
                                        forcoid
                                FROM
                                        forma_cobranca
                                WHERE
                                        forccobranca_registrada IS TRUE
                                OR
                                        forcoid = 1
                                OR
                                        forcdebito_conta IS TRUE
			   		)";
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('001');
        }
    }

    /*
     * Verifica se existe algum contrato ativo do cliente informado
     * 
     * @autor Renato Teixeira Bueno
     * @email renato.bueno@meta.com.br
     */

    public function contratoAtivoCliente($id_cliente) {

        $sql = "SELECT 
                        connumero
                FROM
                        contrato
                WHERE
                        concsioid = 1 
                AND
                        conclioid = $id_cliente";
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('002');
        }

        return (pg_num_rows($rs) > 0) ? true : false;
    }
    
    /*
     * Busca os contratos ativos do cliente
    *
    * @autor Renato Teixeira Bueno
    * @email renato.bueno@meta.com.br
    */
    public function getContratosAtivosByCliente($id_cliente) {
    
    	$sql = "SELECT
		    		connumero
		    	FROM
		    		contrato
		    	WHERE
		    		concsioid = 1
		    	AND
		    		condt_exclusao IS NULL
		    	AND
		    		conclioid = $id_cliente";
    	if (!$rs = pg_query($this->conn, $sql)) {
	    	throw new Exception('001');
	    }
		    
	    return (pg_num_rows($rs) > 0) ? pg_fetch_all($rs) : array();
    }

    /*
     * Busca o email do cliente
     * Se o campo cliemail estiver vazio
     * Pega o campo cliemail_nfe
     * Se ambos estiverem vazios retorna false
     * 
     * @autor Renato Teixeira Bueno
     * @email renato.bueno@meta.com.br
     */

    public function getEmailCliente($id_cliente) {

        $sql = "SELECT
                        CASE WHEN cliemail IS NOT NULL THEN 
                                cliemail
                        WHEN cliemail_nfe IS NOT NULL THEN
                                cliemail_nfe
                        ELSE
                                ''
                        END as email_cliente
                FROM
                        clientes
                WHERE
                        clioid = $id_cliente";
        if (!$rs = pg_query($this->conn, $sql)) {
           throw new Exception('002');
        }

        return pg_fetch_object($rs);
    }

    /*
     * Busca o texto do termo para envio de email
     * através da descricao (gctdescricao)
     * 
     * @autor Renato Teixeira Bueno
     * @email renato.bueno@meta.com.br
     */

    public function getModeloTexto($descricao) {

        $sql = "SELECT
                        gcttexto as texto_mensagem
                FROM
                        gerador_contrato_texto
                WHERE
                        gctdescricao = '$descricao'";
        if (!$rs = pg_query($this->conn, $sql)) {
           throw new Exception('002');
        }

        return pg_fetch_object($rs);
    }

    /*
     * Busca os dados do usuario pelo id
     * 
     * @autor Renato Teixeira Bueno
     * @email renato.bueno@meta.com.br
     */

    public function getDadosUsuario($id_usuario) {

        $sql = "SELECT
                        nm_usuario as nome_usuario
                FROM
                        usuarios
                WHERE
                        cd_usuario = $id_usuario";
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('ERRO: <b>Falha ao buscar dados do usuario.</b>');
        }

        return pg_fetch_object($rs);
    }
    
    public function buscaDadosFormaCobranca($arrParams){
    	
    	if(is_array($arrParams)){
    		$where = implode(' , ', $arrParams);
    	}
    	
    	$sql = " SELECT
			    	forcoid,
			    	forcdebito_conta as debito_em_conta,
			    	forcnome as descricao_forma_cobranca,
			    	forccfbbanco as banco
		    	 FROM
		    		forma_cobranca
		    	 WHERE
		    		forcexclusao IS NULL
    			 $where
    			 ORDER BY
    				forcnome";
    	
    	if (!$rs = pg_query($this->conn, $sql)) {
    	throw new Exception('002');
    	}
    	
    	$cont = 0;
    	while ($rforma_cobranca = pg_fetch_assoc($rs)) {
    	
    		$resultado[$cont]['forcoid'] = $rforma_cobranca['forcoid'];
    		$resultado[$cont]['debito_em_conta'] = $rforma_cobranca['debito_em_conta'];
    		$resultado[$cont]['descricao_forma_cobranca'] = utf8_encode($rforma_cobranca['descricao_forma_cobranca']);
    		$resultado[$cont]['banco'] = utf8_encode($rforma_cobranca['banco']);
    		$cont++;
    	}
    	
    	return $resultado;
    	
    }

    /*
     * Busca os dados da forma de cobranca
     * 
     * @autor Renato Teixeira Bueno
     * @email renato.bueno@meta.com.br
     */

	public function getDadosFormaCobranca($pforma_cobranca = null, $pid_proposta = null) {

        if ($pforma_cobranca) {
            $where = " AND forcoid = $pforma_cobranca";
        }
		
		$order = " ORDER BY descricao_forma_cobranca ";
		
		if($pid_proposta){
			$union .= " UNION
						SELECT
							ppagforcoid as forcoid,
							null as debito_em_conta,
							'' as descricao_forma_cobranca,
							ppagbancodigo as banco
						FROM
							proposta_pagamento
						WHERE
							ppagprpoid = $pid_proposta";
			$order = "";
		}

        $sql = "SELECT                          
                    forcoid,
                    forcdebito_conta as debito_em_conta,
                    forcnome as descricao_forma_cobranca,
                    forccfbbanco as banco
            FROM
                    forma_cobranca
            WHERE
                forcexclusao IS NULL 
                $where
				$order
				$union";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('002');
        }

        $cont = 0;
        while ($rforma_cobranca = pg_fetch_assoc($rs)) {

            $resultado[$cont]['forcoid'] = $rforma_cobranca['forcoid'];
            $resultado[$cont]['debito_em_conta'] = $rforma_cobranca['debito_em_conta'];
            $resultado[$cont]['descricao_forma_cobranca'] = utf8_encode($rforma_cobranca['descricao_forma_cobranca']);
            $resultado[$cont]['banco'] = utf8_encode($rforma_cobranca['banco']);
            $cont++;
        }

        return $resultado;
    }
    

    /*
     * Busca os dados do banco (nome = Itau, Bradesco, etc)
     * 
     * @autor Renato Teixeira Bueno
     * @email renato.bueno@meta.com.br
     */

    public function getDadosBanco($banco = null) {

        if ($banco) {
            $where = " WHERE bancodigo = $banco ";
        }

        $sql = "SELECT
                    bancodigo,    
                    bannome as nome_banco
                FROM
                    banco
                $where
                ORDER BY nome_banco";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('ERRO: <b>Falha ao buscar dados do banco.</b>');
        }

        $cont = 0;
        while ($rbanco = pg_fetch_assoc($rs)) {

            $resultado[$cont]['bancodigo'] = $rbanco['bancodigo'];
            $resultado[$cont]['nome_banco'] = utf8_encode($rbanco['nome_banco']);
            $cont++;
        }

        return $resultado;
    }
    
    public function getBancoPorFormaCobranca($forma_cobranca_posterior) {
    	
    	$sql = "
    			SELECT
                                bancodigo as id_banco,
    				bannome as banco
				FROM
					forma_cobranca
				INNER JOIN 
					banco ON bancodigo = forccfbbanco
    			WHERE
    			  forcoid = $forma_cobranca_posterior";
    	
    	if (!$rs = pg_query($this->conn, $sql)) {
    		throw new Exception('ERRO: <b>Falha ao buscar dados do banco.</b>');
    	}
    	
    	$resultado = array();
    	
    	if(pg_num_rows($rs) > 0){
    	
	    	$cont = 0;
	    	while ($rbanco = pg_fetch_assoc($rs)) {    	
                        $resultado[$cont]['id_banco'] = utf8_encode($rbanco['id_banco']);
	    		$resultado[$cont]['nome_banco'] = utf8_encode($rbanco['banco']);
	    		$cont++;
	    	}
    	}
    	
    	return $resultado;
    	
    }

    /*
     * Insere historico do cliente
     * Através da funcao existente no banco cliente_historico_i
     * @params
     * 		clioid integer
     * 		usuoid integer
     * 		obs text
     * 		tipo text
     * 		protocolo text
     * 		id_atendimento text
     *
     * @autor Renato Teixeira Bueno
     * @email renato.bueno@meta.com.br
     */

    public function inserirHistoricoCliente($params) {

        $sql = "SELECT 
                    cliente_historico_i(
                            {$params['id_cliente']},
                            {$params['id_usuario']},
                            '{$params['texto_alteracao']}',
                            '{$params['tipo']}',
                            '{$params['protocolo']}',
                            {$params['id_atendimento']}
                    );";
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('001');
        }
    }

    /*
     * Construtor
     * 
     * @autor Renato Teixeira Bueno
     * @email renato.bueno@meta.com.br
     */

    public function PrnDebitoAutomaticoDAO($conn) {

        $this->conn = $conn;
    }

}