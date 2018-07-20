<?php

/**
 * @name Agente_Model_TbMensagemProjetoMapper
 * @package Modules/Admissibilidade
 * @subpackage Models
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 01/10/2017
 *
 * @copyright © 2012 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://salic.cultura.gov.br
 */
class Projeto_Model_TbHomologacaoMapper extends MinC_Db_Mapper
{

    public function __construct()
    {
        $this->setDbTable('Projeto_Model_DbTable_TbHomologacao');
    }

    public function isUniqueCpfCnpj($value)
    {
        return ($this->findBy(array("cnpjcpf" => $value))) ? true : false;
    }

    public function isValid($model)
    {
        $booStatus = true;
        $arrData = $model->toArray();
        $arrRequired = [
            'idPronac',
            'tpHomologacao',
            'dsHomologacao',
        ];
        foreach ($arrRequired as $strValue) {
            if (!isset($arrData[$strValue]) || empty($arrData[$strValue])) {
                $this->setMessage('Campo obrigat&oacute;rio!', $strValue);
                $booStatus = false;
            }
        }

        return $booStatus;
    }

    /*
     * @todo Verificar por que não está alterando a sitacao.
     */
    public function save($arrData)
    {
        $booStatus = false;
        if (!empty($arrData)) {

            $model = new Projeto_Model_TbHomologacao($arrData);
            try {
                // pega a autenticacao
                $auth = Zend_Auth::getInstance();
                $arrAuth = array_change_key_case((array)$auth->getIdentity());
                if (!isset($arrData['idHomologacao']) || empty($arrData['idHomologacao'])) {
                    $model->setDtHomologacao(date('Y-m-d h:i:s'));
                }
                $model->setIdUsuario($arrAuth['usu_codigo']);
                $intId = parent::save($model);
                if ($intId) {
                    $booStatus = 1;
                    $this->setMessage('Salvo com sucesso!');
                } else {
                    $this->setMessage('N&atilde;o foi poss&iacute;vel salvar!');
                }
            } catch (Exception $e) {
                $this->setMessage($e->getMessage());
            }

        }
        return $booStatus;
    }

    /**
     * Assina o projeto apos ser homologado.
     */
    public function encaminhar($arrData)
    {
        try {
            $intIdPronac = $arrData['idPronac'];

            if (!$intIdPronac) {
                $this->setMessage('Identificador do Projeto não informado.');
                $booStatus = true;
            }
            $objTbProjetos = new Projeto_Model_DbTable_Projetos();
            $projeto =$objTbProjetos->findBy(['IdPRONAC' => $intIdPronac]);
                if (!$projeto) {
                throw new Exception('Projeto n&atilde;o encontrado.');
                }
            $dbTableHomologacao = new Projeto_Model_DbTable_TbHomologacao();
            $parecerHomolog = $dbTableHomologacao->getBy(['idPronac' => $arrData['idPronac'], 'tpHomologacao' => '1']);

            if (!$parecerHomolog || empty($parecerHomolog['dsHomologacao'])) {
                throw new Exception('Parecer de homologa&ccedil;&atilde;o n&atilde;o encontrado.');
            }
                $auth = Zend_Auth::getInstance();
                $arrAuth = array_change_key_case((array) $auth->getIdentity());
                $arrProjeto = [
                    'idPRONAC' => $intIdPronac,
                    'situacao' => Projeto_Model_Situacao::ANALISE_TECNICA,
                    'dtSituacao' => $this->_dbTable->getExpressionDate(),
                    'providenciaTomada' => 'Projeto aguardando an&aacute;lise documental',
                    'logon' => $arrAuth['usu_codigo']
                ];
                $tbProjetosMapper = new Projeto_Model_TbProjetosMapper();
                $modelTbProjetos = new Projeto_Model_TbProjetos($arrProjeto);
                $booStatus = false;if ($tbProjetosMapper->save($modelTbProjetos)) {
                //    $objModelDocumentoAssinatura = new Assinatura_Model_TbDocumentoAssinatura();
                //    $objModelDocumentoAssinatura
                //        ->setIdPRONAC($intIdPronac)
                //        ->setIdTipoDoAtoAdministrativo(Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_HOMOLOGAR_PROJETO)
                //        ->setIdAtoDeGestao($arrData['IdEnquadramento'])
                //        ->setConteudo($arrData['conteudo'])
                //        ->setIdCriadorDocumento($auth->getIdentity()->usu_codigo)
                //        ->setCdSituacao(Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_DISPONIVEL_PARA_ASSINATURA)
                //        ->setStEstado(Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO)
                //        ->setDtCriacao($objTbProjetos->getExpressionDate());//
                //                    $objDocumentoAssinatura = new MinC_Assinatura_Servico_Assinatura($this->post, $auth->getIdentity());
                //                    $servicoDocumento = $objDocumentoAssinatura->obterServicoDocumento();
                //                    $servicoDocumento->registrarDocumentoAssinatura($objModelDocumentoAssinatura);

                //                    $this->setMessage('Documento gerado e encaminhado com sucesso!');
                $this->setMessage('Projeto encaminhado com sucesso!');
                $booStatus = true;
            } else {
                $this->setMessage('N&atilde;o foi poss&iacute;vel alterar a situa&ccedil;&atilde;o do projeto.', 'IdPRONAC');
                $this->setMessage($tbProjetosMapper->getMessages());
                $booStatus = false;
            }
        } catch (Exception $e) {
            $this->setMessage($e->getMessage());
            $booStatus = false;
        }

        return $booStatus;
    }
}
