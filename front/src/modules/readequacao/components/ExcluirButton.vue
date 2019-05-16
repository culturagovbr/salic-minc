<template>
    <v-layout
        v-if="perfilAceito"
    >
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

        <v-dialog
            v-model="dialog"
            max-width="350"
        >
            <v-card>
                <v-card-title class="headline">Excluir Readequação?</v-card-title>
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
                        @click="excluir()"
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
import verificarPerfil from '../mixins/verificarPerfil';

export default {
    name: 'ExcluirButton',
    mixins: [
        utils,
        verificarPerfil,
    ],
    props: {
        obj: {
            type: Object,
            default: () => {},
        },
        dadosProjeto: {
            type: Object,
            default: () => {},
        },
        dadosReadequacao: {
            type: Object,
            default: () => {},
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
            this.excluirReadequacao({
                idReadequacao: this.dadosReadequacao.idReadequacao,
                idPronac: this.dadosReadequacao.idPronac,
            });
            this.dialog = false;
            this.$emit('excluir-readequacao', { idReadequacao: this.dadosReadequacao.idReadequacao });
        },
    },
};
</script>
