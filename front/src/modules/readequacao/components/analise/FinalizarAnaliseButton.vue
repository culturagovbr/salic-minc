<template>
    <v-layout>
        <v-tooltip
            v-if="disponivelFinalizar"
            bottom
        >
            <v-icon
                slot="activator"
                color="orange accent-4"
                @click.stop="dialog = true"
              >
              send
            </v-icon>
            <span>Finalizar Readequação</span>
        </v-tooltip>
        <div
            v-else
            style="width:24px"
        />
        <v-dialog
            v-model="dialog"
            max-width="350"
        >
            <v-card>
                <v-card-title class="headline">Finalizar análise da Readequação?</v-card-title>
                <v-card-text>
                    <h4
                        class="title mb-2"
                        v-html="dadosReadequacao.NomeProjeto"
                    />
                    <h4>Readequação do Tipo:</h4>
                    <span v-html="dadosReadequacao.dsTipoReadequacao"/>
                    <h4>Data de abertura: </h4>
                    <span>{{ dadosReadequacao.dtSolicitacao | formatarData }}</span>
                </v-card-text>
                <v-card-actions>
                    <v-spacer/>
                    <v-btn
                        color="red darken-1"
                        flat="flat"
                        @click="dialog = false"
                    >
                        Cancelar
                    </v-btn>

                    <v-btn
                        color="green darken-1"
                        flat="flat"
                        @click="finalizarAnalise()"
                    >
                        OK
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-layout>
</template>

<script>
import { mapActions } from 'vuex';
import { utils } from '@/mixins/utils';
import Const from '../../const';

export default {
    name: 'FinalizarAnaliseButton',
    mixins: [
        utils,
    ],
    props: {
        dadosReadequacao: {
            type: Object,
            default: () => {},
        },
    },
    data() {
        return {
            dialog: false,
        };
    },
    computed: {
        disponivelFinalizar() {
            if (typeof this.dadosReadequacao.siEncaminhamento !== 'undefined') {
                if (this.dadosReadequacao.siEncaminhamento === Const.SI_ENCAMINHAMENTO_SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_FINAL) {
                    return true;
                }
            }
            return false;
        },
    },
    methods: {
        finalizarAnalise() {
            // TODO
        },
    },
};
</script>
