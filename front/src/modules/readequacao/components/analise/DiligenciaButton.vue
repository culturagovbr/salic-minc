<template>
    <v-layout>
        <v-tooltip
            bottom
        >
            <v-icon
                slot="activator"
                :color="corDiligencia"
                @click.stop="redirect()"
            >
                notification_important
            </v-icon>
            <span>{{ tituloDiligencia }}</span>
        </v-tooltip>
        <v-dialog
            v-model="dialog"
            fullscreen
            hide-overlay
            transition="dialog-bottom-transition"
            @keydown.esc="dialog = false"
        >
            <div>diligencia</div>
        </v-dialog>
    </v-layout>
</template>
<script>
import Const from '../../const';

export default {
    name: 'DiligenciaButton',
    props: {
        dadosReadequacao: {
            type: Object,
            default: () => {},
        },
        perfil: {
            type: [Number, String],
            default: 0,
        },
    },
    data() {
        return {
            dialog: false,
            tiposDiligencia: {
                diligenciado: {
                    cor: 'blue lighten-2',
                    titulo: 'Diligenciado',
                },
                nao_respondida: {
                    cor: 'orange lighten-3',
                    titulo: 'Não respondida',
                },
                diligencia_respondida: {
                    cor: 'green lighten-1',
                    titulo: 'Diligência respondida',
                },
                a_diligenciar: {
                    cor: 'grey lighten-1',
                    titulo: 'A diligenciar',
                },
            },
        };
    },
    computed: {
        corDiligencia() {
            return this.tiposDiligencia[this.dadosReadequacao.diligencia.tipoDiligencia].cor;
        },
        tituloDiligencia() {
            return this.tiposDiligencia[this.dadosReadequacao.diligencia.tipoDiligencia].titulo;
        },
    },
    methods: {
        redirect() {
            if (typeof this.dadosReadequacao.idPronac !== 'undefined') {
                let tpDiligencia = 0;
                if (parseInt(this.perfil, 10) === Const.PERFIL_PARECERISTA) {
                    tpDiligencia = 179;
                } else if (parseInt(this.idPerfil, 10) === Const.PERFIL_TECNICO_ACOMPANHAMENTO) {
                    tpDiligencia = 171;
                }
                const baseUrlDiligencia = '/proposta/diligenciar/listardiligenciaanalista?idPronac=';
                const complemento = `${this.dadosReadequacao.idPronac}&situacao=E59&tpDiligencia=${tpDiligencia}`;
                window.open(baseUrlDiligencia + complemento, '_blank');
            }
        },
    },
};
</script>
