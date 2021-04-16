<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CadTipoSegmentacaoDAO
 *
 * @author Christian Monterio
 */
class CadTipoSegmentacaoDAO {
    
    private $conn;
	
	public function pesquisar($and) {
        
		$sql = "
			SELECT  
				f.*, 
                '' AS tipo_segmentacao_pai 
			FROM 
				tipo_segmentacao AS f 
            WHERE
                f.tpsdt_exclusao IS NULL 
            AND 
                f.tpssegmentacaooid IS NULL 
            $and

                UNION 

			SELECT  
				f.*, 
                p.tpsdescricao AS tipo_segmentacao_pai 
			FROM 
				tipo_segmentacao AS f 
            LEFT JOIN 
                tipo_segmentacao AS p ON p.tpsoid = f.tpssegmentacaooid 
            WHERE
                f.tpsdt_exclusao IS NULL
            AND 
                f.tpssegmentacaooid IS NOT NULL 
            $and
            ORDER by
                tipo_segmentacao_pai, tpsdescricao ASC             
            ";
        
		$rs = pg_query($this->conn, $sql);
        
        $rows = array();
        while ($row = pg_fetch_object($rs)) {
            
            $chave = !empty($row->tpssegmentacaooid) ? $row->tpssegmentacaooid : $row->tpsoid;
            
            $row->tpsdescricao = utf8_encode($row->tpsdescricao);
            $row->tipo_segmentacao_pai = utf8_encode($row->tipo_segmentacao_pai);
            
            $rows[$chave][] = $row;
        }
        
        $registros = array();
        foreach ($rows as $idPai => $filhos) {
            foreach ($filhos as $filho) {
                $registros[] = $filho;
            }
        }
		
		$resultado = array(
			'numero_resultados' => pg_num_rows($rs),
            'registros' => $registros
		);
        
        return $resultado;	
	}
    
    public function inserir($TipoSegmentacao) {
        
        $TipoSegmentacao->tpsprincipal = $TipoSegmentacao->tpsprincipal == 'sim' ? 'TRUE' : 'FALSE';
        $TipoSegmentacao->tpsdescricao = addslashes(utf8_decode($TipoSegmentacao->tpsdescricao));
        
        pg_query($this->conn, 'BEGIN');
        
        $sql = "
            INSERT INTO
                tipo_segmentacao
            (                
                tpssegmentacaooid, -- Id da segmentação pai - Foreign Key.
                tpsdescricao, -- Descrição da segmentação.
                tpsprincipal, -- Determina se é segmentação principal.
                tpscodigoslug
            )
            VALUES
            (
               $TipoSegmentacao->tpssegmentacao,
               '$TipoSegmentacao->tpsdescricao',
               $TipoSegmentacao->tpsprincipal,
               ''
            )
            RETURNING tpsoid";
        
        $rs = pg_query($this->conn, $sql);
                
        if(!$rs) {
            return false;
        }
                
        $tpsoid = (int)pg_fetch_result($rs, 0, 'tpsoid');
        
        if(empty($tpsoid)) {
            pg_query($this->conn, 'ROLLBACK');
            return false;
        }
        
        $slug_existe = $this->verificaSlugExiste($TipoSegmentacao->tpcodigoslug);        
        
        if($slug_existe) {
            $TipoSegmentacao->tpcodigoslug .= '_' . $tpsoid;
        }
        
        $atualizou = $this->atualizaSlug($tpsoid, $TipoSegmentacao->tpcodigoslug);
        
        if(!$atualizou) {
            pg_query($this->conn, 'ROLLBACK');
            return false;
        }
         
        pg_query($this->conn, 'COMMIT');
        return true;
        
    }
    
    private function verificaSlugExiste($slug) {
        $sql = "
            SELECT
                COUNT(1) AS quantidade
            FROM 
                tipo_segmentacao
            WHERE
                tpscodigoslug = '$slug'
            AND
                tpsdt_exclusao IS NULL
            ";
        
        $rs = pg_query($this->conn, $sql);
        $quantidade = (int)pg_fetch_result($rs, 0, 'quantidade');
        
        return $quantidade > 0 ? true : false;
    }
    
    private function atualizaSlug($tpsoid, $slug) {
        $sql = "
            UPDATE
                tipo_segmentacao
            SET 
                tpscodigoslug = '$slug'
            WHERE
                tpsoid = $tpsoid
            ";
        
        $rs = pg_query($this->conn, $sql);
        
        return pg_affected_rows($rs);
    }
    
