<?php
/**
 * PrnSeguradoraTokioMarineDAO.class.php
 *
 * DAO
 *
 * @author Bruno Bonfim Affonso - <bruno.bonfim@sascar.com.br>
 * @package Principal
 * @version 1.0
 * @since 15/04/2013
 */
class PrnSeguradoraTokioMarineDAO{
    private $conn = "";
    
    function __construct(){
        global $conn;
        $this->conn = $conn;
    }
    
    /**
     * Inicia uma transação
     */
    public function beginTransaction(){
        pg_query($this->conn, "BEGIN;");
    }
    
    /**
     * Confirma uma transação
     */
    public function commitTransaction(){
        pg_query($this->conn, "COMMIT;");
    }
    
    /**
     * Reverte uma transação
     */
    public function rollbackTransaction(){
        pg_query($this->conn, "ROLLBACK;");
    }
    
    /**
     * Retorna o id do arquivo processado.
     * @param int $prpstpcoid
     * @param String $arquivo
     * @return boolean
     */
    public function getProcessamento($prpstpcoid,$arquivo){
        $sql = "SELECT
                    bsegoid
                FROM
                    bordero_seguradora
                WHERE
                    bsegnome_arquivo = '$arquivo'
                AND
                    bsegtpcoid = $prpstpcoid;";
                    
        $result = pg_query($this->conn, $sql);
        
        if(pg_num_rows($result) > 0){
            return true;
        } else{
            return false;
        }
    }
    
    /**
     * @param int $prpstpcoid
     * @return Array
     */
    public function getTipoContratoParametrizacao($prpstpcoid){
        $sql = "SELECT
                    tcpprazo_instalacao,
                    tcpdias_quarentena,
                    tcpdia_corte_quarentena,
                    tcpemail_retorno
                FROM
                    tipo_contrato_parametrizacao
                WHERE
                    tcptpcoid = $prpstpcoid;";
                    
        $result = pg_query($this->conn, $sql);        
        $result = pg_fetch_assoc($result);
        
        return $result;
    }
    
    /**
     * @param String $arquivo
     * @param int $id_usuario
     * @param String $caminho
     * @param int $prpstpcoid
     * @param String $origem
     * @return int primary_key
     */
    public function inserirPropostaSeguradoraArquivo($arquivo,$id_usuario,$caminho,$prpstpcoid,$origem){
        $sql = "INSERT INTO proposta_seguradora_arquivo
                    (prpsqnome_arquivo,prpsqdt_cadastro,prpsusuoid,prpsqcaminho,prpsqstatus,prpsqtgcoid,prpsqtpcoid,prpsqorigem)
                VALUES
                    ('$arquivo',now(),$id_usuario,'$caminho','P',44,$prpstpcoid,'$origem')
                RETURNING
                    prpsqoid;";

        $sql    = pg_query($this->conn, $sql);      
        $result = pg_fetch_result($sql,"prpsqoid");
                    
        //retorna a primary key que acabou de ser criada. 
        return (int) $result;    
    }
    
    /**
     * Altara o status do arquivo na tabela 'proposta_seguradora_arquivo'.
     * P = Processado | R = Rejeitado/Fora do Layout
     * @param int $prpsqoid
     * @param String $status
     */
    public function setStatusArquivo($prpsqoid,$status){
        if($prpsqoid != 0){
            $sql = "UPDATE
                        proposta_seguradora_arquivo
                    SET
                        prpsqstatus = '$status'
                    WHERE
                        prpsqoid = $prpsqoid;";
            
            pg_query($this->conn, $sql);
        }        
    }
    
    /**
     * @param int $prpstpcoid
     * @param String $prazoinst
     * @param String $proposta
     * @param String $placa
     * @param String $chassi
     * @param String $numapolice
     * @param String $tipo_solicitacao
     * @param String $numitemapolice
     * @param int $bsegprpsaoid
     * @param String $arquivo
     * @param String $bsegcia
     * @return int primary_key
     */
    public function inserirBorderoSeguradora($prpstpcoid,$prazoinst,$proposta,$placa,$chassi,$numapolice,$tipo_solicitacao,$numitemapolice,$bsegprpsaoid,$arquivo,$bsegcia){
        $sql = "INSERT INTO bordero_seguradora
                    (bsegtpcoid,bsegdt_cadastro,bsegprazo_inst,bsegproposta,bsegplaca,bsegchassi,bsegapolice,bsegtipo_solic,bsegitem,bsegprpsaoid,bsegnome_arquivo,bsegcia)
                VALUES
                    ($prpstpcoid,now(),$prazoinst,'$proposta','$placa','$chassi','$numapolice','$tipo_solicitacao','$numitemapolice',$bsegprpsaoid,'$arquivo','$bsegcia')
                RETURNING
                    bsegoid;";
                    
        $sql    = pg_query($this->conn, $sql);
        $result = pg_fetch_result($sql, "bsegoid");
        
        return (int) $result;
    }
    
    /**
     * Retorma uma proposta.
     * @param String $proposta
     * @param String $cpf_cnpj
     * @param String $chassi
     * @return array or null
     */
    public function getProposta($proposta, $cpf_cnpj = null, $chassi = null){
        $sql = "";
        
        if($cpf_cnpj != null && $chassi != null){
            $sql = "SELECT
                        prpsoid,
                        prpstpcoid,
                        prpsprazo_inst,
                        prpsveioid 
                    FROM
                        proposta_seguradora,
                        proposta_seguradora_segurado
                    WHERE
                        prpssprpsoid = prpsoid
                    AND
                        prpsscnpj_cpf ILIKE '%$cpf_cnpj%'
                    AND
                        prpschassi ILIKE '%$chassi%'
                    AND
                        prpstpcoid = 883                      
                    AND
                        prpsdt_exclusao IS NULL
                    ORDER BY
                        prpsoid DESC;";
        } else{
            $sql = "SELECT
                        prpsoid,
                        prpstpcoid,
                        prpsprazo_inst,
                        prpsveioid 
                    FROM
                        proposta_seguradora,
                        tipo_contrato  
                    WHERE
                        prpsproposta = '$proposta'
                    AND
                        prpstpcoid = tpcoid 
                    AND
                        tpctcgoid = 44
                    AND
                        prpsdt_exclusao IS NULL;";
        }
        
        $result = pg_query($this->conn, $sql);
        
        if(pg_num_rows($result) > 0){
            $data = array("prpsoid"        => pg_fetch_result($result,0,'prpsoid'),
                          "prpstpcoid"     => pg_fetch_result($result,0,'prpstpcoid'),
                          "prpsprazo_inst" => pg_fetch_result($result,0,'prpsprazo_inst'),
                          "prpsveioid"     => pg_fetch_result($result,0,'prpsveioid'));
        } else{
            $data = null;
        }
        
        return $data;
    }
    
    /**
     * Atualiza a proposta com os dados do veículo.
     * @param String $placa
     * @param String $placa_seguradora
     * @param String $chassi
     * @param String $chassi_seguradora
     * @param int $numapolice
     * @param int $numitemapolice
     * @param int $prpsoid
     * @return affected_rows
     */
    public function atualizarPropostaDadosVeiculo($placa,$placa_seguradora,$chassi,$chassi_seguradora,$numapolice,$numitemapolice,$prpsoid){
        $sql = "UPDATE
                    proposta_seguradora
                SET
                    prpsdt_ultimo_processamento = now(),
                    prpsplaca = '$placa',
                    prpsplaca_seguradora = '$placa_seguradora',
                    prpschassi = '$chassi',
                    prpschassi_seguradora = '$chassi_seguradora',
                    prpsapolice = $numapolice,
                    prpsno_item = $numitemapolice
                WHERE
                    prpsoid = $prpsoid;";
                    
        $result = pg_query($this->conn, $sql);
        $result = pg_affected_rows($result);
        
        return (int) $result;        
    }
    
