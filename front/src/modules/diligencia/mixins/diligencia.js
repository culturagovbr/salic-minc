export default {
    methods: {
        obterConfigDiligencia(item) {
            let diligencia = {};
            switch (item.stDiligencia) {
            case 1:
                diligencia = {
                    cor: 'yellow accent-4',
                    corIcone: 'yellow darken-4',
                    texto: `Diligenciado há ${item.diasEmDiligencia} dia(s)`,
                };
                break;
            case 2:
                diligencia = {
                    cor: 'green lighten-3',
                    corIcone: 'green darken-4',
                    texto: 'Diligência respondida',
                };
                break;
            case 3:
                diligencia = {
                    cor: 'orange lighten-3',
                    corIcone: 'orange darken-4',
                    texto: 'Diligência não respondida',
                };
                break;
            default:
                diligencia = {
                    cor: 'grey lighten-3',
                    corIcone: 'blue-grey darken-2',
                    texto: 'Disponível para dilig&ecirc;ncia',
                };
                break;
            }
            return diligencia;
        },
    },
};
