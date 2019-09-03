<template>
    <v-layout>
        <v-tooltip
            bottom
        >
            <v-btn
                slot="activator"
                :color="obterConfigDiligencia(dadosReadequacao).cor"
                target="_blank"
                icon
                @click="visualizarDiligencia()"
            >
                <v-badge
                    :value="dadosReadequacao.diasEmDiligencia > 0"
                    color="grey lighten-1"
                    overlap
                    left
                >
                    <span slot="badge">{{ dadosReadequacao.diasEmDiligencia }}</span>
                    <v-icon
                        :color="obterConfigDiligencia(dadosReadequacao).corIcone"
                    >
                        notification_important
                    </v-icon>
                </v-badge>
            </v-btn>
            <span> {{ obterConfigDiligencia(dadosReadequacao).texto }} </span>
        </v-tooltip>
        <s-dialog-diligencias
            v-model="dialogDiligencias"
            :id-pronac="diligenciaVisualizacao.idPronac"
            :tp-diligencia="dadosReadequacao.tpDiligencia"
        />
    </v-layout>
</template>
<script>
import Const from '../../const';
import MxDiligencia from '@/modules/diligencia/mixins/diligencia';
import SDialogDiligencias from '@/modules/diligencia/components/SDialogDiligencias';

export default {
    name: 'DiligenciaButton',
    components: {
        SDialogDiligencias,
    },
    mixins: [
        MxDiligencia,
    ],
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
            dialogDiligencias: false,
            diligenciaVisualizacao: {
                idPronac: 0,
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
        visualizarDiligencia() {
            this.dialogDiligencias = true;
            this.diligenciaVisualizacao = {
                idPronac: this.dadosReadequacao.idPronac,
            };
        },
    },
};
</script>
