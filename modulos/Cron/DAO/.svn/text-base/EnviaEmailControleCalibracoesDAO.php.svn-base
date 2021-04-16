<?php

/**
 * @class EnviaEmailControleCalibracoesDAO
 * @author Angelo Frizzo Junior <angelo.frizzo@meta.com.br>
 * @since 02/05/2013
 * Camada de regras de persistência de dados.
 */
class EnviaEmailControleCalibracoesDAO {

    private $conn;
    
    /**
     * @author  Angelo Frizzo Junior <angelo.frizzo@meta.com.br>
     * Lista os equipamentos do controle de calibração na base sascar com identificação
     * IGUAL a Engenharia, Desenvolvimento ou Pesquisa e Desenvovlimento
     */
    public function getEquipamentosCalibracaoEng() {
        
    	$sql = "SELECT
						maqoid,
        				maqdescricao,
        				mqmadescricao,
        				mqmodescricao,
        				maqidentificacao,
        				maqidentcodigo,
						(SELECT to_char(mqhultima,'dd/mm/yyyy') as data
							FROM maquina_historico
							WHERE mqhmaqoid=maqoid and mqhusuoidexclusao is null
							ORDER BY mqhcadastro DESC
							LIMIT 1) as mqhultima,
						(SELECT to_char(mqhproxima,'dd/mm/yyyy') as data
							FROM maquina_historico
							WHERE mqhmaqoid=maqoid and mqhusuoidexclusao is null
							ORDER BY mqhcadastro DESC
							LIMIT 1) as mqhproxima,
						(SELECT mqhstatus
							FROM maquina_historico
							WHERE mqhmaqoid=maqoid and mqhusuoidexclusao is null
							ORDER BY mqhcadastro DESC
							LIMIT 1) as mqhstatus,
        				maqstatus,
        				maqperiodicidade
       				FROM
						maquina, maquina_modelo, maquina_marca
					WHERE
						maqmqmooid=mqmooid
						AND mqmomqmaoid=mqmaoid
						AND maqexclusao IS NULL 
        				AND ((maqidentificacao ilike 'engenharia') OR (maqidentificacao ilike 'desenvolvimento') OR (maqidentificacao ilike 'pesquisa e desenvolvimento'))
       				ORDER BY 
						maqidentificacao,maqdescricao ";
        		
		$resu_pesq = pg_query($this->conn,$sql);
        
		$resultado_pesqmaquina_eng = array();
       	
		$num_rows = pg_num_rows($resu_pesq);
		
        for ($i = 0; $i < $num_rows; $i++) {
			$resultado_pesqmaquina_eng[$i]['maqoid'] = pg_fetch_result($resu_pesq, $i, 'maqoid');
            $resultado_pesqmaquina_eng[$i]['maqdescricao'] = pg_fetch_result($resu_pesq, $i, 'maqdescricao');	            
            $resultado_pesqmaquina_eng[$i]['mqmadescricao'] = pg_fetch_result($resu_pesq, $i, 'mqmadescricao');
            $resultado_pesqmaquina_eng[$i]['mqmodescricao'] = pg_fetch_result($resu_pesq, $i, 'mqmodescricao');
            $resultado_pesqmaquina_eng[$i]['maqidentificacao'] = pg_fetch_result($resu_pesq, $i, 'maqidentificacao');
            $resultado_pesqmaquina_eng[$i]['maqidentcodigo'] = pg_fetch_result($resu_pesq, $i, 'maqidentcodigo');
            $resultado_pesqmaquina_eng[$i]['mqhultima'] = pg_fetch_result($resu_pesq, $i, 'mqhultima');
            $resultado_pesqmaquina_eng[$i]['mqhproxima'] = pg_fetch_result($resu_pesq, $i, 'mqhproxima');
            $resultado_pesqmaquina_eng[$i]['mqhstatus'] = pg_fetch_result($resu_pesq, $i, 'mqhstatus');
            $resultado_pesqmaquina_eng[$i]['maqstatus'] = pg_fetch_result($resu_pesq, $i, 'maqstatus');
            $resultado_pesqmaquina_eng[$i]['maqperiodicidade'] = pg_fetch_result($resu_pesq, $i, 'maqperiodicidade');
        }
        
        return $resultado_pesqmaquina_eng;
    }
    
