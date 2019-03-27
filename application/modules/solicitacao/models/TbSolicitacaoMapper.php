<?php

/**
 * Class Solicitacao_Model_TbSolicitacaoMapper
 */
class Solicitacao_Model_TbSolicitacaoMapper extends MinC_Db_Mapper
{
    protected $_idUsuario;

    public function __construct()
    {
        $this->setDbTable('Solicitacao_Model_DbTable_TbSolicitacao');

        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $arrAuth = array_change_key_case((array)$auth->getIdentity());

        $this->_idUsuario = !empty($arrAuth['usu_codigo']) ? $arrAuth['usu_codigo'] : $arrAuth['idusuario'];
    }

    public function obterSolicitacaoAtiva($arrData)
    {
        $where = [];
        if (isset($arrData['idPronac']) && !empty($arrData['idPronac'])) {
            $where['idPronac = ?'] = $arrData['idPronac'];
        }

        if (isset($arrData['idProjeto']) && !empty($arrData['idProjeto'])) {
            $where['idProjeto = ?'] = $arrData['idProjeto'];
        }

        if (empty($where)) {
            return [];
        }

        $where['stEstado = ?'] = Solicitacao_Model_TbSolicitacao::ESTADO_ATIVO;

        $tbSolicitacao = new Solicitacao_Model_DbTable_TbSolicitacao();
        $result = $tbSolicitacao->buscar($where, ['siEncaminhamento ASC'])->current();
        return ($result) ? $result->toArray() : [];
    }

    public function isValid($model)
    {
        $booStatus = true;
        $arrRequired = [];
        $arrData = $model->toArray();
        if ($arrData['siEncaminhamento'] == Solicitacao_Model_TbSolicitacao::SITUACAO_ENCAMINHAMENTO_CADASTRADA
            || $arrData['siEncaminhamento'] == Solicitacao_Model_TbSolicitacao::SITUACAO_ENCAMINHAMENTO_ENCAMINHADA_AO_MINC
        ) {
            $arrRequired = [
                'idOrgao',
                'dsSolicitacao',
            ];
        }

        if ($arrData['siEncaminhamento'] == Solicitacao_Model_TbSolicitacao::SITUACAO_ENCAMINHAMENTO_FINALIZADA_MINC) {
            $arrRequired = ['dsResposta'];
        }

        foreach ($arrRequired as $strValue) {
            if (!isset($arrData[$strValue]) || empty($arrData[$strValue])) {
                $this->setMessage('Campo obrigat&oacute;rio!', $strValue);
                $booStatus = false;
            }
        }

        return $booStatus;
    }

    public function salvar($arrData)
    {
        $booStatus = 0;
        $idDocumento = '';

        if (!empty($arrData)) {

            try {

                $sp = new Solicitacao_Model_SpSelecionarTecnicoSolicitacao();
                if (!empty($arrData['idPronac'])) {
                    $tecnico = $sp->exec($arrData['idPronac'], 'projeto');
                } elseif (!empty($arrData['idProjeto'])) {
                    $tecnico = $sp->exec($arrData['idProjeto'], 'proposta');
                }

                if (empty($tecnico)) {
                    throw new Exception("Erro ao salvar! T&eacute;cnico n&atilde;o encontrado!");
                }

                if (empty(strip_tags($arrData['dsSolicitacao']))) {
                    throw new Exception("Campo Solicita&ccedil;&atilde;o &eacute; obrigat&oacute;rio!");
                }

                $arrData['idOrgao'] = $tecnico['idOrgao'];
                $arrData['idTecnico'] = $tecnico['idTecnico'];
                $arrData['idAgente'] = $tecnico['idAgente'];
                $model = new Solicitacao_Model_TbSolicitacao();
                $model->setIdSolicitacao($arrData['idSolicitacao']);
                $model->setDtSolicitacao(date('Y-m-d h:i:s'));
                $model->setIdOrgao($arrData['idOrgao']);
                $model->setIdAgente($arrData['idAgente']);
                $model->setDsSolicitacao($arrData['dsSolicitacao']);
                $model->setStEstado(Solicitacao_Model_TbSolicitacao::ESTADO_ATIVO);
                $model->setIdPronac($arrData['idPronac']);
                $model->setIdProjeto($arrData['idProjeto']);
                $model->setIdTecnico($arrData['idTecnico']);
                $model->setIdSolicitante($arrData['idUsuario']);
                $model->setDtEncaminhamento(date('Y-m-d h:i:s'));

                $mensagemSucesso = "Solicita&ccedil;&atilde;o enviada com sucesso!";
                $model->setSiEncaminhamento(Solicitacao_Model_TbSolicitacao::SITUACAO_ENCAMINHAMENTO_ENCAMINHADA_AO_MINC);

                # define se e para salvar ou enviar ao minc
                if ($arrData['siEncaminhamento'] == Solicitacao_Model_TbSolicitacao::SITUACAO_ENCAMINHAMENTO_RASCUNHO) {
                    $model->setSiEncaminhamento(Solicitacao_Model_TbSolicitacao::SITUACAO_ENCAMINHAMENTO_CADASTRADA);
                    $mensagemSucesso = "Rascunho salvo com sucesso!";
                }
                $file = new Zend_File_Transfer();

                $model->setIdDocumento($model->getIdDocumento());
                if($file->isUploaded()){

                    if (!empty($file->getFileInfo())) {

                        $arrDoc = [];
                        $arrDoc['idTipoDocumento'] = Arquivo_Model_TbTipoDocumento::TIPO_DOCUMENTO_ARQUIVO;
                        $arrDoc['dsDocumento'] = 'Anexo solicita&ccedil;&atilde;o';

                        $mapperArquivo = new Arquivo_Model_TbDocumentoMapper();
                        $idDocumento = $mapperArquivo->saveCustom($arrDoc, $file);

                    }
                    $model->setIdDocumento($idDocumento);
                }

                $id = $this->save($model);
                if ($id) {
                    $booStatus = $id;
                    $this->setMessage($mensagemSucesso);
                } else {
                    $this->setMessage('N&atilde;o foi poss&iacute;vel efetuar a opera&ccedil;&atilde;o!');
                }
            } catch (Exception $e) {
                $this->setMessage($e->getMessage());
            }
        }
        return $booStatus;
    }

