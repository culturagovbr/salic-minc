<?php

namespace MinC\Assinatura\Servico;

use Mockery\Exception;

class DocumentoAssinatura implements IServico
{

    public function registrarDocumentoAssinatura(\Assinatura_Model_TbDocumentoAssinatura $objModelDocumentoAssinatura)
    {
        if (!$objModelDocumentoAssinatura->getIdPRONAC()) {
            throw new Exception("Identificador do Projeto n&atilde;o informado.");
        }

        $objTbProjetos = new \Projeto_Model_DbTable_Projetos();
        $dadosProjeto = $objTbProjetos->findBy([
            'IdPRONAC' => $objModelDocumentoAssinatura->getIdPRONAC()
        ]);

        if (!$dadosProjeto) {
            throw new Exception("Projeto n&atilde;o encontrado.");
        }

        $objDbTableDocumentoAssinatura = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();
        $documentoAssinatura = $objDbTableDocumentoAssinatura->obterProjetoDisponivelParaAssinatura(
            $objModelDocumentoAssinatura->getIdPRONAC(),
            $objModelDocumentoAssinatura->getIdTipoDoAtoAdministrativo()
        );

        if (count($documentoAssinatura) > 0) {
            $objDbTableDocumentoAssinatura = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();
            $data = [
                'cdSituacao' => \Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_FECHADO_PARA_ASSINATURA,
                'stEstado' => \Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_INATIVO
            ];
            $where = [
                'idDocumentoAssinatura = ?' => $documentoAssinatura['idDocumentoAssinatura'],
            ];

            $objDbTableDocumentoAssinatura->update(
                $data,
                $where
            );
        }

        $objDocumentoAssinaturaMapper = new \Assinatura_Model_TbDocumentoAssinaturaMapper();
        $objDocumentoAssinaturaMapper->save($objModelDocumentoAssinatura);
    }
}