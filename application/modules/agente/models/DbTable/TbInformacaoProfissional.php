<?php

/**
 * Class Agente_Model_DbTable_TbInformacaoProfissional
 *
 * @name Agente_Model_DbTable_TbInformacaoProfissional
 * @package Modules/Agente
 * @subpackage Models/DbTable
 * @version $Id$
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 05/10/2016
 *
 * @link http://salic.cultura.gov.br
 */
class Agente_Model_DbTable_TbInformacaoProfissional extends MinC_Db_Table_Abstract
{

    /**
     * _banco
     *
     * @var bool
     * @access protected
     */
    protected $_banco = 'agentes';

    /**
     * _name
     *
     * @var bool
     * @access protected
     */
    protected $_name = 'tbinformacaoprofissional';

    /**
     * _schema
     *
     * @var string
     * @access protected
     */
    protected $_schema = 'agentes';

    public function BuscarInfo($idAgente, $situacao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        
        $select->from(
        
            array('a' => $this->_name),
                      array('*', $this->getExpressionToChar('dtiniciovinculo') . ' as dtinicio',
                          $this->getExpressionToChar('dtfimvinculo') . ' as dtfim'),
            $this->_schema
                      );

        $select->joinLeft(
                array('d'=>'tbdocumento'),
            'd.iddocumento = a.iddocumento',
                array('*'),
            $this->getSchema('bdcorporativo', true, 'sccorp')
        );

        $select->joinLeft(
                array('ta'=>'tbarquivo'),
            'ta.idarquivo = d.idarquivo',
                array('*'),
            $this->getSchema('bdcorporativo', true, 'sccorp')
        );

        $select->joinLeft(
                array('tai'=>'tbarquivoimagem'),
            'tai.idarquivo = ta.idarquivo',
                array('*'),
            $this->getSchema('bdcorporativo', true, 'sccorp')
        );
        
        if (!empty($situacao)) {
            $select->where('a.siinformacao = ?', $situacao);
        }
        
        $select->where('a.idagente = ?', $idAgente);
        
        $select->order('a.dtiniciovinculo');
        
        return $this->fetchAll($select);
    }
    
    public function AnosExperiencia($idAgente)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        
        $select->from(
        
            array('A'=>$this->_name),
                      array('DATEDIFF ( YEAR , dtInicioVinculo , dtFimVinculo ) as qtdAnos')
        );


        $select->where('A.idAgente = ?', $idAgente);
        
        return $this->fetchAll($select);
    }


    public function inserirInfo($dados)
    {
        $insert = $this->insert($dados);
        return $insert;
    }

    public function alteraInfo($dados, $idInfo)
    {
        $where = "idInformacaoProfissional = " . $idInfo;
        
        return $this->update($dados, $where);
    }
}
