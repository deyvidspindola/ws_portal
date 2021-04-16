<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CadAtendimentoProntaRespostaDAO
 *
 * @author ricardo.mota
 */
class CadAtendimentoProntaRespostaDAO {
    
    private $conn;
	
	public function pesquisar($where) {
        
		$sql = "
			SELECT 
                p.preroid AS id_atendimento,    
                t.tetdescricao AS equipe,
                p.prerplaca_veiculo as placa_veiculo,
                to_char(p.prerdt_atendimento, 'DD/MM/YYYY') AS data,
                CASE WHEN p.prertp_ocorrencia = '0'
                     THEN 'Cerca'
                     WHEN p.prertp_ocorrencia = '1'
                     THEN 'Roubo'
                     WHEN p.prertp_ocorrencia = '2'
                     THEN 'Furto'
                     WHEN p.prertp_ocorrencia = '3'
                     THEN 'Suspeita'
                     WHEN p.prertp_ocorrencia = '4'
                     THEN 'Sequestro'
                END  AS tipo,
                p.prercliente AS cliente,
                p.preruf_acionamento AS uf,
                p.prercidade_acionamento AS cidade,
                p.prerrecuperado AS is_recuperado,
                p.preraprovado as aprovado
            FROM 
                pronta_resposta AS p 
            INNER JOIN 
                telefone_emergencia_tp AS t ON t.tetoid = p.prertetoid 
            WHERE
                $where
            ORDER BY 
                p.prerdt_atendimento";
		$rs = pg_query($this->conn, $sql);
		
		if(pg_num_rows($rs) > 0) {
			return pg_fetch_all($rs);
		}
		
		return array();
	
	}
    
    public function salvar() {
        
        $this->cliente = $this->getClientePorPlacaVeiculo();
        
        if(empty($this->preroid)){
           $id = $this->inserir();
           $msg = 'Atendimento inserido com sucesso.';                   
        }else{
           $id = $this->atualizar();
           $msg = 'Atendimento atualizado com sucesso.';                   
        }
        
        return array(
            'preroid'   => $id,
            'msg'       => $msg
        );
        
    }
    
    public function inserir() {
        
        $this->uf = is_numeric($this->uf) ? $this->getEstadoById($this->uf) : $this->uf;
        $this->uf_recup = is_numeric($this->uf_recup) ? $this->getEstadoById($this->uf_recup) : $this->uf_recup;
		
		
        $veioid         = $this->getVeiculoByPlaca($this->veiculo_placa);
        $veioid_carreta = $this->getVeiculoByPlaca($this->carreta_placa);
        
        $equipe_id = $this->getEquipeUsuario();
        
        $sql = "
            INSERT INTO 
                pronta_resposta
            (                
                preraprovado, 
                prerdt_atendimento, 
                prerhora_acionamento, 
                prerhora_chegada,
                prerhora_encerramento, 
                prercep_acionamento, 
                preruf_acionamento,
                prercidade_acionamento,
                prerbairro_acionamento,
                prerendereco_acionamento,
                prernumero_acionamento,                
                prerzona_acionamento,
                prercliente,
                prerlatitude,
                prerlongitude,
                prerusuoid_operador,
                prertp_ocorrencia,
                prerrecuperado,
                prerplaca_veiculo,
                prercor_veiculo,
                prerano_veiculo,
                prermarca_veiculo,                
                prermodelo_veiculo,
                prerplaca_carreta,
                prercor_carreta,
                prerano_carreta,
                prermarca_carreta,
                prermodelo_carreta,
                prercarga_carreta,
                prerplaca_busca,
                prerddescricao,
                prercep_recuperacao,
                preruf_recuperacao,
                prercidade_recuperacao,
                prerbairro_recuperacao,
                prerendereco_recuperacao,
                prernumero_recuperacao,                
                prerzona_recuperacao,
                prerdestino,
                prerveioid,
                prerveioid_carreta
            )
            VALUES
            (                
                $this->aprovacao,
                '$this->data',
                '$this->hora_acionamento',
                '$this->hora_chegada_local',
                '$this->hora_encerramento',
                '$this->cep',
                '$this->uf',
                '$this->cidade',
                '$this->bairro',
                '$this->logradouro',
                '$this->end_numero',    
                '$this->zona',
                '$this->cliente',
                '$this->latitude',
                '$this->longitude',
                $this->operador_sascar,
                $this->tipo_ocorrencia,
                $this->recuperado,
                '$this->veiculo_placa',
                '$this->veiculo_cor',
                '$this->veiculo_ano',
                '$this->veiculo_marca',
                '$this->veiculo_modelo',
                '$this->carreta_placa',
                '$this->carreta_cor',
                '$this->carreta_ano',
                '$this->carreta_marca',
                '$this->carreta_modelo',
                '$this->carreta_carga',
                '$this->placa_veiculo_busca',
                '$this->descricao_ocorrencia',
                '$this->cep_recup',
                '$this->uf_recup',
                '$this->cidade_recup',
                '$this->bairro_recup',
                '$this->logradouro_recup',
                '$this->numero_recup',    
                '$this->zona_recup',
                '$this->destino_veiculo',
                $veioid,
                $veioid_carreta
            )
            RETURNING preroid;
            ";
        
        $rs = pg_query($this->conn, $sql);
        $this->preroid = pg_fetch_result($rs, 0, 'preroid');
        
        
        if(!empty($rs) && !empty($equipe_id)) {
            $this->updateEquipe($equipe_id, $this->preroid);
        }
        
        return $this->preroid;
        
    }
    
