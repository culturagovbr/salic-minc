<?php

class Parecer_Model_DbTable_TbAnaliseDeConteudo extends MinC_Db_Table_Abstract
{
    protected $_banco  = "SAC";
    protected $_schema = "SAC";
    protected $_name   = "tbAnaliseDeConteudo";

    /**
     * Metodo para cadastrar
     * @access public
     * @param array $dados
     * @return integer (retorna o �ltimo id cadastrado)
     */
    public function cadastrarDados($dados)
    {
        return $this->insert($dados);
    } // fecha metodo cadastrarDados()


    /**
     * Metodo para alterar
     * @access public
     * @param array $dados
     * @param integer $where
     * @return integer (quantidade de registros alterados)
     */
    public function alterarDados($dados, $where)
    {
        $where = "idAnaliseDeConteudo = " . $where;
        return $this->update($dados, $where);
    } // fecha metodo alterarDados()


    public function buscarOutrasInformacoes($idPronac)
    {
        $select =  new Zend_Db_Expr("
                SELECT idPRONAC,p.Descricao AS Produto,
                  CASE
                     WHEN Artigo18 = 1
                          THEN 'Artigo 18'
                          ELSE 'Artigo 26'
                     END as Enquadramento,
                   CASE
                      WHEN IncisoArtigo27_I = 0
                           THEN 'N&atilde;o'
                           ELSE 'Sim'
                      END as IncisoArtigo27_I,
                  CASE
                      WHEN IncisoArtigo27_II = 0
                           THEN 'N&atilde;o'
                           ELSE 'Sim'
                      END as IncisoArtigo27_II,
                   CASE
                      WHEN IncisoArtigo27_III = 0
                           THEN 'N&atilde;o'
                           ELSE 'Sim'
                      END as IncisoArtigo27_III,
                   CASE
                      WHEN IncisoArtigo27_IV = 0
                           THEN 'N&atilde;o'
                           ELSE 'Sim'
                      END as IncisoArtigo27_IV
            FROM tbAnaliseDeConteudo a
            INNER JOIN Produto p ON (a.idProduto = p.Codigo)
            WHERE idPronac = $idPronac ORDER BY 2");
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
        return $db->fetchAll($select);
    }

    public function cidadoBuscarOutrasInformacoes($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array(
                new Zend_Db_Expr("
                    a.idPronac,
                    p.Descricao AS Produto,
                    CASE
                        WHEN Artigo18 = 1
                            THEN 'Artigo 18'
                            ELSE 'Artigo 26'
                        END as Enquadramento,
                    CASE
                        WHEN IncisoArtigo27_I = 0
                            THEN 'N&atilde;o'
                            ELSE 'Sim'
                        END as IncisoArtigo27_I,
                    CASE
                        WHEN IncisoArtigo27_II = 0
                            THEN 'N&atilde;o'
                            ELSE 'Sim'
                        END as IncisoArtigo27_II,
                    CASE
                        WHEN IncisoArtigo27_III = 0
                            THEN 'N&atilde;o'
                            ELSE 'Sim'
                        END as IncisoArtigo27_III,
                    CASE
                        WHEN IncisoArtigo27_IV = 0
                            THEN 'N&atilde;o'
                            ELSE 'Sim'
                        END as IncisoArtigo27_IV
                ")
            )
        );
        $select->joinInner(
            array('p' => 'Produto'),
            'a.idProduto = p.Codigo',
            array(''),
            'SAC.dbo'
        );
        $select->where('a.idPronac = ?', $idPronac);

        
        return $this->fetchAll($select);
    }

    /**
     * @param $idPreProjeto
     * @param $idPronac
     * @return Zend_Db_Statement_Interface
     * @todo adicionar o parecerFavoravel igual a sim
     */
    public function inserirAnaliseConteudoParaParecerista($idPreProjeto, $idPronac)
    {
        $sqlAnaliseDeConteudo = "INSERT INTO SAC.dbo.tbAnaliseDeConteudo (idPronac,idProduto, ParecerFavoravel)
                                         SELECT {$idPronac},idProduto, 1 FROM SAC.dbo.tbPlanilhaProposta
                                          WHERE idProjeto = {$idPreProjeto} AND idProduto <> 0
                                          GROUP BY idProduto";

        $db= Zend_Db_Table::getDefaultAdapter();
        return $db->query($sqlAnaliseDeConteudo);
    }

    public function dadosAnaliseconteudo($idpronac, $where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            $this,
            array(
                'IdPRONAC',
                'idAnaliseDeConteudo',
                'idProduto',
                'Lei8313',
                'Artigo3',
                'IncisoArtigo3',
                'AlineaArtigo3',
                'Artigo18',
                'AlineaArtigo18',
                'Artigo26',
                'Lei5761',
                'Artigo27',
                'IncisoArtigo27_I',
                'IncisoArtigo27_II',
                'IncisoArtigo27_III',
                'IncisoArtigo27_IV',
                'ParecerFavoravel',
                new Zend_Db_Expr('cast(ParecerDeConteudo as TEXT) as ParecerDeConteudo')
            )
        );
        if ($idpronac) {
            $select->where('IdPRONAC = ?', $idpronac);
        } else {
            foreach ($where as $key=>$val) {
                $select->where($key, $val);
            }
        }
        return $this->fetchAll($select);
    }
}
