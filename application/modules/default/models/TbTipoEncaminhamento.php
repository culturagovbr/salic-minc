<?php 

class TbTipoEncaminhamento extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'tbTipoEncaminhamento';

    const DESISTENCIA_DO_PRAZO_RECURSAL = 0;
    const SOLICITACAO_ENCAMINHADA_AO_MINC = 1;
    const SOLICITACAO_INDEFERIDA_E_DEVOLVIDA_AO_PROPONENTE = 2;
    const SOLICITACAO_ENCAMINHADA_PARA_ANALISE_PELO_MINC = 3;
    const SOLICITACAO_ENCAMINHADA_AO_PARECERISTA = 4;
    const SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_PELO_PARECERISTA = 5;
    const SOLICITACAO_DEVOLVIDA_AO_MINC_PELA_UNIDADE = 6;
    const SOLICITACAO_ENCAMINHADA_PARA_COMPONENTE_COMISSAO = 7;
    const SOLICITACAO_ENCAMINHADA_A_PLENARIA = 8;
    const SOLICITACAO_ENCAMINHADA_PARA_CHECKLIST_PUBLICACAO = 9;
    const SOLICITACAO_DEVOLVIDA_PARA_O_COORDENADOR_PELO_TECNICO = 10;
    const SOLICITACAO_SEM_NECESSIDADE_DE_ENCAMINHAR_AO_MINC = 11;
    const SOLICITACAO_CADASTRADA_PELO_PROPONENTE = 12;
    const SOLICITACAO_INDEFERIDA_E_ENCAMINHADA_PARA_ARQUIVO = 13;
    const SOLICITACAO_ENCAMINHADA_A_UNIDADE_DE_ANALISE_PELA_CNIC = 14;
    const SOLICITACAO_FINALIZADA_PELO_MINC = 15;
    const SOLICITACAO_DEVOLVIDA_AO_PROPONENTE_PARA_AJUSTES = 16;
}