    public function atualizar() {
        
        $this->uf = is_numeric($this->uf) ? $this->getEstadoById($this->uf) : $this->uf;
        $this->uf_recup = is_numeric($this->uf_recup) ? $this->getEstadoById($this->uf_recup) : $this->uf_recup;
		
		
        $veioid         = $this->getVeiculoByPlaca($this->veiculo_placa);
        $veioid_carreta = $this->getVeiculoByPlaca($this->carreta_placa);
        
        $equipe_id = $this->getEquipeUsuario();
        
        $sql = "
            UPDATE  
                pronta_resposta
            SET               
                preraprovado = $this->aprovacao, 
                prerdt_atendimento = '$this->data', 
                prerhora_acionamento = '$this->hora_acionamento', 
                prerhora_chegada = '$this->hora_chegada_local',
                prerhora_encerramento = '$this->hora_encerramento', 
                prercep_acionamento = '$this->cep', 
                preruf_acionamento = '$this->uf',
                prercidade_acionamento = '$this->cidade',
                prerbairro_acionamento = '$this->bairro',
                prerendereco_acionamento = '$this->logradouro',
                prernumero_acionamento = '$this->end_numero',                
                prerzona_acionamento = '$this->zona',
                prercliente = '$this->cliente',
                prerlatitude = '$this->latitude',
                prerlongitude = '$this->longitude',
                prerusuoid_operador = $this->operador_sascar,
                prertp_ocorrencia = $this->tipo_ocorrencia,
                prerrecuperado = $this->recuperado,
                prerplaca_veiculo = '$this->veiculo_placa',
                prercor_veiculo = '$this->veiculo_cor',
                prerano_veiculo = '$this->veiculo_ano',
                prermarca_veiculo = '$this->veiculo_marca',                
                prermodelo_veiculo = '$this->veiculo_modelo',
                prerplaca_carreta = '$this->carreta_placa',
                prercor_carreta = '$this->carreta_cor',
                prerano_carreta = '$this->carreta_ano',
                prermarca_carreta = '$this->carreta_marca',
                prermodelo_carreta = '$this->carreta_modelo',
                prercarga_carreta = '$this->carreta_carga',
                prerplaca_busca = '$this->placa_veiculo_busca',
                prerddescricao = '$this->descricao_ocorrencia',
                prercep_recuperacao = '$this->cep_recup',
                preruf_recuperacao = '$this->uf_recup',
                prercidade_recuperacao = '$this->cidade_recup',
                prerbairro_recuperacao = '$this->bairro_recup',
                prerendereco_recuperacao = '$this->logradouro_recup',
                prernumero_recuperacao = '$this->numero_recup',                
                prerzona_recuperacao = '$this->zona_recup',
                prerdestino = '$this->destino_veiculo',
                prerveioid = $veioid,
                prerveioid_carreta = $veioid_carreta
            WHERE
                preroid = $this->preroid
            RETURNING preroid;
            ";
        
        $rs = pg_query($this->conn, $sql);
        
        return pg_fetch_result($rs, 0, 'preroid');
        
    }
    