    /**
     * Atualiza os dados do veículo.
     * @param int $numapolice
     * @param int $id_usuario
     * @param String $proposta
     * @param int $numitemapolice
     * @param String $tipo_doc
     * @param String $chassi
     * @param String $placa
     * @param int $veioid
     * @param String $prazoinst ou null
     * @return affected_rows
     */
    public function atualizarDadosVeiculo($numapolice,$id_usuario,$proposta,$numitemapolice,$tipo_doc,$chassi,$placa,$veioid,$prazoinst = null){
        $sql = "UPDATE
                    veiculo 
                SET 
                    veiapolice = '$numapolice',
                    veiusuoid_alteracao = $id_usuario,
                    veidt_alteracao = now(), 
                    veino_proposta = '$proposta', 
                    veino_item = '$numitemapolice', 
                    veitipo_doc_seg = '$tipo_doc', 
                    veichassi_seguradora = '$chassi', 
                    veiplaca_seguradora = '$placa'";
                    
        if($prazoinst != null){
            $sql .= " ,veiprazo_inst = $prazoinst";
        }
        
        $sql .= "WHERE
                    veioid = $veioid;";
                    
        $result = pg_query($this->conn, $sql);
        $result = pg_affected_rows($result);
        
        return (int) $result; 
    }
    
    /**
     * Retorna um contrato.
     * @param int $prpstpcoid
     * @param String $proposta
     * @param String $cpf_cnpj
     * @param String $cpf_cnpj_formatado
     * @param boolean $instalacao
     * @param boolean $eveoid equipamento_versao
     * @return array or null
     */
    public function getContrato($prpstpcoid,$proposta,$cpf_cnpj,$cpf_cnpj_formatado,$instalacao = true, $eveoid = false, $chassi = "", $chassi_seguradora = ""){        
        $sql = "SELECT
                    connumero,
                    conclioid,
                    conequoid,
                    conno_tipo";
        
        //Não é tratamento de instalação    
        if(!$instalacao){
            $sql .= " ,veioid ";
            
            //Tokio Marine
            if($prpstpcoid == 884 || $prpstpcoid == 919){
                $sql .= ",(select prpsoid from proposta_seguradora where prpstpcoid = conno_tipo and prpsproposta::text=ltrim(veino_proposta,'0')) as prpsoid";
            } else{
                //Tokio Marine Brasil
                $sql .= ",(select prpsoid from proposta_seguradora where prpstpcoid = conno_tipo and trim(upper(prpschassi))=trim(upper(veichassi))) as prpsoid";
            }
        }
        
        if($eveoid){
            $sql .= " ,eveoid";
        }

        $sql .= " FROM
                    veiculo, contrato, clientes, tipo_contrato";
        if($eveoid){
            $sql .= ",equipamento, equipamento_versao";
        }
        
        $sql .= " WHERE
                    conveioid = veioid 
                AND
                    conclioid = clioid
                AND
                    conno_tipo = tpcoid ";
                    
        //Tokio Marine
        if($prpstpcoid == 884 || $prpstpcoid == 919){
            $sql .= "
                AND
                    veino_proposta = '$proposta' ";
        }
        
        $sql .= "
                AND
                    tpctcgoid = 44
                AND
                    condt_exclusao IS NULL
                AND
                    (CASE WHEN clitipo = 'F' THEN 
                            clino_cpf::text ILIKE '%$cpf_cnpj' 
                        ELSE 
                            clino_cgc::text ILIKE '%$cpf_cnpj' 
                        END
                    OR
                        CASE WHEN clitipo = 'F' THEN 
                            clino_cpf::text ILIKE '%$cpf_cnpj_formatado' 
                        ELSE 
                            clino_cgc::text ILIKE '%$cpf_cnpj_formatado' 
                        END
                    ) ";                    
        if($eveoid){
            $sql .= "AND
                        conequoid = equoid
                    AND
                        equeveoid = eveoid";                        
            if(trim($chassi) != "" && trim($chassi_seguradora) != ""){
                $sql .= "AND
                            (veichassi = '$chassi' OR veichassi_seguradora = '$chassi_seguradora')";
            }
        }
        
        $sql .= " ORDER BY
                    connumero DESC;";
                    
        $result = pg_query($this->conn,$sql);
        
        if(pg_num_rows($result) > 0){
            $data = array("connumero"  => pg_fetch_result($result,0,'connumero'),
                          "conclioid"  => pg_fetch_result($result,0,'conclioid'),
                          "conequoid"  => pg_fetch_result($result,0,'conequoid'),
                          "conno_tipo" => pg_fetch_result($result,0,'conno_tipo'));
            
            if(!$instalacao){
                $array = array("veioid"  => pg_fetch_result($result,0,'veioid'),
                               "prpsoid" => pg_fetch_result($result,0,'prpsoid'));
                               
                $data = array_merge($data,$array);
            }
            
            if($eveoid){
                $array = array("eveoid"  => pg_fetch_result($result,0,'eveoid'));
                               
                $data = array_merge($data,$array);
            }
            
        } else{
            $data = null;
        }
        
        return $data;
    }
    
    /**
     * Atualiza as informaçoes de um contrato. Se o $valor for uma String,
     * concatenar com aspas simples. Exemplo.: "'".$valor."'"
     * @param String $campo
     * @param String $valor
     * @param int $connumero
     * @return affected_rows
     */
    public function atualizarContrato($campo,$valor,$connumero){
        $campo     = trim($campo);
        $valor     = trim($valor);
        $connumero = trim($connumero);
        
        if($campo != "" && $valor != "" && $connumero != ""){
            $sql = "UPDATE
                        contrato
                    SET
                        $campo = $valor
                    WHERE
                        connumero = $connumero;";
            
            $result = pg_query($this->conn, $sql);
            $data   = pg_affected_rows($result);
            
        } else{
            $data = 0;
        }        
        
        return (int) $data;
    }
    
    /**
     * @param int $prpstpcoid
     * @param int $connumero
     * @param int $conequoid
     * @param int $conclioid
     * @param int $prpsveioid
     * @param String $tipo_doc
     * @param String $caseobservacao
     * @param String $casetipo
     * @return int primary_key
     */
    public function inserirContratoAlteracaoSeguradora($prpstpcoid,$connumero,$conequoid,$conclioid,$prpsveioid,$tipo_doc,$caseobservacao = null, $casetipo = "I"){
        $sql = "INSERT INTO contrato_alteracao_seguradora ";
                    
        if($caseobservacao != null){
            $sql .= "(casetpcoid, caseconoid, caseequoid, caseclioid, caseveioid, casetipo, casetipo_documento, caseprpsaoid, caseobservacao)            
                VALUES
                     ($prpstpcoid, $connumero, $conequoid, $conclioid, $prpsveioid, '$casetipo', '$tipo_doc', 5, '$caseobservacao')";
        } else{
            $sql .= "(casetpcoid, caseconoid, caseequoid, caseclioid, caseveioid, casetipo, casetipo_documento, caseprpsaoid)
                VALUES
                     ($prpstpcoid, $connumero, $conequoid, $conclioid, $prpsveioid, '$casetipo', '$tipo_doc', 5)";
        }
        
        $sql .= "RETURNING
                    caseoid;";
        
        $sql    = pg_query($this->conn, $sql);
        $result = pg_fetch_result($sql, "caseoid");
        
        return (int) $result;
    }
    
