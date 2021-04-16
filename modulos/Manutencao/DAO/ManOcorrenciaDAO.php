<?php

class ManOcorrenciaDAO {

	private $conn;
	
	
	public function buscaAcionamentosPorIdOcorrencia($id_ocorrencia){
	
		$sql = "
	 			SELECT 
					p.preroid as id_pronta_resposta,
					t.tetdescricao AS equipe,
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
					p.prerzona_acionamento AS zona,
					p.prerbairro_acionamento AS bairro,
					p.prerrecuperado AS is_recuperado,
					ocooid
				FROM 
					pronta_resposta AS p 
				INNER JOIN 
					telefone_emergencia_tp AS t ON t.tetoid = p.prertetoid 
				INNER JOIN
					ocorrencia ON prerveioid  = ocoveioid
				WHERE 
					ocooid = $id_ocorrencia
				ORDER BY 
					p.prerdt_atendimento";
			$rs = pg_query($this->conn, $sql);
			if (pg_num_rows($rs) > 0) {
				return pg_fetch_all($rs);
			}
			
			return array();
	
	}

	public function __construct() {
        global $conn;

        $this->conn = $conn;
    }

}