<?php

/**
 * Class Agente_Model_InternetMapper
 *
 * @name Agente_Model_InternetMapper
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
class Agente_Model_InternetMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Agente_Model_DbTable_Internet');
    }

    public function saveCustom($arrData)
    {
        $rowsDeleted = $this->deleteBy(array('idagente' => $arrData['idagente']));

        # cadastra todos os e-mails
        $arrId = array();
        for ($i = 0; $i < sizeof($arrData['emails']); $i++) {
            $arrayEmail = array(
                'idagente' => $arrData['idagente'],
                'tipointernet' => $arrData['tipoemails'][$i],
                'descricao' => $arrData['emails'][$i],
                'status' => $arrData['enviaremails'][$i],
                'divulgar' => $arrData['divulgaremails'][$i],
                'usuario' => $arrData['usuario']
            );
            $arrId[] = $this->save(new Agente_Model_Internet($arrayEmail));
        }

        return $arrId;
    }

    public function save(Agente_Model_Internet $model)
    {
        return parent::save($model);
    }
}
