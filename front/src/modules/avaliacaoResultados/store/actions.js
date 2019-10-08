import * as avaliacaoResultadosHelperAPI from '@/helpers/api/AvaliacaoResultados';
import * as types from './types';

export const dadosMenu = ({ commit }) => {
    avaliacaoResultadosHelperAPI.dadosMenu()
        .then((response) => {
            const { data } = response;
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
                commit(types.GET_OBJETO_PARECER, data.objetoParecer);
                resolve();
            }).catch(() => { });
    });
    return p;
};

export const salvarParecer = ({ commit }, params) => {
    const p = new Promise((resolve) => {
        avaliacaoResultadosHelperAPI.criarParecer(params)
            .then(() => {
                commit('noticias/SET_DADOS', { ativo: true, color: 'success', text: 'Salvo com sucesso!' }, { root: true });
                resolve();
            });
    });

    return p;
};

export const mockAvaliacaDesempenho = ({ commit }) => {
    commit(types.MOCK_AVALIACAO_RESULTADOS);
};

export const obterDestinatarios = async ({ commit }, params) => avaliacaoResultadosHelperAPI
    .obterDestinatarios(params)
    .then((response) => {
        const { data } = response;
        const destinatariosEncaminhamento = data.data;
        commit(types.DESTINATARIOS_ENCAMINHAMENTO, destinatariosEncaminhamento.items);
    });

export const obterDadosTabelaTecnico = ({ commit }, params) => {
    commit(types.PROJETOS_AVALIACAO_TECNICA, {});
    avaliacaoResultadosHelperAPI.obterDadosTabelaTecnico(params)
        .then((response) => {
            const { data } = response.data;
            // data.items.forEach((a, index) => {
            //     avaliacaoResultadosHelperAPI.listarDiligencias(a.idPronac).then(
            //         (resp) => {
            //             const obj = resp.data.data;
            //             data.items[index].diligencias = obj.items;
            //         },
            //     );
            // });
            commit(types.PROJETOS_AVALIACAO_TECNICA, data);
        });
};

export const obetDadosDiligencias = async ({ commit }, params) => avaliacaoResultadosHelperAPI
    .listarDiligencias(params)
    .then(
        (response) => {
            const obj = response.data.data;
            commit(types.HISTORICO_DILIGENCIAS, obj);
        },
    );

export const projetosFinalizados = ({ commit }, params) => {
    avaliacaoResultadosHelperAPI.obterDadosTabelaTecnico(params)
        .then((response) => {
            const { data } = response;
            const dadosTabela = data.data;
            commit(types.SET_DADOS_PROJETOS_FINALIZADOS, dadosTabela);
        });
};

export const obterHistoricoEncaminhamento = async ({ commit }, params) => avaliacaoResultadosHelperAPI
    .obterHistoricoEncaminhamento(params)
    .then((response) => {
        const dadosEncaminhamento = response.data.data;
        commit(types.HISTORICO_ENCAMINHAMENTO, dadosEncaminhamento.items);
    });

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

// deprecated
export const planilha = ({ commit }, params) => {
    avaliacaoResultadosHelperAPI.planilha(params)
        .then((response) => {
            const planilhaData = response.data;
            commit(types.GET_PLANILHA, planilhaData);
        }).catch((error) => {
            const data = error.response;
            commit(types.GET_PLANILHA, { error: data.data.data.erro });
        });
};

export const syncPlanilhaAction = ({ commit }, params) => {
    commit(types.GET_PLANILHA, {});
    avaliacaoResultadosHelperAPI.planilha(params)
        .then((response) => {
            const planilhaData = response.data;
            commit(types.GET_PLANILHA, planilhaData);
        }).catch((error) => {
            const data = error.response;
            commit(types.GET_PLANILHA, { error: data.data.data.erro });
        });
};
// deprecated use syncProjetoAction
export const projetoAnalise = ({ commit }, params) => {
    avaliacaoResultadosHelperAPI.projetoAnalise(params)
        .then((response) => {
            const projetoAnaliseData = response.data;
            commit(types.GET_PROJETO_ANALISE, projetoAnaliseData);
        });
};

export const syncProjetoAction = ({ commit }, params) => {
    commit(types.GET_PROJETO_ANALISE, {});
    avaliacaoResultadosHelperAPI.projetoAnalise(params)
        .then((response) => {
            const projetoAnaliseData = response.data;
            commit(types.GET_PROJETO_ANALISE, projetoAnaliseData);
        });
};

export const consolidacaoAnalise = ({ commit }, params) => {
    avaliacaoResultadosHelperAPI.consolidacaoAnalise(params)
        .then((response) => {
            const consolidacaoAnaliseData = response.data;
            commit(types.GET_CONSOLIDACAO_ANALISE, consolidacaoAnaliseData);
        });
};

