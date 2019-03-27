<template>
    <div>
        <v-card>
            <v-card-title>
                <h6>BENS MÓVEIS / IMÓVEIS DOADOS</h6>
            </v-card-title>
            <v-data-table
                :pagination.sync="pagination"
                :headers="headers"
                :items="dados.bensCadastrados"
                class="elevation-1 container-fluid"
            >
                <template
                    slot="items"
                    slot-scope="props"
                >
                    <td class="text-xs-left">{{ (props.item.tpBem === 'M') ? 'Móvel' : 'Imóvel' }}</td>
                    <td class="text-xs-left">{{ props.item.ItemOrcamentario }}</td>
                    <td
                        class="text-xs-left"
                        style="white-space: nowrap"
                    >
                        {{ props.item.CNPJCPF | cnpjFilter }}
                    </td>
                    <td class="text-xs-left">{{ props.item.NomeAgente }}</td>
                    <td class="text-xs-right">{{ props.item.qtBensDoados }}</td>
                    <td class="text-xs-center pl-5">
                        <v-btn
                            slot="activator"
                            flat
                            icon
                            @click="showItem(props.item)"
                        >
                            <v-icon>visibility</v-icon>
                        </v-btn>
                    </td>
                </template>
            </v-data-table>
            <div class="text-xs-center">
                <v-dialog
                    v-model="dialog"
                    width="50%"
                >
                    <v-card>
                        <v-card-title
                            class="headline grey lighten-2"
                            primary-title
                        >
                            Visualizar
                        </v-card-title>
                        <v-card-text>
                            <v-flex>
                                <span><b>Observações</b></span>
                                <p>
                                    {{ itemEmVisualizacao.dsObservacao }}
                                </p>
                            </v-flex>
                        </v-card-text>
                        <v-divider/>
                        <v-card-text>
                            <v-flex>
                                <span><b>Arquivo Doação</b></span>
                                <v-btn
                                    :href="`/upload/abrir?id=${itemEmVisualizacao.idArquivoDoacao}`"
                                    target="_blank"
                                    style="text-decoration: none"
                                    round
                                    small
                                >
                                    {{ itemEmVisualizacao.nmArquivoDoacao }}
                                    <v-icon right>cloud_download</v-icon>
                                </v-btn>
                            </v-flex>
                        </v-card-text>
                        <v-divider/>
                        <v-card-text>
                            <v-flex>
                                <span><b>Arquivo Aceite</b></span>
                                <v-btn
                                    :href="`/upload/abrir?id=${itemEmVisualizacao.idArquivoAceite}`"
                                    round
                                    small
                                    target="_blank"
                                >
                                    {{ itemEmVisualizacao.nmArquivoAceite }}
                                    <v-icon right>cloud_download</v-icon>
                                </v-btn>
                            </v-flex>
                        </v-card-text>
                        <v-divider/>
                        <v-card-actions>
                            <v-spacer/>
                            <v-btn
                                color="red lighten-2"
                                flat
                                @click="dialog = false"
                            >
                                Fechar
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </div>
        </v-card>
    </div>
</template>
<script>
import { mapGetters } from 'vuex';
import { utils } from '@/mixins/utils';
import cnpjFilter from '@/filters/cnpj';

export default {
    name: 'BensImoveisMoveisDoados',
    filters: {
        cnpjFilter,
    },
    mixins: [utils],
    data() {
        return {
            itemEmVisualizacao: {},
            dialog: false,
            pagination: {
                sortBy: '',
                descending: true,
            },
            headers: [
                {
                    text: 'Tipo do Bem',
                    align: 'left',
                    value: 'tpBem',
                },
                {
                    text: 'Item Orçamentário',
                    align: 'left',
                    value: 'ItemOrcamentario',
                },
                {
                    text: 'CNPJ/CPF',
                    align: 'left',
                    value: 'CNPJCPF',
                },
                {
                    text: 'Nome',
                    align: 'left',
                    value: 'NomeAgente',
                },
                {
                    text: 'Quantidade',
                    align: 'right',
                    value: 'qtBensDoados',
                },
                {
                    text: 'Observação',
                    align: 'center',
                    value: 'dsObservacao',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dados: 'prestacaoContas/relatorioCumprimentoObjeto',
        }),
    },
    methods: {
        showItem(item) {
            this.itemEmVisualizacao = Object.assign({}, item);
            this.dialog = true;
        },
    },
};
</script>
