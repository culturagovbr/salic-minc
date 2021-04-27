<template>
    <v-layout
        justify-end
        row
        wrap
    >
        <v-btn
            absolute
            dark
            color="green darken-1"
            class="mt-2 mb-5"
            @click="dialog = true"
        >
            CRIAR
            <v-icon
                right
                dark
            >add</v-icon>
        </v-btn>
        <v-flex
            xs4
            offset-xs4
        >
            <v-dialog
                v-model="dialog"
                transition="dialog-bottom-transition"
                width="450"
                @keydown.esc="dialog = false"
            >
                <v-card
                    v-if="getTiposDisponiveis"
                    class="pa-3"
                >
                    <v-card-title class="headline green darken-4 white--text">Nova Readequação</v-card-title>
                    <v-card-text>
                        <div
                            v-if="loading"
                        >
                            <carregando :text="'Criando readequação...'"/>
                        </div>
                        <div
                            v-else
                        >
                            <v-select
                                v-model="idTipoReadequacao"
                                :items="getTiposDisponiveis"
                                item-text="descricao"
                                item-value="idTipoReadequacao"
                                label="Escolha o tipo de readequação"
                                solo
                            />
                        </div>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn
                            color="red darken-1"
                            flat="flat"
                            @click="dialog = false"
                        >
                            Cancelar
                        </v-btn>
                        <v-spacer/>
                        <v-btn
                            :disabled="idTipoReadequacao == ''"
                            color="green darken-1"
                            dark
                            @click="criarReadequacao"
                        >CRIAR
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-flex>
    </v-layout>
</template>
<script>
import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';

export default {
    name: 'CriarReadequacao',
    components: {
        Carregando,
    },
    props: {
        idPronac: { type: String, default: () => '' },
    },
    data() {
        return {
            dialog: false,
            idTipoReadequacao: '',
            loading: false,
        };
    },
    computed: {
        ...mapGetters({
            getTiposDisponiveis: 'readequacao/getTiposDisponiveis',
        }),
    },
    watch: {
        idPronac() {
            this.obterTiposDisponiveis({ idPronac: this.idPronac });
        },
    },
    created() {
        if (this.idPronac) {
            this.obterTiposDisponiveis({ idPronac: this.idPronac });
        }
    },
    methods: {
        ...mapActions({
            obterTiposDisponiveis: 'readequacao/obterTiposDisponiveis',
            inserirReadequacao: 'readequacao/inserirReadequacao',
        }),
        criarReadequacao() {
            this.loading = true;
            this.inserirReadequacao({
                idPronac: this.idPronac,
                idTipoReadequacao: this.idTipoReadequacao,
            }).then((response) => {
                this.loading = false;
                this.dialog = false;
                this.$emit('criar-readequacao', response.items.idReadequacao);
            });
        },
    },
};
</script>
