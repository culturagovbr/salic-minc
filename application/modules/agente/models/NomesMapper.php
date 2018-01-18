<?php

/**
 * Class Agente_Model_NomesMapper
 *
 * @name Agente_Model_NomesMapper
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
class Agente_Model_NomesMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Agente_Model_DbTable_Nomes');
    }

    public function saveCustom($arrData)
    {
        $arrNome = $this->findBy(array('idagente' => $arrData['idagente']));
        $arrData = array(
            'idagente' => $arrData['idagente'],
            'tiponome' => (strlen($arrData['cpf']) == 11 ? 18 : 19), # 18 = pessoa fisica e 19 = pessoa juridica
            'descricao' => $arrData['nome'],
            'status' => 0,
            'usuario' => $arrData['IdUsuario'],
        );
        if ($arrNome) {
            $arrData['idnome'] = $arrNome['idnome'];
        }

        return parent::save(new Agente_Model_Nomes($arrData));
    }
}
