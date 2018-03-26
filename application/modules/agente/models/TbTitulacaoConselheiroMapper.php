<?php

/**
 * Class Agente_Model_TbTitulacaoConselheiroMapper
 *
 * @name Agente_Model_TbTitulacaoConselheiroMapper
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
class Agente_Model_TbTitulacaoConselheiroMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Agente_Model_DbTable_TbTitulacaoConselheiro');
    }

    public function saveCustom($arrData)
    {
        $titular = $arrData['titular'];
        $areaCultural = $arrData['areacultural'];
        $segmentoCultural = $arrData['segmentocultural'];
        $intVisao = $arrData['visao'];
        $idAgente = $arrData['idagente'];

        # so salva area e segmento para a visao de componente da comissao e se os campos titular e areaCultural forem informados
        if ((int) $intVisao == 210 && ((int)$titular == 0 || (int)$titular == 1) && !empty($areaCultural)) {
            # busca a titulacao do agente (titular/suplente de area cultural)
            $arrTitulacaoConselheiro = $this->findBy(array('idagente' => $idAgente));
            $arrData = array(
                'cdarea' => $areaCultural,
                'cdsegmento' => $segmentoCultural,
                'sttitular' => $titular,
                'idagente' => $idAgente
            );
            if (!$arrTitulacaoConselheiro) {
                $arrData['stConselheiro'] = 'A';
            }
            $intId = $this->save(new Agente_Model_TbTitulacaoConselheiro($arrData));
            return $intId;
        }

        return false;
    }

    public function save(Agente_Model_TbTitulacaoConselheiro $model)
    {
        return parent::save($model);
    }
}