    public function responder($arrData)
    {
        $booStatus = 0;

        if (!empty($arrData)) {

            try {

                if (empty(strip_tags($arrData['dsResposta']))) {
                    throw new Exception("Campo Resposta &eacute; obrigat&oacute;rio!");
                }

                $model = new Solicitacao_Model_TbSolicitacao();
                $model->setIdSolicitacao($arrData['idSolicitacao']);
                $model->setDsResposta($arrData['dsResposta']);
                $model->setDtResposta(date('Y-m-d h:i:s'));
                $model->setIdTecnico($this->_idUsuario);
                $model->setSiEncaminhamento(Solicitacao_Model_TbSolicitacao::SITUACAO_ENCAMINHAMENTO_FINALIZADA_MINC);
                $model->setStEstado(0);

                $file = new Zend_File_Transfer();

                $model->setIdDocumento($model->getIdDocumento());
                if($file->isUploaded()){

                    if (!empty($file->getFileInfo())) {

                        $arrDoc = [];
                        $arrDoc['idTipoDocumento'] = Arquivo_Model_TbTipoDocumento::TIPO_DOCUMENTO_ARQUIVO;
                        $arrDoc['dsDocumento'] = 'Anexo resposta solicita&ccedil;&atilde;o';

                        $mapperArquivo = new Arquivo_Model_TbDocumentoMapper();
                        $idDocumento = $mapperArquivo->saveCustom($arrDoc, $file);

                    }
                    $model->setIdDocumentoResposta($idDocumento);
                }

                $id = $this->save($model);
                if ($id) {
                    $booStatus = $id;
                    $this->setMessage('Solicita&ccedil;&atilde;o respondida com sucesso!');
                } else {
                    $this->setMessage('N&atilde;o foi poss&iacute;vel salvar a resposta da solicita&ccedil;&atilde;o!');
                }
            } catch (Exception $e) {
                $this->setMessage($e->getMessage());
            }
        }
        return $booStatus;
    }

    public function atualizarSolicitacao($model)
    {
        $booStatus = 0;

        if (!empty($model)) {

            try {

                $id = $this->save($model);
                if ($id) {
                    $booStatus = $id;
                    $this->setMessage('Solicita&ccedil;&atilde;o atualizada com sucesso!');
                } else {
                    $this->setMessage('N&atilde;o foi poss&iacute;vel atualizar a solicita&ccedil;&atilde;o!');
                }
            } catch (Exception $e) {
                $this->setMessage($e->getMessage());
            }
        }
        return $booStatus;
    }


    public function deletarArquivo($arrData)
    {
        if (!empty($arrData)) {
            try {

                $model = new Solicitacao_Model_DbTable_TbSolicitacao();
                $model->update(['idDocumento' => 0],"idSolicitacao = {$arrData['idSolicitacao']}");
                $this->updateCustom($model);

            } catch (Exception $e) {
                $this->setMessage($e->getMessage());

            }
        }

    }

}
