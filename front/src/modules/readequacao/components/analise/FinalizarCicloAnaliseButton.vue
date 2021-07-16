<template>
    <v-layout>
        <v-tooltip
            v-if="disponivelFinalizar"
            bottom
        >
            <v-icon
                slot="activator"
                color="light-green darken-1"
                @click.stop="dialog = true"
            >
                check
            </v-icon>
            <span>Finalizar ciclo de an&aacute;lise da Readequação</span>
        </v-tooltip>
        <div
            v-else
            style="width:24px"
        />
        <v-dialog
            v-model="dialog"
            max-width="450"
        >
            <v-card>
                <v-card-title class="headline">Finalizar o ciclo de an&aacute;lise da Readequação?</v-card-title>
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
    name: 'FinalizarCicloAnaliseButton',
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
        ...mapActions({
            finalizarCicloAnalise: 'readequacao/finalizarCicloAnalise',
            setSnackbar: 'noticias/setDados',
        }),
        finalizarAnalise() {
            if (typeof this.dadosReadequacao.idReadequacao !== 'undefined') {
                this.finalizarCicloAnalise({
                    idReadequacao: this.dadosReadequacao.idReadequacao,
                }).then(() => {
                    this.setSnackbar({
                        ativo: true,
                        color: 'success',
                        text: 'Ciclo de an&aacute;lise de readequação finalizado!',
                    });
                    this.dialog = false;
                });
            }
        },
    },
};
</script>
