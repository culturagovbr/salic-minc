export default {
    methods: {
        validarFormulario(readequacao, contador, minChar) {
            const valido = {
                solicitacao: false,
                justificativa: false,
            };
            if (typeof readequacao.dsJustificativa === 'string') {
                if (contador.justificativa >= minChar.justificativa) {
                    valido.justificativa = true;
                }
            }
            if (typeof readequacao.dsSolicitacao === 'string') {
                if (contador.solicitacao >= minChar.solicitacao) {
                    valido.solicitacao = true;
                }
            }
            return (valido.solicitacao && valido.justificativa);
        },
    },
};
