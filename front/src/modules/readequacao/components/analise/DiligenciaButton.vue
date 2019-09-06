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
            :id-pronac="dadosReadequacao.idPronac"
            :id-readequacao="dadosReadequacao.idReadequacao"
            :tp-diligencia="tpDiligencia"
            @diligencia-criada="diligenciaCriada()"
        />
    </v-layout>
</template>
<script>
import { mapActions } from 'vuex';
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
        };
    },
    computed: {
        corDiligencia() {
            return this.tiposDiligencia[this.dadosReadequacao.diligencia.tipoDiligencia].cor;
        },
        tituloDiligencia() {
            return this.tiposDiligencia[this.dadosReadequacao.diligencia.tipoDiligencia].titulo;
        },
        tpDiligencia() {
            if (Object.prototype.hasOwnProperty.call(Const.TP_DILIGENCIA_POR_PERMISSAO, parseInt(this.perfil, 10))) {
                return Const.TP_DILIGENCIA_POR_PERMISSAO[parseInt(this.perfil, 10)];
            }
            return 0;
        },
    },
    methods: {
        ...mapActions({
            buscarReadequacoesPainelTecnico: 'readequacao/buscarReadequacoesPainelTecnico',
        }),
        visualizarDiligencia() {
            this.dialogDiligencias = true;
        },
        diligenciaCriada() {
            this.buscarReadequacoesPainelTecnico({});
        },
    },
};
</script>
