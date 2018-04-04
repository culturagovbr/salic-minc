<?php

/**
 * Class Agente_Model_TelefonesMapper
 *
 * @name Agente_Model_TelefonesMapper
 * @package Modules/Agente
 * @subpackage Models
 * @version $Id$
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 05/09/2016
 *
 * @copyright © 2012 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://salic.cultura.gov.br
 */
class Agente_Model_TelefonesMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Agente_Model_DbTable_Telefones');
    }

    public function saveCustom($arrData)
    {
        $rowsDeleted = $this->deleteBy(array('idagente' => $arrData['idagente']));

        # cadastra todos os telefones
        $arrId = array();
        for ($i = 0; $i < sizeof($arrData['fones']); $i++) {
            $arrData = array(
                'idagente' => $arrData['idagente'],
                'tipotelefone' => $arrData['tipofones'][$i],
                'uf' => $arrData['uffones'][$i],
                'ddd' => $arrData['dddfones'][$i],
                'numero' => $arrData['fones'][$i],
                'divulgar' => $arrData['divulgarfones'][$i],
                'intidusuario' => $arrData['intidusuario']);
            $arrId[] = $this->save(new Agente_Model_Telefones($arrData));
        }
        return $arrId;
    }

    public function save(Agente_Model_Telefones $model)
    {
        return parent::save($model);
    }
}