    private function updateEquipe($equipe_id, $preroid) {
        
       $sql = "
            UPDATE  
                pronta_resposta
            SET               
               prertetoid = $equipe_id
            WHERE
                preroid = $preroid";
        
        pg_query($this->conn, $sql);
        
    }
    
    public function getAtendimentoById($id_atendimento) {
        
        $sql = "
			SELECT 
                p.preroid AS id_atendimento,                
                p.prerplaca_veiculo as placa_veiculo,
                to_char(p.prerdt_atendimento, 'DD/MM/YYYY') AS data_atendimento,
                p.prertp_ocorrencia AS tipo,
                p.prercliente AS cliente,
                p.preruf_acionamento AS uf,
                p.prercidade_acionamento AS cidade,
                p.prerrecuperado AS recuperado,
                p.preraprovado as aprovado,                                
                p.prerhora_acionamento AS hora_acionamento,
                p.prerhora_chegada AS hora_chegada,
                p.prerhora_encerramento AS hora_encerramento,
                p.prercep_acionamento AS cep,
                p.prerendereco_acionamento AS logradouro,
                p.prerbairro_acionamento AS bairro,
                p.prerzona_acionamento AS zona,
                p.prercidade_acionamento AS cidade,
                p.preruf_acionamento AS uf,
                p.prerlatitude AS latitude,
                p.prerlongitude AS longitude,                
                p.prerrecuperado AS recuperado,
                p.prercliente AS cliente,
                p.prerplaca_veiculo AS veiculo_placa,
                p.prerano_veiculo AS veiculo_ano,
                p.prercor_veiculo AS veiculo_cor,
                p.prermarca_veiculo AS veiculo_marca,
                p.prermodelo_veiculo AS veiculo_modelo,
                p.prerplaca_carreta AS carreta_placa,
                p.prerano_carreta AS carreta_ano,
                p.prercor_carreta AS carreta_cor,
                p.prermarca_carreta AS carreta_marca,
                p.prermodelo_carreta AS carreta_modelo,
                p.prerplaca_busca AS placa_busca,
                p.prerddescricao AS descricao,
                p.prercep_recuperacao AS cep_recup,
                p.prerendereco_recuperacao AS logradouro_recup,
                p.prerbairro_recuperacao AS bairro_recup,
                p.prerzona_recuperacao AS zona_recup,
                prercidade_recuperacao AS cidade_recup,
                p.preruf_recuperacao AS uf_recup,
                p.prerdestino AS destino_veiculo,
                p.prerveioid,
                p.prerveioid_carreta,
                p.prercarga_carreta AS carreta_carga,
                p.prerusuoid_operador AS id_operador,
				nm_usuario AS nome_operador,
                p.prernumero_acionamento AS end_numero,
                p.prernumero_recuperacao AS numero_recup
            FROM 
                pronta_resposta AS p   
			INNER JOIN
				usuarios ON p.prerusuoid_operador = cd_usuario
            WHERE
                p.preroid = $id_atendimento
            ";
        
		$rs = pg_query($this->conn, $sql);
		
		if(pg_num_rows($rs) > 0) {
			return pg_fetch_assoc($rs);
		}
		
		return array();
        
    }
    
    private function getClientePorPlacaVeiculo() {
        
        $sql = "
			SELECT 
                clinome
            FROM 
                clientes
            INNER JOIN 
                contrato ON conclioid = clioid
            INNER JOIN
                veiculo ON conveioid = veioid
            WHERE 
                veiplaca = '$this->veiculo_placa'
            AND
                clidt_exclusao IS NULL
            ";
        
		$rs = pg_query($this->conn, $sql);
        $cliente = '';
        
        if(pg_num_rows($rs) > 0) {
            $cliente = pg_fetch_result($rs, 0, 'clinome');
        }
        
        return $cliente;
        
    }
    
    public function verificaCepExiste($cep) {
        
        $sql = "
			SELECT 
                *
            FROM 
                correios_logradouros 
            INNER JOIN 
                correios_localidades ON clgclcoid = clcoid
            WHERE 
                clgcep = '$cep'
            ";
        
		$rs = pg_query($this->conn, $sql);
        
        return pg_num_rows($rs) > 0 ? 'true' : 'false';
        
    }
    