export const finalizarParecer = ({ commit }, params) => {
    avaliacaoResultadosHelperAPI.alterarEstado(params)
        .then(() => {
            commit('noticias/SET_DADOS', { ativo: true, color: 'success', text: 'Finalizado com sucesso!' }, { root: true });
        });
};

export const encaminharParaTecnico = ({ commit, dispatch }, params) => {
    commit(types.SET_DADOS_PROJETOS_PARA_DISTRIBUIR, {});
    commit(types.PROJETOS_AVALIACAO_TECNICA, {});
    avaliacaoResultadosHelperAPI
        .alterarEstado(params)
        .then(() => {
            dispatch('projetosParaDistribuir');
            dispatch('obterDadosTabelaTecnico', { estadoid: 5 });
        });
};

export const alterarParecer = ({ commit }, param) => {
    commit(types.SET_PARECER, param);
};

export const obterDadosItemComprovacao = ({ commit }, params) => {
    commit(types.SET_COMPROVANTES, []);
    return avaliacaoResultadosHelperAPI
        .obterDadosItemComprovacao(params)
        .then((response) => {
            const dados = response.data.data.items;
            commit(types.GET_DADOS_ITEM_COMPROVACAO, dados.dadosItem);
            commit(types.SET_COMPROVANTES, dados.comprovantes);
        });
};

export const getLaudoFinal = ({ commit }, params) => {
    avaliacaoResultadosHelperAPI.obterLaudoFinal(params)
        .then((response) => {
            const dados = response.data.data;
            commit(types.GET_PARECER_LAUDO_FINAL, dados);
        });
};

export const salvarLaudoFinal = ({ commit }, data) => {
    avaliacaoResultadosHelperAPI.criarParecerLaudoFinal(data)
        .then(() => {
            commit('noticias/SET_DADOS', { ativo: true, color: 'success', text: 'Salvo com sucesso!' }, { root: true });
        });
};

export const finalizarLaudoFinal = ({ commit }, data) => {
    avaliacaoResultadosHelperAPI.alterarEstado(data)
        .then(() => {
            commit('noticias/SET_DADOS', { ativo: true, color: 'success', text: 'Finalizado com sucesso!' }, { root: true });
        });
};

export const enviarDiligencia = (_, data) => {
    avaliacaoResultadosHelperAPI.criarDiligencia(data)
        .then(() => {
        });
};

export const projetosParaDistribuir = ({ commit }) => {
    avaliacaoResultadosHelperAPI.obterProjetosParaDistribuir()
        .then((response) => {
            const { data } = response;
            commit(types.SET_DADOS_PROJETOS_PARA_DISTRIBUIR, data);
        });
};

export const projetosAssinatura = async ({ commit }, params) => {
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

    return avaliacaoResultadosHelperAPI.obterProjetosAssinatura(params)
        .then((response) => {
            const { data } = response;
            const dadosTabela = data.data;
            commit(type, dadosTabela);
        });
};

export const obterProjetosLaudoFinal = ({ commit }, param) => {
    avaliacaoResultadosHelperAPI.obterProjetosLaudoFinal(param)
        .then((response) => {
            const { data } = response;
            const dadosTabela = data.data;
            commit(types.SET_DADOS_PROJETOS_LAUDO_FINAL, dadosTabela);
        });
};

export const obterProjetosLaudoAssinar = ({ commit }, param) => {
    avaliacaoResultadosHelperAPI.obterProjetosLaudoFinal(param)
        .then((response) => {
            const dadosTabela = response.data.data;
            commit(types.SET_DADOS_PROJETOS_LAUDO_ASSINAR, dadosTabela);
        });
};

export const obterProjetosLaudoFinalizados = ({ commit }, param) => {
    avaliacaoResultadosHelperAPI.obterProjetosLaudoFinal(param)
        .then((response) => {
            const dadosTabela = response.data.data;
            commit(types.SET_DADOS_PROJETOS_LAUDO_FINALIZADOS, dadosTabela);
        });
};

export const projetosRevisao = ({ commit }, params) => {
    avaliacaoResultadosHelperAPI.projetosRevisao(params)
        .then((response) => {
            const projetosRevisaoData = response.data.data;
            commit(types.SYNC_PROJETOS_REVISAO, projetosRevisaoData);
        });
};

export const buscarComprovantes = ({ commit }, params) => {
    commit(types.SET_COMPROVANTES, []);
    avaliacaoResultadosHelperAPI.buscarComprovantes(params)
        .then((response) => {
            const { data } = response;
            const itens = data.data;
            commit(types.SET_COMPROVANTES, itens);
        });
};

