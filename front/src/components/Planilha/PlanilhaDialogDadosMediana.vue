<template>
    <v-dialog
        v-model="dialog"
        max-width="600px"
    >
        <v-card>
            <v-card-title>
                <span class="headline">
                    {{ label }}
                </span>
            </v-card-title>
            <v-card-text>
                <s-carregando
                    v-if="loading"
                    text="Carregando mediana"
                />
                <v-container
                    v-else-if="Object.keys(mediana).length > 0"
                    grid-list-md
                >
                    <v-layout wrap>
                        <v-flex
                            xs12
                            md8
                        >
                            <b>Produto</b>
                            <div>{{ mediana.Produto }}</div>
                        </v-flex>
                        <v-flex
                            xs12
                            md4
                        >
                            <b>Item</b>
                            <div>{{ mediana.Item }}</div>
                        </v-flex>
                        <v-flex
                            xs12
                            md3
                        >
                            <b>Unidade.</b>
                            <div>{{ mediana.Unidade }}</div>
                        </v-flex>
                        <v-flex
                            xs12
                            md3
                        >
                            <b>Município</b>
                            <div>{{ mediana.Municipio }}</div>
                        </v-flex>
                        <v-flex
                            xs12
                            md6
                        >
                            <b>UF</b>
                            <div>{{ mediana.UF }}</div>
                        </v-flex>
                        <v-flex
                            xs12
                            md3
                        >
                            <b>Mediana</b>
                            <div>R$ {{ mediana.Mediana | filtroFormatarParaReal }}</div>
                        </v-flex>
                        <v-flex
                            xs12
                            md3
                        >
                            <b>Preço máximo</b>
                            <div>R$ {{ mediana.Preco_Maximo | filtroFormatarParaReal }}</div>
                        </v-flex>
                        <v-flex
                            xs12
                            md3
                        >
                            <b>Preço Médio</b>
                            <div>R$ {{ mediana.Preco_Medio | filtroFormatarParaReal }}</div>
                        </v-flex>
                        <v-flex
                            xs12
                            md3
                        >
                            <b>Preço Mínimo</b>
                            <div>R$ {{ mediana.Preco_Minimo | filtroFormatarParaReal }}</div>
                        </v-flex>
                    </v-layout>
                </v-container>
                <v-alert
                    :value="!loading && Object.keys(mediana).length === 0"
                    type="info"
                    outline
                >
                    <b>Valor mediano de referência não encontrado.</b> Adicione este texto na justificativa do item.
                </v-alert>
            </v-card-text>
            <v-card-actions>
                <v-spacer />
                <v-btn
                    color="blue darken-1"
                    flat
                    @click="close"
                >
                    Fechar
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import planilhas from '@/mixins/planilhas';
import SCarregando from '@/components/CarregandoVuetify';

export default {
    name: 'SPlanilhaDialogDadosMediana',
    components: { SCarregando },
    mixins: [planilhas],

    props: {
        label: {
            type: String,
            default: 'Valores de referência para o item',
        },
        value: {
            type: Boolean,
            default: false,
        },
        item: {
            type: Object,
            default: () => {},
        },
    },

    data() {
        return {
            loading: true,
            dialog: false,
        };
    },

    computed: {
        ...mapGetters({
            mediana: 'planilha/obterMediana',
        }),
    },

    watch: {
        value(val) {
            this.dialog = val;
        },
        dialog(val) {
            this.$emit('input', val);
        },
    },

    methods: {
        ...mapActions({
            obterMedianaAction: 'planilha/obterMediana',
        }),
        close() {
            this.dialog = false;
        },
        obterMediana(item) {
            if (Object.keys(item).length === 0) {
                return;
            }
            this.loading = true;
            this.obterMedianaAction(item)
                .finally(() => {
                    this.loading = false;
                });
        },
    },
};
</script>
