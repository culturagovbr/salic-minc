import * as ConstantesTbDistribuirParecer from '../constantes/TbDistribuirParecer';
import { SITUACAO_DILIGENCIA_ANALISE_TECNICA } from '@/modules/projeto/constantes/Projetos';
import { TP_DILIGENCIA_ANALISE_TECNICA } from '@/modules/diligencia/constantes/TbDiligencia';

import {
    SI_ENCAMINHAMENTO_ENVIADO_UNIDADE_ANALISE,
    SI_ENCAMINHAMENTO_ENVIADO_ANALISE_TECNICA,
    SI_ENCAMINHAMENTO_DEVOLVIDO_ANALISE_TECNICA,
} from '@/modules/shared/constantes/TbTipoEncaminhamento';

export default {
    data: () => ({
        ...ConstantesTbDistribuirParecer,
        TP_DILIGENCIA_ANALISE_TECNICA,
        SITUACAO_DILIGENCIA_ANALISE_TECNICA,
        SI_ENCAMINHAMENTO_ENVIADO_UNIDADE_ANALISE,
        SI_ENCAMINHAMENTO_ENVIADO_ANALISE_TECNICA,
        SI_ENCAMINHAMENTO_DEVOLVIDO_ANALISE_TECNICA,
    }),
};
