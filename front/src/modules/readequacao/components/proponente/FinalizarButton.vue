<template>
    <div
        v-if="perfilAceito"
    >
        <v-btn
            v-if="telaEdicao"
            :disabled="disabled"
            color="white--text blue darken-1"
            @click="dialog = true"
        >
            Finalizar Readequação
            <v-icon
                right
                dark
            >send</v-icon>
        </v-btn>
        <div v-else>
            <div v-if="dadosReadequacao.isDisponivelFinalizar === true">
                <v-btn
                    dark
                    icon
                    flat
                    small
                    color="green darken-3"
                    @click.stop="dialog = true"
                >
                    <v-tooltip bottom>
                        <v-icon slot="activator">send</v-icon>
                        <span>Finalizar Readequação</span>
                    </v-tooltip>
                </v-btn>
            </div>
            <div
                v-else
                style="width: 44px"
            />
        </div>
        <v-dialog
            v-model="dialog"
            max-width="350"
        >
            <v-card>
                <v-card-title class="headline">Finalizar Readequação?</v-card-title>
                <v-card-text>
                    <h4
                        class="title mb-2"
                        v-html="dadosProjeto.NomeProjeto"
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
                        @click="finalizar()"
                    >
                        OK
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import _ from 'lodash';
import { mapActions, mapGetters } from 'vuex';
import { utils } from '@/mixins/utils';
import Const from '../../const';
import MxReadequacao from '../../mixins/Readequacao';

export default {
    name: 'FinalizarButton',
    mixins: [
        utils,
        MxReadequacao,
    ],
    props: {
        disabled: {
            type: Boolean,
            default: false,
        },
        dadosProjeto: {
            type: Object,
            default: () => {},
        },
        dadosReadequacao: {
            type: Object,
            default: () => {},
        },
        telaEdicao: {
            type: Boolean,
            default: false,
        },
        minChar: {
            type: Object,
            default: () => {},
        },
        perfisAceitos: {
            type: [Array, Object],
            default: () => [],
        },
        perfil: {
            type: [Number, String],
            default: 0,
        },
        readequacaoEditada: {
            type: Object,
            default: () => {},
        },
        tela: {
            type: String,
            default: 'painel',
        },
    },
    data() {
        return {
            dialog: false,
            validacao: false,
        };
    },
    computed: {
        ...mapGetters({
            campoAtual: 'readequacao/getCampoAtual',
            getReadequacao: 'readequacao/getReadequacao',
        }),
        perfilAceito() {
            return this.verificarPerfil(this.perfil, this.perfisAceitos);
        },
    },
    watch: {
        getReadequacao: {
            handler() {
                this.validar();
            },
            deep: true,
        },
        minChar() {
            if (!_.isEmpty(this.minChar)) {
                this.validar();
            }
        },
    },
    created() {
        this.validar();
    },
    methods: {
        ...mapActions({
            obterCampoAtual: 'readequacao/obterCampoAtual',
            updateReadequacao: 'readequacao/updateReadequacao',
            finalizarReadequacaoPainel: 'readequacao/finalizarReadequacaoPainel',
            finalizarReadequacaoPlanilha: 'readequacao/finalizarReadequacaoPlanilha',
            setSnackbar: 'noticias/setDados',
        }),
        validar() {
            if (typeof this.minChar === 'object') {
                if (typeof this.minChar.solicitacao === 'number') {
                    if (typeof this.getReadequacao.dsSolicitacao !== 'undefined'
                        && typeof this.getReadequacao.dsJustificativa !== 'undefined') {
                        let solicitacao = this.getReadequacao.dsSolicitacao.length;
                        if (parseInt(this.getReadequacao.dsSolicitacao, 10) === 0) {
                            solicitacao = 0;
                        }
                        const contador = {
                            solicitacao,
                            justificativa: this.getReadequacao.dsJustificativa.length,
                        };
                        if (this.getReadequacao.idTipoReadequacao === Const.TIPO_READEQUACAO_PERIODO_EXECUCAO) {
                            const key = `key_${this.getReadequacao.idTipoReadequacao}`;
                            if (typeof this.campoAtual[key] === 'undefined') {
                                this.obterCampoAtual({
                                    idPronac: this.getReadequacao.idPronac,
                                    idTipoReadequacao: this.getReadequacao.idTipoReadequacao,
                                }).then(() => {
                                    this.validacao = this.validarFormulario(
                                        this.getReadequacao,
                                        contador,
                                        this.minChar,
                                        this.campoAtual[`key_${this.getReadequacao.idTipoReadequacao}`].dsCampo,
                                    );
                                });
                            }
                        } else {
                            this.validacao = this.validarFormulario(
                                this.getReadequacao,
                                contador,
                                this.minChar,
                            );
                        }
                    }
                }
            }
        },
        executaFinalizar() {
            switch (this.tela) {
            case 'planilha':
                this.finalizarReadequacaoPlanilha({
                    idReadequacao: this.dadosReadequacao.idReadequacao,
                    idPronac: this.dadosReadequacao.idPronac,
                })
                    .then(() => {
                        this.setSnackbar({
                            ativo: true,
                            color: 'success',
                            text: 'Readequação finalizada!',
                        });
                        this.dialog = false;
                    });
                break;
            default:
                this.finalizarReadequacaoPainel({
                    idReadequacao: this.dadosReadequacao.idReadequacao,
                    idPronac: this.dadosReadequacao.idPronac,
                })
                    .then(() => {
                        this.setSnackbar({
                            ativo: true,
                            color: 'success',
                            text: 'Readequação finalizada!',
                        });
                        this.dialog = false;
                    });
                break;
            }
        },
        finalizar() {
            if (typeof this.readequacaoEditada !== 'undefined') {
                if ((this.readequacaoEditada.dsJustificativa !== this.getReadequacao.dsJustificativa)
                    || (this.readequacaoEditada.dsSolicitacao !== this.getReadequacao.dsSolicitacao)) {
                    this.updateReadequacao(this.readequacaoEditada)
                        .then(() => {
                            this.executaFinalizar();
                        });
                } else {
                    this.executaFinalizar();
                }
            } else {
                this.executaFinalizar();
            }
        },
    },
};
</script>
