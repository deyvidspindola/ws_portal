<?php
class FinTransferenciaTitularidadeDAO {
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
    
    * Abre a transação
    
    */
    public function begin() {
        pg_query ( $this->conn, 'BEGIN' );
    }
    
    /**
    
    * Finaliza uma transação
    
    */
    public function commit() {
        pg_query ( $this->conn, 'COMMIT' );
    }
    
    /**
    
    * Aborta uma transação
    
    */
    public function rollback() {
        pg_query ( $this->conn, 'ROLLBACK' );
    }
    
    
    /**
     * STI 84969
     *
     * @param INT $contrato
     * @throws Exception
     * @return multitype:|boolean
     */
    public function verificarParalisacaoFaturamento($contrato) {
        
        $sql = " SELECT parfconoid AS contrato
				   FROM parametros_faturamento
				  WHERE NOW() BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
				    AND parfconoid  IN ($contrato)
				    AND parfativo IS TRUE ";
        
        if (! $rs = pg_query ( $this->conn, $sql )) {
            
            throw new Exception ( 'Erro ao verificar paralisação do faturamento.' );
            
        }
        
        if (pg_num_rows ( $rs ) > 0) {
            return pg_fetch_all( $rs );
        }
        
        return false;
    }
    
    
    
    
    
    
    // retorna a pesquisa dos clientes do autocomplite
    public function pesquisaCliente($cliente) {
        $sql = "SELECT
						clioid as id,
						clitipo AS tipo,
						clinome AS label,
						clioid AS retorno,
						clino_cpf AS retornoCPF,
						clino_cgc AS retornoCNPJ
				   FROM clientes
				   WHERE
					   clidt_exclusao IS NULL
			       AND
					   clinome iLIKE '%$cliente%'
			       ORDER BY clinome ASC LIMIT 100";
        
        return $rsResponse = pg_query ( $this->conn, $sql );
    }
    
    // retorna a pesquisa dos CNPF ou CNPJ dos clientes do Pesquisar
    public function pesquisaClienteCNPJCPF($idCliente) {
        $sql = "SELECT
						clioid as id,
						clinome AS label,
						clino_cpf AS retornoCPF,
						clino_cgc AS retornoCNPJ
				   FROM clientes
				   WHERE
					   clidt_exclusao IS NULL
			       AND
					   clioid = $idCliente
			       ORDER BY clinome ASC LIMIT 100";
        
        return $rsResponse = pg_query ( $this->conn, $sql );
    }
    
    
    public function pesquisastatuscontrato(){
        
        $sql = "select csioid, csidescricao from contrato_situacao";
        
        $rsResponse = pg_query($this->conn, $sql);
        
        if (! is_resource ( $rsResponse ))
            throw new Exception ( 'Falha ao consultar.' );
            
            return pg_fetch_all ( $rsResponse );
            
    }
    