    /**
     * @author  Angelo Frizzo Junior <angelo.frizzo@meta.com.br>
     * Lista os equipamentos do controle de calibração na base sascar com identificação
     * DIFERENTE de Engenharia ou Desenvolvimento
     */
    public function getEquipamentosCalibracaoNotEng() {

    	$sql = "SELECT
						maqoid,
        				maqdescricao,
        				mqmadescricao,
        				mqmodescricao,
        				maqidentificacao,
        				maqidentcodigo,
						(SELECT to_char(mqhultima,'dd/mm/yyyy') as data
							FROM maquina_historico
							WHERE mqhmaqoid=maqoid and mqhusuoidexclusao is null
							ORDER BY mqhcadastro DESC
							LIMIT 1) as mqhultima,
						(SELECT to_char(mqhproxima,'dd/mm/yyyy') as data
							FROM maquina_historico
							WHERE mqhmaqoid=maqoid and mqhusuoidexclusao is null
							ORDER BY mqhcadastro DESC
							LIMIT 1) as mqhproxima,
						(SELECT mqhstatus
							FROM maquina_historico
							WHERE mqhmaqoid=maqoid and mqhusuoidexclusao is null
							ORDER BY mqhcadastro DESC
							LIMIT 1) as mqhstatus,
        				maqstatus,
        				maqperiodicidade
       				FROM
						maquina, maquina_modelo, maquina_marca
					WHERE
						maqmqmooid=mqmooid
						AND mqmomqmaoid=mqmaoid
						AND maqexclusao IS NULL
        				AND ((maqidentificacao not ilike 'engenharia') AND (maqidentificacao not ilike 'desenvolvimento'))
       				ORDER BY
						maqidentificacao,maqdescricao ";
        
        $resu_pesq_2 = pg_query($this->conn,$sql);
        
        $resultado_pesqmaquina_not_eng = array();
        
        $num_rows_2 = pg_num_rows($resu_pesq_2);
        
        for ($i = 0; $i < $num_rows_2; $i++) {
        	$resultado_pesqmaquina_not_eng[$i]['maqoid'] = pg_fetch_result($resu_pesq_2, $i, 'maqoid');
        	$resultado_pesqmaquina_not_eng[$i]['maqdescricao'] = pg_fetch_result($resu_pesq_2, $i, 'maqdescricao');
        	$resultado_pesqmaquina_not_eng[$i]['mqmadescricao'] = pg_fetch_result($resu_pesq_2, $i, 'mqmadescricao');
        	$resultado_pesqmaquina_not_eng[$i]['mqmodescricao'] = pg_fetch_result($resu_pesq_2, $i, 'mqmodescricao');
        	$resultado_pesqmaquina_not_eng[$i]['maqidentificacao'] = pg_fetch_result($resu_pesq_2, $i, 'maqidentificacao');
        	$resultado_pesqmaquina_not_eng[$i]['maqidentcodigo'] = pg_fetch_result($resu_pesq_2, $i, 'maqidentcodigo');
        	$resultado_pesqmaquina_not_eng[$i]['mqhultima'] = pg_fetch_result($resu_pesq_2, $i, 'mqhultima');
        	$resultado_pesqmaquina_not_eng[$i]['mqhproxima'] = pg_fetch_result($resu_pesq_2, $i, 'mqhproxima');
        	$resultado_pesqmaquina_not_eng[$i]['mqhstatus'] = pg_fetch_result($resu_pesq_2, $i, 'mqhstatus');
        	$resultado_pesqmaquina_not_eng[$i]['maqstatus'] = pg_fetch_result($resu_pesq_2, $i, 'maqstatus');
        	$resultado_pesqmaquina_not_eng[$i]['maqperiodicidade'] = pg_fetch_result($resu_pesq_2, $i, 'maqperiodicidade');
        }
        
        return $resultado_pesqmaquina_not_eng;
    }
    
    /**
     * @author  Angelo Frizzo Junior <angelo.frizzo@meta.com.br>
     * @param $tipo_lista_email => String contendo tipo lista de email (grupo ENG ou NOT_ENG)
     * Lista os emails dos destinatarios constantes na base sascar
     */
    public function getDestinatariosEmail($tipo_lista_email) {

    	if ($tipo_lista_email == 'NOT_ENG') {
    		
    		$filtro = "	AND (departamento.depoid  IN (SELECT depoid FROM departamento WHERE depdescricao ilike '%laboratório%')
						AND (perfil_rh.prhoid IN (SELECT prhoid FROM perfil_rh WHERE prhperfil ilike '%eletrônica%')))";
    	
    	} else { // $tipo_lista_email == 'ENG'
    		
    		$filtro = "	AND (departamento.depoid  IN (SELECT depoid FROM departamento WHERE depdescricao ilike '%engenharia%')
						AND (perfil_rh.prhoid IN (SELECT prhoid FROM perfil_rh WHERE prhperfil 
    						ilike 'Tecnico em Eletronica PL' OR prhperfil 
    						ilike 'Coordenador de P&D' OR prhperfil 
    						ilike 'Engenheiro Senior')))";
    	}
    	
    	$sql="	SELECT
					usuemail
				FROM
					usuarios
					INNER JOIN funcionario ON funcionario.funoid=usuarios.usufunoid
					INNER JOIN perfil_rh ON perfil_rh.prhoid=funcionario.funcargo
					INNER JOIN departamento ON departamento.depoid=perfil_rh.prhdepoid
				WHERE
					usuarios.dt_exclusao IS NULL
					AND departamento.depexclusao IS NULL
					AND perfil_rh.prhexclusao IS NULL
					$filtro
				ORDER BY cd_usuario ";
    	
    	$res = pg_query($this->conn,$sql);
    	
    	$resultado = array();
    	
    	for($x=0;$x < pg_num_rows($res) ; $x++){
    		$resultado[]=pg_fetch_result($res,$x,"usuemail");
    	}
    	 
        return $resultado;
		
    }
    
    public function __construct() {

        global $conn;

        $this->conn = $conn;
    }
    
    public function __get($var) {
        return $this->$var;
    }

}