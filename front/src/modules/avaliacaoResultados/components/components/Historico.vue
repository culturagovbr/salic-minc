<template>
    <v-dialog
        v-model="dialog"
        scrollable
        max-width="750px"
    >
        <v-tooltip
            slot="activator"
            bottom
        >
            <v-btn
                slot="activator"
                flat
                icon
            >
                <v-icon class="material-icons">
                    history
                </v-icon>
            </v-btn>
            <span>Histórico de Encaminhamentos</span>
        </v-tooltip>

        <v-card>
            <v-card-title
                class="headline primary"
                primary-title
            >
                <span class="white--text">
                    Histórico de encaminhamentos
                </span>
            </v-card-title>

            <v-card-text style="height: 500px;">
                <v-subheader class="mb-4">
                    <h4 class="title mb-0 grey--text text--darken-3">
                        {{ pronac }} - {{ nomeProjeto }}
                    </h4>
                </v-subheader>

                <carregando-vuetify
                    v-if="loading"
                    text="Carregando  ..."
                />
                <v-data-table
                    v-else
                    :headers="historicoHeaders"
                    :items="dadosHistoricoEncaminhamento"
                    hide-actions
                >
                    <template
                        slot="items"
                        slot-scope="props"
                    >
                        <td>{{ props.item.dtInicioEncaminhamento | formatarData }}</td>
                        <td>{{ props.item.NomeOrigem }}</td>
                        <td>{{ props.item.NomeDestino }}</td>
                        <td>{{ props.item.dsJustificativa }}</td>
                    </template>
                    <template slot="no-data">
                        <v-alert
                            :value="true"
                            color="info"
                            icon="info"
                        >
                            Nenhum dado encontrado ¯\_(ツ)_/¯
                        </v-alert>
                    </template>
                </v-data-table>
            </v-card-text>

            <v-divider />

            <v-card-actions>
                <v-spacer />
                <v-btn
                    color="red"
                    flat
                    @click="dialog = false"
                >
                    Fechar
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
import { mapActions, mapGetters } from 'vuex';
import CarregandoVuetify from '@/components/CarregandoVuetify';

export default {
    name: 'Painel',
    components: { CarregandoVuetify },
    filters: {
        formatarData(value) {
            const date = new Date(value);

            return date.toLocaleString(['pt-BR'], {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
            });
        },
    },
    props: {
        idPronac: { type: String, default: '' },
        pronac: { type: String, default: '' },
        nomeProjeto: { type: String, default: '' },
    },
    data() {
        return {
            loading: false,
            projetoHeaders: [
                {
                    text: 'PRONAC',
                    align: 'left',
                    sortable: false,
                    value: 'pronac',
                },
                {
                    text: 'Nome do Projeto',
                    align: 'left',
                    sortable: false,
                    value: 'nomeProjeto',
                },
            ],
            historicoHeaders: [
                {
                    text: 'Data de Envio',
                    align: 'left',
                    sortable: false,
                    value: 'dataEnvio',
                },
                {
                    text: 'Nome do Remetente',
                    align: 'left',
                    sortable: false,
                    value: 'nomeRemetente',
                },
                {
                    text: 'Nome do Destinatário',
                    align: 'left',
                    sortable: false,
                    value: 'nomeDestinatario',
                },
                {
                    text: 'Justificativa',
                    align: 'left',
                    sortable: false,
                    value: 'justificativa',
                },
            ],
            dialog: false,
        };
    },
    computed: mapGetters({
        dadosHistoricoEncaminhamento: 'avaliacaoResultados/dadosHistoricoEncaminhamento',
    }),
    watch: {
        dialog(val) {
            if (val) {
                this.obterHistoricoEncaminhamento(this.idPronac);
            }
        },
    },
    methods: {
        ...mapActions({
            obterHistoricoEncaminhamentoAction: 'avaliacaoResultados/obterHistoricoEncaminhamento',
        }),
        obterHistoricoEncaminhamento(params) {
            this.loading = true;
            this.obterHistoricoEncaminhamentoAction(params).finally(() => {
                this.loading = false;
            });
        },
    },
};
</script>
