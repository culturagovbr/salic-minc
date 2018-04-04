<?php

/**
 * Class Agente_Model_DbTable_Vinculacao
 *
 * @name Agente_Model_DbTable_Vinculacao
 * @package Modules/Agente
 * @subpackage Models/DbTable
 * @version $Id$
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 05/09/2016
 *
 * @link http://salic.cultura.gov.br
 */
class Agente_Model_DbTable_Vinculacao extends MinC_Db_Table_Abstract
{
    protected $_name = 'vinculacao';
    protected $_schema = 'agentes';
    protected $_banco = 'agentes';

    public function BuscarVinculos($idAgente)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array('V' => $this->_name),
                array()
        );
        $slct->joinInner(
                array('N' => 'Nomes'),
                'N.idAgente = V.idAgente',
                array('N.Descricao as vinculado')
        );
        $slct->where('V.idVinculoPrincipal = ? ', $idAgente);

        return $this->fetchRow($slct);
    }

    public function verificarDirigente($cpfPropoenente, $cpfProcurador)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array('a' => $this->_name),
                array()
        );
        $slct->joinInner(
                array('b' => 'Agentes'),
                'a.idAgente = b.idAgente',
                array('idAgente'),
            'AGENTES.dbo'
        );
        $slct->joinInner(
                array('c' => 'Agentes'),
                'a.idVinculoPrincipal = c.idAgente',
                array(),
            'AGENTES.dbo'
        );
        $slct->joinInner(
                array('d' => 'Visao'),
                'd.idAgente = a.idAgente',
                array(),
            'AGENTES.dbo'
        );
        $slct->where('b.CNPJCPF = ? ', $cpfProcurador);
        $slct->where('c.CNPJCPF = ? ', $cpfPropoenente);
        $slct->where('d.Visao = ? ', 198);

        return $this->fetchRow($slct);
    }

    public function verificarDirigenteIdAgentes($cpfLogado)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array('a' => $this->_name),
                array(),
            $this->_schema
        );
        $slct->joinInner(
                array('b' => 'Agentes'),
                'a.idAgente = b.idAgente',
                array(),
            $this->_schema
        );
        $slct->joinInner(
                array('c' => 'Agentes'),
                'a.idVinculoPrincipal = c.idAgente',
                array('a.idVinculoPrincipal AS idAgente'),
            $this->_schema
        );
        $slct->joinInner(
                array('d' => 'Visao'),
                'd.idAgente = a.idAgente',
                array(),
            $this->_schema
        );
        $slct->where('b.CNPJCPF = ? ', $cpfLogado);
        $slct->where('d.Visao = ? ', 198);

        return $this->fetchAll($slct);
    }
    
    public function Desvincular($where)
    {
        return $this->delete($where);
    } // fecha m�todo excluirDados()
} // fecha class