export const devolverProjeto = ({ commit, dispatch }, params) => {
    commit(types.SET_DADOS_PROJETOS_FINALIZADOS, {});
    commit(types.SYNC_PROJETOS_ASSINAR_COORDENADOR, {});
    commit(types.PROJETOS_AVALIACAO_TECNICA, {});

    let projetosTecnico = {};
    let projetosFinalizadosEstatos = {};
    const laudoDevolver = { estadoId: params.atual };

    if (
        parseInt(params.usuario.grupo_ativo, 10) === 125
        || parseInt(params.usuario.grupo_ativo, 10) === 126
    ) {
        projetosTecnico = {
            estadoid: 5,
        };

        projetosFinalizadosEstatos = {
            estadoid: 6,
        };
    } else {
        projetosTecnico = {
            estadoid: 5,
            idAgente: params.usuario.usu_codigo,
        };

        projetosFinalizadosEstatos = {
            estadoid: 6,
            idAgente: params.usuario.usu_codigo,
        };
    }

    if (params.idTipoDoAtoAdministrativo === '622' && params.proximo === '5') {
        avaliacaoResultadosHelperAPI.alterarEstado(params)
            .then((response) => {
                const devolverProjetoData = response.data;
                commit(types.SET_DEVOLVER_PROJETO, devolverProjetoData);
                dispatch('projetosAssinarCoordenador', { estadoid: 9 });
                dispatch('projetosAssinarCoordenadorGeral', { estadoid: 15 });
                dispatch('projetosFinalizados', projetosFinalizadosEstatos);
                dispatch('obterDadosTabelaTecnico', projetosTecnico);
                dispatch('obterProjetosLaudoFinal', { estadoId: '10' });
            });
    } if (params.idTipoDoAtoAdministrativo === '623' && params.proximo === '10') {
        avaliacaoResultadosHelperAPI.alterarEstado(params)
            .then((response) => {
                const devolverProjetoData = response.data;
                commit(types.SET_DEVOLVER_PROJETO, devolverProjetoData);
                dispatch('obterProjetosLaudoAssinar', laudoDevolver);
                dispatch('obterProjetosLaudoFinal', { estadoId: '10' });
            });
    }

    return null;
};

export const projetosAssinarCoordenador = ({ commit }) => {
    avaliacaoResultadosHelperAPI.projetosPorEstado({ estadoid: 9 })
        .then((response) => {
            const dados = response.data;
            commit(types.SYNC_PROJETOS_ASSINAR_COORDENADOR, dados.data);
        });
};

export const projetosAssinarCoordenadorGeral = ({ commit }) => {
    avaliacaoResultadosHelperAPI.projetosPorEstado({ estadoid: 15 })
        .then((response) => {
            const dados = response.data;
            commit(types.SYNC_PROJETOS_ASSINAR_COORDENADOR_GERAL, dados.data);
        });
};

export const salvarAvaliacaoComprovante = async ({ commit }, avaliacao) => {
    const params = {
        idPronac: avaliacao.IdPRONAC,
        dsJustificativa: avaliacao.dsOcorrenciaDoTecnico,
        stItemAvaliado: avaliacao.stItemAvaliado,
        idComprovantePagamento: avaliacao.idComprovantePagamento,
    };
    const valor = await avaliacaoResultadosHelperAPI.salvarAvaliacaoComprovante(params)
        .then((response) => {
            commit(types.EDIT_COMPROVANTE, avaliacao);
            return response.data;
        }).catch((e) => {
            throw new TypeError(e.response.data.message, 'salvarAvaliacaoComprovante', 10);
        });
    return valor;
};

export const alterarAvaliacaoComprovante = ({ commit }, params) => {
    commit(types.ALTERAR_DADOS_ITEM_COMPROVACAO, params);
};

export const alterarPlanilha = ({ commit }, params) => commit(types.ALTERAR_PLANILHA, params);

export const dashboardQuantidades = ({ commit }) => {
    avaliacaoResultadosHelperAPI.dashboardQuantidade()
        .then((response) => {
            const { data } = response.data;
            commit(types.DASHBOARD_QUANTIDADE, data);
        }).catch((e) => {
            throw new TypeError(e.response.data.message, 'error', 10);
        });
};

export const projetoSimilaresAction = ({ commit }, params) => {
    avaliacaoResultadosHelperAPI.projetosSimilares(params)
        .then((response) => {
            const { data } = response;
            commit(types.SYNC_PROJETOS_SIMILARES, data);
        }).catch((e) => {
            throw new TypeError(e.response.data.message, 'error', 10);
        });
};
