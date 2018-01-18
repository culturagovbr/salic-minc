<?php

/**
 * Class Agente_Model_DbTable_Telefones
 *
 * @name Agente_Model_DbTable_Telefones
 * @package Modules/Agente
 * @subpackage Models/DbTable
 * @version $Id$
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 06/09/2016
 *
 * @copyright © 2012 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://salic.cultura.gov.br
 */
class Agente_Model_DbTable_Telefones extends MinC_Db_Table_Abstract
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
    protected $_name = 'telefones';

    /**
     * _primary
     *
     * @var bool
     * @access protected
     */
    protected $_primary = 'idTelefone';



    /**
     * Metodo para buscar os telefones do agente
     *
     * @access public
     * @static
     * @param integer $idAgente
     * @return object
     */
    public function buscarFones($idAgente = null)
    {
        $tblAgentes = new Agente_Model_DbTable_Agentes();
        $db = Zend_Db_Table::getDefaultAdapter();

        $tl = array(
            'tl.idtelefone',
            'tl.tipotelefone',
            'tl.numero',
            'tl.divulgar',
            new Zend_Db_Expr("
                    CASE
                    WHEN tl.tipotelefone = 22 or tl.tipotelefone = 24
                    THEN 'Residencial'
                    WHEN tl.tipotelefone = 23 or tl.tipotelefone = 25
                    THEN 'Comercial'
                    WHEN tl.tipotelefone = 26
                    THEN 'Celular'
                    WHEN tl.tipotelefone = 27
                    THEN 'Fax'
                    END as dstelefone
            ")
        );

        $ddd = array(
            'ddd.codigo as ddd',
            'ddd.codigo as codigo',
        );

        $sql = $db->select()->distinct()
            ->from(array('tl' => $this->_name), $tl, $this->_schema)
            ->join(array('uf' => 'uf'), 'uf.iduf = tl.uf', array('uf.sigla as ufsigla'), $this->_schema)
            ->join(array('ag' => 'agentes'), 'ag.idagente = tl.idagente', array('ag.idagente'), $this->_schema)
            ->joinLeft(array('ddd' => 'ddd'), 'tl.ddd = ddd.codigo', $ddd, $this->_schema);

        if (!empty($idAgente)) { // busca de acordo com o id do agente
            $sql->where('tl.idagente = ?', $idAgente);
        }

        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }
}