    public function atualizar($TipoSegmentacao) {
        
        if(!$TipoSegmentacao->tpsoid) {
            return false;
        }
        
        $TipoSegmentacao->tpsprincipal = $TipoSegmentacao->tpsprincipal == 'sim' ? 'TRUE' : 'FALSE';
        $TipoSegmentacao->tpsdescricao = addslashes(utf8_decode($TipoSegmentacao->tpsdescricao));
        
        pg_query($this->conn, 'BEGIN');
        
        $sql = "
            UPDATE
                tipo_segmentacao
            SET                            
                tpssegmentacaooid = $TipoSegmentacao->tpssegmentacao,
                tpsdescricao = '$TipoSegmentacao->tpsdescricao', 
                tpsprincipal = $TipoSegmentacao->tpsprincipal
            WHERE
                tpsoid = $TipoSegmentacao->tpsoid";
        
        ob_start();
        $rs = pg_query($this->conn, $sql);
        ob_end_clean();
        
        if(!$rs) {
            return false;
        }
        
        $slug_existe = $this->verificaSlugExiste($TipoSegmentacao->tpcodigoslug);        
        
        if($slug_existe) {
            $TipoSegmentacao->tpcodigoslug .= '_' . $TipoSegmentacao->tpsoid;
        }
        
        $atualizou = $this->atualizaSlug($TipoSegmentacao->tpsoid, $TipoSegmentacao->tpcodigoslug);
        
        if(!$atualizou) {
            pg_query($this->conn, 'ROLLBACK');
            return false;
        }
         
        pg_query($this->conn, 'COMMIT');
        return true;
        
    }
    
    
    public function getComboTiposSegmentacao($id = 0) {
        
        $and = !empty($id) ? " AND tpsoid != $id" : '';
      
        $sql = "
            SELECT 
                tpsoid,
                tpsdescricao 
            FROM 
                tipo_segmentacao 
            WHERE
                tpsdt_exclusao IS NULL
            AND
                tpsprincipal = TRUE
            $and
            ORDER BY
                tpsdescricao";
        
        $rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[$i]['tpsoid']       = pg_fetch_result($rs, $i, 'tpsoid');
            $result[$i]['tpsdescricao'] = pg_fetch_result($rs, $i, 'tpsdescricao');
        }
        
        return $result;
        
    }
    
    public function getTipoSegmentacaoById($id, $utf8 = false) {
        
        if(empty($id)) {
            return false;
        }
        
        $sql = "
            SELECT 
                tpsoid,
                tpssegmentacaooid,
                tpsdescricao,
                tpsprincipal,
                tpsdt_exclusao,
                tpsusuoid_exclusao,
                tpscodigoslug
            FROM 
                tipo_segmentacao 
            WHERE   
                tpsoid = $id";
        
        $rs = pg_query($this->conn, $sql);
        
        if(!pg_num_rows($rs) > 0) {
            return false;
        }
        
        $TipoSegmentacao = new stdClass();
        $id_pai = pg_fetch_result($rs, 0, 'tpssegmentacaooid');
        
        $TipoSegmentacao->tpsoid = pg_fetch_result($rs, 0, 'tpsoid');
        $TipoSegmentacao->TipoSegmentacaoPai = $this->getTipoSegmentacaoById($id_pai, $utf8);
        $TipoSegmentacao->tpsdescricao = $utf8 ? utf8_encode(pg_fetch_result($rs, 0, 'tpsdescricao')) : pg_fetch_result($rs, 0, 'tpsdescricao');
        $TipoSegmentacao->tpsprincipal = pg_fetch_result($rs, 0, 'tpsprincipal');
        $TipoSegmentacao->tpsdt_exclusao = pg_fetch_result($rs, 0, 'tpsdt_exclusao');
        $TipoSegmentacao->tpsusuoid_exclusao = pg_fetch_result($rs, 0, 'tpsusuoid_exclusao');
        $TipoSegmentacao->tpscodigoslug = pg_fetch_result($rs, 0, 'tpscodigoslug');
        
        return $TipoSegmentacao;
        
    }
    
    public function excluir($tpsoid) {
                
        $sql = "
            UPDATE 
                tipo_segmentacao
            SET
                tpsdt_exclusao = NOW(),
                tpsusuoid_exclusao = {$_SESSION['usuario']['oid']}
            WHERE
                tpsoid = $tpsoid;
            ";
        
        return pg_affected_rows(pg_query($this->conn, $sql));
    }
    
    public function getQtdFilhosSegmentacao($tpsoid) {
                
        $sql = "
            SELECT 
                COUNT(1) as qtd
            FROM 
                tipo_segmentacao 
            WHERE   
                tpssegmentacaooid = $tpsoid
            AND
                tpsdt_exclusao IS NULL";
        
        $rs = pg_query($this->conn, $sql);
        
        if(!pg_num_rows($rs) > 0) {
            return 0;
        }
        
        return (int) pg_fetch_result($rs, 0, 'qtd');
    }
    
    public function getQtdPaisSegmentacao($tpsoid) {
                
        $sql = "
            SELECT 
                COUNT(1) as qtd
            FROM 
                tipo_segmentacao 
            WHERE   
                tpssegmentacaooid = $tpsoid
            AND
                tpsdt_exclusao IS NULL";
        
        $rs = pg_query($this->conn, $sql);
        
        if(!pg_num_rows($rs) > 0) {
            return 0;
        }
        
        return (int) pg_fetch_result($rs, 0, 'qtd');
    }
    
    public function getQtdRegistrosClientes($tpsoid) {
        
        $sql = "
            SELECT 
                COUNT(1) as qtd
            FROM 
                cliente_segmentacao
            INNER JOIN
                tipo_segmentacao ON clstsgoid = tpsoid
            WHERE   
                clstsgoid  = $tpsoid
            AND
                tpsdt_exclusao IS NULL";
        
        $rs = pg_query($this->conn, $sql);
        
        if(!pg_num_rows($rs) > 0) {
            return 0;
        }
        
        return (int) pg_fetch_result($rs, 0, 'qtd');
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

