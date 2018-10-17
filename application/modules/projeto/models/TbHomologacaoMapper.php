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

    public function encaminhar($arrData)
    {
        try {

            $retorno = ['data' => [], 'status' => false];

            $idPronac = $arrData['idPronac'];

            if (empty($idPronac)) {
                throw new Exception('Identificador do Projeto não informado.');
            }

            $objTbProjetos = new Projeto_Model_DbTable_Projetos();
            $projeto = $objTbProjetos->findBy(['IdPRONAC' => $idPronac]);

            if (empty($projeto)) {
                throw new Exception('Projeto n&atilde;o encontrado.');
            }

            $dbTableHomologacao = new Projeto_Model_DbTable_TbHomologacao();
            $parecerHomolog = $dbTableHomologacao->getBy(['idPronac' => $idPronac, 'tpHomologacao' => '1']);

            if (empty($parecerHomolog['dsHomologacao'])) {
                throw new Exception('Parecer de homologa&ccedil;&atilde;o n&atilde;o encontrado.');
            }

            $situacao = $this->obterNovaSituacao($idPronac);

            $objProjetos = new \Projetos();
            $updated = $objProjetos->alterarSituacao(
                $idPronac,
                null,
                $situacao['codigo'],
                $situacao['mensagem']
            );

            if ($updated) {
                if ($situacao['codigo'] == Projeto_Model_Situacao::PROJETO_ENCAMINHADO_PARA_HOMOLOGACAO) {
                    $idDocumentoAssinatura = $this->iniciarFluxoAssinatura($idPronac);
                    $retorno['data'] = ['idDocumentoAssinatura' => $idDocumentoAssinatura];
                }

                $this->setMessage($situacao['mensagem']);
                $retorno['status'] = true;
            } else {
                $this->setMessage('N&atilde;o foi poss&iacute;vel alterar a situa&ccedil;&atilde;o do projeto.', 'IdPRONAC');
            }
        } catch (Exception $e) {
            $this->setMessage($e->getMessage());
        }

        return $retorno;
    }

    final public function obterNovaSituacao($idPronac)
    {
        $situacao = [
            'codigo' => Projeto_Model_Situacao::PROJETO_ENCAMINHADO_PARA_HOMOLOGACAO,
            'mensagem' => "Projeto encaminhado para homologa&ccedil;&atilde;o."
        ];

        $tbRecursoMapper = new Recurso_Model_TbRecursoMapper();
        if ($this->isValorHomologadoDiferenteDoValorAdequado($idPronac)
            && $tbRecursoMapper->isProjetoComDireitoARecursoPorFase($idPronac, 2)) {
            $situacao['codigo'] = Projeto_Model_Situacao::PROJETO_HOMOLOGADO;
            $situacao['mensagem'] = "Aguardando a supera&ccedil;&atilde;o do prazo recursal.";
        }

        return $situacao;
    }

    final public function isValorHomologadoDiferenteDoValorAdequado($idPronac)
    {
        $dbTableEnquadramento = new Projeto_Model_DbTable_Enquadramento();
        $enquadramentoProjeto = $dbTableEnquadramento->obterProjetoAreaSegmento(
            ['a.IdPRONAC = ?' => $idPronac]
        )->current();

        return ($enquadramentoProjeto['VlHomologadoIncentivo'] != $enquadramentoProjeto['VlAdequadoIncentivo']);
    }

    final private function iniciarFluxoAssinatura($idPronac)
    {
        if (empty($idPronac)) {
            throw new Exception(
                "Identificador do projeto &eacute; necess&amp;aacute;rio para acessar essa funcionalidade."
            );
        }

        $dbTableParecer = new Parecer();
        $parecer = $dbTableParecer->findBy([
            'TipoParecer' => '1',
            'idTipoAgente' => '1',
            'IdPRONAC' => $idPronac
        ]);

        if (count($parecer) < 1 || empty($parecer['IdParecer'])) {
            throw new Exception(
                "&Eacute; necess&amp;aacute;rio ao menos um parecer para iniciar o fluxo de assinatura."
            );
        }

        $objDbTableDocumentoAssinatura = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();
        $documentoAssinatura = $objDbTableDocumentoAssinatura->obterProjetoDisponivelParaAssinatura(
            $idPronac,
            Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_HOMOLOGAR_PROJETO
        );

        if (count($documentoAssinatura) < 1) {
            $servicoDocumentoAssinatura = new \Application\Modules\Projeto\Service\Assinatura\DocumentoAssinatura(
                $idPronac,
                Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_HOMOLOGAR_PROJETO,
                $parecer['IdParecer']
            );
            $idDocumentoAssinatura = $servicoDocumentoAssinatura->iniciarFluxo();
        } else {
            $idDocumentoAssinatura = $documentoAssinatura['idDocumentoAssinatura'];
        }

        return $idDocumentoAssinatura;
    }
}
