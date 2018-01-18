<?php

class Agente_Model_DbTable_Visao extends MinC_Db_Table_Abstract
{
    /**
     * _schema
     *
     * @var string
     * @access protected
     */
    protected $_schema = 'agentes';

    /**
     * _name
     *
     * @var bool
     * @access protected
     */
    protected $_name = 'visao';

    /**
     * _primary
     *
     * @var bool
     * @access protected
     */
    protected $_primary = 'idVisao';

    /**
     * @access public
     * @param integer $idAgente
     * @param integer $visao
     * @param boolean $todasVisoes
     * @return array
     */
    public function buscarVisao($idAgente = null, $visao = null, $todasVisoes = false)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        if ($todasVisoes) {
            $sql = "select distinct idVerificacao, Descricao from  " . GenericModel::getStaticTableName('agentes', 'verificacao') . "  where idtipo = 16 and sistema = 21 ";
            $dados = $db->fetchAll($sql);
        } else {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);

            $objSelect = $db->select();
            $objSelect->from(
                array('vis' => 'visao'),
                array('idvisao', 'idagente', 'usuario', 'stativo', 'Visao'),
                $this->getSchema('agentes')
            );
            $objSelect->joinInner(
                array('ver' => 'verificacao'),
                "ver.idVerificacao = vis.visao",
                array('ver.Descricao', 'ver.idVerificacao'),
                $this->getSchema('agentes')
            );
            $objSelect->joinLeft(
                array('ttc' => 'tbtitulacaoconselheiro'),
                "ttc.idagente =  vis.idagente",
                array(),
                $this->getSchema('agentes')
            );
            $objSelect->joinLeft(
                array('ar' => 'area'),
                "ttc.cdArea = ar.Codigo",
                array('area' => 'ar.descricao'),
                $this->getSchema('sac')
            );
            $objSelect->where('ver.idVerificacao = vis.visao');
            $objSelect->where('ver.idtipo = ? ', 16);
            $objSelect->where('sistema = ? ', 21);

            if (!empty($idAgente)) {
                $objSelect->where('vis.idagente = ? ', $idAgente);
            }

            if (!empty($visao)) {
                $objSelect->where('vis.visao = ? ', $visao);
            }
            $objSelect->order("2");
            $dados = $db->fetchAll($objSelect);
        }
        return $dados;
    }


    public function cadastrarVisao($dados)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        $schema = $this->getSchema('agentes') . '.' . $this->_name;
        $insert = $db->insert($schema, $dados);

        return $insert ? true : false;
    }

    public function buscarVisoes($visao = null)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);

        $objSelect = $db->select();
        $objSelect->from(
            array('ver' => 'verificacao'),
            array('idVerificacao', 'Descricao'),
            $this->getSchema('agentes')
        );

        if (!empty($visao)) {
            $objSelect->where('idVerificacao = ? ', $visao);
        } else {
            $objSelect->where('idtipo = ? ', 16);
            $objSelect->where('sistema = ? ', 21);
        }

        $objSelect->limit(100);

        return $db->fetchAll($objSelect);
    }

    /**
     * @param integer $idAgente
     * @param array $dados
     * @return boolean
     */
    public function alterarVisao($idAgente, $dados)
    {
        $where = "idAgente = " . $idAgente;

        $update = $this->update($dados, $where);
        if ($update) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param integer $idAgente
     * @return boolean
     */
    public function excluirVisao($idAgente)
    {
        $where = "idAgente = " . $idAgente; // condi��o para exclus�o
        $delete = $this->delete($where); // exclui
        if ($delete) {
            return true;
        }
    }
}