    /* Pesquisa clientes Clientes*/
    public function consulaClienteID($clioid){
        
        try{
            
            if(empty($clioid) && !is_numeric($clioid)){
                throw new Exception('ERRO: <b>Falha ao recuperar dados do cliente informado.</b>');
            }
            
            $sql = " SELECT
	                    clioid,
	                    clinome,
	                    clitipo,
					    clino_cpf,
					    clino_cgc,
	                    CASE WHEN clitipo = 'F' THEN
	                        clino_cpf
	                    ELSE
	                        clino_cgc
	                    END AS clino_documento,
	                    forcoid,
						cdvoid,
	                    clidia_vcto,
	                    bancodigo,
	                    clicagencia,
	                    clicconta,
	                    cliemail,
						endemail,
	                    cliemail_nfe,
	                    endddd,
	                    endfone,
	                    endno_cep,
	                    endcep,
	                    CASE
	                        WHEN endpaisoid is not null THEN endpaisoid
	                        ELSE 1
	                    END AS endpaisoid,
                
			    		CASE
	                        WHEN endestoid is not null THEN endestoid
	                        ELSE (SELECT estoid FROM estado WHERE estuf = enduf)
	                    END AS endestoid,
		   			    enduf,
	                    endcidade,
	                    endbairro,
	                    endlogradouro,
	                    endno_numero,
	                    endcomplemento,
	                    forcdebito_conta,
						clicdias_prazo,
						clicdias_uteis,
						clictipo,
						clicdia_mes,
						clicdia_semana,
						clictitular_conta,
						clifaturamento,
						clifat_locacao,
                        cliclicloid,
                        clidt_nascimento,
                        clipaisoid,
		                endfone_array[1] as fone1,
		                endfone_array[2] as fone2,
		                endfone_array[3] as fone3,
                        forccobranca_cartao_credito,
                        cliccartao,
                        cliccartao_validade,
                        clireg_simples,
                        clino_rg,
                        cliemissor_rg,
                        clirg
                
                
	                FROM
	                    clientes
	                    LEFT JOIN endereco ON endoid = cliend_cobr
	                    LEFT JOIN cliente_cobranca ON clicclioid = clioid
	                    LEFT JOIN forma_cobranca ON forcoid  = clicformacobranca
	                    LEFT JOIN banco ON bancodigo = forccfbbanco
					    LEFT JOIN cliente_dia_vcto ON cdvdia = clidia_vcto
	                WHERE
	                    clicexclusao IS NULL AND
	                    clioid = " . $clioid . "
	                ORDER BY
	                    clicoid DESC
	                LIMIT 1  ";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta  na table clientes " );
            }
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar consulta  na table clientes " );
        }
        
        $clientes = 0;
        if(pg_num_rows($result) > 0){
            $clientes = pg_fetch_assoc($result);
        }
        
        return $clientes;
    }
    public function pesquisaContrato($connumero){
        
        $sql = "SELECT
                          contrato.connumero,
                          proposta.prptermo,
                          proposta.prptermo_original,
                          proposta.prpclioid,
                          clientes.clino_cgc as prpno_cpf_cgc_jur,
                          clientes.clino_cgc as prpno_cgc_jur,
                          clientes.cliuf_res as prpinscricao_uf,
                          clientes.cliinscr as prpinscricao,
                          clientes.clireg_simples,
                          CASE WHEN clientes.clitipo='F'
                          THEN
                            '1'
                          ELSE
                           '2'
                          END as prptipo_pessoa,
                          proposta.prptipo_pessoa_prop,
                          tipo_contrato.tpcoid,
                          ''               as validar_familia_sascar,
                          proposta.prptipo_proposta,
                          to_char(proposta.prpdt_cadastro,'dd/mm/yyyy') as dataCad,
                          (SELECT tpcfamilia FROM tipo_contrato WHERE tpcoid = prptpcoid) AS tpcfamilia,
                          (SELECT tpcseguradora FROM tipo_contrato WHERE tpcoid = prptpcoid) AS prptpcseguradora,
                          veiculo.veichassi,
                          veiculo.veiplaca,
                          veiculo.veino_renavan,
                          proposta.prpmcaoid,
                          modelo.mlotipveioid as prpno_serie_veiculo,
                          ''                as tipo_veiculo,
                          modelo.mlooid,
                          veiculo.veino_motor,
                          veiculo.veino_ano,
                          veiculo.veicor,
                          proposta.prpseguradora,
                          proposta.prpnumero_proposta,
                          proposta.prpapolice,
                          proposta.prputilizacao,
                          proposta.prpdt_inicio_seguro,
                          to_char(prpdt_fim_seguro,'dd/mm/yyyy') as prpdt_fim_seguro,
                          proposta.prpcod_cia,
                          proposta.prpcod_unid_emis,
                          proposta.prpcod_ramo,
                          proposta.prpnum_item,
                          proposta.prpnum_adiantamento,
                          proposta.prpno_endosso,
                          prptom_municipio,
                		  '' as pembnome ,
                  		  '' as  pembcomprimento,
                  		  '' as   pembpotencia ,
                	      '' as pembregistro ,
                	      '' as pembcasco ,
                	      '' as pembtransmissao,
                	      '' as pembhelices,
                	      '' as tmp_num_veiculos,
                	      '' as posicao_veiculo,
                	      proposta.prpdias_demonstracao,
                	      '' as execcontas,
                	      proposta.prpregcoid ,
                	      proposta.prprczoid ,
                	      '' as telemkt ,
                	      '' as propconta,
                	      proposta.prpcorroid,
                	      '' as prpcorroid_funcao_selecionar,
                	      '' as prpcorroid_funcao_limpar ,
                	      '' as prggeroid,
                	      '' as prpcorroid_c,
                	      '' as prpcorretor_c,
                    	  '' as prpcorroid_c_funcao_selecionar,
                	      '' as prpcorroid_c_funcao_limpar,
                	      proposta.prpemail_corretor,
                	      proposta.prpfone_rescorretor,
                	      proposta.prpfone_comcorretor,
                	      proposta.prpfone_celcorretor ,
                		  proposta.prpgerente_neg,
                		  to_char(prpdt_solicitacao,'dd/mm/yyyy') as prpdt_solicitacao,
                          to_char(prphr_solicitacao,'hh24:mi') as prphr_solicitacao,
                		  proposta.prpmotivo,
                		  proposta.prpcorretor_recebe_comissao,
                		  '' as  tipo_associacao,
                		  proposta.prpforcoid,
                		  '' as  prpdia_vcto_boleto,
                		  '' as  dataVencimentoAtualCliente,
                		  '' as  prpcartao,
                		  '' as  nome_portador,
                		  '' as  prpcartao_validade,
                		  '' as  prpbancodigo_cheque,
                		  '' as  prpcheque,
                		  '' as  prpbancodigo_hidden,
                		  '' as  prpbancodigo,
                		  '' as  prpdebito_agencia,
                		  '' as  prpdebito_cc,
                		  '' as  prpcampcoid,
                		  proposta.prpeqcoid,
                		  '' as  prpobroid,
                		  '' as  prpvl_tabela,
                		  '' as  prpvl_minimo,
                		  '' as  eqcprazo_inst,
                		  '' as  prpcpvoid,
                		  '' as  hidden_prpvl_servico,
                		  '' as  prpvl_servico2,
                		  '' as  ppagvl_negociado_adesao,
                		  '' as  valorTaxaInstalacao,
                		  '' as  prpprazo_contrato,
                		  '' as  prpagmulta_rescissoria,
                		  '' as  prpfamilia_produtoSASCARGA,
                		  '' as  ppagvl_deslocamento0,
                   		  '' as  prosobroid,
                		  '' as  prossituacaoL,
                		  '' as  prosvalor,
                		  '' as  prosqtde,
                		  '' as  proscontrato,
                		  '' as  prosno_cep,
                		  '' as  prospaisoid,
                		  '' as  hd3_correios_servico,
                		  '' as  prosuf,
                		  '' as  proscidade,
                		  '' as  hd5_correios_servico,
                		  '' as  prosbairro,
                		  '' as  prosendereco,
                		  '' as prosno_endereco,
                		  '' as  proscompl,'' as  prosmun_ibge,
                		  '' as  posicao_servico,
                		  '' as  prosvalor_minimo,
                		  '' as  prosendoid_gerenciador,
                		  '' as  nao_instalar,
                		  '' as  instalar,
                    	  '' as  acaoTaxaInstalacao,
                	      '' as  origem_chamadaPC,
                	      '' as  documento_pesquisado,
                	      '' as  entradaI,
                	      '' as  indice_vet,
                	      proposta.prpoid,
                	      '' as  prpoid_todos,
                	      '' as  migrar_contrato,
                	      '' as  prospostaCliente,
                	      '' as  pcoroid,
                	      (SELECT tpcassociacao FROM tipo_contrato WHERE tpcoid = proposta.prptpcoid) AS  prptpcassociacao,
                	      proposta.prpautorizacao_alcada,
                          proposta.prpautorizacao_tecnica,
                          proposta.prpstatus_aprovacao_taxa,
                          '' as  materialExclusao,
                          '' as  tipoAutorizacaoDesconto,
                          proposta.prpclifunoid,
                          '' as  obrvl_minimo,
                          '' as  obrvl_maximo,
                          '' as  pagador,
                          '' as  hiddenAcp,
                          '' as  prptipo_proposta,
                          proposta.prpgera_os_instalacao,
                          proposta.prppropensao_churn,
                          proposta.prppropensao_compra,
                          clientes.clioid,
                          clientes.clinome,
                          clientes.clitipo,
                          CASE WHEN clitipo = 'J' THEN
                            clino_cgc
                          ELSE
                            clino_cpf
                          END AS cfp_doc,
                          clientes.clino_cgc as  prpno_cpf_cgc_fis,
                          (SELECT funcpf FROM funcionario WHERE funoid = prpclifunoid limit 1) as prpcpf_funcionario,
                          proposta.prpno_rg,
                          proposta.prpemissor_rg,
                          proposta.prpdt_emissao_rg,
                          proposta.prppai,
                          proposta.prpmae,
                          proposta.prpsexo,
                          proposta.prpestado_civil,
                          clientes.clino_cgc as  prpno_cpf_cgc_jur,
                          proposta.prpoptante_simples,
                	      proposta.prpinscricao_uf,
                	      proposta.prpinscricao ,
                	      proposta.prpinscricao_mun,
                	      proposta.prpclicnae,
                          proposta.prpfone1,
                          proposta.prpfone2,
                          proposta.prpfone3,
                	      '' as    observacaoInstalacao,
                	      '' as   servobroid,
                	      '' as   servvalor,
                	      '' as    servcontrato,
                	      '' as  id_Acp,
                	      '' as  priveiculo,
                	      '' as  priddd,
                	      '' as taxa_instalacao_num_cartao,
                	      '' as  taxa_instalacao_nome_portador,
                	      '' as taxa_instalacao_validade_cartao,
                	      '' as taxa_instalacao_codigo_seguranca,
                	      '' as taxa_instalacao_parcelamento,
                	      '' as taxa_instalacao_qntd_veiculos,
                	      '' as taxa_instalacao_valor,
                	      '' as   taxa_instalacao_parcela,
                	    '' as prcnome_aut,
                	    '' as prccpf_aut,
                	    '' as prcrg_aut,
                	    '' as prcfone_res_aut,
                	    '' as prcfone_com_aut,
                	    '' as prcfone_cel_aut,
                	    '' as prcid_nextel_aut,
                	    '' as prcoid_aut,
                	    '' as replicar_aut,
                	    '' as prcnome_eme,
                	    '' as prcfone_res_eme,
                	    '' as prcfone_com_eme,
                	    '' as prcfone_cel_eme,
                	    '' as prcid_nextel_eme,
                	    '' as prcoid_eme,
                	    '' as replicar_eme,
                	    '' as tmp_pessoa_eme1,
                	    '' as prcnome_ins,
                	    '' as prcfone_res_ins,
                	    '' as prcfone_com_ins,
                	    '' as prcfone_cel_ins,
                	    '' as prcid_nextel_ins,
                	    '' as prcobs_ins,
                	    '' as prcoid_ins,
                	    '' as replicar_ins,
                	    '' as campoAutorizacao,
                	    '' as valorAutorizacao,
                	    proposta.prpstatus,
                	    '' as prpobservacao_financeiro,
                	    proposta.prpresultado_aciap,
                	    '' as prphobs,
                        veiculo.veiebs,
                        veiculo.veimodeoid,
                        veiculo.veiacessorios_pneu,
                        veiculo.veieixcoid,
                        veiculo.veipneus_germinados,
                        veiculo.veidimpoid,
                        veiculo.veicomprimento,
                        veiculo.veicapacidade,
                        proposta.prptipcoid
                        FROM contrato
                            LEFT JOIN proposta on proposta.prptermo = contrato.connumero
                            INNER JOIN clientes on contrato.conclioid = clientes.clioid
                            LEFT JOIN tipo_contrato ON tipo_contrato.tpcoid  = proposta.prptpcoid
                            INNER JOIN veiculo on veiculo.veioid =  contrato.conveioid
                            INNER JOIN modelo ON veiculo.veimlooid = modelo.mlooid
                            INNER JOIN tipo_veiculo ON tipo_veiculo.tipvoid = modelo.mlotipveioid
                        WHERE contrato.connumero =  $connumero    "  ;
        
        
        $result = pg_query ( $this->conn, $sql );
        
        return pg_fetch_all( $result );
    }
    
    
    //@@Retorna os contratos que podem ser transferido passando os parametros do filtro de novo cliente
    public function pesquisaTransferencia($parametros) {
        if ($parametros ['id_cliente'] != '' || ! empty ( $parametros ['id_cliente'] )) {
            $filtro .= " AND conclioid = '" . $parametros ['id_cliente'] . "'";
        }
        
        echo "<pre>";
        //var_dump($parametros);
        //echo " alaa  ". count($parametros);
        echo "</pre>";
        
        
        if ($parametros ['id_cliente'] != '' || ! empty ( $parametros ['id_cliente'] )) {
            $filtro .= " AND conclioid = '" . $parametros ['id_cliente'] . "'";
            
        }
        
        /*if ($parametros ['cliente'] != '' || ! empty ( $parametros ['cliente'] )) {
         $filtro .= " AND conclioid = '" . $parametros ['cliente'] . "'";
         ;
         }*/
        
        $numeroCpfCnpj = $parametros ['cpfcnpj'];
        if(!empty($numeroCpfCnpj)){
            $numeroCpfCnpj = explode(".", $numeroCpfCnpj);
            $numeroCpfCnpj = implode("", array_values($numeroCpfCnpj));
            $numeroCpfCnpj = explode("-", $numeroCpfCnpj);
            $numeroCpfCnpj = implode("", array_values($numeroCpfCnpj));
            $numeroCpfCnpj = explode("/", $numeroCpfCnpj);
            $numeroCpfCnpj = implode("", array_values($numeroCpfCnpj));
        }
        $digitosCPFCNPJ = strlen($numeroCpfCnpj);
        echo "<pre>";
        //var_dump($parametros);
        echo "</pre>";
        
        if ($parametros ['cpfcnpj'] != '' || ! empty ( $parametros ['cpfcnpj'] )) {
            if ($digitosCPFCNPJ == 11) {
                $filtro .= " AND clino_cpf = '" . $numeroCpfCnpj . "'";
                ;
            }
            if ($digitosCPFCNPJ == 14) {
                $filtro .= " AND clino_cgc = '" . $numeroCpfCnpj . "'";
                ;
            }
        }
        
        if ($parametros ['contrato'] != '' || ! empty ( $parametros ['contrato'] )) {
            $filtro .= " AND connumero = " . $parametros ['contrato'] ;
        }
        
        if ( isset($parametros ['contratoIdArray'] ) && !empty ($parametros ['contratoIdArray'] ) ) {
            if(!is_array( $parametros ['contratoIdArray'] )){
                throw new Exception ( 'DAO Param contratoIdArray is not array' );
            }
            $connumero =  implode(',',  $parametros ['contratoIdArray']);
            $filtro .= " AND connumero IN( $connumero )";
        }
        
        if ($parametros ['placa'] != '' || ! empty ( $parametros ['placa'] )) {
            $filtro .= " AND veiplaca = '" . $parametros ['placa'] . "'";
        }
        
        if ($parametros ['classecontrato'] != '' || ! empty ( $parametros ['classecontrato'] )) {
            $classeContratos = $parametros ['classecontrato'];
            
            $filtro .= " AND coneqcoid = '" . $parametros ['classecontrato'] . "'";
            ;
        }
        
        
        
        
        if(!empty($parametros['concsioidArray'])){
            
            if(!is_array($parametros['concsioidArray'])){
                throw new Exception('DAO: Parametro concsioidArray deve ser um array.');
            }
            
            $concsioid = implode(',', $parametros['concsioidArray']);
            
            if(empty($concsioid)){
                throw new Exception('DAO: Parametro concsioidArray deve ser um array com inteiros.');
            }
            
            if($concsioid != 'all') {
                $filtro .= " AND concsioid IN($concsioid) ";
            }
        }
        
        $filtro .= " AND conmodalidade = 'L'";
        
        if ($parametros ['ordenaresultados'] != '' || ! empty ( $parametros ['ordenaresultados'] )) {
            $filtro .= " ORDER BY " . $parametros ['ordenaresultados'] . "";
        }
        
        if ($parametros ['classificaresultados'] != '' || ! empty ( $parametros ['classificaresultados'] )) {
            $classificaResultados = $parametros ['classificaresultados'];
            
            if($classificaResultados == "desc"){
                $filtro .= " DESC";
            }else{
                $filtro .= " ASC ";
            }
        }
        
        if ($parametros ['numeroresultados'] != '' || ! empty ( $parametros ['numeroresultados'] )) {
            $numeroResultados = $parametros ['numeroresultados'];
            
            if($numeroResultados != "all"){
                $filtro .= " LIMIT $numeroResultados";
            }
        }
        
        $sql = "SELECT clinome,
			       veiplaca,
			       connumero,
			       contrato_situacao.csidescricao,
                   contrato.concsioid,
			       cliemail,
			       conprazo_contrato,
			       CASE
			         WHEN clitipo = 'F' THEN clifone_res
			         WHEN clitipo = 'J' THEN clifone_com
			         ELSE NULL
			       END AS telefone1,
			       CASE
			         WHEN clitipo = 'F' THEN clifone_cel
			         WHEN clitipo = 'J' THEN clifone2_com
			         ELSE NULL
			       END AS telefone2,
			       
                               (SELECT eqcdescricao
                                FROM equipamento_classe
                                WHERE eqcoid=coneqcoid) as classe_contrato,
                                
                               (SELECT eqcoid
                                FROM equipamento_classe
                                WHERE eqcoid=coneqcoid) as eqcoid,
                                
                                (SELECT tpcdescricao
                                FROM tipo_contrato
                                WHERE tpcoid=conno_tipo) as tipo_contrato,
                                
                                (SELECT tpcoid
                                FROM tipo_contrato
                                WHERE tpcoid=conno_tipo) as tpcoid,
                                condt_cadastro,
                                to_char(condt_cadastro,'dd/mm/yyyy') as inicio_vigencia
				 FROM contrato
			       JOIN contrato_situacao
				 ON contrato.concsioid = csioid
			       JOIN clientes
			         ON clioid = conclioid
			       JOIN veiculo
			         ON veioid = conveioid
					where 1=1
    				$filtro
    				";
    				
    				$rs = pg_query ( $this->conn, $sql );
    				
    				if (! is_resource ( $rs )) {
    				    throw new Exception ('Falha ao consultar.');
    				}
    				return pg_fetch_all ( $rs );
    }
    
    //@@Retorna os contratos que podem ser transferido passando os parametros do filtro de novo cliente
    public function upgradeDown($contrato){
        try {
            $sql = "SELECT prp.prptermo_original,
                    prp.prptermo,
                    prp.prpmsuboid,
                    ms.msubdescricao,
                    CASE
                    WHEN ms.msubdescricao LIKE 'UP%' THEN 1
                    WHEN ms.msubdescricao LIKE 'TRA%' THEN 2
                    ELSE 3
                    END AS TIPO, prp.prpdt_aprovacao_fin
                    FROM proposta prp inner join motivo_substituicao ms
                    ON prp.prpmsuboid = ms.msuboid WHERE  1 = 1
                    AND prp.prptermo = $contrato
                    AND ( ms.msubdescricao LIKE 'DOWN%' OR ms.msubdescricao ILIKE 'UP%' OR ms.msubdescricao ILIKE 'TRA%')
                    AND prp.prptermo_original IS NOT NULL";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta de busca de propostas De transferï¿½ncia de titularidade arquivo csv" );
            }
            
            while ($row = pg_fetch_object($result)) {
                if($row->tipo == 2){
                    return array(date( 'd/m/Y' , strtotime( $row->prpdt_aprovacao_fin ) ), $row->tipo);
                }else{
                    //Quando não a busca encontra o contrato e ele nao e do tipo 2(Transferencia), o contrato e ou do tipo 1(Upgrade) ou 3(Downgrade)
                    $row->tipo = 333;
                    //verifica a data que foi concluida a data de instalacao da O.S e atribui a data de inicio de vigencia
                    $sql2 = "SELECT ordoid, ordclioid,
                            (SELECT orsdt_situacao
                                FROM ordem_situacao
                                WHERE  orsordoid = ordoid
                                ORDER  BY orsdt_situacao DESC
                                LIMIT  1) AS orddt_ordem,
                                mtioid
                            FROM ordem_servico
                                INNER JOIN motivo_instalacao
                                ON mtioid = ordmtioid
                            WHERE  ordconnumero = $contrato
                                AND mtioid = 2
                                AND ordstatus = 3
                            ORDER BY orddt_ordem
                            LIMIT  1";
                    
                    if (! $result2 = pg_query ( $this->conn, $sql2 )) {
                        throw new Exception ( "Erro ao efetuar a consulta de busca de propostas De transferência de titularidade arquivo csv" );
                    }
                    
                    while ($row2 = pg_fetch_object($result2)) {
                        return array(date( 'd/m/Y' , strtotime( $row2->orddt_ordem ) ), $row->tipo);
                    }
                }
            }
        } catch ( Exception $e ) {
            throw new Exception ( "Erro ao efetuar o consulta dos paises" );
        }
        //return pg_fetch_all ( $result );
    }
    
    //@@Retorna os contratos que podem ser transferido passando os parametros do filtro de novo cliente
    public function paralizacao($contrato, $dataInicioVigencia, $prazoFidelidade){
        try {
            $dataaux = str_replace("/","-",$dataInicioVigencia);

            $dataInicioVigenciaaux = date('Y-m-d',strtotime($dataaux));
            
            $sql = "SELECT parfconoid,
                        SUM((date_part('month', parfdt_fin_cobranca::date) - date_part('month', parfdt_ini_cobranca::date)) + 1 ) AS qtdPrl                    
                    FROM parametros_faturamento
                    WHERE parfconoid = $contrato
                        AND parfativo IS TRUE
                        AND parfdt_ini_cobranca::date >= '$dataInicioVigenciaaux'::date
                    GROUP BY parfconoid";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta de contratos que foram feitos a acao de Paralizacao." );
            }
            
            while ($row = pg_fetch_object($result)) {
                return array( $row->qtdPrl );
            }
        } catch ( Exception $e ) {
            throw new Exception ( "Erro ao efetuar a consulta de contratos que foram feitos a acao de Paralizacao." );
        }
        //return pg_fetch_all ( $result );
    }
    
    //@@Retorna os contratos que podem ser transferido passando os parametros do filtro de novo cliente
    public function fidelizacao($contrato){
        try {
            $sql = "SELECT  hfcprazo,
                    hfcdt_fidelizacao
                    FROM  historico_fidelizacao_contrato
                    WHERE hfcoid = (SELECT
                                hfcoid
                            FROM
                                historico_fidelizacao_contrato
                            WHERE
                                hfcconnumero = $contrato
                            ORDER BY hfcdt_fidelizacao
                            DESC LIMIT 1)";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta de contratos que foram feitos a acao de Fidelizacao." );
            }
            
            while ($row = pg_fetch_object($result)) {
                return array(date('d/m/Y', strtotime($row->hfcdt_fidelizacao)), $row->hfcprazo);
            }
        } catch ( Exception $e ) {
            throw new Exception ( "Erro ao efetuar a consulta de contratos que foram feitos a acao de Fidelizacao." );
        }
        //return pg_fetch_all ( $result );
    }
    
    //@@Retorna os contratos que podem ser transferido passando os parametros do filtro de novo cliente
    public function pesquisaLocacao($contrato) {
        
        $sql = "select pcsidescricao from parametros_configuracoes_sistemas_itens where pcsipcsoid = 'TRANSFERENCIA_LOTE' and pcsioid='VALOR_NF_LOCACAO'";
        
        $result = pg_query ( $this->conn, $sql );
        $array_locacao = pg_fetch_result($result, 0, 'pcsidescricao');
        
        
        try{
            
            
            if (empty($contrato)){
                
                die(utf8_encode("&nbsp&&nbsp&nbsp Não existe contrato com este Status vinculado a este cliente."));
                
                die ( "Nao existe contrao vinculado" );
                
            }
            
            
            
            $sql =    "select
                                       case
                                               when nota_fiscal_item.nfitipo = 'L' then nota_fiscal_item.nfivl_item
                                               else 0.00
                                       end as valor_locacao
                               from
                                       nota_fiscal_item
                               left join contrato on nota_fiscal_item.nficonoid = contrato.connumero
                               inner join obrigacao_financeira on nota_fiscal_item.nfiobroid = obrigacao_financeira.obroid
                               inner join obrigacao_financeira_tipo on obrigacao_financeira_tipo.oftoid = obrigacao_financeira.obroftoid
                               left join contrato_servico on contrato_servico.consconoid = contrato.connumero
                               left join contrato_pagamento on contrato_pagamento.cpagconoid = contrato_servico.consconoid
                               left join contrato_obrigacao_financeira on contrato_obrigacao_financeira.cofconoid = contrato.connumero
                               where
                                       contrato_servico.consiexclusao is null and $array_locacao
                                       and contrato_servico.consconoid = $contrato
                               group by
                                       nota_fiscal_item.nfitipo,
                                       nota_fiscal_item.nfivl_item limit 1 ";
            
            $result = pg_query ( $this->conn, $sql );
            
            $resultArray = pg_fetch_all($result);
            
            if (empty( $resultArray)){
            
                $sql = "select cpagvl_servico AS valor_locacao 
                        from contrato_pagamento where cpagconoid = $contrato ";
                
                $result = pg_query ( $this->conn, $sql );
            }
            if (! $result = pg_query ( $this->conn, $sql)) {
                $msg =  "Erro ao efetuar a consulta valor locacao acessorios.";
                throw new Exception ($msg );
            }
        }catch ( Exception $e ) {
            
            throw new Exception ( "Erro ao efetuar a consulta valor locação dos acessorios: ".$e->getMessage() );
            
        }

        $faltante = 0;
        
        if(pg_num_rows($result) > 0){
            
            //var_dump(pg_fetch_result($result["valor_servico"]));
            if( pg_fetch_result($result,0,0) != '' ||  pg_fetch_result($result,0,0) != null ){
                $faltante = pg_fetch_result($result,0,0);
            }
        }
        
        return pg_fetch_all ( $result );
        
        
    }
    
    
    //@@Retorna os contratos que podem ser transferido passando os parametros do filtro de novo cliente
    public function pesquisaAcessorios($contrato)
    {
        $sql = "select pcsidescricao from parametros_configuracoes_sistemas_itens where pcsipcsoid = 'TRANSFERENCIA_LOTE' and pcsioid='VALOR_NF_ACESSORIO'";
        
        $result = pg_query ( $this->conn, $sql );
        $array_locacao = pg_fetch_result($result, 0, 'pcsidescricao');
        //  print_r($array_locacao);
        //die;
        
        try {
            echo "<pre>";
            //var_dump($contrato);
            echo "</pre>";
            
            
            $sql = "SELECT
             SUM(tpi.tpivalor) AS valor_acessorios
            from tabela_preco_item tpi
            INNER join tabela_preco tap ON tpi.tpitproid=tap.tproid
            INNER join obrigacao_financeira obf ON tpi.tpiobroid=obf.obroid
            inner join obrigacao_financeira_tipo on obrigacao_financeira_tipo.oftoid = obf.obroftoid
            INNER join contrato_obrigacao_financeira cobf ON obf.obroid=cobf.cofobroid
            where cobf.cofconoid = $contrato AND tpi.tpiexclusao IS null AND tap.tprstatus = 'A' and obrigacao_financeira_tipo.oftoid in  $array_locacao ";
            
            //  var_dump ($sql);
            // die;
            if (!$result = pg_query($this->conn, $sql)) {
                
                throw new Exception ("Erro ao efetuar a consulta valor locação acessorios");
                
            }
        } catch (Exception $e) {
            
            throw new Exception ("Erro ao efetuar a consulta valor locação acessorios");
            
        }
        
        $faltante = 0;
        
        if (pg_num_rows($result) > 0) {
            
            //var_dump(pg_fetch_result($result["valor_servico"]));
            if (pg_fetch_result($result, 0, 0) != '' || pg_fetch_result($result, 0, 0) != null) {
                $faltante = pg_fetch_result($result, 0, 0);
            }
        }
        //echo $result;
        //echo pg_fetch_result($result[]);
        
        return pg_fetch_all($result);
    }
    
    //@@Retorna os contratos que podem ser transferido passando os parametros do filtro de novo cliente
    public function pesquisaMonitoramento($contrato)
    {
        try {
            echo "<pre>";
            //var_dump($contrato);
            echo "</pre>";
            
            
            $sql = "SELECT
							CASE WHEN nota_fiscal_item.nfitipo = 'M'  AND  nota_fiscal_item.nfivl_item > 0 THEN nota_fiscal_item.nfivl_item
							WHEN contrato_pagamento.cpagmonitoramento > 0 THEN contrato_pagamento.cpagmonitoramento
							ELSE 0.00 END AS valor_monitoramento FROM contrato
							LEFT JOIN nota_fiscal_item on nota_fiscal_item.nficonoid = contrato.connumero
							LEFT JOIN contrato_pagamento ON contrato_pagamento.cpagconoid = contrato.connumero
                        WHERE connumero = $contrato  ORDER BY nota_fiscal_item.nfidt_inclusao desc limit 1
                        ;
                        
				";
            
            
            
            if (!$result = pg_query($this->conn, $sql)) {
                
                
                throw new Exception ("Erro ao efetuar a consulta valor locação acessorios");
                
            }
        } catch (Exception $e) {
            
            
            throw new Exception ("Erro ao efetuar a consulta valor locação acessorios");
            
        }
        
        $faltante = 0;
        
        if (pg_num_rows($result) > 0) {
            
            //var_dump(pg_fetch_result($result["valor_servico"]));
            if (pg_fetch_result($result, 0, 0) != '' || pg_fetch_result($result, 0, 0) != null) {
                $faltante = pg_fetch_result($result, 0, 0);
            }
        }
        //echo $result;
        //echo pg_fetch_result($result[]);
        
        return pg_fetch_all($result);
    }
    
    
    
    //Retorna todos contratos cadastrado na proposta passando o id da proposta
    public function retornaTransferenciasPorContrato($id){
        
        $sql = "SELECT clinome,
					veiplaca,
					connumero,
					contrato_situacao.csidescricao
				FROM   contrato
				       LEFT JOIN contrato_situacao
				         ON contrato.concsioid = csioid
				      LEFT JOIN clientes
				         ON clioid = conclioid
				      LEFT JOIN veiculo
				         ON veioid = conveioid
							LEFT JOIN proposta_transferencia_contrato
				         ON connumero = pttcoconoid
				WHERE
					 pttcoptraoid = $id";
        
        $rs = pg_query ( $this->conn, $sql );
        if (! is_resource ( $rs ))
            throw new Exception ( 'Falha ao consultar.' );
            
            return pg_fetch_all ( $rs );
    }
    
    
    
    
    //retorna todos os titulos que estão pendentes passando o contrato
    
    public function titulosPendentesContratos($dados) {
        
        $sql = "SELECT DISTINCT connumero,
								 nflvl_total,
								 nflno_numero,
								 nfiserie,
								 sum(nfivl_item) AS valor_contrato,
								 To_char(titdt_vencimento, 'DD/MM/YYYY') AS titdt_vencimento,
								 titoid
								FROM   nota_fiscal
								INNER JOIN clientes
								       ON clioid = nflclioid
								INNER JOIN contrato
								       ON conclioid = clioid
								INNER JOIN nota_fiscal_item
								       ON nfinfloid = nfloid
								   AND connumero = nficonoid
								LEFT JOIN titulo
								      ON titnfloid = nfloid
								LEFT JOIN forma_cobranca
								      ON titformacobranca = forcoid
								WHERE  1 = 1
								AND connumero IN ( $dados )
								AND titdt_vencimento <= Now()
								AND titdt_pagamento IS NULL
								AND nfldt_cancelamento is null
								GROUP BY connumero,
								 nflvl_total,
								 nflno_numero,
								 nfiserie,
								 titoid";
        
        $rs = pg_query ( $this->conn, $sql );
        if (! is_resource ( $rs ))
            throw new Exception ( 'Falha ao consultar.' );
            
            return pg_fetch_all ( $rs );
    }
    
    
    // Cadastra a nova proposta de transferencia na tabela proposta_transferencia e cadastra os contratos que estão sendo transferido na tabela
    
    //proposta_transferencia_contrato passando o idProposta
    public function cadastraSolicitacaoTransferencia($dados) {
        $ptraoid = 0;
        try {
            
            $this->begin ();
            
            $sql = "INSERT INTO proposta_transferencia
							  (
							  ptrafone_tit_anterior,
							  ptrafone2_tit_anterior,
							  ptraemail_tit_anterior,
							  ptraresp_tit_anterior,
							  ptrano_documento ,
							  ptranome ,
							  ptranome_contato ,
							  ptrafone_contato1 ,
							  ptrafone_contato2 ,
							  ptracontato_email,
							  ptramotivo_trans,
							  ptrausuoid_cadastro,
							  ptrasfoid_analise_credito,
							  ptrasfoid_analise_divida,
							  ptraresultado_serasa,
							  ptrastatus_conclusao_proposta)
						VALUES
							  (
							  '$dados[tel_tit_anterior]',
							  '$dados[tel2_tit_anterior]',
							  '$dados[ptraemail_tit_anterior]',
							  '$dados[ptraresp_tit_anterior]',
							   $dados[cpfcnpj],
							   '$dados[nome]',
							   '$dados[contato]',
							   '$dados[contato1]',
							   '$dados[contato2]',
							   '$dados[email]',
							   '$dados[ptramotivo_trans]',
							   $dados[idusuario],
							   $dados[ptrasfoid_analise],
							   $dados[ptrasfoid_status],
							   '".$dados[ptraresultado_serasa]."',
							  '$dados[statusconclusaoproposta]'
							  ) RETURNING ptraoid";
							   
							   
							   if (! $rs = pg_query ( $this->conn, $sql )) {
							       throw new Exception ( "Erro ao efetuar o cadastro de proposta de transferencia" );
							       return $ptraoid = 0;
							   }
							   
							   $arr = pg_fetch_array ( $rs, 0 );
							   
							   $ptraoid = $arr [ptraoid];
							   
							   
							   for($i = 0; $i < count ( $dados [contratos] ); $i ++) {
							       $contrato = $dados [contratos] [$i];
							       
							       //cadastra os contratos que estão sendo transferido
							       
							       $sqlContrato = "INSERT INTO proposta_transferencia_contrato
									(pttcoptraoid,pttcoconoid)
						       VALUES
						            ($arr[ptraoid],$contrato)";
							       
							       if (! $rs = pg_query ( $this->conn, $sqlContrato )) {
							           throw new Exception ( "Erro ao efetuar o cadastro de proposta de transferencia" );
							           return $ptraoid = 0;
							       }
							   }
							   
							   $this->commit ();
        } catch ( Exception $e ) {
            $this->rollback ();
            return $ptraoid = 0;
        }
        
        return $ptraoid;
    }
    
    //retorna a quantidade de contratos cadastrado para transferencia
    public function listaNumContratosPorProposta($id) {
        
        $sql = "SELECT
		*
		FROM
		proposta_transferencia_contrato
		where
		pttcoptraoid = $id ";
        
        if (! $result = pg_query ( $this->conn, $sql )) {
            throw new Exception ( "Erro ao efetuar a consulta numero contratos por id da proposta" );
        }
        
        return pg_num_rows($result);
    }
    
    /*
     * atualiza o status do ptrasfoid_analise_credito,ptramotivo_reprov_analise_credito,ptrausuoid_analise_credito_manual tabela proposta_transferencia
     
     * setando para reprovado(3) o status manual do serasa add o motivo e usuario que reprovou e o status de concluão altera para F = finalizado
     */
    public function atualizaSolicitacaoTransferenciaCreditoSerasa($dados){
        
        try{
            $sql = "UPDATE proposta_transferencia
					SET
					ptrasfoid_analise_credito = 3,
					ptramotivo_reprov_analise_credito ="."'$dados[motivo]'".",
					ptrausuoid_analise_credito_manual= $dados[idUsuario],
					ptrastatus_conclusao_proposta ='F'
					where ptraoid = $dados[idProposta]";
            
            if (! $rs = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar o cadastro de proposta de transferencia" );
                return false;
            }
        }catch(Exception $e){
            
            throw new Exception("Falha ao atualizar solicitação de transfetransferencia");
            
            return false;
        }
        
        return true;
    }
    
    /*
     * atualiza o status do ptrasfoid_analise_divida,ptramotivo_reprov_analise_divida,ptrastatus_conclusao_proposta tabela proposta_transferencia
     
     * setando para reprovado (3)  o status manual transferencia titularidade e add o motivo e usuario que reprovou e o status de conclusão altera para F = finalizado
     
     */
    public function atualizaSolicitacaoTransferenciaTitularidade($dados){
        
        try{
            $sql = "UPDATE proposta_transferencia
					SET
						ptrasfoid_analise_divida = 3,
					    ptramotivo_reprov_analise_divida ="."'$dados[motivo]'".",
						ptrausuoid_analise_credito_manual= $dados[idUsuario],
						ptrastatus_conclusao_proposta ='F'
						where ptraoid = $dados[idProposta]";
            
            if (! $rs = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar o cadastro de proposta de transferencia" );
                return false;
            }
        }catch(Exception $e){
            
            throw new Exception("Falha ao atualizar solicitação de transfetransferencia");
            
            return false;
        }
        
        return true;
    }
    
    /*
     * altera o status para aprovado (2) a transferencia de divida, atualizando os campos ptrasfoid_analise_divida, ptrausuoid_analise_credito_manual, ptrastatus_conclusao_proposta e status
     * ptrastatus_conclusao_proposta altera para A = EM ANDAMENTO
     */
    public function aprovaTransferenciaTitularidade($dados){
        
        try{
            $sql = "UPDATE proposta_transferencia
					SET
							ptrasfoid_analise_divida = 2,
						    ptrausuoid_analise_credito_manual= $dados[idUsuario],
						    ptrastatus_conclusao_proposta ='A'
						    where ptraoid = $dados[idProposta]";
            
            if (! $rs = pg_query ( $this->conn, $sql )) {
                
                throw new Exception ( "Erro ao efetuar aprovação manual de proposta de transferencia" );
                
                return false;
            }
        }catch(Exception $e){
            
            throw new Exception("Falha ao atualizar aprovação manual de transfetransferencia");
            
            return false;
        }
        
        return true;
    }
    
    /*
     * altera o status para aprovado (2) a transferencia de divida, atualizando os campos ptrasfoid_analise_divida, ptrausuoid_analise_credito_manual, ptrastatus_conclusao_proposta e status
     * ptrastatus_conclusao_proposta altera para A = EM ANDAMENTO
     */
    public function aprovaTransferenciaCreditoSerasa($dados){
        
        try{
            $sql = "UPDATE proposta_transferencia
			SET
			ptrasfoid_analise_credito = 2,
			ptrausuoid_analise_credito_manual= $dados[idUsuario],
			ptrastatus_conclusao_proposta ='A'
			where ptraoid = $dados[idProposta]";
            
            if (! $rs = pg_query ( $this->conn, $sql )) {
                
                throw new Exception ( "Erro ao efetuar aprovação manual de credito serasa" );
                
                return false;
            }
        }catch(Exception $e){
            
            throw new Exception("Falha ao atualizar aprovação manual  de credito serasa");
            
            return false;
        }
        
        return true;
    }
    
    /*
     * altera o status para aprovado (2) a transferencia de divida, atualizando os campos ptrasfoid_analise_divida, ptrausuoid_analise_credito_manual, ptrastatus_conclusao_proposta e status
     * ptrastatus_conclusao_proposta altera para A = EM ANDAMENTO
     */
    public function listSolicitacaoTransferenciaPorID($id){
        
        $sql = "SELECT *
				FROM
				proposta_transferencia
				LEFT JOIN usuarios ON ptrausuoid_analise_credito_manual = cd_usuario
				WHERE
				ptraoid = $id";
        
        $rs = pg_query ( $this->conn, $sql );
        if (! is_resource ( $rs ))
            throw new Exception ( 'Falha ao consultar.' );
            
            return pg_fetch_assoc($rs);
    }
    
    //retorna a lista de paises
    public function Paises() {
        try {
            $sql = "SELECT
					paisoid,paisnome
                FROM paises
                ORDER BY paisnome ; ";
            
            if (! $rs = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Problemas na  consulta dos paises" );
            }
        } catch ( Exception $e ) {
            throw new Exception ( "Erro ao efetuar o consulta dos paises" );
        }
        return pg_fetch_all ( $rs );
    }
    
    //retorna a lista de estados
    public function Estados() {
        $sql = "SELECT estoid, estuf
                            FROM estado
                            ORDER BY estuf";
        
        if (! $rs = pg_query ( $this->conn, $sql )) {
            throw new Exception ( "Erro ao efetuar a consulta dos estados" );
        }
        
        return pg_fetch_all ( $rs );
    }
    
    //Insere as pessoas autorizadas
    public function insertPessoaAutorizada($dados) {
        $sql = "INSERT INTO proposta_transferencia_pessoas_autorizadas
				( ptpaptraoid,
				  ptpanome,
				  ptpacpf,
				  ptparg,
				  ptpafone_residencial,
				  ptpafone_celular,
				  ptpafone_comercial,
				  ptpaidnextel)
				VALUES
				(
					 $dados[ptraoid],
					'$dados[prtnome_aut]',
					'$dados[prtcpf_aut]',
					'$dados[prtrg_aut]',
					'$dados[prtfone_res_aut]',
					'$dados[prtfone_com_aut]',
					'$dados[prtfone_cel_aut]',
					'$dados[prtid_nextel_aut]'
				)";
					 
					 if (! $rs = @pg_query ( $this->conn, $sql )) {
					     return false;
					     throw new Exception ( "Erro ao efetuar o cadastro de pessoa autorizada" );
					     
					 }
					 
					 return true;
    }
    
    //lista pessoas autorizadas pelo o id da proposta
    public function listaPessoaDaoIdProposta($id) {
        $sql = "SELECT
					*
				FROM
				proposta_transferencia_pessoas_autorizadas
				where
				ptpaptraoid = $id ";
        
        if (! $result = pg_query ( $this->conn, $sql )) {
            throw new Exception ( "Erro ao efetuar a consulta da pessoas autorizadas" );
        }
        
        return pg_fetch_all ( $result );
    }
    
    //lista pessoas autorizadas para o id da pessoa autorizada
    public function listaPessoaDaoId($id) {
        $sql = "SELECT
		*
		FROM
		proposta_transferencia_pessoas_autorizadas
		where
		ptpaoid = $id ";
        
        if (! $result = pg_query ( $this->conn, $sql )) {
            throw new Exception ( "Erro ao efetuar a consulta da pessoas autorizadas id " );
        }
        
        return pg_fetch_assoc ( $result );
    }
    
    //metodo para inserir o contato de emergencia no banco
    public function insertContatoEmergencia($dados) {
        try {
            $sql = "INSERT INTO proposta_transferencia_contato_emergencia(
			ptceptraoid,
			ptcenome,
			ptcefone_residencial,
			ptcefone_celular,
			ptcefone_comercial,
			ptceidnextel)
			
			VALUES
			(
			$dados[ptceptraoid],
			'$dados[ptcenome]',
			'$dados[ptcefone_residencial]',
			'$dados[ptcefone_celular]',
			'$dados[ptcefone_comercial]',
			'$dados[ptceidnextel]'
			)";
			
			if (! $rs = @pg_query ( $this->conn, $sql )) {
			    throw new Exception ( "Erro ao efetuar o cadastro de contato emergÃªncia" );
			    
			    return false;
			}
        } catch ( Exception $e ) {
            
            
            throw new Exception ( "Erro ao efetuar o cadastro de contato emergÃªncia" );
            
            return false;
        }
        
        return true;
    }
    
    //lista todos os contatos de emergencia  passando o id da proposta
    public function listaContatoEmergencia($id) {
        $sql = "SELECT
		*
		FROM
		proposta_transferencia_contato_emergencia
		where
		ptceptraoid = $id ";
        
        if (! $result = pg_query ( $this->conn, $sql )) {
            throw new Exception ( "Erro ao efetuar a consulta dos contatos emergencias" );
        }
        
        return pg_fetch_all ( $result );
    }
    
    //lista todos os contatos de emergencia  passando contrato
    public function listaContatoEmergenciaContrato($contrato){
        try {
            $sql = "SELECT
					*
				FROM
				  proposta_transferencia_contato_emergencia
				INNER JOIN
					proposta_transferencia_contrato ON pttcoptraoid = ptceptraoid
				where
					pttcoconoid = $contrato";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                $this->rollback ();
                throw new Exception ( "Erro SQL  ao efetuar o consulta de contato emergencia contrato" );
            }
        } catch ( Exception $e ) {
            $this->rollback ();
            throw new Exception ( "Erro ao efetuar o consulta de contato emergencia contrato" );
        }
        return pg_fetch_all ( $result );
    }
    
    
    //lista pessoas autorizadas pelo o contrato da proposta
    public function listaPessoaAutorizaDaContrato($contrato) {
        try {
            $sql = "SELECT
					*
				FROM
				  proposta_transferencia_pessoas_autorizadas
				INNER JOIN
					proposta_transferencia_contrato ON pttcoptraoid = ptpaptraoid
				where
					pttcoconoid = $contrato";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                $this->rollback ();
                throw new Exception ( "Erro SQL  ao efetuar o consulta de pessoa autorizada por contrato" );
            }
        } catch ( Exception $e ) {
            $this->rollback ();
            throw new Exception ( "Erro ao efetuar o consulta de pessoa autorizada por contrato" );
        }
        return pg_fetch_all ( $result );
    }
    
    
    //insere o contatos de instalaÃ§Ãµes na tabela proposta_transferencia_contato_instalacao
    
    public function insertContatoInstalacao($dados) {
        
        try {
            $sql = "INSERT INTO proposta_transferencia_contato_instalacao(
			ptciptraoid,
			ptcinome,
			ptcifone_residencial,
			ptcifone_celular,
			ptcifone_comercial,
			ptcidnextel)
			
			VALUES
			(
			$dados[ptciptraoid],
			'$dados[ptcinome]',
			'$dados[ptcifone_residencial]',
			'$dados[ptcifone_celular]',
			'$dados[ptcifone_comercial]',
			'$dados[ptcidnextel]'
			)";
			
			if (! $rs = @pg_query ( $this->conn, $sql )) {
			    
			    throw new Exception ( "Erro ao efetuar o cadastro de contato instalação" );
			    
			    return false;
			}
        } catch ( Exception $e ) {
            
            
            throw new Exception ( "Erro ao efetuar o cadastro de contato instalação" );
            
            return false;
        }
        
        return true;
    }
    
    
    //lista todos os contatos emergencia inserindo o id da proposta_transferencia_contato_emergencia
    public function listaContatoEmergenciaID($id) {
        
        try{
            $sql = "SELECT
			*
			FROM
			proposta_transferencia_contato_emergencia
			where
			ptceoid = $id ";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta dos contato emergencia por id" );
            }
        }catch ( Exception $e ) {
            throw new Exception ( "Problemas ao efetuar a consulta dos contato emergencia por id" );
        }
        
        
        return pg_fetch_assoc ( $result );
    }
    //lista todos os contatos instalacoes inserindo o id da proposta
    public function listaContatoInstalacaoIDProposta($id) {
        
        try{
            $sql = "SELECT
			*
			FROM
			proposta_transferencia_contato_instalacao
			where
			ptciptraoid = $id ";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                
                throw new Exception ( "Erro ao efetuar a consulta dos contato instalação" );
                
            }
        }catch ( Exception $e ) {
            
            throw new Exception ( "Problemas ao efetuar a consulta dos contato instalação" );
            
        }
        
        
        return pg_fetch_all ( $result );
    }
    
    public function listaContatoInstalacaoContrato($contrato){
        
        try {
            $sql = "SELECT
					*
				FROM
				  proposta_transferencia_contato_instalacao
				INNER JOIN
					proposta_transferencia_contrato ON   ptciptraoid = pttcoptraoid
				where
					pttcoconoid = $contrato";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                $this->rollback ();
                
                throw new Exception ( "Erro SQL  ao efetuar o consulta de contato instalação por contrato" );
                
            }
        } catch ( Exception $e ) {
            $this->rollback ();
            
            throw new Exception ( "Erro ao efetuar o consulta de contato instalação por contrato" );
            
        }
        return pg_fetch_all ( $result );
    }
    
    
    //retorna contato instalacao passando o id da proposta_transferencia_contato_instalacao
    public function ContatoInstalacaoID($id) {
        
        try{
            $sql = "SELECT
			*
			FROM
			proposta_transferencia_contato_instalacao
			where
			ptcioid = $id ";
            
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                
                throw new Exception ( "Erro ao efetuar a consulta dos contato instalação por id" );
                
            }
        }catch ( Exception $e ) {
            
            throw new Exception ( "Problemas ao efetuar a consulta dos contato instalação por id" );
            
        }
        
        
        return pg_fetch_assoc ( $result );
    }
    
    
    //retorna os valores da locação dos acessorios passando o numero de contrato
    
    public function ValorLocacaoAcessorios($contrato){
        
        
        try{
            $sql = "select  a.faltante
                    from (select  CASE
                               when contrato_servico.consvalor > 0 then  (SUM(contrato_servico.consvalor) + contrato_pagamento.cpagvl_servico)
                            ELSE 0.00 END as faltante
                    from  nota_fiscal_item
                    inner join obrigacao_financeira on nota_fiscal_item.nfiobroid = obrigacao_financeira.obroid
                    inner join obrigacao_financeira_tipo on obrigacao_financeira_tipo.oftoid = obrigacao_financeiraobroftoid and oftoid in(3,4,5)
                    left join contrato on nota_fiscal_item.nficonoid = contrato.connumero
                    left join contrato_servico on  contrato_servico.consconoid = contrato.connumero
                    left join contrato_pagamento  on contrato_pagamento.cpagconoid = contrato_servico.consconoid
                    left join contrato_obrigacao_financeira  on contrato_obrigacao_financeira.cofconoid = contrato.connumero
                    where contrato_servico.consiexclusao IS null and contrato_servico.consconoid = $contrato
                    group by contrato_servico.consvalor , contrato_pagamento.cpagvl_servico
                    union all
                    select  CASE
                               when  nota_fiscal_item.nfitipo = 'L' then  nota_fiscal_item.nfivl_item
                             ELSE 0.00 END as faltante
                    from  nota_fiscal_item
                     inner join obrigacao_financeira on nota_fiscal_item.nfiobroid = obrigacao_financeira.obroid
                    inner join obrigacao_financeira_tipo on obrigacao_financeira_tipo.oftoid = obrigacao_financeiraobroftoid and oftoid in(3,4,5)
                    left join contrato on nota_fiscal_item.nficonoid = contrato.connumero
                    left join contrato_servico on  contrato_servico.consconoid = contrato.connumero
                    left join contrato_pagamento  on contrato_pagamento.cpagconoid = contrato_servico.consconoid
                    left join contrato_obrigacao_financeira  on contrato_obrigacao_financeira.cofconoid = contrato.connumero
                    where contrato_servico.consiexclusao IS null  and contrato_servico.consconoid = $contrato
                    group by nota_fiscal_item.nfitipo  , nota_fiscal_item.nfivl_item ) a  ";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                
                throw new Exception ( "Erro ao efetuar a consulta valor locação equipamento" );
                
            }
        }catch ( Exception $e ) {
            
            
            throw new Exception ( "Erro ao efetuar a consulta valor locação equipamento" );
            
        }
        
        $faltante = 0;
        if(pg_num_rows($result) > 0){
            if( pg_fetch_result($result,0,0) != '' ||  pg_fetch_result($result,0,0) != null ){
                $faltante = pg_fetch_result($result,0,0);
            }
        }
        
        return $faltante;
    }
    
    //retorna o valor de monitoramento passando o numero do contrato
    public function ValorMonitoramento($contrato){
        try{
            $sql = "SELECT cofvl_obrigacao
					FROM   contrato_obrigacao_financeira cof
					WHERE  cof.cofconoid = $contrato -- parametro
					       AND cof.cofobroid = 1
					       AND cof.cofdt_termino IS NULL  ";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta valor monitoramento" );
            }
        }catch ( Exception $e ) {
            throw new Exception ( "Erro ao efetuar a consulta valor monitoramento" );
        }
        
        $faltante = 0;
        if(pg_num_rows($result) > 0){
            if( pg_fetch_result($result,0,0) != '' ||  pg_fetch_result($result,0,0) != null ){
                $faltante = pg_fetch_result($result,0,0);
            }
        }
        
        return $faltante;
    }
    
    //busca os dados da proposta transferencia passando os dados nos filtros desejado  e retorna a paginacao
    public function BuscaPropostaTranferencia($dados,$paginacao= null){
        
        
        if ($dados ->nomeCliente != '' || ! empty ( $dados->nomeCliente)) {
            $nomeCliente = $dados->nomeCliente;
            $filtro .= " AND clinome ILIKE('%$nomeCliente%')";
        }
        
        if ($dados ->novoTitular != '' || ! empty ( $dados->novoTitular )) {
            $nomeClientenovo = $dados ->novoTitular;
            $filtro .= " AND ptranome ILIKE('%$nomeClientenovo%')";
        }
        
        if (($dados ->dt_ini != '' || ! empty ( $dados->dt_ini )) && ($dados->dt_fim != '' || ! empty ( $dados->dt_fim ))) {
            $arrdtini = explode ( "/", $dados ->dt_ini );
            $dti = $arrdtini [2] . "-" . $arrdtini [1] . "-" . $arrdtini [0];
            
            $arrdfim = explode ( "/", $dados->dt_fim );
            $dtf = $arrdfim [2] . "-" . $arrdfim [1] . "-" . $arrdfim [0];
            $dti = $dti." 00:00:00";
            $dtf = $dtf." 23:59:59";
            $filtro .= " AND ptradt_cadastro BETWEEN '" . $dti . "' AND '" . $dtf . "'";
        }
        
        if ($dados ->statusSolicitacaoTransDivida != '' || ! empty ( $dados ->statusSolicitacaoTransDivida )) {
            $filtro .= " AND ptrasfoid_analise_divida = ".$dados ->statusSolicitacaoTransDivida."";
        }
        
        if ($dados ->statusSolicitacaoSerasa != '' || ! empty ( $dados ->statusSolicitacaoSerasa )) {
            
            $filtro .= " AND ptrasfoid_analise_credito = ".$dados->statusSolicitacaoSerasa."";
            
            
        }
        
        if($dados->numeroSolicitacao != '' || ! empty ( $dados->numeroSolicitacao)){
            $filtro .= " AND ptraoid =" . $dados->numeroSolicitacao . "";
        }
        
        if($dados ->numeroContrato != '' || ! empty ( $dados->numeroContrato )){
            $filtro .= " AND pttcoconoid =" . $dados ->numeroContrato. "";
        }
        
        if (($dados->dt_ini_conclusao != '' || ! empty ( $dados->dt_ini_conclusao)) && ($dados->dt_fim_conclusao != '' || ! empty ( $dados->dt_fim_conclusao))) {
            $arrdtini = explode ( "/", $dados->dt_ini_conclusao );
            $dtic = $arrdtini [2] . "-" . $arrdtini [1] . "-" . $arrdtini [0];
            
            $arrdfim = explode ( "/", $dados->dt_fim_conclusao );
            $dtfc = $arrdfim [2] . "-" . $arrdfim [1] . "-" . $arrdfim [0];
            
            $dtic = $dtic." 00:00:00";
            $dtfc = $dtfc." 23:59:59";
            
            $filtro .= " AND ptradt_conclusao_proposta BETWEEN '" . $dtic . "' AND '" . $dtfc . "'";
        }
        
        if($dados->usuarios_conclusao != '' || ! empty ( $dados->usuarios_conclusao )){
            $filtro .= " AND ptrausuoid_conclusao_proposta = '" . $dados->usuarios_conclusao . "'";
        }
        
        if (isset($paginacao->limite) && isset($paginacao->offset)) {
            $paginas = "
                LIMIT
                    " . intval($paginacao->limite) . "
                OFFSET
                    " . intval($paginacao->offset) . "
            ";
        }
        
        
        
        try{
            $sql = "SELECT  ptraoid,
						   clientes.clinome,
						   TO_CHAR(ptradt_cadastro, 'DD/MM/YYYY') as ptradt_cadastro,
						   ptrasfoid_analise_credito,
						   ptrasfoid_analise_divida,
						   ptrastatus_conclusao_proposta,
						   ptrausuoid_conclusao_proposta
				     FROM
				     	   proposta_transferencia
					LEFT JOIN
					       proposta_transferencia_contrato ON ptraoid = pttcoptraoid
					LEFT JOIN
					      contrato ON 	pttcoconoid = connumero
					LEFT JOIN
					  	  clientes  ON   clioid = contrato.conclioid
					WHERE 1=1
					$filtro
					GROUP BY
				ptraoid,
						   clientes.clinome,
						   ptradt_cadastro,
						   ptrasfoid_analise_credito,
						   ptrasfoid_analise_divida,
						   ptrausuoid_conclusao_proposta
            ORDER BY
                 ptraoid
			$paginas
			";
			
			if (! $result = pg_query ( $this->conn, $sql )) {
			    
			    throw new Exception ( "Erro ao efetuar a consulta de busca de propostas De transfetransferencia de titularidade" );
			    
			}
			
        }catch ( Exception $e ) {
            
            throw new Exception ( "Erro ao efetuar a consulta de busca de propostas De transfetransferencia de titularidade" );
            
        }
        
        
        
        while ($row = pg_fetch_object($result)) {
            $retorno[] = $row;
        }
        
        return $retorno;
        
    }
    
    //retorna o csv da pesquisa das proposta
    public function BuscaPropostaTranferenciaArquivoCSV($dados){
        
        
        if ($dados ->nomeCliente != '' || ! empty ( $dados->nomeCliente)) {
            $nomeCliente = $dados->nomeCliente;
            $filtro .= " AND clinome ILIKE('%$nomeCliente%')";
        }
        
        if ($dados ->novoTitular != '' || ! empty ( $dados->novoTitular )) {
            $nomeClientenovo = $dados ->novoTitular;
            $filtro .= " AND ptranome ILIKE('%$nomeClientenovo%')";
        }
        
        if (($dados ->dt_ini != '' || ! empty ( $dados->dt_ini )) && ($dados->dt_fim != '' || ! empty ( $dados->dt_fim ))) {
            $arrdtini = explode ( "/", $dados ->dt_ini );
            $dti = $arrdtini [2] . "-" . $arrdtini [1] . "-" . $arrdtini [0];
            
            $arrdfim = explode ( "/", $dados->dt_fim );
            $dtf = $arrdfim [2] . "-" . $arrdfim [1] . "-" . $arrdfim [0];
            $dti = $dti." 00:00:00";
            $dtf = $dtf." 23:59:59";
            $filtro .= " AND ptradt_cadastro BETWEEN '" . $dti . "' AND '" . $dtf . "'";
        }
        
        if ($dados ->statusSolicitacaoTransDivida != '' || ! empty ( $dados ->statusSolicitacaoTransDivida )) {
            $filtro .= " AND ptrasfoid_analise_divida = ".$dados ->statusSolicitacaoTransDivida."";
        }
        
        if ($dados ->statusSolicitacaoSerasa != '' || ! empty ( $dados ->statusSolicitacaoSerasa )) {
            
            $filtro .= " AND ptrasfoid_analise_credito = ".$dados->statusSolicitacaoSerasa."";
            
            
        }
        
        if($dados->numeroSolicitacao != '' || ! empty ( $dados->numeroSolicitacao)){
            $filtro .= " AND ptraoid =" . $dados->numeroSolicitacao . "";
        }
        
        if($dados ->numeroContrato != '' || ! empty ( $dados->numeroContrato )){
            $filtro .= " AND pttcoconoid =" . $dados ->numeroContrato. "";
        }
        
        if (($dados->dt_ini_conclusao != '' || ! empty ( $dados->dt_ini_conclusao)) && ($dados->dt_fim_conclusao != '' || ! empty ( $dados->dt_fim_conclusao))) {
            $arrdtini = explode ( "/", $dados->dt_ini_conclusao );
            $dtic = $arrdtini [2] . "-" . $arrdtini [1] . "-" . $arrdtini [0];
            
            $arrdfim = explode ( "/", $dados->dt_fim_conclusao );
            $dtfc = $arrdfim [2] . "-" . $arrdfim [1] . "-" . $arrdfim [0];
            
            $dtic = $dtic." 00:00:00";
            $dtfc = $dtfc." 23:59:59";
            
            $filtro .= " AND ptradt_conclusao_proposta BETWEEN '" . $dtic . "' AND '" . $dtfc . "'";
        }
        
        if($dados->usuarios_conclusao != '' || ! empty ( $dados->usuarios_conclusao )){
            $filtro .= " AND ptrausuoid_conclusao_proposta = '" . $dados->usuarios_conclusao . "'";
        }
        
        try{
            $sql = "SELECT  ptraoid,
			clientes.clinome,
			TO_CHAR(ptradt_cadastro, 'DD/MM/YYYY') as ptradt_cadastro,
			ptrasfoid_analise_credito,
			ptrasfoid_analise_divida,
			ptrastatus_conclusao_proposta,
			ptrausuoid_conclusao_proposta
			FROM
			proposta_transferencia
			LEFT JOIN
			proposta_transferencia_contrato ON ptraoid = pttcoptraoid
			LEFT JOIN
			contrato ON 	pttcoconoid = connumero
			LEFT JOIN
			clientes  ON   clioid = contrato.conclioid
			WHERE 1=1
			$filtro
			GROUP BY
			ptraoid,
			clientes.clinome,
			ptradt_cadastro,
			ptrasfoid_analise_credito,
			ptrasfoid_analise_divida,
			ptrausuoid_conclusao_proposta
			ORDER BY
			ptraoid
			";
			
			if (! $result = pg_query ( $this->conn, $sql )) {
			    
			    throw new Exception ( "Erro ao efetuar a consulta de busca de propostas De transfetransferencia de titularidade arquivo csv" );
			    
			}
			
        }catch ( Exception $e ) {
            
            throw new Exception ( "Erro ao efetuar a consulta de busca de propostas De transfetransferencia de titularidade arquivo csv" );
            
        }
        
        
        
        while ($row = pg_fetch_object($result)) {
            $retorno[] = $row;
        }
        
        return $retorno;
        
    }
    
    
    //retorna o usuario que concluiu a proposta
    public function retornaUsuarioConcluirProposta(){
        
        try{
            $sql = "SELECT ptrausuoid_conclusao_proposta,
					       ds_login
					FROM   proposta_transferencia
					       INNER JOIN usuarios
              				 ON ptrausuoid_conclusao_proposta = cd_usuario ";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta de usuario concluiu proposta" );
            }
        }catch ( Exception $e ) {
            throw new Exception ( "Erro ao efetuar a consulta de usuario concluiu proposta" );
        }
        
        return pg_fetch_all ( $result );
    }
    
    //retorna os valores das taxas de transfencia indo buscar na tabela dominio
    public function taxaTransferencia(){
        try{
            $sql = "SELECT valregoid,valvalor
					FROM   valor
					INNER JOIN registro
					ON valregoid = regoid
					INNER JOIN dominio
					ON regdomoid =  domoid
					where
					domoid = 13
					and valoid in(64,65,66)
					order by valoid";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta de usuario concluiu proposta" );
            }
        }catch ( Exception $e ) {
            throw new Exception ( "Erro ao efetuar a consulta de taxa de transferencia" );
        }
        
        while ($row = pg_fetch_object($result)) {
            $retorno[] = $row;
        }
        
        return $retorno;
    }
    
    
    // retorna o valor das taxas de transferencia passando o id do valor
    public function taxaTransferenciaPorID($id){
        try{
            $sql = "SELECT valvalor
					FROM   valor
					INNER JOIN registro
					ON valregoid = regoid
					INNER JOIN dominio
					ON regdomoid =  domoid
					where
					domoid = 13
					and valoid in(59,61,63)
					and valregoid =$id";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta de usuario concluiu proposta" );
            }
        }catch ( Exception $e ) {
            throw new Exception ( "Erro ao efetuar a consulta de taxa de transferencia" );
        }
        
        $valor = 0;
        if(pg_num_rows($result) > 0){
            if( pg_fetch_result($result,0,0) != '' ||  pg_fetch_result($result,0,0) != null ){
                $valor = pg_fetch_result($result,0,0);
            }
        }
        
        
        
        return $valor;
    }
    
    
    //inseri os anexos na tabela proposta_transferencia_anexos
    public function inserirAnexosProposta($dados){
        
        try {
            $sql = "INSERT INTO proposta_transferencia_anexos(
			ptaptraoid,
			ptanm_arquivo,
			ptadescricao,
			ptratpanexo,
			ptrausuoid)
			
			VALUES
			(
			$dados[ptaptraoid],
			'$dados[ptanm_arquivo]',
			'$dados[ptadescricao]',
			'$dados[ptratipo_anexo]',
			$dados[ptrausuoid]
			)";
			
			if (! $rs = pg_query ( $this->conn, $sql )) {
			    throw new Exception ( "Erro ao inserir anexos da proposta" );
			    return false;
			}
        } catch ( Exception $e ) {
            throw new Exception ( "Erro ao efetuar o cadastro de anexos" );
            return false;
        }
        
        return true;
    }
    
    //lista os anexos relacionado ao id da proposta passado
    public function listAnexosPropostaId($id){
        
        try{
            $sql = "SELECT
			ptaoid,
			ptaptraoid,
			ptanm_arquivo,
			ptadescricao,
			ptrausuoid,
			TO_CHAR(ptadt_anexo_proposta, 'DD/MM/YYYY') as data,
			nm_usuario
			FROM
			proposta_transferencia_anexos
			INNER JOIN usuarios ON ptrausuoid = cd_usuario
			where
			ptaptraoid = $id
			and ptratpanexo = false";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta dos anexos" );
            }
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar a consulta dos anexos" );
        }
        
        
        return pg_fetch_all ( $result );
    }
    
    
    //lista os anexo da carta passando o id da proposta
    public function listaAnexoCartaId($id){
        try{
            $sql = "SELECT
			ptaoid,
			ptaptraoid,
			ptanm_arquivo,
			ptadescricao,
			ptrausuoid,
			TO_CHAR(ptadt_anexo_proposta, 'DD/MM/YYYY') as data,
			nm_usuario
			FROM
			proposta_transferencia_anexos
			INNER JOIN usuarios ON ptrausuoid = cd_usuario
			where
			ptaptraoid = $id
			and ptratpanexo = true";
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta dos anexos" );
            }
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar a consulta dos anexos" );
        }
        
        return pg_fetch_all ( $result );
        
    }
    
    //excluir carta passando o id
    public function excluirCarta($id){
        $retorno = true;
        try{
            $sql = "DELETE FROM proposta_transferencia_anexos WHERE ptaoid = $id";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao deletar o arquivo da carta" );
                $retorno = false;
            }
        }catch(Exception $e){
            throw new Exception ( "Problemas para deletar a carta" );
            $retorno = false;
        }
        
        return $retorno;
    }
    
    //excluir os arquivos passando o id
    public function excluirArquivo($id){
        $retorno = true;
        try{
            $sql = "DELETE FROM proposta_transferencia_anexos WHERE ptaoid = $id";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao deletar o arquivo " );
                $retorno = false;
            }
        }catch(Exception $e){
            throw new Exception ( "Problemas para deletar arquivo" );
            $retorno = false;
        }
        
        return $retorno;
    }
    
    //exclui o contato de instalacao passando o id
    public function excluircontatoInstalacao($id){
        $retorno = true;
        try{
            $sql = "DELETE FROM proposta_transferencia_contato_instalacao WHERE ptcioid = $id";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                
                throw new Exception ( "Erro ao excluir o registro de contato instalação " );
                
                $retorno = false;
            }
        }catch(Exception $e){
            
            throw new Exception ( "Problemas para deletar registro de contato instalação" );
            
            $retorno = false;
        }
        
        return $retorno;
    }
    
    
    //atualiza o contato de instalação passando os dados
    
    public function updatecontatoInstalacao($dados){
        
        
        
        $retorno = true;
        try{
            $sql = "UPDATE  proposta_transferencia_contato_instalacao
					SET
						ptcinome = '$dados[prcnome_cont_inst]',
						ptcifone_residencial ='$dados[prcfone_res_cont_inst]',
						ptcifone_celular = '$dados[prcfone_cel_cont_inst]',
						ptcifone_comercial = '$dados[prcfone_com_cont_inst]' ,
						ptcidnextel = '$dados[prcid_nextel_cont_inst]'
					WHERE ptcioid = $dados[ptraoid] ";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                
                throw new Exception ( "Erro ao atualizar o registro de contato instalação " );
                
                $retorno = false;
            }
        }catch(Exception $e){
            
            throw new Exception ( "Problemas para atualizar registro de contato instalação" );
            
            $retorno = false;
        }
        
        return $retorno;
        
    }
    
    //exclui os contato de emergencia
    public function excluircontatoEmergencia($id){
        $retorno = true;
        try{
            $sql = "DELETE FROM proposta_transferencia_contato_emergencia WHERE ptceoid = $id";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao excluir o registro de contato emergencia " );
                $retorno = false;
            }
        }catch(Exception $e){
            throw new Exception ( "Problemas para deletar registro de contato emergencia" );
            $retorno = false;
        }
        
        return $retorno;
    }
    
    //atualiza os contato de emergencia
    public function updatecontatoEmergencia($dados){
        $retorno = true;
        try{
            $sql = "UPDATE  proposta_transferencia_contato_emergencia
			SET
			ptcenome = '$dados[prcnome_cont_emerg]',
			ptcefone_residencial ='$dados[prcfone_res_cont_emerg]',
			ptcefone_celular = '$dados[prcfone_cel_cont_emerg]',
			ptcefone_comercial = '$dados[prcfone_com_cont_emerg]' ,
			ptceidnextel = '$dados[prcid_nextel_cont_emerg]'
			WHERE ptceoid = $dados[ptceoid] ";
            
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao atualizar o registro de contato emergencia " );
                $retorno = false;
            }
        }catch(Exception $e){
            throw new Exception ( "Problemas para atualizar registro de contato emergencia" );
            $retorno = false;
        }
        
        return $retorno;
        
    }
    
    //atualiza as pessoas autorizadas
    public function updatecontatoPessoasAutorizadas($dados){
        
        $retorno = true;
        try{
            $sql = "UPDATE  proposta_transferencia_pessoas_autorizadas
			SET
			ptpanome = '$dados[ptpanome]',
			ptpacpf = '$dados[ptpacpf]',
			ptparg = '$dados[ptparg]',
			ptpafone_residencial ='$dados[ptpafone_residencial]',
			ptpafone_celular = '$dados[ptpafone_celular]',
			ptpafone_comercial = '$dados[ptpafone_comercial]' ,
			ptpaidnextel = '$dados[ptpaidnextel]'
			WHERE ptpaoid = $dados[ptpaoid] ";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao atualizar o registro de pessoas autorizadas " );
                $retorno = false;
            }
        }catch(Exception $e){
            throw new Exception ( "Problemas para atualizar o registro de pessoas autorizadas" );
            $retorno = false;
        }
        
        return $retorno;
        
    }
    
    //exclui os contatos de pessoas autorizadas
    public function excluircontatoPessoaAutorizada($id){
        $retorno = true;
        try{
            $sql = "DELETE FROM proposta_transferencia_pessoas_autorizadas WHERE ptpaoid = $id";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao excluir o registro de contato pessoa autorizada " );
                $retorno = false;
            }
        }catch(Exception $e){
            throw new Exception ( "Problemas para deletar registro de pessoa autorizada" );
            $retorno = false;
        }
        
        return $retorno;
    }
    
    //retorna todas as formas de pagamento
    public function formasPagamento(){
        
        try{
            $sql = "SELECT
					forcoid as codigo,
					forcnome as descricao ,
					forcdebito_conta AS debito
				FROM forma_cobranca
				WHERE forcvenda IS TRUE
					AND forcexclusao IS NULL
					AND (forccobranca_cartao_credito IS FALSE or ( forccobranca_cartao_credito IS TRUE
					AND forcaccoid IS NOT NULL) )";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta da forma de pagamento" );
            }
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar consulta da forma de pagamento" );
        }
        
        return pg_fetch_all ( $result );
        
    }
    
    //Retorna a forma de pagamento que foi cadastrada na tabela proposta_transferencia_forma_pagamento
    //passando o ID da proposta
    public function listaFormaPagamentoIdProposta($id){
        try{
            $sql = "SELECT
					  ptfptraoid ,
					  ptfpforcoid,
					  ptfpcdvoid ,
					  ptfpbancodigo,
					  ptfpagencia,
					  ptfpnumconta,
					  ptfpnumcartaocredito,
					  ptfpvalidadeCartaoCredito,
					  bannome,
					  cdvdia
					FROM
					proposta_transferencia_forma_pagamento
					LEFT JOIN banco ON ptfpbancodigo = bancodigo
					LEFT JOIN cliente_dia_vcto ON ptfpcdvoid = cdvoid
                    WHERE ptfptraoid = $id";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta de pagamento da tabela proposta transferencia forma pagamento" );
            }
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar consulta de pagamento da tabela proposta transferencia forma pagamento" );
        }
        
        return pg_fetch_assoc($result);
    }
    
    //retorna os dias de vencimentos
    public function diaVencimentoBoleto(){
        try{
            $sql = "SELECT cdvoid AS codigo,
					       cdvdia AS descricao
                    FROM cliente_dia_vcto
                    WHERE cdvdt_exclusao IS NULL
                    ORDER BY cdvdia";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta data vencimento" );
            }
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar consulta data vencimento" );
        }
        
        return pg_fetch_all ( $result );
    }
    
    //retorna o nome do banco passando o id
    public function getNomeBancoID($id){
        
        try{
            $sql = "SELECT
						bancodigo as id_banco,
						bannome as banco
					FROM forma_cobranca
					INNER JOIN banco ON bancodigo = forccfbbanco
					WHERE forcoid = $id";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta nome do banco" );
            }
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar consulta nome do banco" );
        }
        
        return pg_fetch_assoc($result);
    }
    
    //retorna a forma de pagamento de credito e debito passando o id
    public function getFormaPagamentoCreditoDebito($id){
        
        try{
            
            $sql = "SELECT
					forcoid as codigo,
					forcnome as descricao ,
					forcdebito_conta AS debito
				FROM forma_cobranca
				WHERE forcvenda IS TRUE
					AND forcexclusao IS NULL
					AND (forccobranca_cartao_credito IS FALSE or ( forccobranca_cartao_credito IS TRUE
					AND forcaccoid IS NOT NULL) )
					AND forcoid in(3,4,12,13,2,24,25,81)
				        AND forcoid = $id";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                
                
                throw new Exception ( "Erro ao efetuar a consulta forma de pagamento debito ou credito " );
                
            }
        }catch(Exception $e){
            
            
            throw new Exception ( "Problemas para efetuar consulta forma de pagamento debito ou credito" );
            
        }
        
        return pg_fetch_assoc($result);
    }
    
    
    //insere a proposta transferencia cliente , endereco, endereco cobrança e forma de pagamento
    
    public function insertPropostaTransferencia($dados){
        
        try {
            
            $this->begin ();
            
            $sqlClienteDel = " DELETE FROM  proposta_transferencia_cliente where ptcptraoid = $dados[ptcptraoid]";
            
            if (!$rsDelCliente = @pg_query($this->conn,$sqlClienteDel)) {
                $this->rollback ();
                return false;
                throw new Exception ( "Erro ao deletar o cadastro do cliente" );
            }
            
            //insere cliente
            $sqlCliente = " INSERT INTO proposta_transferencia_cliente(
					            ptcptraoid, ptcnumdocumento, ptcnome, ptcrg, ptcorgaoemissor,
					            ptcdataemissao, ptcdatanasc, ptcnomepai, ptcnomemae, ptcsexo,
					            ptcivil, ptcoptantesimples, ptcdatafundacao, ptcestadoinscricaoest,
					            ptcinscricaoest, ptctipopessoa)
		   				 VALUES ($dados[ptcptraoid],
		   				 		 '$dados[ptcnumdocumento]',
		   				 		 '$dados[ptcnome]',
		   				 		 '$dados[ptcrg]',
		   				 		 '$dados[ptcorgaoemissor]',
		   				 		  $dados[ptcdataemissao],
		            			  $dados[ptcdatanasc],
		            			 '$dados[ptcnomepai]',
		            			 '$dados[ptcnomemae]',
		            			 '$dados[ptcsexo]',
		            			 '$dados[ptcivil]',
		           				 '$dados[ptcoptantesimples]',
		           				  $dados[ptcdatafundacao],
		           				 '$dados[ptcestadoinscricaoest]',
		           				 '$dados[ptcinscricaoest]',
		           				 '$dados[ptctipopessoa]')";
		           				  
		           				  
		           				  if (!$rsCliente = @pg_query($this->conn,$sqlCliente)) {
		           				      $this->rollback ();
		           				      return false;
		           				      throw new Exception ( "Erro ao efetuar o cadastro do cliente" );
		           				  }
		           				  
		           				  
		           				  $sqlEndDel = " DELETE FROM  proposta_transferencia_endereco where ptendptraoid = $dados[ptcptraoid]";
		           				  
		           				  if (!$rsDelEnd = @pg_query($this->conn,$sqlEndDel)) {
		           				      $this->rollback ();
		           				      return false;
		           				      throw new Exception ( "Erro ao deletar o endereco do cliente" );
		           				  }
		           				  
		           				  //insere endereco
		           				  $sqlEndereco = "INSERT INTO proposta_transferencia_endereco(
	           					ptendptraoid, ptendpaisoid, ptendestoid, ptendcep,
					            ptendcidade, ptendbairro, ptendlogradouro, ptendnumero, ptendcomplemento,
					            ptendfone, ptendfone2, ptendfone3, ptendemail, ptendemailnf)
						    VALUES ($dados[ptcptraoid],
						            $dados[ptendpaisoid],
						            $dados[ptendestoid],
						            '$dados[ptendcep]',
						            '$dados[ptendcidade]',
						            '$dados[ptendbairro]',
						            '$dados[ptendlogradouro]',
						             $dados[ptendnumero],
						            '$dados[ptendcomplemento]',
						            '$dados[ptendfone]',
						            '$dados[ptendfone2]',
									'$dados[ptendfone3]',
									'$dados[ptendemail]',
									'$dados[ptendemailnf]');";
						             
						             
						             if (! $rs = @pg_query ( $this->conn, $sqlEndereco )) {
						                 $this->rollback ();
						                 return false;
						                 
						                 throw new Exception ( "Erro ao efetuar o cadastro do endereço do cliente" );
						                 
						             }
						             
						             $sqlEndCobDel = " DELETE FROM  proposta_transferencia_endereco_cobranca where ptendcobraoid = $dados[ptcptraoid]";
						             
						             if (!$rsDelEndCob = @pg_query($this->conn,$sqlEndCobDel)) {
						                 $this->rollback ();
						                 return false;
						                 
						                 throw new Exception ( "Erro ao deletar o endereco cobrança do cliente" );
						                 
						             }
						             
						             
						             //insere endereco cobrança
						             
						             $sqlEnderecoCobranca = "INSERT INTO proposta_transferencia_endereco_cobranca(
							           ptendcobraoid, ptendcobpaisoid, ptendcobestoid,
							            ptendcobcep, ptendcobcidade, ptendcobbairro, ptendcoblogradouro,
							            ptendcobnumero, ptendcobcomplemento)
								    VALUES ($dados[ptcptraoid],
								    		$dados[ptendcobpaisoid],
								    		$dados[ptendcobestoid],
								    		'$dados[ptendcobcep]',
								            '$dados[ptendcobcidade]',
								            '$dados[ptendcobbairro]',
								            '$dados[ptendcoblogradouro]',
								            $dados[ptendcobnumero],
								            '$dados[prpendcob_compl]');";
								            
								            
								            if (! $rs = @pg_query ( $this->conn, $sqlEnderecoCobranca )) {
								                $this->rollback ();
								                return false;
								                throw new Exception ( "Erro ao efetuar o cadastro de proposta de transferencia" );
								            }
								            
								            $sqlFormaPagDel = " DELETE FROM proposta_transferencia_forma_pagamento where ptfptraoid = $dados[ptcptraoid]";
								            
								            if (!$rsDelFormaPag = @pg_query($this->conn,$sqlFormaPagDel)) {
								                $this->rollback ();
								                return false;
								                throw new Exception ( "Erro ao deletar a forma de pagamento do cliente" );
								            }
								            
								            //insere forma de pagamento
								            $sqlFormaPagamento = "INSERT INTO proposta_transferencia_forma_pagamento(
								            ptfptraoid, ptfpforcoid, ptfpcdvoid, ptfpbancodigo,
								            ptfpagencia, ptfpnumconta, ptfpnumcartaocredito, ptfpvalidadecartaocredito)
								    VALUES ($dados[ptcptraoid],
								    		$dados[ptfpforcoid],
								    		$dados[ptfpcdvoid],
								    		$dados[ptfpbancodigo],
								    		'$dados[ptfpagencia]',
								            '$dados[ptfpnumconta]',
											'$dados[ptfpnumcartaocredito]',
											'$dados[ptfpvalidadeCartaoCredito]');";
								    		
								    		
								    		if (! $rs = @pg_query ( $this->conn, $sqlFormaPagamento )) {
								    		    
								    		    $this->rollback ();
								    		    return false;
								    		    throw new Exception ( "Erro ao efetuar o cadastro de proposta de transferencia" );
								    		}
								    		
								    		$this->commit();
        } catch ( Exception $e ) {
            $this->rollback ();
            return false;
            throw new Exception ( "Erro ao efetuar o cadastro de proposta de transferencia" );
        }
        
        return true;
    }
    
    /*
     * Busca as informações do novo cliente pelo id na tabela proposta_Transferencia_cliente
     */
    public function consultaNovoClientePropostaId($id) {
        
        try{
            
            $sql = "SELECT ptcptraoid,
					       ptcnumdocumento,
					       ptcnome,
					       ptcrg,
					       To_char(ptcdataemissao, 'DD/MM/YYYY') AS ptcdataemissao,
					       ptcorgaoemissor,
					       To_char(ptcdatanasc, 'DD/MM/YYYY') AS ptcdatanasc,
					       ptcnomepai,
					       ptcnomemae,
					       ptcsexo,
					       ptcivil,
					       ptcoptantesimples,
					       ptcdatafundacao,
					       ptcestadoinscricaoest,
					       ptcinscricaoest,
					       ptendpaisoid,
					       ptendestoid,
					       ptendcep,
					       ptendcidade,
					       ptendbairro,
					       ptendlogradouro,
					       ptendnumero,
					       ptendcomplemento,
					       ptendfone,
					       ptendfone2,
					       ptendfone3,
					       ptendemail,
					       ptendemailnf,
					       ptendcobpaisoid,
					       ptendcobestoid,
					       ptendcobcep,
					       ptendcobcidade,
					       ptendcobbairro,
					       ptendcoblogradouro,
					       ptendcobnumero,
					       ptendcobcomplemento,
					       ptfpforcoid,
					       ptfpcdvoid,
					       ptfpbancodigo,
					       ptfpagencia,
					       ptfpnumconta,
					       ptfpnumcartaocredito,
					       ptfpvalidadecartaocredito
					FROM   proposta_transferencia_cliente
					       INNER JOIN proposta_transferencia_endereco
					               ON ptcptraoid = ptendptraoid
					       INNER JOIN proposta_transferencia_endereco_cobranca
					               ON ptcptraoid = ptendcobraoid
					       INNER JOIN proposta_transferencia_forma_pagamento
					               ON ptcptraoid = ptfptraoid
					WHERE  ptcptraoid = $id";
            
            if (!$result =pg_query($this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta  novo cliente da proposta " );
            }
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar consulta do novo cliente da proposta " );
        }
        
        return pg_fetch_assoc($result);
    }
    
    
    //retorna o metodo pagamento ja¡ cadastrato pelo cliente
    
    public function consultaFormaPagamentoExistente($cliente){
        
        try{
            
            $sql = "
 					SELECT
 					
	                    clioid,
	                    clinome,
	                    clitipo,
					    clino_cpf,
					    clino_cgc,
	                    CASE WHEN clitipo = 'F' THEN
	                        clino_cpf
	                    ELSE
	                        clino_cgc
	                    END AS clino_documento,
	                    forcoid,
						cdvoid,
	                    clidia_vcto,
	                    bancodigo,
	                    bannome,
	                    clicagencia,
	                    clicconta,
	                    cliemail,
	                    cliemail_nfe,
	                    endddd,
	                    endfone,
	                    endno_cep,
	                    endcep,
	                    CASE
	                        WHEN endpaisoid is not null THEN endpaisoid
	                        ELSE 1
	                    END AS endpaisoid,
	                    
			    		CASE
	                        WHEN endestoid is not null THEN endestoid
	                        ELSE (SELECT estoid FROM estado WHERE estuf = enduf)
	                    END AS endestoid,
		   			    enduf,
	                    endcidade,
	                    endbairro,
	                    endlogradouro,
	                    endno_numero,
	                    endcomplemento,
	                    forcdebito_conta,
						clicdias_prazo,
						clicdias_uteis,
						clictipo,
						clicdia_mes,
						clicdia_semana,
						clictitular_conta,
						clifaturamento,
						clifat_locacao
	                FROM
	                    clientes
	                    LEFT JOIN endereco ON endoid = cliend_cobr
	                    LEFT JOIN cliente_cobranca ON clicclioid = clioid
	                    LEFT JOIN forma_cobranca ON forcoid  = clicformacobranca
	                    LEFT JOIN banco ON bancodigo = forccfbbanco
					    LEFT JOIN cliente_dia_vcto ON cdvdia = clidia_vcto
	                WHERE
	                    clicexclusao IS NULL AND
	                    clioid = $cliente
	                ORDER BY
	                    clicoid DESC
	                LIMIT 1 ";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                
                throw new Exception ( "Erro no sql ao efetuar a consulta de pagamento ja¡ existente " );
                
            }
        }catch(Exception $e){
            
            throw new Exception ( "Problemas para efetuar a consulta de pagamento ja¡ existente" );
            
        }
        
        
        
        
        return pg_fetch_assoc($result);
    }
    
    
    
    //retorna o metodo pagamento jÃ¡ cadastrato pelo cliente
    
    public function consultaFormaPagamentoExistenteCadastrada($cliente){
        
        try{
            
            $sql = " SELECT forcoid, forcnome, forccobranca_cartao_credito, forcdebito_conta, forccobranca_registrada,
							CASE WHEN cccativo = 'f' THEN ''
							ELSE cccsufixo
							END as cccsufixo,
							cccativo,
							cccnome_cartao
						FROM cliente_cobranca
						LEFT JOIN forma_cobranca ON forcoid = clicformacobranca
						LEFT JOIN cliente_cobranca_credito ON cccclioid = clicclioid
						WHERE clicclioid = $cliente
						AND clicexclusao IS NULL
						ORDER BY cccoid DESC
						LIMIT 1";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro no sql ao efetuar a consulta de pagamento cadastrada " );
            }
        }catch(Exception $e){
            throw new Exception ( "Problemas para  efetuar a consulta de pagamento cadastrada" );
        }
        
        
        
        
        return pg_fetch_assoc($result);
    }
    
    
    
    /*Verifica se jÃ¡ existe o cliente cadastrado na tabela Clientes*/
    
    public function consultaClienteCadastrado($noDocumento){
        
        try{
            if(strlen($noDocumento) <= 11) {
                $filtro = "and clino_cpf = $noDocumento";
            }else {
                
                $filtro = "and clino_cgc = $noDocumento";
            }
            
            $sql = " SELECT
					              clioid,
					   clitipo,
					   clipaisoid,
		                CASE WHEN clitipo='F' THEN
		                    clino_cpf
		                WHEN clitipo='J' THEN
		                   clino_cgc
		                END AS cpf_cgc,
                       cliclicloid,
				       clinome,
				       clino_cpf,
				       clino_cgc,
				       clidt_nascimento,
				       clisexo,
				       clino_rg,
				       cliemissor_rg,
				       To_char(clidt_emissao_rg, 'DD/MM/YYYY') AS clidt_emissao_rg,
				       clipai,
				       climae,
				       cliestado_civil,
					   clireg_simples,
					   cliinscr_municipal,
					   cliuf_inscr,
					   cliemail,
					   cliemail_nfe,
					   clipaisoid,
                       			cliformacobranca,
					                CASE WHEN clitipo='F' THEN
					                    to_char(clidt_nascimento,'dd/mm/yyyy')
					                WHEN clitipo='J' THEN
					                    to_char(clidt_fundacao,'dd/mm/yyyy')
					                END AS clidt_nascimento,
					 CASE
									         WHEN clitipo = 'F' THEN ( COALESCE(clirua_res, '')
									                                   || ', '
									                                   || COALESCE(clino_res, 0)
									                                   || ', '
									                                   || COALESCE(clicompl_res, '')
									                                   || ', '
									                                   || COALESCE(clibairro_res, '')
									                                   || ', '
									                                   || COALESCE(clicep_res, '')
									                                   || ', '
									                                   || COALESCE(clicidade_res, '')
									                                   || ', '
									                                   || COALESCE(cliuf_res, '')
									                                   || ', '
									                                   || COALESCE(clifone_res, '') )
									         WHEN clitipo = 'J' THEN ( COALESCE(clirua_com, '')
									                                   || ', '
									                                   || COALESCE(clino_com, 0)
									                                   || ', '
									                                   || COALESCE(clicompl_com, '')
									                                   || ', '
									                                   || COALESCE(clibairro_com, '')
									                                   || ', '
									                                   || COALESCE(clicep_com, '')
									                                   || ', '
									                                   || COALESCE(clicidade_com, '')
									                                   || ', '
									                                   || COALESCE(cliuf_com, '')
									                                   || ', '
									                                   || COALESCE(clifone_com, '') )
													         ELSE ''
													       END AS cliente_end_ant,
									                CASE WHEN cliemail IS NULL THEN
									                    E1.endemail
									                ELSE
									                    cliemail
									                END AS cliemail,
									                
									                clino_cgc, clino_cpf, cliinscr,
									                clireg_simples, cliemail_nfe,
									                
									                E1.endno_numero,
									                E1.endcomplemento,
									                E1.endbairro,
									                E1.enduf,
									                E1.endpaisoid,
									                CASE WHEN E1.endcep IS NULL THEN
									                    E1.endno_cep::text
									                ELSE
									                    E1.endcep::text
									                END AS endno_cep,
									                E1.endcidade,
									                E1.endlogradouro,
									                E1.endddd,
									                E1.endfone_array[1] as fone1,
									                E1.endfone_array[2] as fone2,
									                E1.endfone_array[3] as fone3,
													E2.endno_numero as endnocobr,
									                E2.endcomplemento as endcomplcobr,
									                E2.endbairro as endbairrocobr,
									                E2.enduf as endufcobr ,
									                E2.endpaisoid as endpaisoidcobr,
									                CASE WHEN E2.endcep IS NULL THEN
									                    E2.endno_cep::text
									                ELSE
									                    E2.endcep::text
									                END AS endcepcobr,
									                E2.endcidade as endcidcobr,
									                E2.endlogradouro as endlogradcobr
									                FROM clientes
									                INNER JOIN endereco AS E1 ON cliendoid = E1.endoid
									                LEFT JOIN endereco AS E2 ON cliend_cobr = E2.endoid
									                WHERE  1 = 1
									                AND clidt_exclusao IS NULL
													$filtro ";
													
													if (! $result = pg_query ( $this->conn, $sql )) {
													    throw new Exception ( "Erro ao efetuar a consulta  na table clientes " );
													}
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar consulta  na table clientes " );
        }
        
        $clientes = 0;
        if(pg_num_rows($result) > 0){
            $clientes = pg_fetch_assoc($result);
        }
        
        return $clientes;
    }
    
    
    //retorna a forma atual de pagamento do cliente que ja¡ tem cadastro no sistema,
    //caso seja novo cliente não tras forma de pagamento
    
    public function consultaFormadePagamentoAtualIDCliente($id) {
        
        try{
            
            $sql = "SELECT cpagbancodigo,
					       clidia_vcto,
					       bannome,
					       cpagforcoid,
					       cpagcartao,
					       cpagcartao_validade,
					       cpagdebito_agencia,
					       cpagdebito_cc
					FROM   contrato_pagamento
					       INNER JOIN contrato
					               ON connumero = cpagconoid
					       INNER JOIN clientes
					               ON conclioid = clioid
					       INNER JOIN banco
					               ON cpagbancodigo = bancodigo
					WHERE  conclioid = $id
					ORDER  BY condt_geracao_contrato DESC
					LIMIT  1  ";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta forma de pagamento  " );
            }
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar consulta forma de pagamento " );
        }
        
        return pg_fetch_assoc($result);
    }
    
    //Passa a sigla do estado e retorna o estoid do estado
    public function buscaIdEstado($uf){
        try{
            $estado = trim($uf);
            $sql = "SELECT
					     estoid
					FROM estado
					WHERE estuf='$estado'";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta do ID do estado" );
            }
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar consulta do ID do estado " );
        }
        
        return pg_fetch_assoc($result);
    }
    
    
    //Passa o id  do estado e retorna o sigla do estado
    public function buscaSiglaEstado($id){
        
        try{
            $estado = trim($uf);
            $sql = "SELECT
			estuf
			FROM estado
			WHERE estoid = $id";
            
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta da sigla do estado" );
            }
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar consulta da sigla do estado " );
        }
        
        return pg_fetch_assoc($result);
    }
    
    
    //Metodo para pesquisar o endereÃ§o passando o CEP
    
    public function buscaEnderecoCEP($cep){
        
        try{
            
            $sql = "SELECT
					  clguf_sg,
					  clgcep,
					  clgoid,
					  clgnome,
					  clgclcoid,
					  clcnome
				    FROM
					  correios_logradouros_view
					WHERE clgcep='".$cep."'
					";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                
                throw new Exception ( "Erro ao efetuar a consulta de endereço  " );
                
            }
        }catch(Exception $e){
            
            throw new Exception ( "Problemas para efetuar consulta do endereço " );
            
        }
        
        return pg_fetch_assoc($result);
    }
    
    /*
     * Metodo para buscar o nome do bairro passando os parametros UF, clgclcoid,cep
     * essas informação já recuperada a partir do retorno do metodo buscaEnderecoCep
     */
    
    public function buscaBairroCep($uf,$id,$cep){
        
        try{
            
            $sql = "SELECT
					  clgcep,
					  cbanome,
					  cbaoid
					FROM
					  correios_logradouros,
					  correios_localidades,
					  correios_bairros
					WHERE clgclcoid=clcoid and clguf_sg = '".$uf."'
					AND clgclcoid = $id
					AND clgcep='".$cep."'
					AND (clgcbaoid_ini = cbaoid or clgcbaoid_fim = cbaoid)
					ORDER BY cbanome
					";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta de bairro  " );
            }
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar consulta do bairro " );
        }
        
        return pg_fetch_assoc($result);
    }
    
    //busca as cidades passando a sigla do estado
    public function buscaCidadesSiglaEstado($estado){
        try{
            
            $sql = "SELECT
			            clcnome
			        FROM
			            correios_localidades
			        WHERE
			            clcuf_sg='$estado'
			        GROUP BY clcoid,clcnome
			        ORDER BY clcnome";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta de bairro  " );
            }
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar consulta do bairro " );
        }
        
        return pg_fetch_all($result);
    }
    
    
    //metodo que ira¡ buscar o id da table correios localidades passando a sigla do estado e o nome da cidade
    
    public function buscaIdLocalidade($siglaEstado,$cidade){
        try{
            
            $sql = "SELECT
					   clcoid
					FROM
					   correios_localidades
					WHERE
					   clcuf_sg='$siglaEstado'
					AND
					    clcnome = '$cidade'
					";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta do id correrios_localidades  " );
            }
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar consulta do id correrios_localidades " );
        }
        
        return pg_fetch_assoc($result);
    }
    
    
    //metodo que ira¡ buscar todos os bairros passando o id da tabela correios_localidade
    
    public function buscaBairrosIdLocalidade($id){
        try{
            
            $sql = "SELECT
					  trim(cbanome) AS cbanome
					FROM
					  correios_bairros
					WHERE cbaclcoid=$id";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta dos bairros" );
            }
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar consulta dos bairros" );
        }
        
        return pg_fetch_all($result);
    }
    
    //Altera o status da proposta transferencia para cancelado
    public function cancelaSolicitacaoPropostaTransferencia($id){
        try{
            $sql = "UPDATE proposta_transferencia
					SET
					   ptrastatus_conclusao_proposta = 'CA'
					WHERE ptraoid = $id";
            
            if (! $rs = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar ao cancelar proposta de transferencia" );
                return false;
            }
        }catch(Exception $e){
            
            throw new Exception("Falha ao atualizar solicitação de cancelamento proposta de transferencia");
            
            return false;
        }
        
        return true;
    }
    
    
    
    /*
     * Metodo que verifica se cliente já existe na tabela clientes, caso não exista ele salva
     */
    public function SaveAtualizaCliente($dados){
        
        
        try{
            $this->begin ();
            
            $cliend_cobr = '';
            $cliendoid = '';
            $clino_cgc = 0;
            $clino_cpf = 0;
            $endnunFis = 0;
            $endnumJur = 0;
            
            
            $estado = $this->buscaSiglaEstado($dados['ptendestoid']);
            $estadoCobranca = $this->buscaSiglaEstado($dados['ptendcobestoid']);
            $filtro .= " AND clitipo = '" . $dados['ptctipopessoa'] . "'";
            
            if ($dados ['ptctipopessoa'] == 'J') {
                $filtro .= " AND clino_cgc = ".$dados['ptcnumdocumento']. "";
                $clino_cgc = $dados['ptcnumdocumento'];
                $cepJur = $dados[ptendcep];
                $ufJur = $estado[estuf];
                $cidJur = $dados[ptendcidade];
                $logJur = $dados[ptendlogradouro];
                $endnunJur = $dados[ptendnumero];
                $complJur = $dados[ptendcomplemento];
                $bairroJur = $dados[ptendbairro];
            }else{
                $filtro .= " AND clino_cpf =". $dados['ptcnumdocumento']."";
                $clino_cpf = $dados['ptcnumdocumento'];
                $cepFis = $dados[ptendcep];
                $ufFis = $estado[estuf];
                $cidFis = $dados[ptendcidade];
                $logFis = $dados[ptendlogradouro];
                $endnunFis = $dados[ptendnumero];
                $complFis = $dados[ptendcomplemento];
                $bairroFis = $dados[ptendbairro];
            }
            
            $sql = "SELECT
							*
						 FROM   clientes
							where 1=1
		    				$filtro
		    				";
		    				
		    				$rs = pg_query ( $this->conn, $sql );
		    				if (! is_resource ( $rs )){
		    				    return 0;
		    				    throw new Exception ( 'Falha ao consultar.' );
		    				}
		    				
		    				if(pg_num_rows($rs) == 0) {
		    				    $fones = "{".$dados[ptendfone] .','. $dados[ptendfone2] .','. $dados[ptendfone3]."}";
		    				    $sqlInsertEndereco = "INSERT INTO endereco
					(
		  			endno_numero,
					endcomplemento,
					endbairro,
					enduf,
					endcidade,
					endlogradouro,
					endemail,
					endpaisoid,
					endestoid,
					endfone_array,
					endcep
					)
					VALUES
					(
					$dados[ptendnumero],
				    '$dados[ptendcomplemento]',
				    '$dados[ptendbairro]',
					'$estado[estuf]',
					'$dados[ptendcidade]',
					'$dados[ptendlogradouro]',
					'$dados[ptendemail]',
					$dados[ptendpaisoid],
					$dados[ptendestoid],
					'$fones',
					$dados[ptendcep]) RETURNING endoid";
					
					if (! $resultEnd = pg_query ( $this->conn, $sqlInsertEndereco )) {
					    $this->rollback ();
					    return 0;
					    throw new Exception ( "Erro ao efetuar o cadastro de endereco" );
					}
					
					$arr = pg_fetch_array ( $resultEnd, 0 );
					$cliendoid = $arr[endoid];
					
					$sqlInsertEnderecoCobr = "INSERT INTO endereco
									  (
									      endno_numero,
										  endcomplemento,
										  endbairro,
										  enduf,
										  endcidade,
										  endlogradouro,
										  endpaisoid,
										  endestoid,
										  endcep
									  )
								VALUES
									  (
										$dados[ptendnumero],
										'$dados[prpendcob_compl]',
										'$dados[ptendcobbairro]',
										'$estadoCobranca[estuf]',
										'$dados[ptendcobcidade]',
										'$dados[ptendcoblogradouro]',
										$dados[ptendcobpaisoid],
									    $dados[ptendcobestoid],
									    $dados[ptendcobcep]) RETURNING endoid";
									    
									    
									    
									    if (! $resultEndCob = pg_query ( $this->conn, $sqlInsertEnderecoCobr )) {
									        $this->rollback ();
									        return 0;
									        throw new Exception ( "Erro ao efetuar o cadastro de endereco" );
									    }
									    
									    $arr = pg_fetch_array ( $resultEndCob, 0 );
									    $cliend_cobr = $arr [endoid];
									    
									    
									    
									    
									    $sqlInsertCliente = "INSERT INTO clientes
									  (
									  clitipo,
									  clinome,
									  clidt_nascimento,
									  clisexo ,
									  cliemissor_rg ,
									  clidt_emissao_rg ,
									  clino_cpf ,
									  clipai ,
									  climae,
									  cliestado_civil,
									  clidt_fundacao,
									  cliinscr,
									  clino_cgc,
									  cliuf_res,
									  clicidade_res,
									  clirua_res,
									  clino_res,
									  clicompl_res,
									  clibairro_res,
									  clifone_res,
									  cliuf_com,
									  clicidade_com,
									  clirua_com,
									  clino_com,
									  clicompl_com,
									  clibairro_com,
									  clifone_com,
									  clifone_cel,
									  cliformacobranca,
									  clidia_vcto,
									  clireg_simples,
									  cliusuoid,
									  cliemail,
									  clirg ,
									  cliinscr_municipal,
									  cliemail_nfe ,
									  clicep_res,
									  clicep_com,
									  cliend_cobr,
									  clipaisoid,
									  cliendoid
									  )
								VALUES
									  (
									  '$dados[ptctipopessoa]',
									  '$dados[ptcnome]',
									  $dados[ptcdatanasc],
									  '$dados[ptcsexo]',
									  '$dados[ptcorgaoemissor]',
									  $dados[ptcdataemissao],
									   $clino_cpf,
									  '$dados[ptcnomepai]',
									  '$dados[ptcnomemae]',
									  '$dados[ptcivil]',
									   $dados[ptcdatafundacao],
									  '$dados[ptcinscricaoest]',
									  '$clino_cgc',
									  '$ufFis',
									  '$cidFis',
									  '$logFis',
									   $endnunFis,
									  '$complFis',
									  '$bairroFis',
									  '$telefone',
									  '$ufJur',
									  '$cidJur',
									  '$logJur',
									   $endnumJur,
									  '$complJur',
									  '$bairroJur',
									  '$clifone_com',
									  '$clifone_cel',
									  '$dados[ptfpforcoid]',
									  '$dados[ptfpcdvoid]',
									  '$dados[ptcoptantesimples]',
									  '$dados[usuoid]',
									  '$dados[ptendemail]',
									  '$dados[ptcrg]',
									  '$dados[ptcinscricaoest]',
									  '$dados[ptendemailnf]',
									  '$cepFis ',
									  '$cepJur',
									  $cliend_cobr,
									  $dados[ptendcobpaisoid],
									  $cliendoid
									  ) RETURNING clioid";
									  
									  if (! $resultCliente = pg_query ( $this->conn, $sqlInsertCliente )) {
									      $this->rollback ();
									      return 0;
									      throw new Exception ( "Erro ao efetuar o cadastrar clientes" );
									  }
									  
									  $row = pg_fetch_array ( $resultCliente, 0 );
									  $clioid = $row [clioid];
									  
									  
									  
		    				}else {
		    				    
		    				    $arr = pg_fetch_array ( $rs, 0 );
		    				    $clioid = $arr [clioid];
		    				    $cliend_cobr = $arr [cliend_cobr];
		    				    $cliendoid = $arr [cliendoid];
		    				    $fones = "{".$dados[ptendfone] .','. $dados[ptendfone2] .','. $dados[ptendfone3]."}";
		    				    
		    				    if(!empty($cliendoid) || $cliendoid == '') {
		    				        
		    				        $sqlUpdateEndereco = "UPDATE  endereco SET endno_numero = $dados[ptendnumero] ,endcomplemento = '$dados[ptendcomplemento]',
						endbairro = '$dados[ptendbairro]', enduf = '$estado[estuf]', endcidade = '$dados[ptendcidade]', endlogradouro = '$dados[ptendlogradouro]',
						endemail = '$dados[ptendemail]', endpaisoid = $dados[ptendpaisoid], endestoid = $dados[ptendestoid], endfone_array = '$fones',
						endcep = $dados[ptendcep] WHERE endoid = $cliendoid ";
		    				        
		    				        if (! $resultEndereco = pg_query ( $this->conn, $sqlUpdateEndereco )) {
		    				            $this->rollback ();
		    				            return 0;
		    				            throw new Exception ( "Erro ao atualizar o cadastro de endereco" );
		    				        }
		    				        
		    				    }else{
		    				        
		    				        $sqlInsertEndereco = "INSERT INTO endereco
						(
						endno_numero,
						endcomplemento,
						endbairro,
						enduf,
						endcidade,
						endlogradouro,
						endemail,
						endpaisoid,
						endestoid,
						endfone_array,
						endcep
						)
						VALUES
						(
						$dados[ptendnumero],
						'$dados[ptendcomplemento]',
						'$dados[ptendbairro]',
						'$estado[estuf]',
						'$dados[ptendcidade]',
						'$dados[ptendlogradouro]',
						'$dados[ptendemail]',
						$dados[ptendpaisoid],
						$dados[ptendestoid],
						'$fones',
						$dados[ptendcep]) RETURNING endoid";
						
						if (! $resultEnd = pg_query ( $this->conn, $sqlInsertEndereco )) {
						    $this->rollback ();
						    return 0;
						    throw new Exception ( "Erro ao efetuar o cadastro de endereco" );
						}
						
						$arr = pg_fetch_array ( $resultEnd, 0 );
						$cliendoid = $arr[endoid];
						
		    				    }
		    				    
		    				    
		    				    if(!empty($cliend_cobr) || $cliend_cobr == '') {
		    				        
		    				        $sqlInsertEnderecoCobr = "UPDATE  endereco SET endno_numero = $dados[ptendnumero], endcomplemento = '$dados[prpendcob_compl]',
						endbairro, ='$dados[ptendcobbairro]', enduf = '$estadoCobranca[estuf]', endcidade = '$dados[ptendcobcidade]',
						endlogradouro = '$dados[ptendcoblogradouro]', endpaisoid = $dados[ptendcobpaisoid], endestoid = $dados[ptendcobestoid],
		                endcep = $dados[ptendcobcep]  WHERE endoid = $cliend_cobr ";
		    				        
		    				        if (! $resultEndereco = pg_query ( $this->conn, $sqlUpdateEndereco )) {
		    				            $this->rollback ();
		    				            return 0;
		    				            
		    				            throw new Exception ( "Erro ao atualizar o cadastro de endereco cobrança" );
		    				            
		    				        }
		    				        
		    				    }else{
		    				        
		    				        $sqlInsertEnderecoCobr = "INSERT INTO endereco
						(
						endno_numero,
						endcomplemento,
						endbairro,
						enduf,
						endcidade,
						endlogradouro,
						endpaisoid,
						endestoid,
						endcep
						)
						VALUES
						(
						$dados[ptendcobpaisoid],
						'$dados[prpendcob_compl]',
						'$dados[ptendcobbairro]',
						'$estadoCobranca[estuf]',
						'$dados[ptendcobcidade]',
						'$dados[ptendcoblogradouro]',
						$dados[ptendcobpaisoid],
						$dados[ptendcobestoid],
						$dados[ptendcobcep]) RETURNING endoid";
						
						
						if (! $resultEnd = pg_query ( $this->conn, $sqlInsertEndereco )) {
						    $this->rollback ();
						    return 0;
						    throw new Exception ( "Erro ao efetuar o cadastro de endereco" );
						}
						
						$arr = pg_fetch_array ( $resultEnd, 0 );
						$cliend_cobr = $arr[endoid];
		    				    }
		    				    
		    				    
		    				    $sqlUpdateCliente = "UPDATE  clientes SET clitipo ='$dados[ptctipopessoa]', clinome = '$dados[ptcnome]', clidt_nascimento = $dados[ptcdatanasc], clisexo = '$dados[ptcsexo]',
					cliemissor_rg = '$dados[ptcorgaoemissor]', clidt_emissao_rg =$dados[ptcdataemissao], clino_cpf = $clino_cpf, clipai ='$dados[ptcnomepai]' , climae = '$dados[ptcnomemae]',
					cliestado_civil = '$dados[ptcivil]', clidt_fundacao = $dados[ptcdatafundacao],cliinscr = '$dados[ptcinscricaoest]', clino_cgc = '$clino_cgc',cliuf_res = '$ufFis', clicidade_res = '$cidFis',
					clirua_res = '$logFis', clino_res = $endnunFis, clicompl_res = '$complFis', clibairro_res = '$bairroFis', clifone_res = '$telefone', cliuf_com = '$ufJur', clicidade_com = '$cidJur',
					clirua_com = '$logJur', clino_com = $endnumJur, clicompl_com = '$complJur', clibairro_com = '$bairroJur', clifone_com = '$clifone_com', clidt_alteracao = now(), clifone_cel = '$clifone_cel', cliformacobranca = '$dados[ptfpforcoid]',
					clidia_vcto = '$dados[ptfpcdvoid]', clireg_simples = '$dados[ptcoptantesimples]', cliusuoid = '$dados[usuoid]', cliemail = '$dados[ptendemail]', clirg = '$dados[ptcrg]', cliinscr_municipal = '$dados[ptcinscricaoest]',
					cliemail_nfe = '$dados[ptendemailnf]', clicep_res = '$cepFis ', clicep_com = '$cepJur', cliend_cobr = $cliend_cobr, clipaisoid = $dados[ptendcobpaisoid], cliendoid = $cliendoid,cliusuoid_alteracao = $dados[usuoid]
					WHERE clioid =  $clioid";
		    				    
		    				    if (! $resultEnd = pg_query ( $this->conn, $sqlUpdateCliente )) {
		    				        $this->rollback ();
		    				        return 0;
		    				        
		    				        throw new Exception ( "Erro ao efetuar atualização na tabela  clientes" );
		    				        
		    				    }
		    				    
		    				    
		    				    
		    				}
		    				$this->commit ();
		    				
        } catch ( Exception $e ) {
            $this->rollback ();
            return  0;
        }
        return $clioid;
    }
    
    // retorna a quantidades de contatos emergencias  cadastrado no banco com o id
    public function listaContatoEmergenciaCount($id) {
        
        try{
            $sql = "SELECT
						*
					FROM
						proposta_transferencia_contato_emergencia
					where
						ptceptraoid = $id ";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta dos contatos emergencias" );
            }
        }catch ( Exception $e ) {
            throw new Exception ( "Problemas ao efetuar a consulta  dos contatos emergencias" );
        }
        
        
        return pg_num_rows ( $result );
    }
    
    // retorna a quantidades de pessoas autorizadas  cadastrado no banco com o id
    public function listaPessoaDaoIdPropostaCount($id) {
        
        
        try{
            $sql = "SELECT
				*
			FROM
				proposta_transferencia_pessoas_autorizadas
			where
				ptpaptraoid = $id ";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta da pessoas autorizadas" );
            }
        }catch ( Exception $e ) {
            throw new Exception ( "Problemas ao efetuar a consulta  da pessoas autorizadas" );
        }
        
        
        return pg_num_rows ( $result );
    }
    
    
    // retorna a quantidades de contatos/instalação  cadastrado no banco com o id
    
    public function listaContatoInstalacaoIDPropostaCount($id) {
        
        try{
            $sql = "SELECT
				*
			FROM
				proposta_transferencia_contato_instalacao
			where
				ptciptraoid = $id ";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                
                throw new Exception ( "Erro ao efetuar a consulta dos contato instalação" );
                
            }
        }catch ( Exception $e ) {
            
            throw new Exception ( "Problemas ao efetuar a consulta dos contato instalação" );
            
        }
        
        
        return pg_num_rows ( $result );
    }
    
    // retorna a quantidades de anexo  cadastrado no banco com o id
    public function listAnexosPropostaIdCount($id){
        
        try{
            $sql = "SELECT
						*
					FROM
						proposta_transferencia_anexos
					WHERE
						ptaptraoid = $id
						and ptratpanexo = false";
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta dos anexos count" );
            }
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar a consulta dos anexos count" );
        }
        
        
        return pg_num_rows ( $result );
    }
    
    // retorna a quantidades de anexo de cartas cadastrada no banco com o id
    public function listaAnexoCartaIdCount($id){
        try{
            $sql = "SELECT
						*
					FROM
						proposta_transferencia_anexos
					WHERE
						ptaptraoid = $id
						and ptratpanexo = true";
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta dos anexos count" );
            }
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar a consulta dos anexos count" );
        }
        
        return pg_num_rows ( $result );
        
    }
    
    
    //deverá gerar um novo contrato para o cliente, para isso criar um registro na tabela contrato
    
    public function geranovoContrato($dados,$idProposta){
        
        
        $this->retornaValorTotalContratosTransferencia($idProposta);
        
        $this->begin();
        
        
        try{
            $count = 0;
            
            foreach ($dados as $key){
                
                $retornoContratoEquipamento = $this->retornaContratoTransrenciaEquipamento($key[contrato]);
                
                $retornoupgradedowngrade = $this->retornaUpgradeDownGrade($key[contrato]);
                
                if(count($retornoupgradedowngrade) > 0 && !empty($retornoupgradedowngrade) || $retornoupgradedowngrade != null) {
                    
                    $dtInicioVigencia = $retornoupgradedowngrade['hfcdt_fidelizacao'];
                    
                    $prazo = $retornoupgradedowngrade['hfcprazo'];
                    
                    $data = date('Y-m-d', strtotime("+$prazo month", strtotime($dtInicioVigencia)));
                    
                    $meses = $this->somaDatas($data);
                    
                    $conprazo_contrato = $this->retornoMesesVigencia($meses);
                    
                }else{
                    $retornoDataVigencia = $this->retornoCondicaoDataVigencia($key[contrato]);
                    
                    $meses = $this->somaDatas($retornoDataVigencia);
                    
                    $conprazo_contrato = $this->retornoMesesVigencia($meses);
                    
                    $conprazo_contrato = abs($conprazo_contrato);
                }
                
                
                
                if($retornoContratoEquipamento['coneqcoid'] == null || $retornoContratoEquipamento['coneqcoid'] == '' ){
                    $pedidoTrocaVeiculo = $this->retornaPedidoTrocaVeiculo($key[contrato]);
                }
                
                $cdUsuario = $key['usuario'];
                $dataReferencia = $key['dataReferencia'];
                $dataVencimento = $key['dataVencimento'];
                $clienteID = $key[idCliente];
                
                $dadosPagamento = array();
                
                $retornoContrato = $this->listContratoNumContrato ($key[contrato]);
                
                if($retornoContrato[conveioid] == '' || $retornoContrato[conveioid] == null) {
                    $retornoContrato[conveioid] = "null";
                }
                
                $sqlUpdateteste = "UPDATE contrato SET
						conequoid =null,
						conveioid =null
						WHERE
						connumero =$key[contrato]";
                
                if (! $result = pg_query ( $this->conn, $sqlUpdateteste )) {
                    $this->rollback ();
                    return 0;
                    throw new Exception ( "Erro ao efetuar a update da tabela contrato" );
                }
                
                $sqlConTipo = "select
				    					coneqcoid,
				    					conno_tipo
				    		       from
				    		           contrato
				    		       where
				    		            connumero  =  $key[contrato]";
                
                if (! $result = pg_query ( $this->conn, $sqlConTipo )) {
                    $this->rollback ();
                    return 0;
                    throw new Exception ( "Erro ao efetuar a consulta  na tabela contrato" );
                }
                
                $rows = pg_fetch_array ( $result, 0 );
                $conno_tipo = $rows [conno_tipo];
                $coneqcoid = $rows [coneqcoid];
                
                
                if($retornoContrato[conequoid] == '' || $retornoContrato[conequoid] == null || empty($retornoContrato[conequoid])) {
                    $retornoContrato[conequoid] = "null";
                }
                
                
                $sql = "INSERT INTO contrato(
							            condt_cadastro,
									    conclioid ,
									    conequoid,
									    conveioid,
									    constatus,
							            condt_ini_vigencia,
							            condt_alteracao,
							            conno_tipo,
										condt_instalacao,
										condt_substituicao,
										connumero_antigo,
										coneqcoid,
										conmsuboid ,
										conusuoid ,
										concsioid,
										conusuoid_via,
										condt_via ,
										condt_primeira_instalacao,
										conusuoid_exclusao,
										conprazo_contrato
							 )
  						         VALUES (
										now(),
										$key[idCliente],
										$retornoContrato[conequoid],
										$retornoContrato[conveioid],
										'T',
										now(),
										now(),
										$conno_tipo,
										now(),
										now(),
										$key[contrato],
										$coneqcoid,
										1,
										$key[usuario],
										1,
										null,
										null,
										now(),
										null,
										$conprazo_contrato) RETURNING connumero";
										
										
										if (! $result = pg_query ( $this->conn, $sql )) {
										    $this->rollback ();
										    return 0;
										    throw new Exception ( "Erro ao efetuar a cadastro na tabela contrato" );
										}
										
										$row = pg_fetch_array ( $result, 0 );
										$connumero = $row [connumero];
										
										
										
										$sqlUpdate = "UPDATE contrato SET
						condt_alteracao =now(),
						condt_exclusao =now(),
						conveioid_antigo = $retornoContrato[conveioid],
						conequoid_antigo = $retornoContrato[conequoid],
						connumero_novo = $connumero,
						concsioid = 13,
						conusualteracaooid =$key[usuario]
						WHERE
						connumero =$key[contrato]";
										
										
										if (! $result = pg_query ( $this->conn, $sqlUpdate )) {
										    $this->rollback ();
										    return 0;
										    throw new Exception ( "Erro ao efetuar a update da tabela contrato" );
										}
										
										
										$sqlhistorico = "SELECT historico_termo_i($connumero,$cdUsuario,'Transferencia de titularidade do termo $key[contrato] para o  Termo $connumero ')";
										
										
										if (! $resultHistorico = pg_query ( $this->conn, $sqlhistorico )) {
										    $this->rollback ();
										    return 0;
										    
										    throw new Exception ( "Erro ao efetuar a inserção no historico do termo" );
										    
										}
										
										
										$valormonitoramento =number_format($key['valormonitoramento'],2,',','.');
										
										$valorparcelanovocontrato = number_format($key['valorparcelanovocontrato'],2,',','.');
										
										$dadosPagamento = array(
										    'Cpagconoid' => $connumero,
										    'Cpagforcoid'=> $key['formapagamento'],
										    'cpagmonitoramento' => $key['valormonitoramento'],
										    'cpagusuoid' => $key['usuario'],
										    'cpagcpvoid' => $key['condicoespagamento'],
										    'cpagobroid_servico' =>$key['obrfinanceiraserv'],
										    'cpagvl_servico' => $key['valorparcelanovocontrato'],
										    'contrato_antigo' => $key[contrato]
										    
										);
										
										$this->saveupdateContratoPagamento($dadosPagamento);
										
										
										//atualiza o cadastro do contrato de servico e id do novo cliente
										
										//$upadateContratoServidoUsuario = $this->dao->updateContratoServico($key['consoid'],$retornoContrato,$valorTotalFaltanteNovoValor);
										if(isset($key['obr']) &&  count($key['obr']) > 0 && !empty($key['obr']) || $key['obr'] != null) {
										    $this->updateContratoServico($key['obr'],$connumero);
										}
										
										if(isset($key['titulo']) &&  count($key['titulo']) > 0 && !empty($key['titulo']) || $key['titulo'] != null) {
										    $this->updateTitulo($key['titulo']);
										}
										
										if(isset($key['tituloInsere']) &&  count($key['tituloInsere']) > 0 && !empty($key['tituloInsere']) || $key['tituloInsere'] != null) {
										    $this->insertTitulos($key['tituloInsere']);
										}
										
										
										//$this->insertContratoObrigacaoFinanceira($key[contrato], $connumero);
										
										
										//------------------------------------Regra 6.12 ----------------------------------------------------------------//
										//Verifica se existem notas atrasadas
										if(isset($key['notaAtrasadas']) &&  count($key['notaAtrasadas']) > 0 && !empty($key['notaAtrasadas']) || $key['notaAtrasadas'] != null) {
										    
										    
										    //somente sera criada uma nota para todos contratos atrasados
										    
										    if($count == 0) {
										        //inserindo notas atrasada
										        $numeroNotaFiscal = $this->insertNotaFiscal($key['notaAtrasadas']);
										        
										        
										        //se a taxa cobrada for mais que zero ele insere uma vez em nota fiscal items
										        if($key['taxa'] > 0) {
										            
										            $dominio = $this->retornaDominio("OBRIGACOES TRANSFERENCIA DE TITULARIDADE",76);
										            $this->inserItemtNotaFiscal($numeroNotaFiscal['notaNumero'],$connumero,$dominio['valvalor'],$dominio['obrobrigacao'],$key['taxa'],$numeroNotaFiscal['idNota'],"");
										        }
										    }
										    
										    //verica se o contrato possui nota fiscal atrasada
										    $itensNotaFiscaisAtrasadas = $this->retornaNotasEmAtrasoPorContrato($key[contrato]);
										    
										    //verifica se existe nota fiscal atrasada
										    if(isset($itensNotaFiscaisAtrasadas) &&  count($itensNotaFiscaisAtrasadas) > 0 && !empty($itensNotaFiscaisAtrasadas) || $itensNotaFiscaisAtrasadas != null) {
										        
										        foreach ($itensNotaFiscaisAtrasadas as $notas){
										            
										            if($notas['nfitipo'] == "M") {
										                
										                //busca o id da obrigação
										                
										                $dominio = $this->retornaDominio("OBRIGACOES TRANSFERENCIA DE TITULARIDADE",72);
										                //insere o item da nota fiscal
										                $this->inserItemtNotaFiscal($numeroNotaFiscal['notaNumero'],$connumero,$dominio['valvalor'],$dominio['obrobrigacao'],$notas['valor_contrato'],$numeroNotaFiscal['idNota'],"M");
										                
										            }else{
										                
										                //busca o id da obrigação
										                $dominio = $this->retornaDominio("OBRIGACOES TRANSFERENCIA DE TITULARIDADE",74);
										                //insere o item da nota fiscal
										                $this->inserItemtNotaFiscal($numeroNotaFiscal['notaNumero'],$connumero,$dominio['valvalor'],$dominio['obrobrigacao'],$notas['valor_contrato'],$numeroNotaFiscal['idNota'],"L");
										            }
										            
										        }
										    }
										    
										    
										    
										}else if($key['taxa'] > 0 && $count == 0) {
										    
										    //caso não tenha notas atrasadas o sistema verifica se a taxa Ã© maior que zero caso for ele cria uma nota com a obrigação de taxa
										    
										    $numeroNotaFiscal = $this->insertNotaFiscal($key['taxaTransferencia']);
										    $dominio = $this->retornaDominio("OBRIGACOES TRANSFERENCIA DE TITULARIDADE",76);
										    $this->inserItemtNotaFiscal($numeroNotaFiscal['notaNumero'],$connumero,$dominio['valvalor'],$dominio['obrobrigacao'],$key['taxa'],$numeroNotaFiscal['idNota'],"");
										}
										
										//chama o metodo de insert para alterar  o cofconoid passando o numero do novo contrato e antigo contrato,
										$this->insertContratoObrigacaoFinanceiraMensalidades($connumero,$key[contrato]);
										
										
										if(count($pedidoTrocaVeiculo) > 0 && !empty($pedidoTrocaVeiculo) || $pedidoTrocaVeiculo != null || $pedidoTrocaVeiculo!= ''){
										    
										    $ordoid_nova = '';
										    
										    $sql = "SELECT
								*
							FROM
								contrato
							WHERE connumero = $connumero";
										    
										    if(!$result = pg_query($this->conn,$sql)){
										        $this->rollback ();
										        
										        throw new Exception("Erro para efetuar a consulta dos registro  do contrato que esta¡ sendo transfereido");
										        
										    }
										    
										    $novoContratoInf = pg_fetch_assoc($result);
										    $coneqcoid = $novoContratoInf[coneqcoid];
										    $sql = "SELECT ordem_servico_i('\"" . $novoContratoInf[conclioid] .
										    "\" \"" . $novoContratoInf[conveioid ] .
										    "\" \"" . $novoContratoInf[connumero] .
										    "\" \"4\" \"" . $novoContratoInf[conequoid] .
										    "\" \"" . $novoContratoInf[coneqcoid] .
										    "\" \"NULL\" \"f\" \"" .
										    
										    $key[usuario] . "\" \"NULL\" \"TROCA DE veiculo (Reinstalação)\" \"TROCA DE veiculo (Reinstalação)\" ') AS ordoid";
										    
										    
										    
										    
										    if (!$rs = pg_query($conn, $sql)) {
										        $this->rollback ();
										        
										        throw new Exception("Erro para efetuar a inserção na tabela ordem de servico");
										        
										    }else{
										        $ordoid_nova = pg_fetch_result($rs, 0, 'ordoid');
										    }
										    
										    if ($ordoid_nova != '') {
										        
										        
										        //geração da os processo reinstalacao
										        
										        $servico_gerar = 98;
										        
										        $obs = "TROCA DE veiculo (Reinstalação)";
										        
										        
										        $sqlOrdemServicoItem = "SELECT ordem_servico_item_i('{\"" . $servico_gerar .
										        "\" \"" . $ordoid_nova .
										        "\" \"\" \"" . $coneqcoid .
										        "\" \"" . $obs . "\" \"P\" \"NULL\" }');";
										        
										        if(!pg_query($conn,$sqlOrdemServicoItem )){
										            $this->rollback ();
										            
										            throw new Exception("Erro para efetuar a inserção na tabela ordem de servico item");
										            
										        }
										        
										        
										        /*Orsordoid = id da os criado pela funï¿½ï¿½o ordem_servico_i;
										         Orssituacao = o texto ï¿½Serviï¿½o adicionado:ï¿½ contatenado com o valor informado para o parï¿½metro ï¿½obsï¿½ da funï¿½ï¿½o ordem_servico_item_i;
										         
										         Orsusuoid = id do usuario logado;
										         orsstatus  = null;*/
										        
										        
										        $situacao = "Serviço adicionado: ".$obs;
										        
										        $sqlOrdemSituacao = "INSERT INTO
												ordem_situacao
													(orsordoid,
													 orsusuoid,
													 orssituacao,
													 orsstatus)
											 VALUES(
													$ordoid_nova,
													$cdUsuario,
													'$situacao',
													NULL
											     )";
													
													if(!pg_query($conn,$sqlOrdemServicoItem )){
													    $this->rollback ();
													    
													    throw new Exception("Erro para efetuar a inserção na tabela ordem_situacao equipamento ");
													}
													
													$sqlContratoServico = "select	otioid,
									    otidescricao,
									    coneqcoid,
									    otidescricao
								FROM contrato
									INNER JOIN contrato_servico ON consconoid = connumero
									INNER JOIN obrigacao_financeira ON consobroid=obroid
									LEFT JOIN obrigacao_financeira_tecnica ON oftcobroid = obroid
									LEFT JOIN atuador_loc_inst ON consalioid = alioid
									INNER JOIN os_tipo_item ON otiobroid = obroid
										AND otiostoid = 2
								    where connumero= $key[contrato]
									and consiexclusao is null
									and oftcexclusao is null";
													
													if(!$resultContrato = pg_query($conn,$sqlContratoServico)) {
													    $this->rollback ();
													    throw new Exception("Erro para efetuar a consulta  na tabela contrato em ordem de servico");
													}
													
													
													
													for ($i = 0; $i < pg_num_rows($resultadoContrato); $i++) {
													    $obs = '';
													    $motivo = pg_fetch_result($rs, $i, 'otioid');
													    $obs = pg_fetch_result($rs, $i, 'obs');
													    
													    $obser = "Reinstalação ".$obs;
													    
													    
													    $sqlAcessorios = "SELECT ordem_servico_item_i('{\"" . $motivo . "\" \"" . $ordoid_nova . "\" \"\" \"" . $coneqcoid . "\" \"" . $obser . "\" \"P\" \"NULL\" }');";
													    
													    if(!$resultContrato = pg_query($conn,$sqlContratoServico)) {
													        $this->rollback ();
													        
													        throw new Exception("Erro para efetuar a inserção  na funcão ordem_servico_item_i acessorios");
													        
													    }
													    
													    
													    $situacaoAcessorios = "Serviço adicionado: ".$obser;
													    
													    $sqlOrdemSituacao = "INSERT INTO
														ordem_situacao
															(orsordoid,
															 orsusuoid,
															 orssituacao,
															 orsstatus)
													 VALUES(
															$ordoid_nova,
															$cdUsuario,
															'$situacaoAcessorios',
															NULL
													     )";
															
															if(!pg_query($conn,$sqlOrdemServicoItem )){
															    $this->rollback ();
															    
															    throw new Exception("Erro para efetuar a inserção na tabela ordem_situacao acessorios ");
															    
															}
													}
													
										    }
										    
										    
										}
										
										$this->insertTabelaTelefoneContatoContatoEmergencia($key[contrato],$connumero,$cdUsuario);
										$this->insertTabelaTelefoneContatoPessoaAutorizadaContrato($key[contrato],$connumero,$cdUsuario);
										$this->insertTabelaClienteContatoContatoInstalacao($key[contrato],$connumero,$cdUsuario,$clienteID);
										
										$count = $count + 1;
										
										
            }
            
            
            
            
            //retorno o valor total dos contratos para baixar as notas
            $valorContratoTransferencia = $this->retornaValorTotalContratosTransferencia($idProposta);
            
            //verifica se retornou um array com os valores
            if(isset($valorContratoTransferencia) &&  count($valorContratoTransferencia) > 0 && !empty($valorContratoTransferencia) || $valorContratoTransferencia != null){
                
                
                foreach ($valorContratoTransferencia as $keyContrato) {
                    $nflvl_total = str_replace(".", "", $keyContrato['nflvl_total']);
                    $nflvl_contrato = str_replace(".", "", $keyContrato['valortotal']);
                    
                    if($nflvl_total == $nflvl_contrato) {
                        
                        $this->updateTituloNotasAtrasadas($keyContrato['titoid'],$cdUsuario,'',true);
                    }else{
                        
                        $valor = ($keyContrato['nflvl_total'] - $keyContrato['valortotal']);
                        
                        $this->updateTituloNotasAtrasadas($keyContrato['titoid'],$cdUsuario,$valor,false);
                        
                        
                        
                        //se a data de referencia for vazia pega o primeiro dia domês
                        
                        if($keyContrato['titdt_referencia'] == '' || $keyContrato['titdt_referencia'] == null || empty($keyContrato['titdt_referencia'])) {
                            $titdt_referencia = date("Y-m");
                            $dataAtualReferencia = $titdt_referencia."-01";
                        }else{
                            $dataAtualReferencia = $keyContrato['titdt_referencia'];
                        }
                        
                        
                        $this->insertTitulosNotasAtrasadas($keyContrato['titnfloid'],$dataAtualReferencia,$keyContrato['titdt_vencimento'],$keyContrato['titno_parcela'],$keyContrato['titclioid'],$cdUsuario,$keyContrato['valortotal']);
                    }
                }
                
                
            }
            
            //verefica se existe uma nota para e insere na tabelas titulos , e atualiza o valor na tabela nota fiscal
            if($numeroNotaFiscal != '' || !empty($numeroNotaFiscal) || $numeroNotaFiscal != null) {
                $valorTotalItemNotaFiscal = $this->retornaItensNotaFiscal($numeroNotaFiscal['notaNumero']);
                $this->insertTitulosNotaFiscalAtrasadas($numeroNotaFiscal['idNota'],$dataReferencia,$dataVencimento,$valorTotalItemNotaFiscal['valor_total_item'],$clienteID,$cdUsuario);
                $this->updateNotaFiscalValor($numeroNotaFiscal['idNota'],$valorTotalItemNotaFiscal['valor_total_item']);
                
            }
            
            
            
            //Para cada contrato verificar os títulos que ainda não venceram e tambem não estão pagas
            
            $titulosAbertosBaixa = $this->retornaTitulosAbertos($idProposta);
            
            //verifica se retornou algum
            if(isset($titulosAbertosBaixa) &&  count($titulosAbertosBaixa) > 0 && !empty($titulosAbertosBaixa) || $titulosAbertosBaixa != null){
                
                foreach ($titulosAbertosBaixa as $keyTitulosAbertos) {
                    $nflvl_total = str_replace(".", "", $keyTitulosAbertos['nflvl_total']);
                    $nflvl_contrato = str_replace(".", "", $keyTitulosAbertos['valor_contrato']);
                    
                    if($nflvl_total == $nflvl_contrato) {
                        
                        $this->updateTituloNotasAtrasadas($keyTitulosAbertos['titoid'],$cdUsuario,'',true);
                    }else{
                        
                        $valor = ($keyTitulosAbertos['nflvl_total'] - $keyTitulosAbertos['valor_contrato']);
                        $this->updateTituloNotasAtrasadas($keyTitulosAbertos['titoid'],$cdUsuario,$valor,false);
                        
                        
                        //se a data de referencia for vazia pega o primeiro dia domês
                        
                        if($keyTitulosAbertos['titdt_referencia'] == '' || $keyTitulosAbertos['titdt_referencia'] == null || empty($keyTitulosAbertos['titdt_referencia'])) {
                            $titdt_referencia = date("Y-m");
                            $dataAtualReferencia = $titdt_referencia."-01";
                        }else{
                            $dataAtualReferencia = $keyTitulosAbertos['titdt_referencia'];
                        }
                        
                        
                        
                        $this->insertTitulosNotasAtrasadas($keyTitulosAbertos['titnfloid'],$dataAtualReferencia,$keyTitulosAbertos['titdt_vencimento'],$keyTitulosAbertos['titno_parcela'],$keyTitulosAbertos['titclioid'],$cdUsuario,$keyTitulosAbertos['nflvl_total']);
                    }
                }
                
            }
            
            $this->concluiSolicitacaoPropostaTransferencia($idProposta,$cdUsuario);
            
            
            $this->commit ();
            
        }catch(Exception $e){
            $this->rollback ();
            return 0;
            throw new Exception ( "Problemas para efetuar insert ou update na tabela contrato" );
        }
        
        return $connumero;
    }
    
    
    //insere os contatos na tabela telefone contato , recebendo os valores da tabela proposta_transferencia_contato_emergencia
    public function insertTabelaTelefoneContatoContatoEmergencia($contratoAntigo,$contrato,$usuario){
        
        try{
            
            $resultContatoEmergencia = $this->listaContatoEmergenciaContrato($contratoAntigo);
            
            foreach ($resultContatoEmergencia as $key) {
                
                if($key['ptcefone_residencial'] != '' && $key['ptcefone_residencial'] !== null && !empty($key['ptcefone_residencial'])){
                    $dddResidencial = substr($key['ptcefone_residencial'], 0, 2);
                    $TelResidencial = substr($key['ptcefone_residencial'], 3);
                }
                if($key['ptcefone_comercial'] != '' && $key['ptcefone_comercial'] !== null && !empty($key['ptcefone_comercial'])){
                    $dddComercial = substr($key['ptcefone_comercial'], 0, 2);
                    $TelComercial = substr($key['ptcefone_comercial'], 3);
                }
                
                if($key['ptcefone_celular'] != '' && $key['ptcefone_celular'] !== null && !empty($key['ptcefone_celular'])){
                    $dddCelular = substr($key['ptcefone_celular'], 0, 2);
                    $TelCelular = substr($key['ptcefone_celular'], 3);
                }
                
                $sql = "INSERT INTO telefone_contato(
  															 tctdt_cadastro,
  															 tctconnumero ,
  															 tctno_ddd_res,
  															 tctno_fone_res,
															 tctno_ddd_com,
															 tctno_fone_com,
															 tctno_ddd_cel,
															 tctno_fone_cel,
															 tctorigem,
  															 tctcontato,
															 tctusuoid,
															 tctid_nextel
															)
															VALUES (
																'now()',
																$contrato,
																'$dddResidencial',
																'$TelResidencial',
																'$dddComercial',
																'$TelComercial',
																'$dddCelular',
																'$TelCelular',
																'E',
																'$key[ptcenome]',
																 $usuario,
																 '$key[ptceidnextel]'
															)";
																 
																 
																 if (! $result = pg_query ( $this->conn, $sql )) {
																     $this->rollback ();
																     
																     throw new Exception ( "Erro de SQL ao efetuar a inserção na tabela telefone contato" );
																     
																 }
            }
            
        }catch(Exception $e){
            $this->rollback ();
            
            throw new Exception ( "Problemas para efetuar a inserção no telefone contato " );
            
        }
    }
    
    //insere os contatos na tabela telefone contato , recebendo os valores da tabela proposta_transferencia_pessoas_autorizadas
    public function insertTabelaTelefoneContatoPessoaAutorizadaContrato($contratoAntigo,$contrato,$usuario){
        
        try{
            $resultPessoasAutorizadas = $this->listaPessoaAutorizaDaContrato($contratoAntigo);
            
            foreach ($resultPessoasAutorizadas as $key) {
                
                if($key['ptpafone_residencial'] != '' && $key['ptpafone_residencial'] !== null && !empty($key['ptpafone_residencial'])){
                    $dddResidencial = substr($key['ptpafone_residencial'], 0, 2);
                    $TelResidencial = substr($key['ptpafone_residencial'], 3);
                }
                if($key['ptpafone_comercial'] != '' && $key['ptpafone_comercial'] !== null && !empty($key['ptpafone_comercial'])){
                    $dddComercial = substr($key['ptpafone_comercial'], 0, 2);
                    $TelComercial = substr($key['ptpafone_comercial'], 3);
                }
                
                if($key['ptpafone_celular'] != '' && $key['ptpafone_celular'] !== null && !empty($key['ptpafone_celular'])){
                    $dddCelular = substr($key['ptpafone_celular'], 0, 2);
                    $TelCelular = substr($key['ptpafone_celular'], 3);
                }
                
                $sql = "INSERT INTO telefone_contato(
					tctdt_cadastro,
					tctconnumero ,
					tctno_ddd_res,
					tctno_fone_res,
					tctno_ddd_com,
					tctno_fone_com,
					tctno_ddd_cel,
					tctno_fone_cel,
					tctrg ,
 					tctcpf,
					tctorigem,
					tctcontato,
					tctusuoid,
					tctid_nextel
					)
					VALUES (
					'now()',
					$contrato,
					'$dddResidencial',
					'$TelResidencial',
					'$dddComercial',
					'$TelComercial',
					'$dddCelular',
					'$TelCelular',
					'$key[ptparg]',
					'$key[ptpacpf]',
					'A',
					'$key[ptpanome]',
					$usuario,
					'$key[ptpaidnextel]'
					)";
					
					if (! $result = pg_query ( $this->conn, $sql )) {
					    $this->rollback ();
					    
					    throw new Exception ( "Erro de SQL ao efetuar a inserção na tabela telefone contato no metodo" );
					    
					}
            }
            
        }catch(Exception $e){
            $this->rollback ();
            
            throw new Exception ( "Problemas para efetuar a inserção no telefone contato " );
            
        }
    }
    
    
    //insere os contatos na tabela telefone contato , recebendo os valores da tabela proposta_transferencia_pessoas_autorizadas
    public function insertTabelaClienteContatoContatoInstalacao($contratoAntigo,$contrato,$usuario,$idCliente){
        
        try{
            $resultContatoInstalacao = $this->listaContatoInstalacaoContrato($contratoAntigo);
            
            foreach ($resultContatoInstalacao as $key) {
                
                $clicfone_array = "{";
                
                if($key['ptcifone_residencial'] != '' && $key['ptcifone_residencial'] !== null && !empty($key['ptcifone_residencial'])){
                    
                    $clicfone_array .= $key['ptcifone_residencial'];
                }
                
                if($key['ptcifone_comercial'] != '' && $key['ptcifone_comercial'] !== null && !empty($key['ptcifone_comercial'])){
                    
                    if($key['ptcifone_residencial'] != '' && $key['ptcifone_residencial'] !== null && !empty($key['ptcifone_residencial'])){
                        $clicfone_array.=",";
                    }
                    
                    $clicfone_array .= $key['ptcifone_comercial'];
                }
                
                if($key['ptcifone_celular'] != '' && $key['ptcifone_celular'] !== null && !empty($key['ptcifone_celular'])){
                    
                    if($key['ptcifone_residencial'] != '' && $key['ptcifone_residencial'] !== null && !empty($key['ptcifone_residencial'])){
                        $clicfone_array.=",";
                    }else if($key['ptcifone_comercial'] != '' && $key['ptcifone_comercial'] !== null && !empty($key['ptcifone_comercial'])){
                        $clicfone_array.=",";
                    }
                    
                    $clicfone_array .= $key['ptcifone_celular'];
                }
                
                $clicfone_array .= "}";
                
                
                
                $sql = "INSERT INTO cliente_contato(
														  clicnome,
														  clicfone,
														  clicdt_cadastro,
														  clicusuoid ,
														  clicclioid,
														  clicconnumero,
														  clicfone_array,
														  clicid_nextel
														)
														VALUES (
														'$key[ptcinome]',
														'$dddResidencial',
														'now()',
														 $usuario,
														 $idCliente,
														 $contrato,
														 '$clicfone_array',
														'$key[ptpaidnextel]'
														)";
														 
														 if (! $result = pg_query ( $this->conn, $sql )) {
														     $this->rollback ();
														     throw new Exception ( "Erro de SQL ao efetuar a inserção na tabela cliente_contato" );
														     
														 }
            }
            
        }catch(Exception $e){
            $this->rollback ();
            
            throw new Exception ( "Problemas para efetuar a inserção no cliente_contato " );
            
        }
    }
    
    
    //deverar retornar os campos conequoid,conveioid passando o contrato
    public function listContratoNumContrato($contrato){
        
        try{
            $sql = "SELECT
						conequoid ,
						conveioid
					FROM
						contrato
					WHERE
						connumero = $contrato";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta dos contrato passando o numero do contrato" );
            }
            
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar a consulta contrato passando o numero do contrato" );
        }
        
        return pg_fetch_assoc($result);
        
    }
    
    //deverar retornar todos os contratos relacionado ao id da proposta
    public function listContratoIdProposta($id){
        
        try{
            $sql = "SELECT
						*
					FROM
						proposta_transferencia_contrato
					WHERE
						pttcoptraoid  = $id";
            
            if (! $result = pg_query ( $this->conn, $sql )) {
                throw new Exception ( "Erro ao efetuar a consulta dos contrato proposta" );
            }
            
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar a consulta contrato proposta" );
        }
        
        return pg_fetch_all($result);
    }
    
    //inseri um registro na tabela contrato_pagamento
    public function saveupdateContratoPagamento($data){
        
        $status = "P";
        $cpagcorretor_recebe_comissao = "F";
        $cpaglocal_inst  = "R";
        try{
            
            
            $sqlSelect = "select coalesce (cofvl_obrigacao , 0 ) AS valor
				from contrato_obrigacao_financeira
 				where cofconoid =  $data[contrato_antigo]
 				and (cofdt_termino  is null or cofdt_termino < now() )
 				and cofobroid  = 1
 				order by cofdt_termino desc
 				limit  1";
            
            if (! $resultObrigacao = pg_query ( $this->conn, $sqlSelect )) {
                pg_query($this->conn, "ROLLBACK");
                
                throw new Exception ( "Erro ao efetuar a consulta obrigação financeira" );
                
            }
            
            $row = pg_fetch_array ( $resultObrigacao, 0 );
            
            $sql = "INSERT INTO contrato_pagamento(
			cpagconoid ,
			cpagforcoid,
			cpagmonitoramento,
			cpagstatus,
			cpagusuoid,
			cpagcorretor_recebe_comissao,
			cpagcpvoid,
			cpagobroid_servico,
			cpagvl_servico,
			cpaglocal_inst
			)
			VALUES (
			$data[Cpagconoid],
			$data[Cpagforcoid],
			$row[valor],
			'$status',
			$data[cpagusuoid],
			'$cpagcorretor_recebe_comissao',
			$data[cpagcpvoid],
			$data[cpagobroid_servico],
			$data[cpagvl_servico],
			'$cpaglocal_inst'
			) RETURNING cpagoid";
			
			
			if (! $result = pg_query ( $this->conn, $sql )) {
			    pg_query($this->conn, "ROLLBACK");
			    throw new Exception ( "Erro ao efetuar a cadastro na tabela contrato_pagamento" );
			}
			
			
        }catch(Exception $e){
            pg_query($this->conn, "ROLLBACK");
            throw new Exception ( "Problemas para efetuar insert ou update na tabela contrato" );
        }
        
        
    }
    
    //retorna os valores de monitoramento somente dos contratos serie A
    public function retornaValorMonitoramentoSerieA($contrato){
        
        try{
            
            $sql = "SELECT ROUND( CASE
							       WHEN COALESCE(cpag.cpagvl_servico, 0::numeric) > 0::numeric THEN
							        -- Valor com desconto (Se completou vigencia)
							        CASE
							        WHEN (now() > (con.condt_ini_vigencia + ('1 MONTH'::INTERVAL * con.conprazo_contrato))::date) THEN -- RN39
							         COALESCE(cpag.cpagvl_servico, 0) - (COALESCE(cpag.cpagvl_servico,0) * COALESCE((SELECT cpag.cpagpercentual_desconto_locacao FROM contrato_pagamento WHERE cpagconoid=connumero LIMIT 1) / 100, 0))
							        ELSE
							         COALESCE(cpag.cpagvl_servico, 0)
							        END
							       ELSE
							        -- Valor com desconto (Se completou vigencia)
							        CASE
							        WHEN (now() > (con.condt_ini_vigencia + ('1 MONTH'::INTERVAL * con.conprazo_contrato))::date) THEN -- RN39
							         COALESCE(cpag.cpaghabilitacao, 0) - (COALESCE(cpag.cpaghabilitacao,0) * COALESCE((SELECT cpag.cpagpercentual_desconto_locacao FROM contrato_pagamento WHERE cpagconoid=connumero LIMIT 1) / 100, 0))
							        ELSE
							         COALESCE(cpag.cpaghabilitacao, 0)
							        END
							       END, 2)  AS valor
					FROM contrato_pagamento cpag
					JOIN contrato con ON cpag.cpagconoid = con.connumero
					WHERE con.connumero = $contrato";
            
            
            if(!$result = pg_query($this->conn,$sql)){
                throw new Exception("Erro para efetuar a consulta valor monitoramento serie A");
            }
            
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar a consulta monitoramento serie A" );
        }
        
        return pg_fetch_assoc($result);
    }
    
    
    //obter a quantidade de parcelas faltantes do contrato que esta¡ sendo transferido
    
    public function retornaQtdaParcelasContrato($contrato){
        
        try{
            
            $sql = "SELECT cpvparcela
					FROM   contrato_pagamento
					JOIN cond_pgto_venda
					         ON cpagcpvoid = cpvoid
					WHERE  cpagconoid  = $contrato";
            
            if(!$result = pg_query($this->conn,$sql)){
                throw new Exception("Erro para efetuar a consulta qtda parcelas");
            }
            
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar a consulta qtda parcelas" );
        }
        
        return pg_fetch_assoc($result);
    }
    
    
    //consulta obrigação financeira esta¡ sendo faturada a locação do contrato
    
    public function retornaObrigacaoFinanceiraFaturada($contrato){
        try{
            
            $sql = "SELECT
						 obroid
					FROM
						 contrato
					JOIN equipamento_classe ON eqcoid = coneqcoid
					JOIN obrigacao_financeira ON obroid = CASE
					WHEN (
						 (
						  SELECT
						   COUNT (msuboid) AS COUNT
						  FROM
						   motivo_substituicao
						  JOIN obrigacao_financeira ON msubeqcoid_orig = obreqcoid_orig
						  AND msubeqcoid = obreqcoid
						  WHERE
						   msubeqcoid IS NOT NULL
						  AND msuboid = conmsuboid
						  LIMIT 1
						 )
						) > 0 THEN
						 (
						  SELECT
						   obroid
						  FROM
						   motivo_substituicao
						  JOIN obrigacao_financeira ON msubeqcoid_orig = obreqcoid_orig
						  AND msubeqcoid = obreqcoid
						  WHERE
						   msubeqcoid IS NOT NULL
						  AND msuboid = conmsuboid
						  LIMIT 1
						 )
						ELSE
						 CASE
						WHEN (
						 (
						  SELECT
						   COUNT (msuboid) AS COUNT
						  FROM
						   motivo_substituicao
						  WHERE
						   msuboid = conmsuboid
						  AND msubeqcoid IS NULL
						  AND msubtrans_titularidade IS TRUE
						  LIMIT 1
						 )
						) > 0 THEN
						 25
						ELSE
						 eqcobroid
						END
						END
						WHERE
						 connumero =  $contrato";
            
            if(!$result = pg_query($this->conn,$sql)){
                
                throw new Exception("Erro para efetuar a consulta obrigação financeira");
                
            }
            
        }catch(Exception $e){
            
            
            throw new Exception ( "Problemas para efetuar a consulta obrigação financeira" );
            
        }
        
        return pg_fetch_assoc($result);
    }
    
    
    //verifica quantas parcelas foram faturadas passando a  obrigação financeira e contrato, que Ã© a obrigação financeira da locação
    
    public function retornaParcelasFaturadasObrigacaoContratoID($conid,$idobr){
        try{
            
            $sql = "SELECT
							COUNT (nfloid)
						FROM
							nota_fiscal_item nfi4,
							nota_fiscal nfl4,
							titulo tit
						WHERE
						TRUE
						AND nfl4.nflno_numero = nfi4.nfino_numero
						AND nfl4.nflserie = nfi4.nfiserie
						AND nfi4.nficonoid = $conid
						AND nfi4.nfiobroid = $idobr
						AND nfl4.nfldt_cancelamento IS NULL
						AND tit.titnfloid = nfl4.nfloid
						AND (
							tit.titdt_pagamento IS NOT NULL
						OR (
							tit.titdt_vencimento <= now()
						AND tit.titdt_pagamento IS NULL
							)
							)
						AND titformacobranca <> 62
				";
            
            
            if(!$result = pg_query($this->conn,$sql)){
                throw new Exception("Erro para efetuar a consulta das parcelas faturadas");
            }
            
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar as consulta as parcelas faturadas" );
        }
        
        return pg_fetch_assoc($result);
    }
    
    
    //verifica em qual condição de pagamento o novo contrato se encaixarÃ¡
    
    public function retornaCondicoesPagamentoNovoContrato($qtda){
        
        
        try{
            if($qtda <= 12) {
                $filtro .=  " AND cpvparcela =  $qtda";
            }else{
                $filtro .=  " AND cpvparcela <=  $qtda";
                $filtro .=	" AND cpvoid IN (13, 12, 36, 14, 34, 15, 32, 35)";
            }
            
            $sql = "SELECT
					 *
					FROM
					 cond_pgto_venda
					WHERE 1=1
					 $filtro
					ORDER BY
					 cpvparcela DESC
					LIMIT 1";
					 
					 
					 if(!$result = pg_query($this->conn,$sql)){
					     throw new Exception("Erro para efetuar a consulta de condicoes de pagamento");
					 }
					 
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar as consulta de condicoes de pagamento" );
        }
        
        return pg_fetch_assoc($result);
    }
    
    
    //Obtem o valor pago de locação atualmente
    
    public function retornaValorPagoLocacao($contrato){
        
        try{
            
            $sql = " select ROUND( CASE
					       WHEN COALESCE(cpag.cpagvl_servico, 0::numeric) > 0::numeric THEN
					       
					       
					       
					        -- Valor com desconto (Se completou vigencia)
					        
					        CASE
					        WHEN (now()::date > (con.condt_ini_vigencia + ('1 MONTH'::INTERVAL * con.conprazo_contrato))::date) THEN -- RN39
					         COALESCE(cpag.cpagvl_servico, 0) - (COALESCE(cpag.cpagvl_servico,0) * COALESCE((SELECT cpag.cpagpercentual_desconto_locacao FROM contrato_pagamento WHERE cpagconoid=connumero LIMIT 1) / 100, 0))
					        ELSE
					         COALESCE(cpag.cpagvl_servico, 0)
					        END
					       ELSE
					       
					        -- Valor com desconto (Se completou vigencia)
					        
					        CASE
					        WHEN (now()::date > (con.condt_ini_vigencia + ('1 MONTH'::INTERVAL * con.conprazo_contrato))::date) THEN -- RN39
					         COALESCE(cpag.cpaghabilitacao, 0) - (COALESCE(cpag.cpaghabilitacao,0) * COALESCE((SELECT cpag.cpagpercentual_desconto_locacao FROM contrato_pagamento WHERE cpagconoid=connumero LIMIT 1) / 100, 0))
					        ELSE
					         COALESCE(cpag.cpaghabilitacao, 0)
					        END
					       END, 2) AS valor
					       
					from contrato con
					inner join contrato_pagamento cpag on cpag.cpagconoid = con.connumero
					where con.connumero = $contrato";
            
            if(!$result = pg_query($this->conn,$sql)){
                
                throw new Exception("Erro para efetuar a consulta de valor de locação");
                
            }
            
        }catch(Exception $e){
            
            throw new Exception ( "Problemas para efetuar as consulta de valor de locação" );
            
        }
        
        return pg_fetch_assoc($result);
        
    }
    
    
    //código da Obrigacao Financeira do Servico
    
    public function retornaObrigacaoFinanceiroContrato($contrato){
        try{
            
            $sql = "SELECT
						eqcobroid
			         FROM
						contrato
					INNER JOIN equipamento_classe ON coneqcoid = eqcoid
					WHERE
						connumero =  $contrato";
            
            if(!$result = pg_query($this->conn,$sql)){
                
                throw new Exception("Erro para efetuar a consulta da obrigação financeira do servico");
                
            }
            
        }catch(Exception $e){
            
            throw new Exception ( "Problemas para efetuar as consulta da obrigação financeira do servico" );
            
        }
        
        return pg_fetch_assoc($result);
    }
    
    
    //filtrar os registros do contrato que esta¡ sendo transferido.
    
    public function retornaContratosTransferidosObrigacoesAcessorios($contrato){
        
        try{
            
            $sql = "SELECT
						*
					FROM
						contrato_servico
					WHERE
						consconoid  = $contrato";
            
            if(!$result = pg_query($this->conn,$sql)){
                throw new Exception("Erro para efetuar a consulta dos registro contratos transferidos");
            }
            
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar as consulta dos registro contratos transferidos" );
        }
        
        return pg_fetch_all($result);
    }
    
    // buscar sua referencia do campo tadoid para atualizar o tadclioid
    public function retornaReferenciaContratoTransferido($tadoid){
        
        try{
            
            $sql = "SELECT
						*
					FROM
						termo_aditivo
					WHERE
						tadoid = $tadoid";
            
            if(!$result = pg_query($this->conn,$sql)){
                throw new Exception("Erro para efetuar a consulta a referencia termo_aditivo");
            }
            
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar consulta a referencia termo_aditivo" );
        }
        
        return pg_fetch_assoc($result);
    }
    
    
    //Obter a condição de pagamento do contrato que esta sendo transferido
    
    public function retornaCondicaoPagamentoAntigoContratoTransferido($tadoid){
        
        try{
            
            $sql = "SELECT
						cpvparcela
					FROM
						 termo_aditivo
					LEFT JOIN cond_pgto_venda ON tadcpvoid = cpvoid
					WHERE
						 tadoid = $tadoid";
            
            if(!$result = pg_query($this->conn,$sql)){
                
                throw new Exception("Erro para efetuar a consulta condição de pagamento do contrato");
                
            }
            
        }catch(Exception $e){
            
            throw new Exception ( "Problemas para efetuar as consulta condição de pagamento do contrato" );
            
        }
        
        return pg_fetch_assoc($result);
    }
    
    
    //obter a quantidade de parcelas restantes para a locação do acessÃ³rio
    
    public function retornaParcelasFaltantesLocacaoAcessorio($contrato,$consobroid){
        
        try{
            
            $sql = "SELECT
							count(distinct nflno_numero)
						FROM nota_fiscal_item nfi
						INNER JOIN nota_fiscal nfl ON (nfl.nflno_numero = nfi.nfino_numero AND     nfl.nflserie = nfi.nfiserie)
						INNER JOIN titulo tit on  tit.titnfloid = nfl.nfloid
						WHERE nfi.nficonoid =  $contrato
						AND nfi.nfiobroid = $consobroid
						AND nfl.nfldt_cancelamento IS NULL
						AND (
							tit.titdt_pagamento IS NOT NULL
						OR (
							tit.titdt_vencimento <= now()
						AND tit.titdt_pagamento IS NULL
						)
						)";
            
            if(!$result = pg_query($this->conn,$sql)){
                throw new Exception("Erro para efetuar a consulta dos registro contratos transferidos");
            }
            
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar as consulta dos registro contratos transferidos" );
        }
        
        return pg_fetch_assoc($result);
    }
    
    //funcao que altera os valores da tabela  contrato_servico
    public function updateContratoServico($data,$contrato){
        try{
            foreach ($data as $key) {
                
                $updateContratoServ = "UPDATE contrato_servico
									  SET consconoid =$contrato, consvalor =$key[novovalor]
									  WHERE
										consoid = $key[consoid]";
                
                if(!$result = pg_query($this->conn,$updateContratoServ)){
                    $this->rollback ();
                    throw new Exception("Erro para efetuar o update da tabela contrato servico");
                }
                
                if(!empty($key['constadoid']) || $key['constadoid'] != '') {
                    
                    $this->updateTermoAditivo($key['idCliente'],$key['referencia']);
                }
                
            }
        }catch(Exception $e){
            $this->rollback ();
            throw new Exception("Erro para efetuar o update das tabelas contrato servico");
        }
        
    }
    
    //atualiza o usuario do termo aditivo
    public function updateTermoAditivo($idClienteNovo,$idTermoAditivo){
        
        try{
            $updateContratoServico = "UPDATE termo_aditivo
			SET tadclioid = $idClienteNovo
			WHERE
			tadoid = $idTermoAditivo";
            
            if(!$result = pg_query($this->conn,$updateContratoServico)){
                $this->rollback ();
                throw new Exception("Erro para efetuar o update da tabela termo aditivo");
            }
            
        }catch(Exception $e){
            $this->rollback ();
            throw new Exception("Erro para efetuar o update das tabelas termo aditivo");
        }
        
    }
    
    
    //verifica se o contrato que esta¡ sendo transferido, possua­ notas a vencer que não estejam pagas
    
    public function retornaNotasNaoVencidas($contrato){
        
        try{
            $sql="SELECT nflvl_total,
			         nflno_numero,
			         nfiserie,
			         sum(nfivl_item) AS valor_contrato,
		             titoid,
					 titnfloid,
					 titdt_referencia,
					 titdt_vencimento,
					 titno_parcela,
					 titclioid
		       FROM   nota_fiscal
		       INNER JOIN clientes
		               ON clioid = nflclioid
		       INNER JOIN contrato
		               ON conclioid = clioid
		       INNER JOIN nota_fiscal_item
		               ON nfinfloid = nfloid
		           AND connumero = nficonoid
		       LEFT JOIN titulo
		              ON titnfloid = nfloid
		       LEFT JOIN forma_cobranca
		              ON titformacobranca = forcoid
		       WHERE  1 = 1
		       AND connumero IN ($contrato)
		       AND titdt_vencimento >=  Now()
		       AND titdt_pagamento IS NULL
		       AND nfldt_cancelamento is null
		       GROUP BY connumero,
		         nflvl_total,
		         nflno_numero,
		         nfiserie,
		         titoid";
            
            
            if(!$result = pg_query($this->conn,$sql)){
                throw new Exception("Erro para efetuar a consulta de notas a vencer");
            }
            
        }catch(Exception $e){
            $this->rollback ();
            throw new Exception("Erro para efetuar a consulta de notas a vencer");
        }
        
        return pg_fetch_all($result);
        
        
    }
    
    //tabela titulo atualizar
    public function updateTitulo($dados){
        
        
        try{
            
            foreach($dados as $key) {
                if($key['baixatotalnota']) {
                    $filtro = "SET titdt_pagamento = Now(),titdt_credito = Now(),titformacobranca = 60,
						   titusuoid_alteracao=$key[titusuoid_alteracao],
				           titdt_alteracao =Now()";
                }else{
                    $filtro = "SET titusuoid_alteracao  = $key[titusuoid_alteracao],titdt_alteracao = Now(),
								titvl_titulo  = $key[titvl_titulo]";
                }
                
                $updateTitulo = "UPDATE titulo
										$filtro
									WHERE
										titoid = $key[titoid]";
										
										
										if(!$result = pg_query($this->conn,$updateTitulo)){
										    $this->rollback ();
										    throw new Exception("Erro para efetuar o update da tabela termo aditivo");
										}
            }
            
        }catch(Exception $e){
            $this->rollback ();
            throw new Exception("Erro para efetuar o update das tabelas termo aditivo");
        }
        
    }
    
    //insere um novo registro na tabela titulo
    public function insertTitulos($data){
        
        
        try{
            
            foreach ($data as $row) {
                
                $sql = "INSERT INTO titulo(
						titdt_inclusao,
						titnfloid ,
						titdt_referencia,
						titdt_vencimento,
						titno_parcela,
						titvl_titulo,
						titvl_pagamento,
						titvl_desconto,
						titvl_acrescimo,
						titvl_juros,
						titvl_multa,
						titvl_tarifa_banco,
						titdt_pagamento,
						titdt_credito,
						titformacobranca,
						tittaxa_administrativa,
						titclioid,
						titvl_ir,
						titvl_piscofins,
						titimpresso,
						titvl_iss,
						titusuoid_alteracao,
						titnao_cobravel,
						titfaturamento_variavel,
						titbaixa_automatica_banco,
						tittransacao_cartao,
						titfatura_unica,
						titdt_alteracao
				)
				VALUES (
						now(),
						$row[titnfloid],
						'$row[titdt_referencia]',
						'$row[titdt_vencimento]',
						$row[titno_parcela],
						$row[titvl_titulo],
						0,
						0,
						0,
						0,
						0,
						0,
						now(),
						now(),
						60,
						0,
						$row[titclioid],
						0,
						0,
						'f',
						0,
						$row[titusuoid_alteracao],
						'f',
						'f',
						'f',
						'f',
						'f',
						now())";
						
						
						if (! $result = pg_query ( $this->conn, $sql )) {
						    pg_query($this->conn, "ROLLBACK");
						    throw new Exception ( "Erro ao efetuar a cadastro tabelas termo aditivo" );
						}
						
						
						$sqlUpdate = "update
				                nota_fiscal
				             set nflvl_total = nflvl_total - $row[titvl_titulo]
				             where nfloid = $row[titnfloid]";
						
						if (! $result = pg_query ( $this->conn, $sqlUpdate )) {
						    pg_query($this->conn, "ROLLBACK");
						    
						    throw new Exception ( "Erro ao efetuar a atualização tabelas nota fiscal" );
						    
						}
            }
            
        }catch(Exception $e){
            pg_query($this->conn, "ROLLBACK");
            throw new Exception ( "Problemas para efetuar insert tabelas termo aditivo" );
        }
    }
    
    //inserir o novo contrato na tabela contrato_obrigacao_financeira
    public function insertContratoObrigacaoFinanceira($contratoAntigo, $contratoNovo){
        
        try{
            
            
            
            $sql = "INSERT INTO contrato_obrigacao_financeira (
						cofconoid,
						cofobroid,
						cofvl_obrigacao,
						cofdt_inicio,
						cofdt_termino,
						cofno_periodo_mes,
						cofdt_ult_referencia,
						cofeqcoid
						) SELECT
						$contratoNovo,
						cofobroid,
						cofvl_obrigacao,
						'now()',
						cofdt_termino,
						cofno_periodo_mes,
						NULL,
						cofeqcoid
						FROM
						contrato_obrigacao_financeira
						WHERE
						cofconoid = $contratoAntigo";
						
						
						if (! $result = pg_query ( $this->conn, $sql )) {
						    pg_query($this->conn, "ROLLBACK");
						    throw new Exception ( "Erro ao efetuar a cadastro tabelas contrato_obrigacao_financeira" );
						}
						
						
        }catch(Exception $e){
            pg_query($this->conn, "ROLLBACK");
            throw new Exception ( "Problemas para efetuar insert tabelas contrato_obrigacao_financeira" );
        }
        
    }
    
    
    
    //verificar se possua­ notas em atraso
    
    public function retornaNotasEmAtraso($contrato){
        
        try{
            
            $sql = "SELECT
						connumero,
						nflvl_total,
						nflno_numero,
						nfiserie,
						SUM (nfivl_item) AS valor_contrato,
						To_char(
							titdt_vencimento,
							'DD/MM/YYYY'
						) AS titdt_vencimento,
						titoid,
						nfitipo
					FROM
						nota_fiscal
					INNER JOIN clientes ON clioid = nflclioid
					INNER JOIN contrato ON conclioid = clioid
					INNER JOIN nota_fiscal_item ON nfinfloid = nfloid
					AND connumero = nficonoid
					LEFT JOIN titulo ON titnfloid = nfloid
					LEFT JOIN forma_cobranca ON titformacobranca = forcoid
					WHERE
						1 = 1
					AND connumero IN ($contrato)
					AND titdt_vencimento :: DATE <= Now() :: DATE
					AND titdt_pagamento IS NULL
					AND nfldt_cancelamento is null
					GROUP BY
						connumero,
						nflvl_total,
						nflno_numero,
						nfiserie,
						titoid,
						nfitipo";
            
            if(!$result = pg_query($this->conn,$sql)){
                throw new Exception("Erro para efetuar a consulta dos registro contratos transferidos");
            }
            
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar as consulta dos registro contratos transferidos" );
        }
        
        return pg_fetch_all($result);
    }
    
    //busca  ultimo numero gerado no campo nflno_numero
    public function getMaxNotaFiscalNumero() {
        
        $sql = "SELECT MAX(nflno_numero) + 1 as nflno_numero FROM nota_fiscal WHERE nflserie = 'A'";
        
        $res = pg_query($this->conn,$sql);
        if (!$res) {
            
            throw new Exception ("Falha ao gerar numero de serie da nota fiscal");
            
        }
        
        return pg_fetch_result($res,0,0);
    }
    
    //Inserir um registro na tabela nota_fiscal:
    public function insertNotaFiscal($data){
        
        try{
            
            $nflno_numero = $this->getMaxNotaFiscalNumero();
            
            foreach ($data as $row) {
                
                
                $sql = "INSERT INTO nota_fiscal(
						nfldt_inclusao,
						nfldt_nota,
						nfldt_emissao,
						nflnatureza ,
						nfltransporte ,
						nflclioid,
						nflno_numero,
						nflserie,
						nflvl_total,
						nflvl_desconto,
						nfldt_referencia,
						nfldt_vencimento,
						nflusuoid,
						nflnota_ant,
						nflvlr_piscofins,
						nflvlr_ir,
						nflvlr_iss
						)
				VALUES (
						now(),
						now(),
						now(),
						'PRESTACAO DE SERVICOS',
						'RODOVIARIO',
						$row[nflclioid],
						$nflno_numero,
						'A',
						$row[nflvl_total],
						0,
						'$row[nfldt_referencia]',
						'$row[nfldt_vencimento]',
						$row[nflusuoid],
						$nflno_numero,
						0,
						0,
						0
						
			)RETURNING nfloid,nflno_numero";
						
						if (! $result = pg_query ( $this->conn, $sql )) {
						    pg_query($this->conn, "ROLLBACK");
						    throw new Exception ( "Erro ao efetuar a cadastro tabelas nota fiscal" );
						}
						
						$row = pg_fetch_array ( $result, 0 );
						$nfloid = $row [nfloid];
						$notaNumero = $row [nflno_numero];
						
						$nota = array(
						    'idNota' => $nfloid,
						    'notaNumero' =>$notaNumero
						);
            }
            
        }catch(Exception $e){
            pg_query($this->conn, "ROLLBACK");
            throw new Exception ( "Problemas para efetuar insert tabelas nota fiscal" );
        }
        
        return $nota;
    }
    
    
    
    //verificar se possua­ notas em atraso passando o numero de 1 contrato
    
    public function retornaNotasEmAtrasoPorContrato($contrato){
        
        try{
            
            $sql = "SELECT
			connumero,
			nflvl_total,
			nflno_numero,
			nfiserie,
			SUM (nfivl_item) AS valor_contrato,
			To_char(
			titdt_vencimento,
			'DD/MM/YYYY'
			) AS titdt_vencimento,
			titoid,
			nfitipo
			FROM
			nota_fiscal
			INNER JOIN clientes ON clioid = nflclioid
			INNER JOIN contrato ON conclioid = clioid
			INNER JOIN nota_fiscal_item ON nfinfloid = nfloid
			AND connumero = nficonoid
			LEFT JOIN titulo ON titnfloid = nfloid
			LEFT JOIN forma_cobranca ON titformacobranca = forcoid
			WHERE
			1 = 1
			AND connumero = $contrato
			AND titdt_vencimento :: DATE <= Now() :: DATE
			AND titdt_pagamento IS NULL
			AND nfldt_cancelamento is null
			GROUP BY
			connumero,
			nflvl_total,
			nflno_numero,
			nfiserie,
			titoid,
			nfitipo";
            
            if(!$result = pg_query($this->conn,$sql)){
                throw new Exception("Erro para efetuar a consulta dos registro nota fiscais em atrasos por contrato");
            }
            
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar as consulta dos registro nota fiscais em atrasos por contrato" );
        }
        
        return pg_fetch_all($result);
    }
    
    
    
    //Inserir itens na nota fiscal registro na tabela nota_fiscal:
    public function inserItemtNotaFiscal($numeroNotaFiscal,$contrato,$obr,$obrobrigacao,$valorItem,$idnotafiscal,$tipo){
        
        
        try{
            $sql = "INSERT INTO nota_fiscal_item(
								    nfino_numero,
									nfiserie,
									nficonoid,
									nfiobroid,
									nfids_item,
									nfivl_item,
									nfidt_inclusao,
									nfidesconto,
									nfinota_ant,
									nfinfloid,
									Nfitipo
									)
									VALUES (
									$numeroNotaFiscal,
									'A',
									$contrato,
									$obr,
									'$obrobrigacao',
									$valorItem,
									'now()',
									0,
									$numeroNotaFiscal,
									$idnotafiscal,
									'$tipo'
									)";
									
									
									if (! $result = pg_query ( $this->conn, $sql )) {
									    pg_query($this->conn, "ROLLBACK");
									    throw new Exception ( "Erro ao efetuar a cadastro tabelas nota fiscal" );
									}
									
									
        }catch(Exception $e){
            pg_query($this->conn, "ROLLBACK");
            throw new Exception ( "Problemas para efetuar insert tabelas nota fiscal" );
        }
        
    }
    
    
    
    //busca os valores de um determinado domÃ­nio
    
    public function retornaDominio($parm,$id){
        
        try{
            $sql = "select v.*,obf.obrobrigacao
					FROM dominio d
					JOIN registro r on r.regdomoid = d.domoid
					JOIN valor v on v.valregoid = r.regoid
					JOIN obrigacao_financeira obf ON v.valvalor::INTEGER = obroid
					WHERE d.domnome = '$parm'
					AND valoid = $id";
            
            if(!$result = pg_query($this->conn,$sql)){
                throw new Exception("Erro para efetuar a consulta dos registro dos dominios");
            }
            
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar as consulta dos registro dos dominios" );
        }
        
        return pg_fetch_assoc($result);
    }
    
    
    
    //Função para  inserir um registro na tabela titulo
    
    public function insertTitulosNotaFiscalAtrasadas($notafiscal,$dtreferencia,$dtvencimento,$nflvl_total,$cliente,$usuario){
        
        try{
            
            
            $sql = "INSERT INTO titulo(
								titdt_inclusao,
								titnfloid,
								titdt_referencia,
								titdt_vencimento,
								titno_parcela,
								titvl_titulo,
								titvl_pagamento,
								titvl_desconto,
								titvl_acrescimo,
								titvl_juros,
								titvl_multa,
								titvl_tarifa_banco,
								titrecebimento,
								titformacobranca,
								titemissao,
								titclioid,
								titvl_ir,
								titvl_piscofins,
								titimpresso,
								titvl_iss,
								titcobr_terceirizada,
								titobs_historico,
								titusuoid_alteracao,
								titnao_cobravel,
								titfaturamento_variavel,
								titbaixa_automatica_banco,
								tittransacao_cartao,
								titfatura_unica,
								titdt_alteracao
								)
					VALUES (
								'now()',
								$notafiscal,
								'$dtreferencia',
								'$dtvencimento',
								1,
								$nflvl_total,
								0,
								0,
								0,
								0,
								0,
								0,
								'CAIXA',
								74,
								'now()',
								$cliente,
								0,
								0,
								'f',
								0,
								'f',
								'.',
								$usuario,
								'f',
								'f',
								'f',
								'f',
								'f',
								'now()'
								)";
								
								//echo $sql;die();
								if (! $result = pg_query ( $this->conn, $sql )) {
								    pg_query($this->conn, "ROLLBACK");
								    throw new Exception ( "Erro ao efetuar a cadastro tabelas titulos" );
								}
								
								
        }catch(Exception $e){
            pg_query($this->conn, "ROLLBACK");
            throw new Exception ( "Problemas para efetuar insert tabelas titulos" );
        }
    }
    
    
    //fazer insert em contrato_obrigacao_financeira antigo , alterando somemente o cofconoid passando o numero do novo contrato,
    public function insertContratoObrigacaoFinanceiraMensalidades ($contratoNovo,$contratoAntigo){
        
        
        try{
            
            $sql = "INSERT INTO contrato_obrigacao_financeira ( cofconoid, cofobroid, cofvl_obrigacao, cofdt_inicio,
				       cofdt_termino, cofno_periodo_mes, cofdt_ult_referencia, cofeqcoid,
				       cofmigrar_antigos, cofalterado_termino, cofcadastro, cofvl_original,
				       cofalterado_importacao, cofvalor_agregado_monitoramento)
						SELECT
						  $contratoNovo,
						  cofobroid ,
						  cofvl_obrigacao ,
						  cofdt_inicio,
						  cofdt_termino ,
						  cofno_periodo_mes,
						  cofdt_ult_referencia ,
						  cofeqcoid,
						  cofmigrar_antigos,
						  cofalterado_termino,
						  cofcadastro,
						  cofvl_original ,
						  cofalterado_importacao,
						  cofvalor_agregado_monitoramento
						  FROM
							contrato_obrigacao_financeira
						  WHERE
							cofconoid = $contratoAntigo";
						  
						  //echo $sql;die();
						  if (! $result = pg_query ( $this->conn, $sql )) {
						      pg_query($this->conn, "ROLLBACK");
						      
						      
						      throw new Exception ( "Erro ao efetuar a cadastro na  tabelas contrato obrigação financeira" );
						      
						  }
						  
        }catch(Exception $e){
            pg_query($this->conn, "ROLLBACK");
            
            throw new Exception ( "Problemas para efetuar insert tabelas contrato obrigação financeira" );
            
        }
    }
    
    
    
    //verificar os títulos que ainda não venceram e tambÃ©m não estão pagas
    
    public function retornaTitulosAbertos($idProposta){
        try{
            
            $retornoContratos = $this->listContratoIdProposta($idProposta);
            
            foreach ($retornoContratos as $key) {
                $contratos .= $key['pttcoconoid'].",";
            }
            
            $ncontratos = strlen($contratos);
            
            $contratos = substr($contratos,0, $ncontratos-1);
            
            
            $sql = "SELECT connumero,
							 titno_parcela,
					         nflvl_total,
					         nflno_numero,
					         nfiserie,
					         sum(nfivl_item) AS valor_contrato,
					         To_char(titdt_vencimento, 'YYYY-MM-DD') AS titdt_vencimento,
					         titoid,
					         titnfloid,
					        nfitipo,
					        titclioid
					FROM   nota_fiscal
					INNER JOIN clientes
					               ON clioid = nflclioid
					INNER JOIN contrato
					               ON conclioid = clioid
					INNER JOIN nota_fiscal_item
					               ON nfinfloid = nfloid
					           AND connumero = nficonoid
					        LEFT JOIN titulo
					              ON titnfloid = nfloid
					        LEFT JOIN forma_cobranca
					              ON titformacobranca = forcoid
					        WHERE  1 = 1
					        AND connumero IN ($contratos)
					        AND titdt_vencimento::date > Now()::date
					        AND titdt_pagamento IS NULL
					        AND nfldt_cancelamento is null
					        GROUP BY connumero,
					         titno_parcela,
					         nflvl_total,
					         nflno_numero,
					         nfiserie,
					         titoid,
					         titnfloid,
					         nfitipo,
							titclioid";
            
            if(!$result = pg_query($this->conn,$sql)){
                throw new Exception("Erro para efetuar a consulta dos registro de titulos em aberto");
            }
            
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar as consulta dos registro de titulos em aberto" );
        }
        
        return pg_fetch_all($result);
    }
    
    //Retorna os Items da nota fiscal  passando o numero da nota criada
    public function retornaItensNotaFiscal($notafiscal){
        try{
            
            $sql = "SELECT
					 sum(nfivl_item) AS valor_total_item
					FROM
					 nota_fiscal_item
					WHERE nfino_numero = $notafiscal
					AND nfiserie = 'A'";
            
            if(!$result = pg_query($this->conn,$sql)){
                throw new Exception("Erro para efetuar a consulta dos registro de items da nota fiscal");
            }
            
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar as consulta dos registro de items da nota fiscal" );
        }
        
        return pg_fetch_assoc($result);
    }
    
    
    
    
    public function retornaValorTotalContratosTransferencia($idProposta){
        
        try{
            //busca todo contratos da proposta passando o idproposta
            $retornoContratos = $this->listContratoIdProposta($idProposta);
            
            foreach ($retornoContratos as $key) {
                $contratos .= $key['pttcoconoid'].",";
            }
            
            $ncontratos = strlen($contratos);
            
            $contratos = substr($contratos,0, $ncontratos-1);
            
            
            $sql = "SELECT
					SUM (valor_contrato) AS valorTotal,
					nflno_numero,
					nflvl_total,
					nfiserie,
					titoid,
					titdt_vencimento,
					titdt_referencia,
					titnfloid,
					titclioid,
					titno_parcela
				FROM
					(
						SELECT
							connumero,
							nflvl_total,
							nflno_numero,
							nfiserie,
							titnfloid,
							SUM (nfivl_item) AS valor_contrato,
							To_char(
								titdt_vencimento,
								'YYYY-MM-DD'
							) AS titdt_vencimento,
							To_char(
								titdt_referencia,
								'YYYY-MM-DD'
							) AS titdt_referencia,
							titoid,
							nfitipo,
							titclioid,
							titno_parcela
						FROM
							nota_fiscal
						INNER JOIN clientes ON clioid = nflclioid
						INNER JOIN contrato ON conclioid = clioid
						INNER JOIN nota_fiscal_item ON nfinfloid = nfloid
						AND connumero = nficonoid
						LEFT JOIN titulo ON titnfloid = nfloid
						LEFT JOIN forma_cobranca ON titformacobranca = forcoid
						WHERE
							1 = 1
						AND connumero IN ($contratos)
						AND titdt_vencimento :: DATE <= Now() :: DATE
						AND titdt_pagamento IS NULL
						AND nfldt_cancelamento is null
						GROUP BY
							connumero,
							nflvl_total,
							nflno_numero,
							nfiserie,
							titoid,
							nfitipo
					) AS q
				GROUP BY
					nflno_numero,
					nfiserie,
					nflvl_total,
					titoid,
					titdt_vencimento,
					titdt_referencia,
					titnfloid,
					titclioid,
					titno_parcela";
            
            if(!$result = pg_query($this->conn,$sql)){
                throw new Exception("Erro para efetuar a consulta dos registro de items da nota fiscal");
            }
            
        }catch(Exception $e){
            throw new Exception ( "Problemas para efetuar as consulta dos registro de items da nota fiscal" );
        }
        
        return pg_fetch_all($result);
    }
    
    
    
    //Atualiza a tabela titulos com as notas que estão vencidas
    
    public function updateTituloNotasAtrasadas($titoid,$usuario,$titvl_titulo,$baixatotalnota){
        
        try{
            
            if($baixatotalnota) {
                $filtro = "SET titdt_pagamento = Now(),titdt_credito = Now(),titformacobranca = 60,
					titusuoid_alteracao=$usuario,
					titdt_alteracao =Now()";
            }else{
                $filtro = "SET titusuoid_alteracao  = $usuario,titdt_alteracao = Now(),
					titvl_titulo  = $titvl_titulo";
            }
            
            $updateTitulo = "UPDATE titulo
					$filtro
					WHERE
					titoid = $titoid";
					
					
					if(!$result = pg_query($this->conn,$updateTitulo)){
					    $this->rollback ();
					    throw new Exception("Erro para efetuar o update da tabela titulo");
					}
					
					
        }catch(Exception $e){
            $this->rollback ();
            throw new Exception("Erro para efetuar o update das tabelas titulo");
        }
        
    }
    
    
    
    //insere um novo registro na tabela titulo com as notas que estão vencidas e com baixa parcial
    
    public function insertTitulosNotasAtrasadas($titnfloid,$titdt_referencia,$titdt_vencimento,$titno_parcela,$titclioid,$titusuoid_alteracao,$titvl_titulo){
        
        try{
            
            
            $sql = "INSERT INTO titulo(
				titdt_inclusao,
				titnfloid ,
				titdt_referencia,
				titdt_vencimento,
				titno_parcela,
				titvl_titulo,
				titvl_pagamento,
				titvl_desconto,
				titvl_acrescimo,
				titvl_juros,
				titvl_multa,
				titvl_tarifa_banco,
				titdt_pagamento,
				titdt_credito,
				titformacobranca,
				tittaxa_administrativa,
				titclioid,
				titvl_ir,
				titvl_piscofins,
				titimpresso,
				titvl_iss,
				titusuoid_alteracao,
				titnao_cobravel,
				titfaturamento_variavel,
				titbaixa_automatica_banco,
				tittransacao_cartao,
				titfatura_unica,
				titdt_alteracao
				)
				VALUES (
				now(),
				$titnfloid,
				'$titdt_referencia',
				'$titdt_vencimento',
				$titno_parcela,
				$titvl_titulo,
				0,
				0,
				0,
				0,
				0,
				0,
				now(),
				now(),
				60,
				0,
				$titclioid,
				0,
				0,
				'f',
				0,
				$titusuoid_alteracao,
				'f',
				'f',
				'f',
				'f',
				'f',
				now())";
				
				
				if (! $result = pg_query ( $this->conn, $sql )) {
				    pg_query($this->conn, "ROLLBACK");
				    throw new Exception ( "Erro ao efetuar a cadastro tabela titulo notas fiscais vencidas" );
				}
				
				$sqlUpdateNota = "update nota_fiscal set nflvl_total = nflvl_total - $titvl_titulo where nfloid = $titnfloid";
				
				if (! $result = pg_query ( $this->conn, $sqlUpdateNota )) {
				    pg_query($this->conn, "ROLLBACK");
				    
				    throw new Exception ( "Erro ao efetuar a atualização da tabela nota_fiscal" );
				    
				}
				
        }catch(Exception $e){
            pg_query($this->conn, "ROLLBACK");
            throw new Exception ( "Problemas para efetuar insert  tabela titulo notas fiscais vencidas" );
        }
    }
    
    
    //atualiza a nota fiscal com o novo valor somado todos os items
    public function updateNotaFiscalValor($nota,$valor){
        
        try{
            $updateNotaFiscal = "UPDATE nota_fiscal
			SET nflvl_total = $valor
			WHERE
			nfloid = $nota";
            
            
            if(!$result = pg_query($this->conn,$updateNotaFiscal)){
                $this->rollback ();
                throw new Exception("Erro para efetuar o update da tabela nota fiscal atualizando valores");
            }
            
        }catch(Exception $e){
            $this->rollback ();
            throw new Exception("Erro para efetuar o update das tabela nota fiscal atualizando valores");
        }
    }
    
    
    
    //verificar se o contrato que esta¡ sendo transferido não possua­ equipamento
    
    public function retornaContratoTransrenciaEquipamento($contrato){
        try{
            
            $sql = "SELECT
					 coneqcoid
					FROM
					 contrato
				   WHERE connumero = $contrato";
            
            if(!$result = pg_query($this->conn,$sql)){
                $this->rollback ();
                
                throw new Exception("Erro para efetuar a consulta dos registro  do contrato que esta¡ sendo transfereido");
                
            }
            
        }catch(Exception $e){
            $this->rollback ();
            
            throw new Exception ( "Problemas para efetuar as consulta dos registro do contrato que esta¡ sendo transfereido" );
            
        }
        
        return pg_fetch_assoc($result);
    }
    
    // 	/função que  identifica se o motivo de não possuir equipamento Ã© por conta de pedido de troca de veiculo
    
    public function  retornaPedidoTrocaVeiculo($contrato) {
        
        try{
            
            $sql = "SELECT
				 *
				FROM
					ordem_servico
				WHERE
					ordconnumero = $contrato
				AND
					ordmtioid = 6";
            
            if(!$result = pg_query($this->conn,$sql)){
                $this->rollback ();
                
                throw new Exception("Erro para efetuar a consulta dos registro  do contrato que esta¡ sendo transfereido");
                
            }
            
        }catch(Exception $e){
            $this->rollback ();
            
            throw new Exception ( "Problemas para efetuar as consulta dos registro do contrato que esta¡ sendo transfereido" );
            
        }
        
        return pg_fetch_assoc($result);
    }
    
    
    //Altera o status da proposta transferencia para concluido quando finaliza todo processo
    public function concluiSolicitacaoPropostaTransferencia($id,$idusuario){
        try{
            $sql = "UPDATE proposta_transferencia
			SET
			ptrastatus_conclusao_proposta = 'C',
			ptrausuoid_conclusao_proposta = $idusuario,
			ptradt_conclusao_proposta = now()
			WHERE ptraoid = $id";
            
            if (! $rs = pg_query ( $this->conn, $sql )) {
                $this->rollback ();
                throw new Exception ( "Erro ao efetuar ao concluir proposta de transferencia" );
            }
        }catch(Exception $e){
            $this->rollback ();
            
            throw new Exception("Falha ao atualizar solicitação de conclusao proposta de transferencia");
            
        }
        
    }
    
    //verificado se o contrato sofreu upgrade ou down grade
    public function retornaUpgradeDownGrade($numeroContrato){
        
        try{
            
            $sql = "SELECT  hfcprazo,
							TO_CHAR(hfcdt_fidelizacao, 'YYYY-MM-DD') as hfcdt_fidelizacao
					FROM  historico_fidelizacao_contrato
					WHERE  hfcoid = ( SELECT
										hfcoid
									  FROM
										historico_fidelizacao_contrato
									 WHERE
										hfcconnumero = $numeroContrato
										ORDER BY hfcdt_fidelizacao desc
										limit 1  )";
            
            if(!$result = pg_query($this->conn,$sql)){
                $this->rollback ();
                throw new Exception("Erro SQL para efetuar a consulta dos registro  da historico_fidelizacao_contrato");
            }
            
            
            
        }catch(Exception $e){
            $this->rollback ();
            throw new Exception ( "Problemas para efetuar as consulta dos registro da historico_fidelizacao_contrato" );
        }
        
        return pg_fetch_assoc($result);
        
    }
    
    
    //traz o condt_ini_vigencia, esse Ã© o inicio de vigÃªncia e conprazo_contrato Ã© o prazo .
    
    public function retornoCondicaoDataVigencia($contrato){
        
        try{
            
            $sqlContrato = "SELECT conprazo_contrato,
                           coalesce(TO_CHAR(condt_ini_vigencia, 'YYYY-MM-DD'),TO_CHAR(condt_cadastro, 'YYYY-MM-DD')) as condt_ini_vigencia
                    FROM contrato con
                    WHERE connumero = $contrato
					LIMIT 1";
            
            
            
            if(!$resultContrato = pg_query($this->conn,$sqlContrato)){
                $this->rollback ();
                throw new Exception("Erro SQL para efetuar a consulta dos registro  da historico_fidelizacao_contrato");
            }
            
            $linhas = pg_fetch_array ($resultContrato, 0 );
            $dtInicioVigencia = $linhas['condt_ini_vigencia'];
            $prazo = $linhas['conprazo_contrato'];
            
            $sqlProposta = "SELECT prp.prptermo_original,
                             prp.prptermo,
                             prp.prpmsuboid,
                             ms.msubdescricao,
                             CASE
                             WHEN ms.msubdescricao LIKE 'UP%' THEN 1
                             WHEN ms.msubdescricao LIKE 'TRA%' THEN 2
                             ELSE 3
                             END AS TIPO,
                             TO_CHAR(prp.prpdt_aprovacao_fin, 'YYYY-MM-DD') as prpdt_aprovacao_fin
                     FROM   proposta prp
                     inner join motivo_substituicao ms
                     ON prp.prpmsuboid = ms.msuboid
                     WHERE  1 = 1
                     AND prp.prptermo = $contrato
                    AND ( ms.msubdescricao LIKE 'DOWN%'
                    OR ms.msubdescricao ILIKE 'UP%'
                    OR ms.msubdescricao ILIKE 'TRA%' )
                    AND prp.prptermo_original IS NOT NULL";
            
            
            if(!$resultProposta = pg_query($this->conn,$sqlProposta)){
                $this->rollback ();
                throw new Exception("Erro SQL para efetuar a consulta dos registro  da historico_fidelizacao_contrato");
            }
            
            if(pg_num_rows($resultProposta) > 0){
                
                $rows = pg_fetch_array ( $resultProposta, 0 );
                $tipo = 1;
                
                if($tipo == 2) {
                    $dtInicioVigencia = $rows ['prpdt_aprovacao_fin'];
                }else{
                    
                    $sqlOrdemServico = "SELECT ordoid,
                           ordclioid,
                           (SELECT TO_CHAR(orsdt_situacao, 'YYYY-MM-DD') as orsdt_situacao
                    FROM   ordem_situacao
                    WHERE  orsordoid = ordoid
                    ORDER  BY orsdt_situacao DESC
                    LIMIT  1) AS orddt_ordem,
                    mtioid
                    FROM   ordem_servico
                    INNER JOIN motivo_instalacao
                    ON mtioid = ordmtioid
                    WHERE  ordconnumero = $contrato
                    AND mtioid = 2
                    AND ordstatus = 3
                    ORDER  BY orddt_ordem
                    LIMIT  1";
                    
                    if(!$resultOrdemServico = pg_query($this->conn,$sqlOrdemServico)){
                        $this->rollback ();
                        throw new Exception("Erro SQL para efetuar a consulta dos registro  da historico_fidelizacao_contrato");
                    }
                    
                    $rowsOrdem = pg_fetch_array ($resultOrdemServico, 0 );
                    $dtInicioVigencia = $rowsOrdem ['orddt_ordem'];
                    
                    
                }
                
            }
            
            
        }catch(Exception $e){
            $this->rollback ();
            throw new Exception ( "Problemas para efetuar as consulta dos registro da historico_fidelizacao_contrato" );
        }
        
        $data = date('Y-m-d', strtotime("+$prazo month", strtotime($dtInicioVigencia)));
        
        
        return $data;
    }
    
    //soma os meses faltantes para terminar a vigencia
    public function somaDatas($dataVigencia){
        
        $dataVerificarVigente = explode("-",$dataVigencia);
        $dataAtualVigente = explode("-",date('Y-m-d'));
        
        if(($dataAtualVigente[0] == $dataVerificarVigente[0]) && ($dataAtualVigente[1] >= $dataVerificarVigente[1])){
            $meses = 0;
        }else{
            $dataInicioVigencia  =strtotime($dataVigencia);
            $dataAtual =  date('Y-m-d');
            $dataInicioAtual  =strtotime($dataAtual);
            
            
            $intervalo=($dataInicioVigencia-$dataInicioAtual); //transformação do timestamp em dias
            
            $meses = (int)floor( $intervalo / (30 * 60 * 60 * 24)); // meses
        }
        
        return $meses;
    }
    
    
    //retorna  quantos meses tera¡ de vigencia
    
    public function retornoMesesVigencia($meses){
        $mesesAtual = 0;
        
        if($meses == 60 || $meses > 60) {
            $mesesAtual = 60;
        }else if($meses < 60 && $meses >= 48) {
            $mesesAtual = 48;
        }else if($meses < 48 && $meses >= 36) {
            $mesesAtual = 36;
        }else if($meses < 36 && $meses >= 28){
            $mesesAtual = 28;
        }else if($meses < 28 && $meses >= 24){
            $mesesAtual = 24;
        }else if($meses < 24 && $meses >= 18){
            $mesesAtual = 18;
        }else if($meses < 18 && $meses >= 12){
            $mesesAtual = 12;
        }else{
            $mesesAtual = $meses;
        }
        
        return $mesesAtual;
    }
    
    
}
