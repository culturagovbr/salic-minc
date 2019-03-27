import * as fooHelperAPI from '@/helpers/api/Foo';
import * as desencapsularResponse from '@/helpers/actions';
import * as types from './types';


export const obterDadosTabela = ({ commit }) => {
    fooHelperAPI.obterDadosTabela()
        .then((response) => {
            const items = desencapsularResponse.default(response);
            commit(types.SET_REGISTROS_TABELA, items);
        });
};

export const criarRegistro = ({ commit }, params) => {
    fooHelperAPI.criarRegistro(params)
        .then((response) => {
            const items = desencapsularResponse.default(response);
            commit(types.SET_REGISTRO_TABELA, items);
        });
};

export const atualizarRegistro = ({ commit }, params) => {
    fooHelperAPI.atualizarRegistro(params)
        .then((response) => {
            const items = desencapsularResponse.default(response);
            commit(types.ATUALIZAR_REGISTRO_TABELA, items);
        });
};

export const setRegistroAtivo = ({ commit }, registro) => {
    commit(types.SET_REGISTRO_ATIVO, registro);
};

export const removerRegistro = ({ commit }, registro) => {
    fooHelperAPI.removerRegistro(registro)
        .then(() => {
            commit(types.REMOVER_REGISTRO, registro);
        });
};
