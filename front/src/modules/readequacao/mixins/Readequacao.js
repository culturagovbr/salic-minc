import _ from 'lodash';
import Const from '../const';

export default {
    methods: {
        validarFormulario(readequacao, contador, minChar, campo = '') {
            const valido = {
                solicitacao: false,
                justificativa: false,
            };
            if (typeof readequacao.dsJustificativa === 'string') {
                if (contador.justificativa >= minChar.justificativa) {
                    valido.justificativa = true;
                }
            }
            if (typeof readequacao.dsSolicitacao === 'string'
                && readequacao.dsSolicitacao.trim() !== ''
            ) {
                if (typeof readequacao.idTipoReadequacao !== 'undefined') {
                    if (readequacao.idTipoReadequacao === Const.TIPO_READEQUACAO_PERIODO_EXECUCAO) {
                        let campoLocal = campo;
                        if (campo.includes('/')) {
                            const [day, month, year] = campo.split('/');
                            campoLocal = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
                        }
                        if (campoLocal !== ''
                            && readequacao.dsSolicitacao.trim() !== campoLocal) {
                            if (contador.solicitacao >= minChar.dataExecucao) {
                                valido.solicitacao = true;
                            }
                        }
                    } else if (contador.solicitacao >= minChar.solicitacao) {
                        valido.solicitacao = true;
                    }
                }
            }
            return (valido.solicitacao && valido.justificativa);
        },
        validarItemPlanihla(item, contador, minChar) {
            const valido = {
                justificativa: false,
            };
            if (typeof item.dsJustificativa === 'string') {
                if (contador.justificativa >= minChar.justificativa) {
                    valido.justificativa = true;
                }
            }
            return (valido.justificativa);
        },
        removeLetras(valor) {
            const re = /([^0-9]*)/g;
            const novoValor = valor.replace(re, '');
            return novoValor;
        },
        verificarPerfil(perfil, perfisAceitos) {
            if (!_.isEmpty(perfisAceitos)
                && typeof perfisAceitos.includes === 'function'
            ) {
                if (perfisAceitos.includes(parseInt(perfil, 10))) {
                    return true;
                }
                return false;
            }
            return true;
        },
        abrirArquivo(idDocumento) {
            const urlArquivo = `/readequacao/readequacoes/abrir-documento-readequacao?id=${idDocumento}`;
            window.location.href = urlArquivo;
        },
        checkAlreadyLoadedData(loaded, usuario, projeto, readequacao) {
            const localLoaded = {
                readequacao: loaded.readequacao,
            };

            if (typeof loaded.projeto !== 'undefined') {
                Object.assign(
                    loaded,
                    {
                        projeto: loaded.projeto,
                    },
                );
            }
            if (typeof loaded.usuario !== 'undefined') {
                Object.assign(
                    loaded,
                    {
                        usuario: loaded.usuario,
                    },
                );
            }
            if (typeof usuario === 'object') {
                if (Object.keys(usuario).length > 0) {
                    localLoaded.usuario = true;
                }
            }
            if (typeof projeto === 'object') {
                if (Object.keys(projeto).length > 0) {
                    localLoaded.projeto = true;
                }
            }
            if (typeof readequacao === 'object') {
                if (Object.keys(readequacao).length > 0) {
                    localLoaded.readequacao = true;
                }
            }
            return localLoaded;
        },
        voltar() {
            this.$router.back();
        },
        parseDadosCampo(campoAtual) {
            const chave = `key_${this.dadosReadequacao.idTipoReadequacao}`;
            if (Object.prototype.hasOwnProperty.call(campoAtual, chave)) {
                return {
                    valor: campoAtual[chave].dsCampo,
                    titulo: campoAtual[chave].descricao,
                    tpCampo: campoAtual[chave].tpCampo,
                };
            }
            return {};
        },
    },
};
