<?php

/**
 * Class Agente_Model_DbTable_Verificacao
 *
 * @name Agente_Model_DbTable_Verificacao
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
class Agente_Model_DbTable_Verificacao extends MinC_Db_Table_Abstract
{
    protected $_banco = 'agentes';
    protected $_schema = 'agentes';
    protected $_name = 'verificacao';

    const PROPOSTA_PARA_ANALISE_INICIAL = 96;
    const PROPOSTA_EM_CONFORMIDADE_VISUAL_OU_ANÁLISE_DOCUMENTAL = 97;
    const PROPOSTA_EM_ANALISE_FINAL = 128;

    public function combosNatureza($idTipo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array($this->_name), // 'v' => $this->_schema . '.' . $this->_name),
            array('idVerificacao', 'Descricao')
        );
        $select->where('idTipo = ?', $idTipo);
        return $this->fetchAll($select);
    }
}
