<?php

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
class CadNfAtendimentoDAO {

    private $conn;
    private $usuoid;

    /*
     * Construtor
     * 
     * @autor Willian Ouchi
     * @email willian.ouchi@meta.com.br
     */

    public function CadNfAtendimentoDAO($conn) {

        $this->conn = $conn;
        $this->usuoid = $_SESSION['usuario']['oid'];
    }

    
    public function buscarEquipeUsuario() {

        $sql = "
            SELECT
                eqatetoid As tetoid
            FROM
                equipe_apoio
            WHERE 
                eqadt_exclusao IS NULL
                AND eqausuoid = $this->usuoid
        ";

        $rs = pg_query($this->conn, $sql);

        return $rs;
    }
    
    public function buscarEmailEquipe() {
        $sql = "
            SELECT
                tetemail AS email_equipe
            FROM
                telefone_emergencia_tp
            WHERE
                tetoid = $this->tetoid
            AND
                tetexclusao IS NULL
        ";
        $rs = pg_query($this->conn, $sql);
        
        return pg_fetch_assoc($rs);
                
    }
    
    public function buscarEquipes($tetoid = null) {

        $where = "";

        if ($tetoid) {
            $where .= " AND tetoid =  $tetoid";
        }

        $sql = "
            SELECT
                tetoid,
                tetdescricao
            FROM
                telefone_emergencia_tp
            WHERE 
                tetexclusao IS NULL
                $where
            ORDER BY 
                tetdescricao
        ";
        //echo $sql;
        $rs = pg_query($this->conn, $sql);

        return $rs;
    }

    public function buscarAcionamentos() {
        
        $rs = null;
        
        //$this->buscarEquipeUsuario();
        
        //if ($this->tetoid){
            $sql = "
                SELECT 
                    preroid,
                    to_char(prerdt_atendimento, 'DD/MM/YYYY') AS prerdt_atendimento,
                    prerplaca_veiculo
                FROM 
                    pronta_resposta 
                WHERE 
                    prertetoid = $this->tetoid 
                    AND preroid NOT IN (
                        SELECT 
                            nfacpreroid 
                        FROM 
                            nf_itens_acionamento 
                        WHERE 
                            nfacdt_exclusao IS NULL
                    )
            ";

            $rs = pg_query($this->conn, $sql);
       // }
       
        return $rs;
    }

    public function buscarItensNota() {

        $rs = null;
        $where = "";
        
        if ($this->nfacoid) {
            $where .= " AND nfacoid = $this->nfacoid ";
        }

        if ($this->nfaoid) {
            
            $nfaoid = (!empty($this->nfaoid)) ? $this->nfaoid : $this->nfaoid['nfaoid'];
            
            $sql = "              
                SELECT
                    nfacoid,
                    nfacdt_exclusao,
                    nfacaprovado,
                    to_char(prerdt_atendimento, 'DD/MM/YYYY') AS prerdt_atendimento,                    
                    prerplaca_veiculo
                FROM
                    nf_itens_acionamento
                    INNER JOIN pronta_resposta ON nfacpreroid = preroid
                WHERE
                    nfacnfaoid = $nfaoid
                    AND nfacdt_exclusao IS NULL
                    $where
            ";        

            $rs = pg_query($this->conn, $sql);
        }

        return $rs;
    }
    
    
    /*
    *   Verifica se existem itens da NF reprovados
    *   @autor Willian Ouchi
    *   @email willian.ouchi@meta.com.br     * 
    *   @return resource
    */
    public function nfReprovada() {
        
        $rs = null;
        
        if ($this->nfaoid){
            
            $sql = "              
                SELECT
                    nfacoid as nfs_reprovadas
                FROM
                    nf_itens_acionamento
                WHERE
                    nfacnfaoid = $this->nfaoid
                    AND nfacaprovado = FALSE
                    AND nfacdt_exclusao IS NULL
            ";
            $rs = pg_query($this->conn, $sql);
            //echo $sql;
        }
        return $rs;
    }
    
    
    /*
     * Insere um acionamento no item da NF de atendimento
     * 
     * @autor Willian Ouchi
     * @email willian.ouchi@meta.com.br     * 
     * @return resource
     */
    public function insereAcionamento() {

        $rs = null;
        
        if ($this->nfaoid && $this->preroid) {

            $sql = "              
                INSERT INTO 
                    nf_itens_acionamento
                (
                    nfacnfaoid,
                    nfacpreroid
                )
                VALUES
                (
                    $this->nfaoid,
                    $this->preroid
                )
            ";            
            $rs = pg_query($this->conn, $sql);            
        }
        //echo $sql;
        return $rs;
    }
    
