<?php

class Autenticacao_Model_Grupos extends MinC_Db_Table_Abstract
{
    protected $_name = 'Grupos';
    protected $_schema = 'tabelas';
    protected $_primary = 'gru_codigo';

    const PROTOCO_DOCUMENTO = 90;
    const PROTOCOLO_RECBIMENTO = 91;
    const PROTOCOLO_ENVIO_RECEBIMENTO = 104;

    const TECNICO_ADMISSIBILIDADE = 92;
    const COORDENADOR_ADMISSIBILIDADE = 131;

    const SUPERINTENDENTE_DE_VINCULADA = 153;
    const PRESIDENTE_DE_VINCULADA = 154;
    const COORDENADOR_DE_PARECERISTA = 93;
    const PARECERISTA = 94;
    
    const CONSULTA = 95;
    const CONSULTA_GERENCIAL = 96;
    const GESTOR_SALIC = 97;

    const ACOMPANHAMENTO = 99;
    const TECNICO_ACOMPANHAMENTO = 121;
    const COORDENADOR_ACOMPANHAMENTO = 122;
    const COORDENADOR_GERAL_ACOMPANHAMENTO = 123;

    const PRESTACAO_DE_CONTAS = 100;
    const TECNICO_PRESTACAO_DE_CONTAS = 124;
    const COORDENADOR_PRESTACAO_DE_CONTAS = 125;

    const COORDENADOR_ANALISE = 103;
    const TECNICO_ANALISE = 110;

    const COORDENADOR_ARQUIVO = 113;
    const COORDENADOR_EDITAIS = 114;
    const ATENDIMENTO_REPRESENTACOES = 115;

    const PRESIDENTE_CNIC = 119;
    const COORDENADOR_CNIC = 120;
    const MEMBROS_NATOS_CNIC = 133;
    const COMPONENTE_COMISSAO = 118;
    
    const COORDENADOR_ATENDIMENTO = 127;
    const TECNICO_PORTARIA = 128;
    const COORDENADOR_ABMISSIBILIDADE = 131;

    const COORDENADOR_FISCALIZACAO = 134;
    const TECNICO_FISCALIZACAO = 135;

    const COORDENADOR_VINCULADA = 136;

    const COORDENADOR_AVALIACAO = 138;
    const TECNICO_AVALIACAO = 139;

    const TECNICO_ADMISSIBILIDADE_EDITAL = 140;

    const COORDENADOR_DO_PRONAC = 137;
    const COORDENADOR_DE_CONVENIO = 142;

    public function obterPerfisEncaminhamentoAvaliacaoProposta($id_perfil)
    {
        $perfis = [];
        if ($id_perfil == Autenticacao_Model_Grupos::TECNICO_ADMISSIBILIDADE) {
            $perfis[] = Autenticacao_Model_Grupos::COORDENADOR_ABMISSIBILIDADE;
            $perfis[] = Autenticacao_Model_Grupos::COORDENADOR_GERAL_ACOMPANHAMENTO;
        }

        if ($id_perfil == Autenticacao_Model_Grupos::COORDENADOR_ABMISSIBILIDADE
            || $id_perfil == Autenticacao_Model_Grupos::COORDENADOR_GERAL_ACOMPANHAMENTO) {
            /**
             * @todo Preencher carregamento das entidades vinculadas.
             */
        }

        return $this->findAll(
            [
                'gru_codigo in (?)' => $perfis,
                'gru_status' => true
            ]
        );
    }
}