    public function getAnexosAtendimento($id_atendimento) {
        
        $sql = "
			SELECT 
                lauaoid,
                lauapreroid,
                to_char(lauadt_inclusao, 'DD/MM/YYYY') AS data_inclusao,
                CASE WHEN lauatipo_imagem = 1 THEN
                    'Foto'
                ELSE
                    'Documento'
                END AS tipo_arquivo,
                lauaarquivo AS nome_arquivo,
                nm_usuario AS usuario
            FROM 
                laudo_atendimento            
            INNER JOIN
                usuarios ON cd_usuario = lauausuoid
            WHERE
                lauapreroid = $id_atendimento
            AND
                lauadt_exclusao IS NULL
			ORDER BY
				lauatipo_imagem DESC
            ";
        
		$rs = pg_query($this->conn, $sql);
		
		if(pg_num_rows($rs) > 0) {
			return pg_fetch_all($rs);
		}
		
		return array();
        
    }
    
    public function inserirAnexo($file_uploaded, $tipo_arquivo, $id_atendimento) {
        
        $sql = "
            INSERT INTO
                laudo_atendimento
            (
                lauapreroid,
                lauatipo_imagem,
                lauaarquivo,
                lauausuoid
            )
            VALUES
            (
              $id_atendimento,
              $tipo_arquivo,
              '{$file_uploaded['name']}',
              {$_SESSION['usuario']['oid']} 
            )
            RETURNING lauaoid";
        
        $rs = pg_query($this->conn, $sql);
        
        return pg_fetch_result($rs, 0, 'lauaoid');
              
    }
    
    public function excluirAnexo($id_anexo) {
        $sql = "
            UPDATE
                laudo_atendimento
            SET
                lauadt_exclusao = NOW()
            WHERE
                lauaoid = $id_anexo";
        
        return pg_affected_rows(pg_query($this->conn, $sql));
    }
    
    private function getEquipeUsuario() {
        
        $sql = "
            SELECT 
                eqatetoid              
            FROM 
                equipe_apoio                  
            WHERE 
                eqausuoid = {$_SESSION['usuario']['oid']} 
            AND
                eqadt_exclusao IS NULL            
            ";
        
        $rs = pg_query($this->conn, $sql);
        $equipe_id = null;
        
        if(pg_num_rows($rs) > 0) {
            $equipe_id = pg_fetch_result($rs, 0, 'eqatetoid');
        }
        
        return $equipe_id;
        
    }
    
    public function getComboEquipes() {
        
        $join = "";
        $and = "";
        
        if(!$_SESSION['funcao']['permissao_total_ocorrencia']) {
           $join = "INNER JOIN equipe_apoio ON tetoid = eqatetoid";
           $and = "
               AND 
                eqausuoid = {$_SESSION['usuario']['oid']} 
               AND
                eqadt_exclusao IS NULL
            ";
        }
        
        $sql = "
            SELECT 
                tetoid, 
                tetdescricao 
            FROM 
                telefone_emergencia_tp
            $join
            WHERE 
                tetexclusao IS NULL
            $and            
            ORDER BY 
                tetdescricao";
        
        $rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[$i]['tetoid']       = pg_fetch_result($rs, $i, 'tetoid');
            $result[$i]['tetdescricao'] = utf8_encode(pg_fetch_result($rs, $i, 'tetdescricao'));
        }
        
