<?php

/**
 * Class Agente_Model_EnderecoNacionalMapper
 *
 * @name Agente_Model_EnderecoNacionalMapper
 * @package Modules/Agente
 * @subpackage Models
 * @version $Id$
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 06/09/2016
 *
 * @copyright © 2012 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://salic.cultura.gov.br
 */
class Agente_Model_EnderecoNacionalMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Agente_Model_DbTable_EnderecoNacional');
    }

    public function saveCustom($arrData)
    {
        $rowsDeleted = $this->deleteBy(array('idagente' => $arrData['idagente']));
        if (!empty($arrData['correspondenciaenderecos'])) {
            $correspondenciaEnderecos = $arrData['correspondenciaEnderecos'];
        } else {
            $correspondenciaEnderecos = 0;
        }

        # cadastra todos os telefones
        $arrId = array();
        for ($i = 0; $i < sizeof($arrData['ceps']); $i++) {
            $arrayEnderecos = array(
                'idagente' => $arrData['idagente'],
                'cep' => str_replace(".", "", str_replace("-", "", $arrData['ceps'][$i])),
                'tipoendereco' => $arrData['tipoenderecos'][$i],
                'uf' => $arrData['ufs'][$i],
                'cidade' => $arrData['cidades'][$i],
                'logradouro' => $arrData['logradouros'][$i],
                'divulgar' => $arrData['divulgarEnderecos'][$i],
                'tipologradouro' => $arrData['tipologradouros'][$i],
                'numero' => $arrData['numeros'][$i],
                'complemento' => $arrData['complementos'][$i],
                'bairro' => $arrData['bairros'][$i],
                'status' => $correspondenciaEnderecos,
                'usuario' => $arrData['IdUsuario']);
            $arrId[] = $this->save(new Agente_Model_EnderecoNacional($arrayEnderecos));
        }

        return $arrId;
    }

    public function save(Agente_Model_EnderecoNacional $model)
    {
        return parent::save($model);
    }
}
