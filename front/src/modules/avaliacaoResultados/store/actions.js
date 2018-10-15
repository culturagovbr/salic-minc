import * as avaliacaoResultadosHelperAPI from '@/helpers/api/AvaliacaoResultados';
import * as desencapsularResponse from '@/helpers/actions';
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

export const salvarParecer = (_, params) => {
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

export const obterDestinatarios = ({ commit }) => {
    avaliacaoResultadosHelperAPI.obterDestinatarios()
        .then((response) => {
            const data = response.data;
            const destinatariosEncaminhamento = data.data;
            commit(types.DESTINATARIOS_ENCAMINHAMENTO, destinatariosEncaminhamento.items);
        });
};

export const obterDadosTabelaTecnico = ({ commit }, params) => {
    avaliacaoResultadosHelperAPI.obterDadosTabelaTecnico(params)
        .then((response) => {
            const data = response.data;
            const dadosTabela = data.data;
            commit(types.PROJETOS_AVALIACAO_TECNICA, dadosTabela);
        });
};

export const projetosFinalizados = ({ commit }, params) => {
    avaliacaoResultadosHelperAPI.obterDadosTabelaTecnico(params)
        .then((response) => {
            const data = response.data;
            const dadosTabela = data.data;
            commit(types.SET_DADOS_PROJETOS_FINALIZADOS, dadosTabela);
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

export const planilha = ({ commit }, params) => {
    avaliacaoResultadosHelperAPI.planilha(params)
        .then((response) => {
            const planilha = response.data;
            commit(types.GET_PLANILHA, planilha);
        });
};

export const projetoAnalise = ({ commit }, params) => {
    avaliacaoResultadosHelperAPI.projetoAnalise(params)
        .then((response) => {
            const projetoAnalise = response.data;
            commit(types.GET_PROJETO_ANALISE, projetoAnalise);
        });
};

export const consolidacaoAnalise = ({ commit }, params) => {
    avaliacaoResultadosHelperAPI.consolidacaoAnalise(params)
        .then((response) => {
            const consolidacaoAnalise = response.data;
            commit(types.GET_CONSOLIDACAO_ANALISE, consolidacaoAnalise);
        });
};

export const finalizarParecer = (_, params) => {
    avaliacaoResultadosHelperAPI.finalizarParecer(params)
        .then(() => {
        });
};

export const encaminharParaTecnico = (_, params) => {
    avaliacaoResultadosHelperAPI.encaminharParaTecnico(params);
};

export const alterarParecer = ({ commit }, param) => {
    commit(types.SET_PARECER, param);
};

export const obterDadosItemComprovacao = ({ commit }, params) => {
    avaliacaoResultadosHelperAPI.obterDadosItemComprovacao(params)
        .then((response) => {
            const itemComprovacao = response.data.data;
            commit(types.GET_DADOS_ITEM_COMPROVACAO, itemComprovacao.items);
        });
};

export const getLaudoFinal = ({ commit }) => {
    avaliacaoResultadosHelperAPI.obterLaudoFinal()
    .then((response) => {
        const dados = response.data.data;
        commit(types.GET_LAUDO_FINAL, dados);
    });
    // const data = { siManifestacao: 'A', dsLaudoFinal: 'Tem mais de 10 caracteres!! 39 no total' };
    // commit(types.GET_LAUDO_FINAL, data);
};

export const atualizarManifestacao = ({ commit }, characterManifestacao) => {
    commit(types.SET_MANIFESTACAO_PROVISORIA, characterManifestacao);
};

export const atualizarParecer = ({ commit }, characterParecer) => {
    commit(types.SET_PARECER_PROVISORIO, characterParecer);
};

export const salvarLaudoFinal = (_, data) => {
    avaliacaoResultadosHelperAPI.criarParecerLaudoFinal(data)
        .then((response) => {
            console.log(response);
        });
};

export const finalizarLaudoFinal = (_, data) => {
    avaliacaoResultadosHelperAPI.finalizarParecerLaudoFinal(data)
        .then((response) => {
            console.log(response);
        });
};

export const enviarDiligencia = (_, data) => {
    avaliacaoResultadosHelperAPI.criarDiligencia(data)
        .then((response) => {
            const data = response.data;
            console.log(data);
        });
};

export const projetosParaDistribuir = ({ commit }) => {
    avaliacaoResultadosHelperAPI.obterProjetosParaDistribuir()
        .then((response) => {
            const data = response.data;
            commit(types.SET_DADOS_PROJETOS_PARA_DISTRIBUIR, data);
        });
};

export const projetosAssinatura = ({ commit }, params) => {
    let type = '';
    switch (params.estado) {
        case 'em_assinatura':
            type = types.SET_DADOS_PROJETOS_EM_ASSINATURA;
            break;
        case 'historico':
            type = types.SET_DADOS_PROJETOS_HISTORICO;
            break;
        case 'assinar':
        default:
            type = types.SET_DADOS_PROJETOS_ASSINAR;
    }

    avaliacaoResultadosHelperAPI.obterProjetosAssinatura(params)
        .then((response) => {
            const data = response.data;
            const dadosTabela = data.data;
            commit(type, dadosTabela);
        });
};

export const obterProjetosLaudoFinal = ({ commit }) => {
    avaliacaoResultadosHelperAPI.obterProjetosLaudoFinal()
        .then((response) => {
            const data = response.data;
            const dadosTabela = data.data;
            commit(types.SET_DADOS_PROJETOS_LAUDO_FINAL, dadosTabela);
        });
};
