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
    const COORDENADOR_GERAL_ADMISSIBILIDADE = 147;
    const COORDENADOR_ABMISSIBILIDADE = 131;

    const SUPERINTENDENTE_DE_VINCULADA = 153;
    const PRESIDENTE_DE_VINCULADA = 154;
    const COORDENADOR_DE_PARECER = 93;
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
    const COORDENADOR_GERAL_PRESTACAO_DE_CONTAS = 126;
    
    const COORDENADOR_GERAL_ACOMPANHAMENTO_PRESTACAO_DE_CONTAS = 150;

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
    const TECNICO_DE_ATENDIMENTO = 155;

    const TECNICO_PORTARIA = 128;

    const COORDENADOR_FISCALIZACAO = 134;
    const TECNICO_FISCALIZACAO = 135;

    const COORDENADOR_VINCULADA = 136;

    const COORDENADOR_AVALIACAO = 138;
    const TECNICO_AVALIACAO = 139;

    const TECNICO_ADMISSIBILIDADE_EDITAL = 140;

    const COORDENADOR_DO_PRONAC = 137;
    const COORDENADOR_DE_CONVENIO = 142;


    const PROPONENTE = 1111;

    const DIRETOR_DEPARTAMENTO = 148;
    const SECRETARIO = 149;
    const PRESIDENTE_VINCULADA_SUBSTITUTO = 151;

    const CHEFE_DE_DIVISAO = 132;

    public function obterPerfisEncaminhamentoAvaliacaoProposta($id_perfil)
    {

        $perfis = [];
        switch ($id_perfil) {
            case Autenticacao_Model_Grupos::TECNICO_ADMISSIBILIDADE:
                $perfis[] = Autenticacao_Model_Grupos::COORDENADOR_ADMISSIBILIDADE;
                break;
            case Autenticacao_Model_Grupos::COORDENADOR_ADMISSIBILIDADE:
                $perfis[] = Autenticacao_Model_Grupos::COMPONENTE_COMISSAO;
                break;
            case Autenticacao_Model_Grupos::COORDENADOR_GERAL_ACOMPANHAMENTO:
                $perfis[] = Autenticacao_Model_Grupos::COORDENADOR_ADMISSIBILIDADE;
                $perfis[] = Autenticacao_Model_Grupos::COMPONENTE_COMISSAO;
                break;
            case Autenticacao_Model_Grupos::COMPONENTE_COMISSAO:
                $perfis[] = Autenticacao_Model_Grupos::COORDENADOR_GERAL_ADMISSIBILIDADE;
                break;
            default:
                break;
        }

        if ($perfis) {
            return $this->findAll(
                [
                    'gru_codigo in (?)' => $perfis,
                    'gru_status' => true
                ]
            );
        }
    }


    public function obterTecnicos()
    {
        $tecnicos = [];
        $tecnicos[] = Autenticacao_Model_Grupos::TECNICO_ADMISSIBILIDADE;
        $tecnicos[] = Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO;
        $tecnicos[] = Autenticacao_Model_Grupos::TECNICO_PRESTACAO_DE_CONTAS;
        $tecnicos[] = Autenticacao_Model_Grupos::TECNICO_ANALISE;
        $tecnicos[] = Autenticacao_Model_Grupos::TECNICO_DE_ATENDIMENTO;
        $tecnicos[] = Autenticacao_Model_Grupos::PROTOCO_DOCUMENTO;
        $tecnicos[] = Autenticacao_Model_Grupos::PROTOCOLO_RECBIMENTO;
        $tecnicos[] = Autenticacao_Model_Grupos::PROTOCOLO_ENVIO_RECEBIMENTO;

        return $tecnicos;
    }

    public function buscarTecnicosPorOrgao($id_orgao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('g' => $this->_name), ['gru_codigo', 'gru_nome'], $this->_schema
        );

        $select->joinInner(['u' => 'UsuariosXOrgaosXGrupos'], 'u.uog_grupo = g.gru_codigo', ['uog_orgao'], $this->_schema );

        $select->where('g.gru_codigo in (?)', $this->obterTecnicos());
        $select->where('g.gru_status = ?', 1);
        $select->where('u.uog_orgao = ?', $id_orgao);
        $select->group(['gru_codigo', 'gru_nome','uog_orgao']);

        return $this->fetchAll($select);
    }
}
