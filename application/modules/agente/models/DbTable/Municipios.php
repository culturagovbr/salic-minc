<?php

/**
 * Class Agente_Model_DbTable_Municipios
 *
 * @name Agente_Model_DbTable_Municipios
 * @package Modules/Agente
 * @subpackage Models/DbTable
 * @version $Id$
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 01/09/2016
 *
 * @copyright © 2012 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://salic.cultura.gov.br
 */
class Agente_Model_DbTable_Municipios extends MinC_Db_Table_Abstract
{
    protected $_name = 'municipios';
    protected $_schema = 'agentes';
    protected $_primary = 'idMunicipioIBGE';


    public function buscar($where = array(), $order = array(), $tamanho = -1, $inicio = -1)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array($this->_name),
            array('idMunicipioIBGE as id',
            'Descricao'),
            $this->_schema
        );

        if (!empty($idCidade)) {
            $select->where('idMunicipioIBGE = ?', $idCidade);
        }

        $select->order('Descricao');

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Cidades: " . $e->getMessage();
        }

        return $db->fetchAll($select);
    }

    public function listar($idUF, $idCidade = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array($this->_name),
            array('idMunicipioIBGE as id',
                'Descricao'),
            $this->_schema
        );

        $select->where('idufibge = ?', $idUF);

        if (!empty($idCidade)) {
            $select->where('idMunicipioIBGE = ?', $idCidade);
        }

        $select->order('Descricao');

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Cidades: " . $e->getMessage();
        }

        return $db->fetchAll($select);
    }

    public function municipioEstado($where = array())
    {
        $sql = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('m' => $this->_name), array('m.idMunicipioIBGE as idMunicipio', 'm.Descricao as Municipio'), $this->getSchema('agentes'))
            ->joinInner(array('u' => 'uf'), 'm.idUFIBGE = u.iduf', array('u.idUF', 'u.Descricao as Uf'), $this->getSchema('agentes'));

        foreach ($where as $coluna => $valor) {
            $sql->where($coluna . '= ?', $valor);
        }

        $result = $this->fetchRow($sql);
        return ($result) ? $result->toArray() : array();
    }
}
