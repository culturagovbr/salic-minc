import * as avaliacaoResultadosHelperAPI from '@/helpers/api/AvaliacaoResultados';
import * as types from './types';

export const dadosMenu = ({ commit }) => {
    avaliacaoResultadosHelperAPI.dadosMenu()
        .then((response) => {
            const data = response.data;
            const dadosTabela = data.data;
            commit(types.SET_REGISTROS_TABELA, dadosTabela);
        });
};

export const setRegistroAtivo = ({ commit }, registro) => {
    commit(types.SET_REGISTRO_ATIVO, registro);
};

export const removerRegistro = ({ commit }, registro) => {
    avaliacaoResultadosHelperAPI.removerRegistro(registro)
        .then(() => {
            commit(types.REMOVER_REGISTRO, registro);
        });
};

export const getDadosEmissaoParecer = ({ commit }, param) => {
    const p = new Promise((resolve) => {
        avaliacaoResultadosHelperAPI.parecerConsolidacao(param)
            .then((response) => {
                const data = response.data.data.items;

                commit(types.GET_PROPONENTE, data.proponente);
                commit(types.GET_PROJETO, data.projeto);
                commit(types.GET_PARECER, data.parecer);
                commit(types.GET_CONSOLIDACAO_PARECER, data.consolidacaoComprovantes);
                resolve();
            }).catch(() => { });
    });
    return p;
};

export const salvarParecer = (params) => {
    const p = new Promise((resolve) => {
        avaliacaoResultadosHelperAPI.criarParecer(params)
            .then(() => {
                resolve();
            });
    });

    return p;
};

export const mockAvaliacaDesempenho = ({ commit }) => {
    commit(types.MOCK_AVALIACAO_RESULTADOS);
};

export const getDestinatariosEncaminhamento = ({ commit }, params) => {
    avaliacaoResultadosHelperAPI.getTeste(params)
        .then((response) => {
            commit(types.DESTINATARIOS_ENCAMINHAMENTO, response.data);
        });
};

export const obterDadosTabelaTecnico = ({ commit }) => {
    avaliacaoResultadosHelperAPI.obterDadosTabelaTecnico()
        .then((response) => {
            const data = response.data;
            const dadosTabela = data.data;
            commit(types.PROJETOS_AVALIACAO_TECNICA, dadosTabela);
        });
};

export const obterHistoricoEncaminhamento = ({ commit }, params) => {
    avaliacaoResultadosHelperAPI.obterHistoricoEncaminhamento(params)
        .then((response) => {
            const dadosEncaminhamento = response.data.data;
            commit(types.HISTORICO_ENCAMINHAMENTO, dadosEncaminhamento.items);
        });
};

export const getTipoAvaliacao = ({ commit }, params) => {
    const p = new Promise((resolve) => {
        avaliacaoResultadosHelperAPI.getTipoAvaliacao(params)
            .then((response) => {
                const data = response.data.data.items;

                commit(types.GET_TIPO_AVALIACAO, data);
                resolve();
            }).catch(() => { });
    });
    return p;
};

export const redirectLinkAvaliacaoResultadoTipo = ({ commit }, params) => {
    if (params.percentual === 0) {
        commit(types.LINK_REDIRECIONAMENTO_TIPO_AVALIACAO_RESULTADO, `/prestacao-contas/realizar-prestacao-contas/index/idPronac/${params.idPronac}`);
    } else {
        commit(types.LINK_REDIRECIONAMENTO_TIPO_AVALIACAO_RESULTADO, `/prestacao-contas/prestacao-contas/amostragem/idPronac/${params.idPronac}/tipoAvaliacao/${params.percentual}`);
    }
};

