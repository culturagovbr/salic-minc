<template>
    <div
        v-if="perfilAceito"
    >
        <v-btn
            v-if="telaEdicao"
            :disabled="disabled"
            dark
            color="red darken-3"
            @click="dialog = true"
        >
            {{ textoBotao }}
            <v-icon
                right
                dark
            >cancel</v-icon>
        </v-btn>
        <div v-else>
            <v-btn
                dark
                icon
                flat
                small
                color="red darken-3"
                @click.stop="dialog = true"
            >
                <v-tooltip bottom>
                    <v-icon slot="activator">close</v-icon>
                    <span>Excluir Readequação</span>
                </v-tooltip>
            </v-btn>
        </div>
        <v-dialog
            v-model="dialog"
            max-width="350"
        >
            <v-card>
                <v-card-title class="headline">Excluir Readequação?</v-card-title>
                <v-card-text
                    v-if="!loading"
                >
                    <h4
                        class="title mb-2"
                        v-html="dadosProjeto.NomeProjeto"
                    />
                    <h4>Readequação do Tipo:</h4>
                    <span v-html="dadosReadequacao.dsTipoReadequacao"/>
                    <h4>Data de abertura: </h4>
                    <span>{{ dadosReadequacao.dtSolicitacao | formatarData }}</span>
                </v-card-text>
                <v-card-actions
                    v-if="!loading"
                >
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
                        @click="excluir()"
                    >
                        OK
                    </v-btn>
                </v-card-actions>
                <v-card-actions
                    v-else
                >
                    <carregando
                        :text="'Removendo a readequação...'"
                        class="mt-5 mb-5"
                    />
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import { mapActions } from 'vuex';
import { utils } from '@/mixins/utils';
import Carregando from '@/components/CarregandoVuetify';
import MxReadequacao from '../mixins/Readequacao';

export default {
    name: 'ExcluirButton',
    components: {
        Carregando,
    },
    mixins: [
        utils,
        MxReadequacao,
    ],
    props: {
        textoBotao: {
            type: String,
            default: 'Excluir',
        },
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
        origem: {
            type: String,
            default: 'painel',
        },
        perfisAceitos: {
            type: Array,
            default: () => [],
        },
        perfil: {
            type: [Number, String],
            default: 0,
        },
    },
    data() {
        return {
            dialog: false,
            loading: false,
        };
    },
    computed: {
        perfilAceito() {
            return this.verificarPerfil(this.perfil, this.perfisAceitos);
        },
    },
    methods: {
        ...mapActions({
            excluirReadequacao: 'readequacao/excluirReadequacao',
        }),
        excluir() {
            this.loading = true;
            this.excluirReadequacao({
                idReadequacao: this.dadosReadequacao.idReadequacao,
                idPronac: this.dadosReadequacao.idPronac,
                origem: this.origem,
            }).then(() => {
                this.dialog = false;
                this.$emit('excluir-readequacao', { idReadequacao: this.dadosReadequacao.idReadequacao });
            });
        },
    },
};
</script>
