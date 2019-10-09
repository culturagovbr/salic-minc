export default {
    methods: {
        statusDiligencia(obj) {
            let fim = new Date();
            const prazo = this.prazoResposta(obj);
            const result = new Date(obj.dataSolicitacao);
            fim.setTime(result.getTime() + (40 * 24 * 60 * 60 * 1000));
            fim = fim.toLocaleString(['pt-BR'], {
                month: '2-digit',
                day: '2-digit',
                year: 'numeric',
            });
            let status = {
                color: 'grey',
                desc: 'Histórico Diligências',
                prazo: fim,
            };
            const prazoPadrao = 20;
            // diligenciado
            if (obj.DtSolicitacao && obj.DtResposta === ''
                && prazo <= prazoPadrao && obj.stEnviado === 'S') {
                status = { color: 'yellow', desc: 'Diligenciado', prazo: fim };
                return status;
                // diligencia não respondida
            } if (obj.DtSolicitacao && obj.DtResposta === '' && prazo > prazoPadrao) {
                status = { color: 'red', desc: 'Diligencia não respondida', prazo: fim };
                return status;
                // diligencia respondida com ressalvas
            } if (obj.DtSolicitacao && obj.DtResposta !== '') {
                if (obj.stEnviado === 'N' && prazo > prazoPadrao) {
                    status = { color: 'red', desc: 'Diligencia não respondida', prazo: fim };
                    return status;
                }
                if (obj.stEnviado === 'N' && prazo < prazoPadrao) {
                    status = { color: 'yellow', desc: 'Diligenciado', prazo: fim };
                    return status;
                }
                status = { color: 'blue', desc: 'Diligencia respondida', prazo: fim };
                return status;
            }
            status = { color: 'green', desc: 'A Diligenciar', prazo: fim };
            return status;
        },
        prazoResposta(obj) {
            let now;
            let timeDiff;
            let prazo;
            if (typeof obj.dataSolicitacao !== 'undefined') {
                now = Date.now();
                timeDiff = Math.abs(now - new Date(obj.dataSolicitacao));
                prazo = Math.ceil(timeDiff / (1000 * 3600 * 24));
                if (prazo > 0) {
                    // prazo positivo
                    return prazo;
                }
                if (prazo <= 0) {
                    // prazo negativo
                    return 0;
                }
                if (prazo === 40) {
                    // para prazo de resposta igual ao padrão
                    return -1;
                }
            }
            return null;
        },
    },
};