        return $result;
        
    }
    
    public function getDadosVeiculo($placa) {
        
        $sql = "
            SELECT 
                veino_ano AS ano, 
                veicor AS cor,
                mlooid AS id_modelo,
                mcaoid AS id_marca,
                mlomodelo AS modelo,
                mcamarca AS marca
            FROM                 
                veiculo
            LEFT JOIN
                modelo ON mlooid = veimlooid
            LEFT JOIN 
                marca ON mcaoid = mlomcaoid 
            WHERE 
                veiplaca ilike trim('$placa')
            AND
                veidt_exclusao IS NULL
            LIMIT 1
            ";
        
        $rs = pg_query($this->conn, $sql);

        $result = array();
        
        if(pg_num_rows($rs) > 0) {
            
            $rVeiculo = pg_fetch_assoc($rs);
            $result['ano'] = pg_fetch_result($rs, 0, 'ano');
            $result['cor'] = pg_fetch_result($rs, 0, 'cor');
            $result['id_modelo'] = pg_fetch_result($rs, 0, 'id_modelo');
            $result['id_marca'] = pg_fetch_result($rs, 0, 'id_marca');
            $result['modelo'] = pg_fetch_result($rs, 0, 'modelo');
            $result['marca'] = pg_fetch_result($rs, 0, 'marca');
        }
        
        return $result;
        
    }
    
    public function getMarcasVeiculo() {
        $sql = "
            SELECT    
                mcaoid AS id_marca,                
                mcamarca AS descricao_marca                               
            FROM                
                marca            
            WHERE
                mcadt_exclusao IS NULL
            ";
        
        $rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[$i]['id_marca'] = pg_fetch_result($rs, $i, 'id_marca');            
            $result[$i]['descricao_marca'] = pg_fetch_result($rs, $i, 'descricao_marca');
        }
            
        return $result;
    }
    
    public function getModelosByMarca($id_marca) {
        $sql = "
            SELECT                 
                mlooid AS id_modelo,                
                mlomodelo AS modelo   
            FROM                
                modelo
            INNER JOIN
                marca ON mcaoid = mlomcaoid 
            WHERE
                mlodt_exclusao IS NULL
            AND 
                mcaoid = $id_marca
            ";
        
        $rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[$i]['id_modelo'] = pg_fetch_result($rs, $i, 'id_modelo');            
            $result[$i]['modelo'] = utf8_encode(pg_fetch_result($rs, $i, 'modelo'));
        }
            
        return $result;
    }
    
    public function getIdMarca($marca){
    	
    	$sql = "
		    	SELECT
		    		mcaoid AS id_marca
		    	FROM
		    		marca
		    	WHERE
		    		mcadt_exclusao IS NULL
		    	AND
		    		mcamarca ILIKE '%$marca%'";
    	
    	$rs = pg_query($this->conn, $sql);
    	
    	$result = array();
    	
    	if(pg_num_rows($rs) > 0){
    		$result['id_marca'] = pg_fetch_result($rs, 0, 'id_marca');
    	}
    	
    	
    	return $result;
    }
    
    public function getOperadoresSascar() {
        
        
        
        $sql = "
            SELECT        
                cd_usuario AS id_usuario,
                nm_usuario AS nome_usuario
            FROM                
                usuarios
            INNER JOIN
                departamento ON depoid = usudepoid
            WHERE
                depoid = 8
            AND
                dt_exclusao IS NULL
            ORDER BY
                nm_usuario ASC
            
            ";
        
        $rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[$i]['id_usuario'] = pg_fetch_result($rs, $i, 'id_usuario');            
            $result[$i]['nome_usuario'] = pg_fetch_result($rs, $i, 'nome_usuario');
        }
            
        return $result;
        
    }
    
    public function getEstadoById($id_uf) {
        $sql = "
            SELECT        
                estuf AS uf
            FROM                
                estado          
            WHERE
                estoid = $id_uf
            ";
        
        $rs = pg_query($this->conn, $sql);
        
        $uf = 'NULL';
        
        if(pg_num_rows($rs) > 0) {
            $uf = pg_fetch_result($rs, 0, 'uf');     
        }
        
        return $uf;
    }
    
    public function getVeiculoByPlaca($placa) {
        
        if(empty($placa)) {
            return 'NULL';
        }
        
        $sql = "
            SELECT        
                veioid
            FROM                
                veiculo          
            WHERE
                veiplaca = '$placa'
            ";
        
        $rs = pg_query($this->conn, $sql);
        
        $veioid = 'NULL';
        
        if(pg_num_rows($rs) > 0) {
            $veioid = pg_fetch_result($rs, 0, 'veioid');     
        }
        
        return $veioid;
        
    }
    
    public function __construct() {
        global $conn;

        $this->conn = $conn;
    }
    
    public function __set($var, $value) {
        $this->$var = $value;
    }
    
    public function __get($var) {
        return $this->$var;
    }
    
}