    /**
     * @param int $prpsoid
     * @param int $prpsprpssoid
     * @param int $prpsultima_acao
     * @param boolean $ultimo_processamento
     * @param String $verificacao_manual
     * @param String $prpstransferencia_titularidade
     * @return affected_rows
     */
    public function atualizarProposta($prpsoid, $prpsprpssoid, $prpsultima_acao, $ultimo_processamento, $verificacao_manual, $prpstransferencia_titularidade = null){
        $sql = "UPDATE
                    proposta_seguradora
                SET
                    prpsdt_ultima_acao = now()";
                    
        if($prpsprpssoid != 0){
            $sql .= " ,prpsprpssoid = $prpsprpssoid";
        }
        
        if($prpsultima_acao != null){
            $sql .= " ,prpsultima_acao = $prpsultima_acao";
        }
        
        if($ultimo_processamento){
            $sql .= " ,prpsdt_ultimo_processamento = now()";
        }
        
        if($verificacao_manual != null){
            $sql .= " ,prpsverificacao_manual = '$verificacao_manual'";
        }    

        if($prpstransferencia_titularidade != null){
            $sql .= " ,prpstransferencia_titularidade = '$prpstransferencia_titularidade'";
        }
                    
        $sql .= " WHERE
                    prpsoid = $prpsoid;";
                    
        $result = pg_query($this->conn, $sql);
        $data   = pg_affected_rows($result);
        
        return (int) $data;
    }
    
