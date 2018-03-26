<?php

class Projeto_Model_Situacao extends MinC_Db_Table_Abstract
{
    protected $_name = 'Situacao';
    protected $_schema = 'sac';
    protected $_primary = 'Codigo';

    const PROPOSTA_TRANSFORMADA_EM_PROJETO = 'B01';
    const PROJETO_ENQUADRADO_COM_RECURSO = 'B03';
    const PROJETO_DEVOLVIDO_PARA_ENQUADRAMENTO = 'B05';
    const ENCAMINHADO_PARA_ANALISE_TECNICA = 'B11';
    const PROJETO_ADEQUADO_A_REALIDADE_DE_EXECUCAO = 'B20';
    const AGUARDANDO_ELABORACAO_DE_PORTARIA_DE_PRORROGACAO = 'D22';
    const PROJETO_APROVADO_AGUARDANDO_ANALISE_DOCUMENTAL = 'D03';
    const PROJETO_LIBERADO_PARA_AJUSTES = 'E90';
    const ANALISE_TECNICA = 'D51';
}