    /*
     * Exclui um acionamento no item da NF de atendimento
     * 
     * @autor Willian Ouchi
     * @email willian.ouchi@meta.com.br     * 
     * @return resource
     */
    public function excluiItemNF() {
        
        $rs = null;
        
        if ($this->nfacoid) {

            $sql = "              
                UPDATE
                    nf_itens_acionamento
                SET
                    nfacdt_exclusao = NOW()
                WHERE
                    nfacoid = $this->nfacoid
            ";            
            $rs = pg_query($this->conn, $sql);            
        }
        //echo $sql;
        return $rs;        
    }
    
    
    /*
     * aprovaItemNF() - Aprova um item da NF de atendimento
     * 
     * @autor Willian Ouchi
     * @email willian.ouchi@meta.com.br     * 
     * @return resource
     */
    public function aprovaItemNF() {
        
        $rs = null;
        
        if ($this->nfacoid) {

            $sql = "              
                UPDATE
                    nf_itens_acionamento
                SET
                    nfacaprovado = TRUE
                WHERE
                    nfacoid = $this->nfacoid
            ";            
            $rs = pg_query($this->conn, $sql);            
        }
        //echo $sql;
        return $rs;        
    }
    
    
    /*
     * reprovaItemNF() - Reprova um item da NF de atendimento
     * 
     * @autor Willian Ouchi
     * @email willian.ouchi@meta.com.br     * 
     * @return resource
     */
    public function reprovaItemNF() {
        
        $rs = null;
        
        if ($this->nfacoid) {

            $sql = "              
                UPDATE
                    nf_itens_acionamento
                SET
                    nfacaprovado = FALSE
                WHERE
                    nfacoid = $this->nfacoid
            ";            
            $rs = pg_query($this->conn, $sql);            
        }
        return $rs;        
    }
    
    
    public function pesquisar() {

        $where = "";

        if ($this->nfaoid) {
            $where .= " AND nfaoid =  $this->nfaoid";
        }

        if ($this->nfadt_nota_ini && $this->nfadt_nota_fin) {
            $where .= " AND (nfadt_nota_inicial BETWEEN '$this->nfadt_nota_ini' AND '$this->nfadt_nota_fin' ";
            $where .= " OR nfadt_nota_final BETWEEN '$this->nfadt_nota_ini' AND '$this->nfadt_nota_fin') ";
        }

        if ($this->tetoid) {
            $where .= " AND nfatetoid = $this->tetoid ";
        }

        if ($this->nfaaprovado) {
            $where .= " AND (SELECT COUNT(nfacoid) FROM nf_itens_acionamento WHERE nfacnfaoid = nfaoid) > 0 AND (SELECT COUNT(nfacoid) FROM nf_itens_acionamento WHERE nfacnfaoid = nfaoid AND nfacdt_exclusao IS NULL AND nfacaprovado = FALSE) = 0 ";
        }

        $sql = "
            SELECT 
                nfaoid,
                nfatetoid,
                tetdescricao,
                to_char(nfadt_nota_inicial, 'DD/MM/YYYY') AS nfadt_nota_inicial,
                to_char(nfadt_nota_final, 'DD/MM/YYYY') AS nfadt_nota_final,
                to_char(nfadt_nota_inicial, 'DD/MM/YYYY') || ' - ' || to_char(nfadt_nota_final, 'DD/MM/YYYY')  AS nfadt_nota,
                nfavalor_fixo,
                nfavalor_unidade_recuperada,
                nfaqtde_recuperada,
                nfatotal_recuperado,
                nfavalor_unidade_nao_recuperado,
                nfaqtde_nao_recuperada,
                nfatotal_nao_recuperado,
                nfavalor_variavel,
                nfaqtde_acionamento_excedente,
                nfavalor_unidade_excedente,
                nfavalor_total,
                to_char(nfadt_previsao_pgto, 'DD/MM/YYYY') AS nfadt_previsao_pgto,                
                CASE (SELECT COUNT(nfacoid) FROM nf_itens_acionamento WHERE nfacnfaoid = nfaoid AND nfacdt_exclusao IS NULL)
		WHEN 0 THEN ''
		ELSE
			CASE (SELECT COUNT(nfacoid) FROM nf_itens_acionamento WHERE nfacnfaoid = nfaoid AND nfacdt_exclusao IS NULL AND nfacaprovado = FALSE)
			WHEN 0 THEN 'V'
			ELSE ''
			END
                END  AS aprovado
            FROM
                nf_atendimento 
                INNER JOIN telefone_emergencia_tp ON tetoid = nfatetoid
            WHERE
                nfadt_exclusao IS NULL                
                $where
            ORDER BY nfaoid
        ";
        $rs = pg_query($this->conn, $sql);
        
        return $rs;
    }

