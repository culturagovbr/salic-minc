export const SI_ENCAMINHAMENTO_ENVIADO_MINC = 1;
export const SI_ENCAMINHAMENTO_SOLICITACAO_INDEFERIDA = 2;
export const SI_ENCAMINHAMENTO_ENVIADO_UNIDADE_ANALISE = 3;
export const SI_ENCAMINHAMENTO_ENVIADO_ANALISE_TECNICA = 4;
export const SI_ENCAMINHAMENTO_DEVOLVIDO_ANALISE_TECNICA = 5;
export const SI_ENCAMINHAMENTO_DEVOLVIDA_AO_MINC = 6;
export const SI_ENCAMINHAMENTO_ENVIADO_CNIC = 7;
export const SI_ENCAMINHAMENTO_ENVIADO_PLENARIA = 8;
export const SI_ENCAMINHAMENTO_CHECKLIST_PUBLICACAO = 9;
export const SI_ENCAMINHAMENTO_DEVOLVIDA_COORDENADOR_TECNICO = 10;
export const SI_ENCAMINHAMENTO_NAO_ENVIA_MINC = 11;
export const SI_ENCAMINHAMENTO_CADASTRADA_PROPONENTE = 12;
export const SI_ENCAMINHAMENTO_FINALIZADA_SEM_PORTARIA = 15;
export const SI_ENCAMINHAMENTO_DEVOLVIDA_CNIC_AO_COORDENADOR = 17;
export const SI_ENCAMINHAMENTO_SOLICITACAO_ENCAMINHADA_AO_COORDENADOR_GERAL = 18;
export const SI_ENCAMINHAMENTO_SOLICITACAO_ENCAMINHADA_AO_DIRETOR = 19;
export const SI_ENCAMINHAMENTO_SOLICITACAO_ENCAMINHADA_AO_SECRETARIO = 20;
export const SI_ENCAMINHAMENTO_SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_PELO_COORDENADOR_GERAL = 21;
export const SI_ENCAMINHAMENTO_SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_PELO_DIRETOR = 22;
export const SI_ENCAMINHAMENTO_SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_PELO_SECRETARIO = 23;
export const SI_ENCAMINHAMENTO_SOLICITACAO_ENCAMINHADA_AO_PRESIDENTE_DA_VINCULADA = 24;
export const SI_ENCAMINHAMENTO_SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_DE_PARECER_PELO_PRESIDENTE = 25;
export const SI_ENCAMINHAMENTO_SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_FINAL = 26;
export const SI_ENCAMINHAMENTO = {
    1: 'Solicitação encaminhada ao MinC pelo proponente',
    2: 'Solicitação indeferida e devolvida ao proponente',
    3: 'Solicitação encaminhada a  unidade de análise pelo MinC',
    4: 'Solicitação encaminhada ao parecerista /  técnico',
    5: 'Solicitação devolvida ao coordenador da unidade de análise pelo parecerista',
    6: 'Solicitação devolvida ao MinC pela unidade de análise',
    7: 'Solicitação encaminhada para o componente da comissão',
    8: 'Solicitação encaminhada  à plenára da CNIC pelo componente da comissão',
    9: 'Solicitação encaminhada para elaboração de portaria',
    10: 'Solicitação devolvida para o coordenador pelo técnico',
    11: 'Solicitação sem a necessidade de encaminhar ao MinC',
    12: 'Solicitação em cadastramento pelo proponente',
    15: 'Solicitação finalizada pelo MinC',
    17: 'Solicitação devolvida pela CNIC ao coordenador',
    18: 'Solicitação encaminhada ao coordenador-geral',
    19: 'Solicitação encaminhada ao diretor',
    20: 'Solicitação encaminhada ao secretario',
    21: 'Solicitação devolvida ao coordenador pelo coordenador-geral',
    22: 'Solicitação devolvida ao coordenador pelo diretor',
    23: 'Solicitação devolvida ao coordenador pelo secretário',
    24: 'Solicitação encaminhada ao presidente da vinculada',
    25: 'Solicitação devolvida ao coordenador de parecer  pelo presidente da vinculada',
    26: 'Solicitação devolvida ao Coordenador após completar o ciclo de assinaturas',
};
