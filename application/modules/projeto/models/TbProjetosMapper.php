<?php

/**
 * @name Agente_Model_TbMensagemProjetoMapper
 * @package Modules/Admissibilidade
 * @subpackage Models
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 20/03/2018
 *
 * @copyright © 2012 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://salic.cultura.gov.br
 */
class Projeto_Model_TbProjetosMapper extends MinC_Db_Mapper
{

    public function __construct()
    {
        $this->setDbTable('Projeto_Model_DbTable_Projetos');
    }

    // public function isUniqueCpfCnpj($value)
    // {
    //     return ($this->findBy(array("cnpjcpf" => $value))) ? true : false;
    // }

    public function isValid($model)
    {
        $booStatus = true;
        $arrData = $model->toArray();
        $arrRequired = [
                'idPRONAC',
                'situacao',
                'dtSituacao',
                'providenciaTomada',
                'logon',
        ];
        foreach ($arrRequired as $strValue) {
            if (!isset($arrData[$strValue]) || empty($arrData[$strValue])) {
                $this->setMessage('Campo obrigat&oacute;rio!', $strValue);
                $booStatus = false;
            }
        }

        return $booStatus;
    }

    // public function save($arrData)
    // {
    //     $booStatus = false;
    //     if (!empty($arrData)) {
    //         $model = new Projeto_Model_TbHomologacao($arrData);
    //         try {
    //             $objProjeto = new Projetos();
    //             $objProjeto->alterarSituacao($this->idPronac, null, 'D51', 'Projeto em an&aacute;lise documental.');

    //             exit;
    //             $auth = Zend_Auth::getInstance(); // pega a autenticacao
    //             $arrAuth = array_change_key_case((array) $auth->getIdentity());
    //             if (!isset($arrData['idHomologacao']) || empty($arrData['idHomologacao'])) {
    //                 $model->setDtHomologacao(date('Y-m-d h:i:s'));
    //             }
    //             $model->setIdUsuario($arrAuth['usu_codigo']);
    //             if ($intId = parent::save($model)) {
    //                 $booStatus = 1;
    //                 $this->setMessage('Salvo com sucesso!');
    //             } else {
    //                 $this->setMessage('Nao foi possivel salvar!');
    //             }
    //         } catch (Exception $e) {
    //             $this->setMessage($e->getMessage());
    //         }
    //     }
    //     return $booStatus;
    // }

    // public function alterarSituacao($arrParam)
    // {
        // $this->idPronac, 'B04', 'Projeto em an&aacute;lise documental.'
    // }
}
