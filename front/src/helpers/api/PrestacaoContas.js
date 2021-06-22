import * as api from './base';

export const buscarPagamentosConsolidados = (idPronac) => {
    const modulo = '/prestacao-contas';
    const controller = '/pagamento-consolidados-rest';
    const metodo = '/index';
    const path = `${modulo}${controller}${metodo}`;
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};

export const buscarPagamentosUfMunicipio = (idPronac) => {
    const modulo = '/prestacao-contas';
    const controller = '/pagamento-uf-municipio-rest';
    const metodo = '/index';
    const path = `${modulo}${controller}${metodo}`;
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};

export const buscarExecucaoReceitaDespesa = (idPronac) => {
    const modulo = '/prestacao-contas';
    const controller = '/execucao-receita-despesa-rest';
    const metodo = '/index';
    const path = `${modulo}${controller}${metodo}`;
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};

export const buscarRelatorioFisico = (idPronac) => {
    const modulo = '/prestacao-contas';
    const controller = '/relatorio-fisico-rest';
    const metodo = '/index';
    const path = `${modulo}${controller}${metodo}`;
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};

export const buscarRelacaoPagamento = (idPronac) => {
    const modulo = '/prestacao-contas';
    const controller = '/relacao-pagamento-rest';
    const metodo = '/index';
    const path = `${modulo}${controller}${metodo}`;
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};

export const buscarRelatorioCumprimentoObjeto = (idPronac) => {
    const modulo = '/prestacao-contas';
    const controller = '/relatorio-cumprimento-objeto-rest';
    const metodo = '/index';
    const path = `${modulo}${controller}${metodo}`;
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};
