export default {
    methods: {
        obterSituacao(produto) {
            let situacao = '';
            switch (true) {
            case produto.siAnalise === 0
                && produto.siEncaminhamento === 4:
                situacao = 'Aguardando análise';
                break;
            case produto.siAnalise === 1
                && produto.siEncaminhamento === 4:
                situacao = 'Em análise';
                break;
            case produto.siAnalise === 2
                && produto.siEncaminhamento === 4:
                situacao = 'Analisado';
                break;
            case produto.siAnalise === 3
                && produto.siEncaminhamento === 4:
                situacao = 'Finalizado';
                break;
            default:
                situacao = 'Aguardando distribuição';
                break;
            }
            return situacao;
        },
        mxObterClasseItem(item) {
            let classe = {};
            switch (true) {
            case item.tipoAnalise === 1:
                classe = { 'purple lighten-5': true };
                break;
            default:
                classe = {};
                break;
            }
            return classe;
        },
    },
};