    public function inserir() {

        $sql = "
            INSERT INTO
                nf_atendimento
            (
                nfatetoid,
                nfadt_nota_inicial,
                nfadt_nota_final,
                nfavalor_fixo,
                nfavalor_unidade_recuperada,
                nfaqtde_recuperada,
                nfatotal_recuperado,
                nfavalor_unidade_nao_recuperado,
                nfaqtde_nao_recuperada,
                nfatotal_nao_recuperado,
                nfaqtde_acionamento_excedente,
                nfavalor_unidade_excedente,
                nfavalor_total,
                nfavalor_variavel              
            )
            VALUES
            (
                $this->tetoid,
                '$this->nfadt_nota_inicial',
                '$this->nfadt_nota_final',
                $this->nfavalor_fixo,
                $this->nfavalor_unidade_recuperada,
                $this->nfaqtde_recuperada,
                $this->nfatotal_recuperado,
                $this->nfavalor_unidade_nao_recuperado,
                $this->nfaqtde_nao_recuperada,
                $this->nfatotal_nao_recuperado,
                $this->nfaqtde_acionamento_excedente,
                $this->nfavalor_unidade_excedente,
                $this->nfavalor_total,
                $this->nfavalor_variavel
            )
            RETURNING nfaoid
        ";
        //echo $sql;
        $rs = pg_query($this->conn, $sql);

        return $rs;
    }

    public function editar() {
        
        $campos_update = "";
        if ($this->nfadt_previsao_pgto){
            $this->nfadt_previsao_pgto = "'".$this->nfadt_previsao_pgto."'";
        }
        else{
            $this->nfadt_previsao_pgto = 'null';
        }
        
        $sql = "
            UPDATE
                nf_atendimento
            SET
                nfadt_nota_inicial = '$this->nfadt_nota_inicial',
                nfadt_nota_final = '$this->nfadt_nota_final',
                nfavalor_fixo = $this->nfavalor_fixo,
                nfavalor_unidade_recuperada = $this->nfavalor_unidade_recuperada,
                nfaqtde_recuperada = $this->nfaqtde_recuperada,
                nfatotal_recuperado = $this->nfatotal_recuperado,
                nfavalor_unidade_nao_recuperado = $this->nfavalor_unidade_nao_recuperado,
                nfaqtde_nao_recuperada = $this->nfaqtde_nao_recuperada,
                nfatotal_nao_recuperado = $this->nfatotal_nao_recuperado,
                nfaqtde_acionamento_excedente = $this->nfaqtde_acionamento_excedente,
                nfavalor_unidade_excedente = $this->nfavalor_unidade_excedente,
                nfavalor_total = $this->nfavalor_total,
                nfavalor_variavel = $this->nfavalor_variavel,
                nfadt_previsao_pgto = $this->nfadt_previsao_pgto  
            WHERE
                nfaoid = $this->nfaoid
        ";
        $rs = pg_query($this->conn, $sql);

        return $rs;
    }


/* ANEXOS */

    
    public function inserirAnexo($file_uploaded, $id_nf) {

        $sql = "
            INSERT INTO
                anexo_nf_acionamento
            (
                anfanfaoid,
                anfaarquivo,
                anfausuoid
            )
            VALUES
            (
              $id_nf,
              '{$file_uploaded['name']}',
              {$this->usuoid} 
            )
            RETURNING anfaoid";

        $rs = pg_query($this->conn, $sql);

        return pg_fetch_result($rs, 0, 'anfaoid');
    }

    
    public function getAnexos($id_nf) {

        $sql = "
            SELECT 
                anfaoid as id_anexo,
                anfaarquivo as arquivo,
                nm_usuario as usuario,
                to_char(anfadt_inclusao, 'DD/MM/YYYY') AS data            
            FROM 
                anexo_nf_acionamento
                INNER JOIN usuarios ON anfausuoid = cd_usuario
            WHERE
                anfanfaoid = $id_nf
            AND
                anfadt_exclusao IS NULL
            ORDER BY
                anfadt_inclusao ASC
            ";

        $rs = pg_query($this->conn, $sql);

        return $rs;
    }

    
    public function excluirAnexo($id_anexo) {
        
        $sql = "
            UPDATE 
                anexo_nf_acionamento            
           SET
                anfadt_exclusao = NOW()
           WHERE
                anfaoid = $id_anexo";

        return pg_affected_rows(pg_query($this->conn, $sql));
    }
    
    public function getEquipeNf($id_nf) {
        
        $sql = "
            SELECT
                nfatetoid AS id_equipe
            FROM
                nf_atendimento
            WHERE
                nfaoid = $id_nf;
        ";
        
        $rs = pg_query($this->conn, $sql);

        return pg_fetch_result($rs, 0, 'id_equipe');
        
    }     
    
    
}