    /**
     * @param int $prpsoid
     * @param int $prpsaoid
     * @param String $tipo_doc
     * @param String $prpshobservacao
     * @param int $prpssoid
     * @param int $id_usuario
     * @param int $apolice
     * @param int $no_item
     * @param String $entrada
     * @return primary_key
     */
    public function inserirPropostaSeguradoraHistorico($prpsoid,$prpsaoid,$tipo_doc,$prpshobservacao,$prpssoid,$id_usuario,$apolice = null,$no_item = null,$entrada = null){
        $sql = "INSERT INTO proposta_seguradora_historico
                    (prpshprpsoid, prpshprpsaoid, prpshtipo_documento, prpshobservacao, prpshprpssoid, prpshusuoid";
        
        if($apolice != null){$sql .= " ,prpshapolice";}
        if($no_item != null){$sql .= " ,prpshno_item";}        
        if($entrada != null){$sql .= " ,prpshentrada";}
                    
        $sql .= ")";
                
        $sql .= " VALUES
                    ($prpsoid, $prpsaoid, '$tipo_doc', '$prpshobservacao', $prpssoid, $id_usuario";
                    
        if($apolice != null){$sql .= " ,$apolice";}        
        if($no_item != null){$sql .= " ,$no_item";}        
        if($entrada != null){$sql .= " ,'$entrada'";}
                    
        $sql .= ") ";
                
        $sql .= "RETURNING
                    prpshoid;";
                    
        $sql    = pg_query($this->conn, $sql);
        $result = pg_fetch_result($sql, "prpshoid");
        
        return (int) $result;
    }
    
    /**
     * Altera o numero da proposta do veículo.
     * @param String $proposta
     * @param int $veioid
     * @return affected_rows
     */
    public function atualizarVeiculoProposta($proposta, $veioid){
        $sql = "UPDATE
                    veiculo
                SET
                    veino_proposta = '$proposta'
                WHERE
                    veioid = $veioid;";
                    
        $sql    = pg_query($this->conn,$sql);
        $result = pg_affected_rows($sql);
        
        return $result;
    }
    
    /**
     * Retorna uma O.S de instalação pendente.
     * @param int $connumero
     * @return array ou null
     */
    public function getInstalacaoPendente($connumero){
        $sql = "SELECT
                    ordoid,
                    ordacomp_usuoid
                FROM
                    contrato
                INNER JOIN
                    ordem_servico ON (ordconnumero = connumero)
                INNER JOIN
                    ordem_servico_item ON (ordoid = ositordoid)
                WHERE
                    connumero = $connumero
                AND
                    ositotioid = 3
                AND
                    ositstatus NOT IN ('N','X','C')
                AND
                    ordstatus IN (1,4);";
                    
        $result = pg_query($this->conn,$sql);
                    
        if(pg_num_rows($result) > 0){
            $data = array("ordoid"          => pg_fetch_result($result,0,'ordoid'),
                          "ordacomp_usuoid" => pg_fetch_result($result,0,'ordacomp_usuoid'));
        } else{
            $data = null;
        }
        
        return $data;
    }
    
    /**
     * Retorna o agendamento para os próximos dias a partir da data corrente.
     * @param int $ordoid
     * @return int ou null
     */
    public function getAgendamento($ordoid){
        $sql = "SELECT
                    osaoid 
                FROM
                    ordem_servico_agenda
                WHERE
                    osaordoid = $ordoid
                AND
                    osadata > now();";
        
        $result = pg_query($this->conn,$sql);
                    
        if(pg_num_rows($result) > 0){
            $data = pg_fetch_result($result,0,'osaoid');
        } else{
            $data = null;
        }
        
        return $data;
    }
    
    /**
     * @param int $prpstpcoid
     * @param int $proposta
     * @param String $tipo_doc
     * @param String $tipo_solicitacao
     * @param int $numapolice
     * @param int $numitemapolice
     * @param int $prpsaoid
     */
    public function inserirArquivoSeguradora($prpstpcoid,$proposta,$tipo_doc,$tipo_solicitacao,$numapolice,$numitemapolice,$prpsaoid){
        $sql = "INSERT INTO arquivo_seguradora
                    (asegtpcoid, asegnum_proposta, asegtipo_documento, asegtipo_solicitacao, asegnum_apolice, asegnum_item_apolice, asegprpsaoid)
                VALUES
                    ($prpstpcoid, $proposta, '$tipo_doc', '$tipo_solicitacao', $numapolice, $numitemapolice, 3);";
        
        pg_query($this->conn, $sql);
    }    
    
    /**
     * Retorna o e-mail do responsavel pela O.S
     * @param int $ordoid
     * @return array or null
     */
    public function getEmail($ordoid){    
        $sql = "SELECT
                    trim(usuemail) as usuemail
                FROM
                    usuarios
                INNER JOIN
                    ordem_servico ON (ordacomp_usuoid = cd_usuario)
                WHERE
                    ordoid = $ordoid;";
        
        $result = pg_query($this->conn,$sql);
        
        if(pg_num_rows($result) > 0){
            $data = pg_fetch_result($result,0,'usuemail');
        } else{
            $data = null;
        }
        
        return $data;
    }
    
    /**
     * Retorna o id_veiculo.
     * @param String $chassi
     * @param String $chassi_seguradora
     * @return array ou null
     */
    public function getVeiculo($chassi, $chassi_seguradora){
        $sql = "SELECT
                    veioid 
                FROM
                    veiculo
                WHERE
                    (veichassi = '$chassi' OR veichassi_seguradora = '$chassi_seguradora')
                AND
                    veioid NOT IN (select conveioid from contrato where conveioid = veioid)
                AND
                    veidt_exclusao IS NULL
                LIMIT 1;";
                
        $result = pg_query($this->conn,$sql);
                    
        if(pg_num_rows($result) > 0){
            $data = array("veioid" => pg_fetch_result($result,0,'veioid'));
        } else{
            $data = null;
        }
        
        return $data;
    }
    
    
    /**
     * Retorna os dados da renovação.
     * @param String $chassi
     * @param String $chassi_seguradora
     * @param String $cpf_cnpj
     * @param String $cpf_cnpj_formatado
     * @param int $proposta
     * @return array ou null
     */
    public function getRenovacao($chassi, $chassi_seguradora, $cpf_cnpj, $cpf_cnpj_formatado, $proposta){
        $sql = "SELECT
                    veioid,
                    veino_proposta,
                    connumero,
                    conclioid,
                    conequoid
                FROM
                    veiculo, contrato, clientes,tipo_contrato
                WHERE
                    conveioid = veioid
                AND
                    conno_tipo = tpcoid
                AND
                    conclioid = clioid
                AND
                    (veichassi='$chassi' OR veichassi_seguradora='$chassi_seguradora')
                AND
                    veino_proposta <> '$proposta'
                AND
                    veidt_exclusao IS NULL 
                AND
                    condt_exclusao IS NULL 
                AND
                    tpctcgoid = 4
                AND
                    (
                        CASE WHEN clitipo='F' THEN 
                            clino_cpf::text ILIKE '%$cpf_cnpj' 
                        ELSE 
                            clino_cgc::text ILIKE '%$cpf_cnpj' 
                        END
                    OR
                        CASE WHEN clitipo='F' THEN 
                            clino_cpf::text ILIKE '%$cpf_cnpj_formatado' 
                        ELSE 
                            clino_cgc::text ILIKE '%$cpf_cnpj_formatado' 
                        END
                    OR
                        trim(UPPER(clinome)) = trim(UPPER('$nomesegurado'))
                    )
                ORDER BY
                    connumero DESC 
                LIMIT 1;";
                
        $result = pg_query($this->conn,$sql);
                
        if(pg_num_rows($result) > 0){
            $data = array("veioid"         => pg_fetch_result($result,0,'veioid'),
                          "veino_proposta" => pg_fetch_result($result,0,'veino_proposta'),
                          "connumero"      => pg_fetch_result($result,0,'connumero'),
                          "conclioid"      => pg_fetch_result($result,0,'conclioid'),
                          "conequoid"      => pg_fetch_result($result,0,'conequoid'));
        } else{
            $data = null;
        }
        
        return $data;
    }
    
    /**
     * Insere histórico de renovação.
     * @param int $connumero
     * @param int $id_usuario
     * @param String $observacao
     */
    public function historico_termo_i($connumero,$id_usuario,$observacao){
        $sql = "SELECT historico_termo_i($connumero, $id_usuario, '$observacao');";
        pg_query($this->conn,$sql);
    }
    
    /**
     * @param int $prpsoid
     * @return affected_rows
     */
    public function atualizarPropostaSeguradoraSegurado($prpsoid){
        $sql = "UPDATE
                    proposta_seguradora
                SET
                    prpsdt_ultimo_processamento = now(),
                    prpsprpssoid = 2
                FROM
                    proposta_seguradora_segurado
                WHERE
                    prpssprpsoid = prpsoid
                AND
                    prpsproposta::text IN  
                        (SELECT
                            veino_proposta
                        FROM
                            veiculo, contrato, clientes
                        WHERE
                            conveioid = veioid
                        AND
                            condt_exclusao IS NULL
                        AND
                            prpsproposta::text = veino_proposta
                        AND
                            conno_tipo = prpstpcoid
                        AND
                            condt_exclusao IS NULL
                        AND
                            (clino_cpf::text = prpsscnpj_cpf::text OR clino_cgc::text = prpsscnpj_cpf::text)
                        )
                AND
                    prpsprpssoid <> 2
                AND
                    prpsultima_acao = 1
                AND
                    prpsoid = $prpsoid;";
                    
        $result = pg_query($this->conn, $sql);
        $result = pg_affected_rows($result);
        
        return (int) $result; 
    }
    
    /**
     * Retorna um contrato Ex-seguradora.
     * @param int $prpstpcoid
     * @param String $chassi
     * @param String $chassi_seguradora
     * @param String $proposta
     * @param String $cpf_cnpj
     * @param String $cpf_cnpj_formatado
     * @return array ou null
     */
    public function getContratoExSeguradora($prpstpcoid,$chassi,$chassi_seguradora,$proposta,$cpf_cnpj,$cpf_cnpj_formatado){
        $sql = "SELECT
                    connumero,
                    conequoid,
                    conclioid,
                    conveioid,
                    conno_tipo
                FROM
                    clientes 
                INNER JOIN
                    contrato ON (conclioid = clioid)
                INNER JOIN
                    veiculo ON (conveioid = veioid)
                INNER JOIN
                    tipo_contrato_parametrizacao ON (tcpoidcorrespondente_ex = conno_tipo)
                WHERE
                    condt_exclusao IS NULL 
                AND
                    tcptpcoid = $prpstpcoid              
                AND
                    conequoid > 0
                AND
                    (veichassi = '$chassi' OR veichassi_seguradora = '$chassi_seguradora')
                AND
                    veino_proposta = '$proposta'
                AND
                    (
                        CASE WHEN clitipo='F' THEN 
                            clino_cpf::text ILIKE '%$cpf_cnpj' 
                        ELSE 
                            clino_cgc::text ILIKE '%$cpf_cnpj' 
                        END
                    OR
                        CASE WHEN clitipo='F' THEN 
                            clino_cpf::text ILIKE '%$cpf_cnpj_formatado' 
                        ELSE 
                            clino_cgc::text ILIKE '%$cpf_cnpj_formatado' 
                        END
                    )
                ORDER BY
                    connumero DESC
                LIMIT 1;";
                    
        $result = pg_query($this->conn, $sql);
        
        if(pg_num_rows($result) > 0){
            $data = array("connumero"  => pg_fetch_result($result,0,'connumero'),
                          "conequoid"  => pg_fetch_result($result,0,'conequoid'),
                          "conclioid"  => pg_fetch_result($result,0,'conclioid'),
                          "conveioid"  => pg_fetch_result($result,0,'conveioid'),
                          "conno_tipo" => pg_fetch_result($result,0,'conno_tipo'));
        } else{
            $data = null;
        }
        
        return $data;
    }
    
    /**
     * Retorna uma O.S de revisão pendente.
     * @param int $connumero
     * @return array ou null
     */
    public function getRevisaoPendente($connumero){
        $sql = "SELECT
                    ordoid
                FROM
                    ordem_servico 
                WHERE
                    ordoid IN (SELECT
                                    ositordoid 
                                FROM
                                    ordem_servico_item 
                                WHERE
                                    ositstatus <> 'X' 
                                AND
                                    ositexclusao IS NULL 
                                AND
                                    ositotioid = 105 
                                LIMIT 1) 
                AND
                    ordstatus IN (1,4) 
                AND
                    ordconnumero IN (SELECT
                                        connumero 
                                    FROM
                                        contrato
                                    WHERE
                                        condt_exclusao IS NULL 
                                    AND
                                        conno_tipo IN (883,884,919)
                                    AND
                                        connumero = $connumero);";
                    
        $result = pg_query($this->conn,$sql);
                    
        if(pg_num_rows($result) > 0){
            $data = array("ordoid" => pg_fetch_result($result,0,'ordoid'));
        } else{
            $data = null;
        }
        
        return $data;
    }
    
    /**
     * @return int primary_key
     */
    public function inserirOrdemServicoRevisao($conequoid, $conclioid, $status, $desc_problema, $id_usuario, $connumero, $ordmtioid, $descr_motivo, $veioid, $ordrelroid, $eveoid, $ordrevisao_seg){
        $sql = "INSERT INTO ordem_servico
                    (ordequoid, ordclioid, ordstatus, orddesc_problema, ordusuoid, ordconnumero, ordmtioid, orddescr_motivo, ordveioid, ordrelroid, ordeveoid, ordrevisao_seg)
                VALUES
                    ($conequoid, $conclioid, $status, '$desc_problema', $id_usuario, $connumero, $ordmtioid, '$descr_motivo', $veioid, $ordrelroid, $eveoid, $ordrevisao_seg)
                RETURNING
                    ordoid";
        
        $sql    = pg_query($this->conn, $sql);      
        $result = pg_fetch_result($sql,"ordoid");
                    
        //retorna a primary key que acabou de ser criada. 
        return (int) $result; 
    }
    
    
    /**
     * Verifica se possui algum outro contrato ativo
     * para outro cliente e com equipamento instalado.
     * @param int $cpf_cnpj
     * @param int $cpf_cnpj_formatado
     * @param String $chassi
     * @param String $chassi_seguradora
     * @param String $proposta
     * @return array ou null
     */
    public function getContratoAtivoCliente($cpf_cnpj, $cpf_cnpj_formatado, $chassi, $chassi_seguradora, $proposta){
        $sql = "SELECT
                    connumero
                FROM
                    clientes 
                INNER JOIN
                    contrato ON (conclioid = clioid)
                INNER JOIN
                    tipo_contrato ON (conno_tipo = tpcoid)
                INNER JOIN
                    veiculo ON (conveioid = veioid)
                WHERE
                    condt_exclusao IS NULL 
                AND
                    tpctcgoid = 44 
                AND
                    conequoid > 0
                AND 
                    (
                        CASE WHEN clitipo = 'F' THEN 
                            clino_cpf::text not ILIKE '%$cpf_cnpj' 
                        ELSE 
                            clino_cgc::text not ILIKE '%$cpf_cnpj' 
                        END
                        OR
                        CASE WHEN clitipo = 'F' THEN 
                            clino_cpf::text not ILIKE '%$cpf_cnpj_formatado' 
                        ELSE 
                            clino_cgc::text not ILIKE '%$cpf_cnpj_formatado' 
                        END
                    )
                AND
                    (veichassi = '$chassi' OR veichassi_seguradora = '$chassi_seguradora')
                AND
                    veino_proposta <> '$proposta'					
                ORDER BY
                    connumero DESC 
                LIMIT 1;";

        $result = pg_query($this->conn,$sql);
                    
        if(pg_num_rows($result) > 0){
            $data = array("connumero" => pg_fetch_result($result,0,'connumero'));
        } else{
            $data = null;
        }
        
        return $data;
    }
    
    /**
     * Retorna o prazo de instalação conforme o tipo do contrato.
     * @param int $prpstpcoid
     * @return date
     */
    public function getPrazoInstalacaoParametrizado($prpstpcoid){
        $sql = "SELECT
                (now()::date + (CASE
                                    WHEN (SELECT tcpprazo_instalacao FROM tipo_contrato_parametrizacao WHERE tcptpcoid = $prpstpcoid) = null THEN 10
                                    ELSE (SELECT tcpprazo_instalacao FROM tipo_contrato_parametrizacao WHERE tcptpcoid = $prpstpcoid)
                                END)) as prpsprazo_inst;";
        
        $result = pg_query($this->conn, $sql);        
        $result = pg_fetch_result($result,0,'prpsprazo_inst');
        
        return $result;
    }
    
    /**
     * Cria uma proposta nova.
     */
    public function inserirProposta($prpsproposta 
                                    ,$prpstpcoid 
                                    ,$prpsprpsgoid 
                                    ,$prpsdt_solicitacao 
                                    ,$prpsprazo_inst 
                                    ,$prpsplaca 
                                    ,$prpsplaca_seguradora 
                                    ,$prpschassi
                                    ,$prpschassi_seguradora 
                                    ,$prpsapolice 
                                    ,$prpsno_item 
                                    ,$prpscorroid
                                    ,$prpsemail_corretor 
                                    ,$prpscia 
                                    ,$prpscod_unid_emis 
                                    ,$prpsprpssoid 
                                    ,$prpsinicio_vigencia 
                                    ,$prpsfim_vigencia 
                                    ,$prpsobs_geral 
                                    ,$prpsdt_ultima_acao 
                                    ,$prpsultima_acao 
                                    ,$prpsverificacao_manual 
                                    ,$prpstransferencia_titularidade 
                                    ,$prpsveioid
                                    ,$prpsscnpj_cpf
                                    ,$prpstipo_pessoa
                                    ,$prpssegurado
                                    ,$prpsemail
                                    ,$prpsendereco
                                    ,$prpsbairro
                                    ,$prpsmunicipio
                                    ,$prpsuf
                                    ,$prpsddd
                                    ,$prpsfone
                                    ,$prpsnumero
                                    ,$prpscep
                                    ,$veisegoid
                                    ,$tipodocumento
                                    ,$prpsaditamento
                                    ,$prpscombinacao
                                    ,$prpsdt_ultimo_processamento
                                    ,$prpssolicitante
                                    ,$prpscorretor
                                    ,$prpsmarca
                                    ,$prpsmodelo
                                    ,$prpsfurtoid
                                    ,$prpsfurtdescricao){
                                    
        $text = "\"".$prpsproposta."\" 
                \"".$prpstpcoid."\"
                \"".$prpsprpsgoid."\"
                \"".$prpsdt_solicitacao."\"
                \"".$prpsprazo_inst."\" 
                \"".$prpsplaca."\" 
                \"".$prpsplaca_seguradora."\"
                \"".$prpschassi."\"
                \"".$prpschassi_seguradora."\"
                \"".$prpsapolice."\" 
                \"".$prpsno_item."\"
                \"".$prpscorroid."\"
                \"".$prpsemail_corretor."\" 
                \"".$prpscia."\" 
                \"".$prpscod_unid_emis."\" 
                \"".$prpsprpssoid."\" 
                \"".$prpsinicio_vigencia."\" 
                \"".$prpsfim_vigencia."\" 
                \"".$prpsobs_geral."\" 
                \"".$prpsdt_ultima_acao."\" 
                \"".$prpsultima_acao."\" 
                \"".$prpsverificacao_manual."\" 
                \"".$prpstransferencia_titularidade."\" 
                \"".$prpsveioid."\"
                \"".$prpsscnpj_cpf."\"
                \"".$prpstipo_pessoa."\"
                \"".$prpssegurado."\"
                \"".$prpsemail."\"
                \"".$prpsendereco."\"
                \"".$prpsbairro."\"
                \"".$prpsmunicipio."\"
                \"".$prpsuf."\"
                \"".$prpsddd."\"
                \"".$prpsfone."\"
                \"".$prpsnumero."\"
                \"".$prpscep."\"
                \"".$veisegoid."\"
                \"".$tipodocumento."\"
                \"".$prpsaditamento."\"
                \"".$prpscombinacao."\"
                \"".$prpsdt_ultimo_processamento."\"
                \"".$prpssolicitante."\"
                \"".$prpscorretor."\"
                \"".$prpsmarca."\"
                \"".$prpsmodelo."\"
                \"".$prpsfurtoid."\"
                \"".$prpsfurtdescricao."\"";
                
        $text = str_replace("'","",$text);
        
        $sql = "SELECT
                    proposta_seguradora_i('$text') as prpsoid;";
                    
        $result  = pg_query($this->conn,$sql);		
		$prpsoid = pg_fetch_result($result,0,0);
        
        if($prpsoid > 0){
            return $prpsoid;
        } else{
            return null;
        }
    }
    
    /**
     * Ver função migrar_contrato() do arquivo: includes/php/contrato_funcoes.php
     */
    public function migrarContrato($id_usuario, $prpstpcoid, $connumero, $novo_termo, $conno_tipo, $opcao_cliente_migrar, $cpf_cnpj_cliente){
        include _SITEDIR_."includes/php/contrato_funcoes.php";        
        migrar_contrato($id_usuario, $prpstpcoid, $connumero, $novo_termo, $conno_tipo, $opcao_cliente_migrar, $cpf_cnpj_cliente, $this->conn);
    }
    
    /**
     * Retorna as informações necessárias para o arquivo de retorno layout 6190.
     * @return array
     */
    public function getDadosRetornoTokioMarine(){
        $sql = "SELECT
                    '00009' as campo1,
                    '000000021' as campo2,
                    'SASCAR TECNOLOGIA AUTOMOTIVA' as campo3,
                    'R' as campo4,
                    'RASTREADOR' as campo5,
                    veino_proposta as campo6,
                    casetipo_documento as campo7,
                    prpsitem_proposta as campo8,
                    prpsitem_anterior_proposta campo9,
                    prpsendosso as campo10,
                    prpstipo_endosso as campo11,
                    veiapolice as campo12,
                    veino_item as campo13,
                    to_char(veidt_ini_apolice,'yyyymmdd') as campo14,
                    to_char(veidt_fim_apolice,'yyyymmdd') as campo15,
                    to_char(prpsdt_solicitacao,'yyyymmdd') as campo16,
                    '000000000' as campo17,
                    '000000000' as campo18,
                    '' as campo19,
                    '000000000' as campo20,
                    prpsmarca as campo21,
                    '000000000' as campo22,
                    prpsmodelo as campo23,
                    veino_ano as campo24,
                    prpsplaca_seguradora campo25,
                    prpschassi_seguradora campo26,
                    prpssegurado as campo27,
                    prpsscnpj_cpf as campo28, 
                    prpsendereco as campo29,
                    prpsnumero as campo30,
                    prpsscomplemento as campo31,  
                    prpsbairro as campo32,
                    prpscep as campo33,
                    prpsmunicipio as campo34,
                    prpsuf as campo35,
                    prpsddd as campo36,             
                    prpsfone as campo37,
                    prpscod_unid_emis as campo38,
                    '00' as campo39,
                    '000000' as campo40,
                    (select to_char(condt_ini_vigencia,'yyyymmdd') from contrato where caseconoid = connumero) as campo41,
                    case when casetipo = 'R' then to_char(casedt_alteracao,'yyyymmdd')  else '' end as campo42,
                    '00' as campo43,
                    (SELECT prpsaevento_correspondente_tm FROM proposta_seguradora_acao WHERE caseprpsaoid = prpsaoid) as campo44,
                    '' as campo45,
                    '' as campo46,
                    (SELECT corrnome from corretor WHERE prpscorroid = corroid) as campo47,
                    prpsddd_corretor as campo48,
                    prpsfone_corretor campo49,
                    prpsemail_corretor campo50,
                    caseoid as id,
                    'contrato_alteracao_seguradora' as tabela
                FROM 
                    contrato_alteracao_seguradora,
                    proposta_seguradora_segurado,
                    proposta_seguradora,   
                    tipo_contrato,
                    contrato,
                    veiculo
                WHERE
                    caseremessa IS NULL
                AND
                    caseveioid = veioid
                AND
                    ltrim(veino_proposta,'0') = prpsproposta::text
                AND
                    casetpcoid = prpstpcoid 
                AND
                    prpssprpsoid = prpsoid 
                AND
                    prpsdt_exclusao IS NULL 
                AND
                    casetpcoid = tpcoid 
                AND
                    casetpcoid = prpstpcoid
                AND
                    caseconoid = connumero
                AND
                    veichassi = prpschassi
                AND 
                    tpcoid IN (884, 919) 

                UNION

                SELECT
                    '00009' as campo1,
                    '000000021' as campo2,
                    'SASCAR TECNOLOGIA AUTOMOTIVA' as campo3,
                    'R' as campo4,
                    'RASTREADOR' as campo5,
                    veino_proposta as campo6,
                    asegtipo_documento as campo7,
                    prpsitem_proposta as campo8,
                    prpsitem_anterior_proposta campo9,
                    prpsendosso as campo10,
                    prpstipo_endosso as campo11,
                    veiapolice as campo12,
                    veino_item as campo13,
                    to_char(veidt_ini_apolice,'yyyymmdd') as campo14,
                    to_char(veidt_fim_apolice,'yyyymmdd') as campo15,
                    to_char(prpsdt_solicitacao,'yyyymmdd') as campo16,
                    '000000000' as campo17,
                    '000000000' as campo18,
                    '' as campo19,
                    '000000000' as campo20,
                    prpsmarca as campo21,
                    '000000000' as campo22,
                    prpsmodelo as campo23,
                    veino_ano as campo24,
                    prpsplaca_seguradora campo25,
                    prpschassi_seguradora campo26,
                    prpssegurado as campo27,
                    prpsscnpj_cpf as campo28, 
                    prpsendereco as campo29,
                    prpsnumero as campo30,
                    prpsscomplemento as campo31,  
                    prpsbairro as campo32,
                    prpscep as campo33,
                    prpsmunicipio as campo34,
                    prpsuf as campo35,
                    prpsddd as campo36,             
                    prpsfone as campo37,
                    prpscod_unid_emis as campo38,
                    '00' as campo39,
                    '000000' as campo40,
                    '' as campo41,
                    '' as campo42,
                    '00' as campo43,
                    (SELECT prpsaevento_correspondente_tm FROM proposta_seguradora_acao WHERE asegprpsaoid = prpsaoid) as campo44,
                    '' as campo45,
                    '' as campo46,
                    (SELECT corrnome from corretor WHERE prpscorroid = corroid) as campo47,
                    prpsddd_corretor as campo48,
                    prpsfone_corretor campo49,
                    prpsemail_corretor campo50,
                    asegoid as id,
                    'arquivo_seguradora' as tabela
                FROM 
                    arquivo_seguradora,
                    proposta_seguradora_segurado,
                    proposta_seguradora,   
                    tipo_contrato,
                    veiculo
                WHERE
                    asegremessa IS NULL
                AND
                    ltrim(veino_proposta,'0') = prpsproposta::text
                AND
                    prpsproposta = asegnum_proposta
                AND
                    asegtpcoid = prpstpcoid 
                AND
                    prpssprpsoid = prpsoid 
                AND
                    prpsdt_exclusao IS NULL 
                AND
                    asegtpcoid = tpcoid 
                AND 
                    asegtpcoid = prpstpcoid
                AND
                    tpcoid IN (884, 919);";
        
        $sql = pg_query($this->conn, $sql);
        
        if(pg_num_rows($sql) > 0){
            for($i = 0; $i < pg_num_rows($sql); $i++){
                $result = pg_fetch_array($sql,$i);            
            
                $data[$i] = array("campo1" => $result['campo1'], "campo2" => $result['campo2'], "campo3" => $result['campo3'],
                                  "campo4" => $result['campo4'], "campo5" => $result['campo5'], "campo6" => $result['campo6'],
                                  "campo7" => $result['campo7'], "campo8" => $result['campo8'], "campo9" => $result['campo9'],
                                  "campo10" => $result['campo10'], "campo11" => $result['campo11'], "campo12" => $result['campo12'],
                                  "campo13" => $result['campo13'], "campo14" => $result['campo14'], "campo15" => $result['campo15'],
                                  "campo16" => $result['campo16'], "campo17" => $result['campo17'], "campo18" => $result['campo18'],
                                  "campo19" => $result['campo19'], "campo20" => $result['campo20'], "campo21" => $result['campo21'],
                                  "campo22" => $result['campo22'], "campo23" => $result['campo23'], "campo24" => $result['campo24'],
                                  "campo25" => $result['campo25'], "campo26" => $result['campo26'], "campo27" => $result['campo27'],
                                  "campo28" => $result['campo28'], "campo29" => $result['campo29'], "campo30" => $result['campo30'],
                                  "campo31" => $result['campo31'], "campo32" => $result['campo32'], "campo33" => $result['campo33'],
                                  "campo34" => $result['campo34'], "campo35" => $result['campo35'], "campo36" => $result['campo36'],
                                  "campo37" => $result['campo37'], "campo38" => $result['campo38'], "campo39" => $result['campo39'],
                                  "campo40" => $result['campo40'], "campo41" => $result['campo41'], "campo42" => $result['campo42'],
                                  "campo43" => $result['campo43'], "campo44" => $result['campo44'], "campo45" => $result['campo45'],
                                  "campo46" => $result['campo46'], "campo47" => $result['campo47'], "campo48" => $result['campo48'],
                                  "campo49" => $result['campo49'], "campo50" => $result['campo50'], "id" => $result['id'],
                                  "tabela" => $result['tabela']);
            }            
        } else{
            $data = null;
        }
        
        return $data;
    }
    
    /**
     * E-mail de retorno da proposta de acordo com o tipo de contrato.
     * @param int $tcptpcoid
     * @return array or null
     */
    public function getEmailRetorno($tcptpcoid){
        $sql = "SELECT
                    trim(tcpemail_retorno) as tcpemail_retorno
                FROM
                    tipo_contrato_parametrizacao
                WHERE
                    tcptpcoid = $tcptpcoid;";
                    
        $result = pg_query($this->conn,$sql);
        
        if(pg_num_rows($result) > 0){
            $data = pg_fetch_result($result,0,'tcpemail_retorno');
        } else{
            $data = null;
        }
        
        return $data;
    }
    
    /**
     *
     */
    public function atualizarPropostaTipoContrato(){
        $sql = "UPDATE
                    proposta_seguradora
                SET
                    prpsprpssoid = 10,
                    prpsultima_acao = 4
                FROM
                    tipo_contrato
                WHERE
                    prpstpcoid = tpcoid
                AND
                    tpctcgoid = 44
                AND
                    prpsultima_acao = 1
                AND
                    now()::date > (prpsdt_ultima_acao::date+10)
                AND
                    prpsprpssoid = 1
                AND
                    prpsproposta::text NOT IN ((SELECT
                                                    ltrim(veino_proposta,'0')
                                                FROM
                                                    veiculo, contrato, equipamento
                                                WHERE
                                                    conequoid = equoid
                                                AND
                                                    conveioid = veioid
                                                AND
                                                    ltrim(veino_proposta,'0') = prpsproposta::text
                                                AND
                                                    conno_tipo = prpstpcoid
                                                AND
                                                    condt_exclusao IS NULL));";
    
        pg_query($this->conn,$sql);
    }
    
    /**
     *
     */
    public function inserirArquivoSeguradoraTipoContrato(){
        $sql = "INSERT INTO arquivo_seguradora
                (asegtpcoid,asegnum_proposta,asegtipo_documento, asegtipo_solicitacao,asegnum_apolice, asegnum_item_apolice, asegprpsaoid) 
                (SELECT prpstpcoid,prpsproposta,'PRO','INS',prpsapolice,prpsno_item,4 FROM tipo_contrato,proposta_seguradora 
                 WHERE prpsprpssoid = 10 AND prpstpcoid = tpcoid AND tpctcgoid = 44 AND prpsdt_exclusao IS NULL
                 AND prpsproposta NOT IN (SELECT asegnum_proposta FROM arquivo_seguradora WHERE asegnum_proposta = prpsproposta
                 AND asegtpcoid = prpstpcoid AND asegprpsaoid = 4));";        
        
        pg_query($this->conn,$sql);
    }
    
    /**
     *
     */
    public function getSequencialTokioMarineBrasil(){
        $sql = "SELECT
                    sisseq_tokiomarine_brasil
                FROM
                    sistema;";
                    
        $result = pg_query($this->conn, $sql);
        
        if(pg_num_rows($result) > 0){
            $data = pg_fetch_result($result,0,'sisseq_tokiomarine_brasil');
        }
        
        return $data;
    }
    
    /**
     * Acrescenta +1 ao sequencial.
     * @param int $sequencial
     * @return affected_rows
     */
    public function setSequencialTokioMarineBrasil($sequencial){
        $sequencial = (int) $sequencial;
        $sequencial = $sequencial + 1;
        
        $sql = "UPDATE
                    sistema
                SET
                    sisseq_tokiomarine_brasil = $sequencial;";
        
        $result = pg_query($this->conn, $sql);
        $result = pg_affected_rows($result);
        
        return (int) $result;
    }
    
    public function getDadosRetornoTokioMarineBrasil(){
        $sql = "SELECT
                    prpsscnpj_cpf as campo0, 
                    'SASCAR - TECNOLOGIA E SEGURANÇA AUTOMOTIVA S.A.' as campo1,
                    '1' as campo2,
                    '1' as campo3,
                    '31' as campo4,
                    prpstipo_dispositivo_af as campo5,
                    prpscodigo_veiculo as campo6,
                    prpsmarca as campo7,
                    prpscod_modelo_veiculo as campo8,
                    prpsmodelo as campo9,
                    veino_ano as campo10,
                    prpsplaca as campo11,
                    prpschassi as campo12,
                    prpsapolice as campo13,
                    prpssegurado as campo14,
                    prpscod_cpf_cnpj_segurado as campo15,
                    prpscod_estabelecimento as campo16,
                    prpscod_digito_segurado as campo17,
                    prpscep as campo18,
                    prpsendereco as campo19,
                    prpsscomplemento as campo20,
                    prpsbairro as campo22,
                    prpsmunicipio as campo23,
                    prpsuf as campo24,
                    prpsddd as campo25,
                    prpsfone as campo26,
                    prpscod_local as campo27,
                    prpscod_sublocal as campo28,
                    prpscod_corretor as campo29,
                    prpssolicitante as campo30,
                    prpsddd_corretor as campo31,             
                    prpsfone_corretor as campo32,
                    prpsemail_corretor as campo33,
                    prpscod_laudo_af as campo34,
                    caseequoid as campo35,
                    (SELECT prpsaevento_correspondente_tm FROM proposta_seguradora_acao WHERE caseprpsaoid = prpsaoid) as campo36,
                    to_char(casedt_alteracao, 'ddmmyyyy') as campo37,
                    '' as campo38,
                    caseoid as id,
                    'contrato_alteracao_seguradora' as tabela
                FROM 
                    contrato_alteracao_seguradora,
                    proposta_seguradora_segurado,
                    proposta_seguradora,   
                    tipo_contrato,
                    contrato,
                    veiculo
                WHERE
                    caseveioid = veioid
                AND caseremessa IS NULL
                AND casetpcoid = prpstpcoid 
                AND prpssprpsoid = prpsoid 
                AND prpsdt_exclusao IS NULL 
                AND casetpcoid = tpcoid 
                AND casetpcoid = prpstpcoid
                AND caseconoid = connumero
                AND prpsproposta::text = ltrim(veino_proposta,'0')::text
                AND tpcoid = 883 

                UNION

                SELECT
                    prpsscnpj_cpf as campo0, 
                    'SASCAR - TECNOLOGIA E SEGURANÇA AUTOMOTIVA S.A.' as campo1,
                    '1' as campo2,
                    '1' as campo3,
                    '31' as campo4,
                    prpstipo_dispositivo_af as campo5,
                    prpscod_veiculo as campo6,
                    prpsmarca as campo7,
                    prpscod_modelo_veiculo as campo8,
                    prpsmodelo as campo9,
                    veino_ano as campo10,
                    prpsplaca as campo11,
                    prpschassi as campo12,
                    prpsapolice as campo13,
                    prpssegurado as campo14,
                    prpscod_cpf_cnpj_segurado as campo15,
                    prpscod_estabelecimento as campo16,
                    prpscod_digito_segurado as campo17,
                    prpscep as campo18,
                    prpsendereco as campo19,
                    prpsscomplemento  as campo20,
                    prpsbairro as campo22,
                    prpsmunicipio campo23,
                    prpsuf as campo24,
                    prpsddd as campo25,
                    prpsfone as campo26,
                    prpscod_local as campo27,
                    prpscod_sublocal as campo28,
                    prpscod_corretor as campo29,
                    prpssolicitante as campo30,
                    prpsddd_corretor as campo31,             
                    prpsfone_corretor as campo32,
                    prpsemail_corretor as campo33,
                    prpscod_laudo_af as campo34,
                    null as campo35,
                    (SELECT prpsaevento_correspondente_tm FROM proposta_seguradora_acao WHERE asegprpsaoid = prpsaoid) as campo36,
                    to_char(asegdt_cadastro, 'ddmmyyyy') as campo37,
                    '' as campo38,
                    asegoid as id,
                    'arquivo_seguradora' as tabela
                FROM 
                    arquivo_seguradora,
                    proposta_seguradora_segurado,
                    proposta_seguradora,
                    veiculo
                WHERE
                    asegremessa IS NULL
                AND asegtpcoid = prpstpcoid 
                AND asegnum_proposta::text = ltrim(veino_proposta,'0')::text
                AND prpssprpsoid = prpsoid 
                AND prpsdt_exclusao IS NULL 
                AND asegnum_proposta = prpsproposta
                AND asegtpcoid = 883;";
        
        $sql = pg_query($this->conn, $sql);
        
        if(pg_num_rows($sql) > 0){
            for($i = 0; $i < pg_num_rows($sql); $i++){
                $result = pg_fetch_array($sql,$i);            
            
                $data[$i] = array("campo0" => $result['campo0'], "campo1" => $result['campo1'], "campo2" => $result['campo2'],
                                  "campo3" => $result['campo3'], "campo4" => $result['campo4'], "campo5" => $result['campo5'],
                                  "campo6" => $result['campo6'], "campo7" => $result['campo7'], "campo8" => $result['campo8'],
                                  "campo9" => $result['campo9'], "campo10" => $result['campo10'], "campo11" => $result['campo11'],
                                  "campo12" => $result['campo12'], "campo13" => $result['campo13'], "campo14" => $result['campo14'],
                                  "campo15" => $result['campo15'], "campo16" => $result['campo16'], "campo17" => $result['campo17'],
                                  "campo18" => $result['campo18'], "campo19" => $result['campo19'], "campo20" => $result['campo20'],
                                  "campo22" => $result['campo22'], "campo23" => $result['campo23'],
                                  "campo24" => $result['campo24'], "campo25" => $result['campo25'], "campo26" => $result['campo26'],
                                  "campo27" => $result['campo27'], "campo28" => $result['campo28'], "campo29" => $result['campo29'],
                                  "campo30" => $result['campo30'], "campo31" => $result['campo31'], "campo32" => $result['campo32'],
                                  "campo33" => $result['campo33'], "campo34" => $result['campo34'], "campo35" => $result['campo35'],
                                  "campo36" => $result['campo36'], "campo37" => $result['campo37'], "campo38" => $result['campo38'],
                                  "id" => $result['id'], "tabela" => $result['tabela']);
            }            
        } else{
            $data = null;
        }
        
        return $data;
    }
    
    /**
     * Atualiza a data de Remessa.
     * @param array $ids
     * @return affected_rows
     */
    public function atualizarContratoRemessa($ids){
        $sql = "UPDATE
                    contrato_alteracao_seguradora
                SET
                    caseremessa = now()
                WHERE
                    caseoid in($ids);";
                    
        $result = pg_query($this->conn, $sql);
        $result = pg_affected_rows($result);
        
        return (int) $result;
    }
    
    /**
     * Atualiza a data de instalacao.
     * @param array $ids
     * @return affected_rows
     */
    public function atualizarArquivoSeguradora($ids){
        $sql = "UPDATE
                    arquivo_seguradora
                SET
                    asegremessa = now()
                WHERE
                    asegoid in($ids);";
                    
        $result = pg_query($this->conn, $sql);
        $result = pg_affected_rows($result);
        
        return (int) $result;
    }
    
    /**
     * @param int $tipo_dispositivo
     * @param int $cod_veiculo
     * @param Strig $marca
     * @param int $cod_modelo_veiculo
     * @param Strig $modelo
     * @param int $cod_cpf_cnpj_segurado
     * @param int $cod_estabelecimento
     * @param int $cod_digito_segurado
     * @param int $cod_local
     * @param int $cod_sublocal
     * @param int $cod_corretor
     * @param int $cod_laudo_af
     * @return affected_rows
     */
    public function atualizarDadosProposta($prpsoid, $tipo_dispositivo, $cod_veiculo, $marca, $cod_modelo_veiculo, $modelo, $cod_cpf_cnpj_segurado, $cod_estabelecimento, $cod_digito_segurado, $cod_local, $cod_sublocal, $cod_corretor, $cod_laudo_af){
        $sql = "UPDATE
                    proposta_seguradora
                SET
                    prpstipo_dispositivo_af   = $tipo_dispositivo,
                    prpscod_veiculo           = $cod_veiculo,
                    prpsmarca                 = '$marca',
                    prpscod_modelo_veiculo    = $cod_modelo_veiculo,
                    prpsmodelo                = '$modelo',
                    prpscod_cpf_cnpj_segurado = $cod_cpf_cnpj_segurado,
                    prpscod_estabelecimento   = $cod_estabelecimento,
                    prpscod_digito_segurado   = $cod_digito_segurado,
                    prpscod_local             = $cod_local,
                    prpscod_sublocal          = $cod_sublocal,
                    prpscod_corretor          = $cod_corretor,
                    prpscod_laudo_af          = $cod_laudo_af
                WHERE
                    prpsoid = $prpsoid;";
                    
        $result = pg_query($this->conn, $sql);
        $result = pg_affected_rows($result);
        
        return (int) $result;
    }
}
?>