import * as avaliacaoResultadosHelperAPI from '@/helpers/api/AvaliacaoResultados';
import * as types from './types';
import CONST from '../const';

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

export const salvarParecer = async ({ commit }, params) => avaliacaoResultadosHelperAPI
    .criarParecer(params)
    .then((response) => {
        commit('noticias/SET_DADOS', { ativo: true, color: 'success', text: 'Salvo com sucesso!' }, { root: true });
        return response;
    });

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

export const obterDadosTabelaTecnico = async ({ commit }, params) => {
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

export const projetosFinalizados = async ({ commit }, params) => avaliacaoResultadosHelperAPI
    .obterDadosTabelaTecnico(params)
    .then((response) => {
        const { data } = response;
        const dadosTabela = data.data;
        commit(types.SET_DADOS_PROJETOS_FINALIZADOS, dadosTabela);
        return response;
    });

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

export const finalizarParecer = async ({ commit }, params) => avaliacaoResultadosHelperAPI
    .alterarEstado(params)
    .then((response) => {
        commit('noticias/SET_DADOS', { ativo: true, color: 'success', text: 'Finalizado com sucesso!' }, { root: true });
        return response;
    });

export const encaminharParaTecnico = async ({ dispatch }, params) => avaliacaoResultadosHelperAPI
    .alterarEstado(params)
    .then(response => response,
        // dispatch('projetosParaDistribuir');
        // dispatch('obterDadosTabelaTecnico', { estadoid: 5, idSecretaria: params.idSecretaria });
    );

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

export const salvarLaudoFinal = async (state, data) => avaliacaoResultadosHelperAPI
    .criarParecerLaudoFinal(data);

export const finalizarLaudoFinal = async ({ commit }, data) => avaliacaoResultadosHelperAPI
    .alterarEstado(data)
    .then((response) => {
        commit('noticias/SET_DADOS', { ativo: true, color: 'success', text: 'Finalizado com sucesso!' }, { root: true });
        return response;
    });

export const enviarDiligencia = (_, data) => {
    avaliacaoResultadosHelperAPI.criarDiligencia(data)
        .then(() => {
        });
};

export const projetosParaDistribuir = async ({ commit }) => avaliacaoResultadosHelperAPI
    .obterProjetosParaDistribuir()
    .then((response) => {
        const { data } = response;
        commit(types.SET_DADOS_PROJETOS_PARA_DISTRIBUIR, data);
        return response;
    });

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

export const devolverProjeto = async ({ commit, dispatch }, params) => {
    // commit(types.SET_DADOS_PROJETOS_FINALIZADOS, {});
    // commit(types.SYNC_PROJETOS_ASSINAR_COORDENADOR, {});
    // commit(types.PROJETOS_AVALIACAO_TECNICA, {});
    const dados = params;
    const { idSecretaria } = dados;
    const isCoordenadorGeral = dados.usuario.grupo_ativo === CONST.PERFIL_COORDENADOR_GERAL;
    const isCoordenador = dados.usuario.grupo_ativo === CONST.PERFIL_COORDENADOR;

    let projetosTecnico = {
        estadoid: CONST.ESTADO_ANALISE_PARECER,
        idAgente: dados.usuario.usu_codigo,
        idSecretaria,
    };

    // let projetosFinalizadosEstados = {
    //     estadoid: CONST.ESTADO_PARECER_FINALIZADO,
    //     idAgente: dados.usuario.usu_codigo,
    //     idSecretaria,
    // };

    if (isCoordenador || isCoordenadorGeral) {
        projetosTecnico = {
            estadoid: CONST.ESTADO_ANALISE_PARECER,
            idSecretaria,
        };

        // projetosFinalizadosEstados = {
        //     estadoid: CONST.ESTADO_PARECER_FINALIZADO,
        //     idSecretaria,
        // };
    }

    if (dados.idTipoDoAtoAdministrativo === CONST.ATO_ADMINISTRATIVO_PARECER_TECNICO) {
        dados.atual = CONST.ESTADO_PARECER_FINALIZADO;
        dados.proximo = CONST.ESTADO_ANALISE_PARECER;

        return avaliacaoResultadosHelperAPI.alterarEstado(dados)
            .then((response) => {
                const devolverProjetoData = response.data;
                commit(types.SET_DEVOLVER_PROJETO, devolverProjetoData);

                if (isCoordenador) {
                    dispatch('projetosAssinarCoordenador', {
                        estadoid: CONST.ESTADO_AGUARDANDO_ASSINATURA_COORDENADOR_PARECER,
                        idSecretaria,
                    });
                }

                if (isCoordenadorGeral) {
                    dispatch('projetosAssinarCoordenadorGeral', {
                        estadoid: CONST.ESTADO_AGUARDANDO_ASSINATURA_COORDENADOR_GERAL_PARECER,
                        idSecretaria,
                    });
                }

                dispatch('obterDadosTabelaTecnico', projetosTecnico);
                // dispatch('projetosFinalizados', projetosFinalizadosEstados);
                // dispatch('obterProjetosLaudoFinal', {
                //     estadoId: CONST.ESTADO_ANALISE_LAUDO,
                //     idSecretaria,
                // });
            });
    }

    if (dados.idTipoDoAtoAdministrativo === CONST.ATO_ADMINISTRATIVO_LAUDO_FINAL
        && dados.proximo === CONST.ESTADO_ANALISE_LAUDO) {
        const laudoDevolver = { estadoId: dados.atual, idSecretaria };

        return avaliacaoResultadosHelperAPI.alterarEstado(dados)
            .then((response) => {
                const devolverProjetoData = response.data;
                commit(types.SET_DEVOLVER_PROJETO, devolverProjetoData);
                dispatch('obterProjetosLaudoAssinar', laudoDevolver);
                dispatch('obterProjetosLaudoFinal', {
                    estadoId: CONST.ESTADO_ANALISE_LAUDO,
                    idSecretaria,
                });
            });
    }

    return null;
};

export const projetosAssinarCoordenador = async ({ commit }, params) => avaliacaoResultadosHelperAPI
    .projetosPorEstado(params) // { estadoid: 9 }
    .then((response) => {
        const dados = response.data;
        commit(types.SYNC_PROJETOS_ASSINAR_COORDENADOR, dados.data);
    });

export const projetosAssinarCoordenadorGeral = async ({ commit }, params) => avaliacaoResultadosHelperAPI
    .projetosPorEstado(params) // { estadoid: 15 }
    .then((response) => {
        const dados = response.data;
        commit(types.SYNC_PROJETOS_ASSINAR_COORDENADOR_GERAL, dados.data);
    });

export const salvarAvaliacaoComprovante = async ({ commit }, avaliacao) => {
    const params = {
        idPronac: avaliacao.IdPRONAC,
        dsJustificativa: avaliacao.dsOcorrenciaDoTecnico,
        stItemAvaliado: avaliacao.stItemAvaliado,
        idComprovantePagamento: avaliacao.idComprovantePagamento,
    };
    return avaliacaoResultadosHelperAPI.salvarAvaliacaoComprovante(params)
        .then((response) => {
            commit(types.EDIT_COMPROVANTE, avaliacao);
            return response.data;
        }).catch((e) => {
            throw new TypeError(e.response.data.message, 'salvarAvaliacaoComprovante', 10);
        });
};

export const alterarAvaliacaoComprovante = ({ commit }, params) => {
    commit(types.ALTERAR_DADOS_ITEM_COMPROVACAO, params);
};

export const alterarPlanilha = ({ commit }, params) => commit(types.ALTERAR_PLANILHA, params);

export const buscarEstatisticasAvaliacao = async ({ commit }, idPronac) => avaliacaoResultadosHelperAPI
    .buscarEstatisticasAvaliacao(idPronac)
    .then((response) => {
        const { data } = response.data;
        commit(types.SET_ESTATISTICAS_AVALIACAO, data);
    });
