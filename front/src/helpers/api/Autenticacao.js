import * as api from './base';

export const usuarioLogado = () => api.getRequest('/autenticacao/usuario/usuario/logado');