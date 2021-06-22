<?php

/**
 * 
 */
class Assinatura_Model_TbAssinaturaMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Assinatura_Model_DbTable_TbAssinatura');
    }

    public function isValid($arrData)
    {
      $booStatus = true;
      $arrData = $model->toArray();
      $intIdPronac = $arrData['idPronac'];
      if (!$intIdPronac) {
        $this->setMessage('Identificador do Projeto não informado.');
        $booStatus = false;
      } else {
            $objTbProjetos = new Projeto_Model_DbTable_Projetos();
            if (!$objTbProjetos->findBy(array('IdPRONAC' => $intIdPronac))) {
				$this->setMessage('Projeto n&atilde;o encontrado.');
				$booStatus = false;
            }
      }
    }

    public function save($arrData) 
    {
        $booStatus = false;
//         if (!empty($arrData)) {
//             $model = new Admissibilidade_Model_TbMensagemProjeto($arrData);
//             try {
//                 $auth = Zend_Auth::getInstance(); // pega a autenticacao
//                 $arrAuth = array_change_key_case((array)$auth->getIdentity());
//                 $grupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
//                 $intUsuOrgao = $grupoAtivo->codOrgao;
//                 //$intUsuOrgao = $grupoAtivo->codGrupo;
//                 //var_dump($intUsuOrgao, $grupoAtivo->codOrgao);die;
//                 $model->setDtMensagem(date('Y-m-d h:i:s'));
//                 $model->setIdRemetente($arrAuth['usu_codigo']);
//                 $model->setIdRemetenteUnidade($intUsuOrgao);
// //                $model->setIdDestinatario($arrAuth['usu_codigo']);
//                 $model->setCdTipoMensagem('E');
//                 $model->setStAtivo(1);
//                 if ($intId = parent::save($model)) {
//                     $booStatus = 1;
//                     $this->setMessage('Pergunta enviada com sucesso!');
//                 } else {
//                     $this->setMessage('Nao foi possivel enviar mensagem!');
//                 }
//             } catch (Exception $e) {
//                 $this->setMessage($e->getMessage());
//             }
//         }
        return $booStatus;
    }
}
