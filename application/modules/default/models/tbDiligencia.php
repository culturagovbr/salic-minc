<?php
class tbDiligencia extends MinC_Db_Table_Abstract
{
    protected $_banco   = "SAC";
    protected $_schema  = "SAC";
    protected $_name    = "tbDiligencia";

    /**
    * M�todo para cadastrar
    * @access public
    * @param array $dados
    * @return integer (retorna o �ltimo id cadastrado)
    */
    public function cadastrarDados($dados)
    {
        return $this->insert($dados);
    }

    /**
     * M�todo para buscarDados
     * @access public
     * @param array $dados
     * @return array
     */
    public function buscarDados($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('d' => $this->_name),
                array("*")
        );

        $select->where("d.idPronac = ".$idPronac);
        $select->where("stEstado = 0");
        $select->where("DtResposta IS NULL");
        return $this->fetchAll($select);
    } // fecha m�todo buscarDados()

    public function buscaDiligencia($idPronac=null, $idProduto=null, $idSolicitante=null, $idDiligencia=null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('d' => $this->_name),
                        array('d.idPronac',
                              'd.idDiligencia',
                              'd.idTipoDiligencia',
                              'd.DtSolicitacao',
                              'd.Solicitacao',
                              'd.idSolicitante',
                              'd.DtResposta',
                              'd.Resposta',
                              'd.idProponente',
                              'd.stEstado',
                              'd.idPlanoDistribuicao',
                              'd.idArquivo',
                              'd.idCodigoDocumentosExigidos',
                              'd.idProduto',
                              'd.stProrrogacao',
                              'd.stEnviado')
        );

        $select->joinInner(
                array('p'=>'Projetos'),
            'd.idPronac = p.IdPRONAC',
                array(new Zend_Db_Expr('(p.AnoProjeto + p.Sequencial) AS PRONAC'),
                      'p.NomeProjeto')
        );

        $select->joinLeft(
                array('doc'=>'DocumentosExigidos'),
            'd.idCodigoDocumentosExigidos = doc.Codigo',
                array('doc.Opcao',
                      'doc.Descricao as DocumentosExigidos')
        );

        $select->joinInner(
                array('v'=>'Verificacao'),
            'd.idTipoDiligencia = v.idVerificacao',
                array('v.Descricao')
        );

        $select->joinLeft(
                array('a'=>'tbArquivo'),
            'd.idArquivo = a.idArquivo',
                array('a.nmArquivo','a.sgExtensao'),
                'BDCORPORATIVO.scCorp'
        );




        if ($idPronac !=null || $idProduto !=null || $idSolicitante !=null || $idDiligencia !=null) {
            if ($idPronac !=null) {
                $select->where('d.idPronac = ?', $idPronac);
            }
            if ($idProduto !=null) {
                $select->where('d.idProduto = ?', $idProduto);
            }
            if ($idSolicitante !=null) {
                $select->where('d.idSolicitante = ?', $idSolicitante);
            }
            if ($idDiligencia !=null) {
                $select->where('d.idDiligencia = ?', $idDiligencia);
            }
        }

        return $this->fetchAll($select);
    }


    /**
     * M�todo para alterar
     * @access public
     * @param array $dados
     * @param integer $where
     * @return integer (quantidade de registros alterados)
     */
    public function alterarDados($dados, $where)
    {
        $where = "idDistribuirParecer = " . $where;
        return $this->update($dados, $where);
    } // fecha m�todo alterarDados()


    /**
     * M�todo para excluir
     * @access public
     * @param integer $where
     * @return integer (quantidade de registros exclu�dos)
     */
    public function excluirDados($where)
    {
        $where = "idDistribuirParecer = " . $where;
        return $this->delete($where);
    } // fecha m�todo excluirDados()


    /**
     * Fun��o para checar o status da dilig�ncia
     */
    public function fnChecarDiligencia($idPronac)
    {
        $sql = 'SELECT SAC.dbo.fnchecarDiligencia('. $idPronac .') AS Diligencia';

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo fnChecarDiligencia()
}
