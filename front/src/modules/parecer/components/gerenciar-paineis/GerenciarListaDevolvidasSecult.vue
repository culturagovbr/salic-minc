<template>
    <div>
        <v-data-table
            :headers="headers"
            :items="produtos"
            :rows-per-page-items="[15, 35, 50, {'text': 'Todos', value: -1}]"
            :search="search"
            :expand="expand"
            item-key="idDistribuirParecer"
            class="elevation-1 table__expand"
            disable-initial-sort
        >
            <template
                slot="items"
                slot-scope="props"
            >
                <tr
                    :class="mxObterClasseItem(props.item)"
                >
                    <td>
                        <v-btn
                            color="blue-grey darken-2"
                            class="ma-0"
                            flat
                            icon
                            @click.prevent="props.expanded = !props.expanded"
                        >
                            <v-icon>
                                {{ props.expanded ? 'expand_less' : 'expand_more' }}
                            </v-icon>
                        </v-btn>
                    </td>
                    <td-numero-pronac :produto="props.item" />
                    <td>{{ props.item.nomeProjeto }}</td>
                    <td-nome-produto :produto="props.item" />
                    <td-tipo-produto :produto="props.item" />
                    <td>{{ props.item.segmento }}</td>
                    <td>{{ props.item.nomeParecerista }}</td>
                    <td class="text-xs-right">
                        {{ props.item.dtDistribuicao | formatarData }}
                    </td>
                    <td
                        class="text-xs-center"
                        style="min-width: 230px"
                    >
                        <v-tooltip
                            bottom
                        >
                            <v-btn
                                slot="activator"
                                color="green darken-2"
                                class="ma-0"
                                flat
                                icon
                                @click="$emit('distribuir-produto', props.item)"
                            >
                                <v-icon>
                                    person
                                </v-icon>
                            </v-btn>
                            <span>Redistribuir/Encaminhar produto</span>
                        </v-tooltip>
                        <v-tooltip
                            bottom
                        >
                            <v-btn
                                slot="activator"
                                color="orange darken-4"
                                flat
                                icon
                                class="ma-0"
                                @click="$emit('devolver-produto-para-secult', props.item)"
                            >
                                <v-icon>
                                    assignment_return
                                </v-icon>
                            </v-btn>
                            <span>Devolver para o Ministério</span>
                        </v-tooltip>
                        <v-tooltip
                            bottom
                        >
                            <v-btn
                                slot="activator"
                                color="blue-grey darken-2"
                                class="ma-0"
                                flat
                                icon
                                @click="$emit('reanalisar-produto', props.item)"
                            >
                                <v-icon>
                                    arrow_back
                                </v-icon>
                            </v-btn>
                            <span>Devolver para reanálise do parecerista</span>
                        </v-tooltip>
                        <v-tooltip
                            bottom
                        >
                            <v-btn
                                slot="activator"
                                color="blue-grey darken-2"
                                flat
                                icon
                                @click="$emit('visualizar-historico', props.item)"
                            >
                                <v-icon>
                                    history
                                </v-icon>
                            </v-btn>
                            <span>Visualizar histórico</span>
                        </v-tooltip>
                    </td>
                </tr>
            </template>

            <template v-slot:expand="props">
                <v-card flat>
                    <v-card-text>
                        <v-layout
                            row
                            wrap
                            class="mb-3"
                        >
                            <v-flex
                                xs12
                                sm12
                                md12
                            >
                                <b>Justificativa do atendimento</b><br>
                                <salic-texto-simples :texto="props.item.justificativaDevolucao" />
                            </v-flex>
                        </v-layout>
                        <v-layout
                            row
                            wrap
                            class="mb-3"
                        >
                            <v-flex
                                xs12
                                sm12
                                md12
                            >
                                <b>Justificativa do componente</b><br>
                                <salic-texto-simples :texto="props.item.justificativaComponente" />
                            </v-flex>
                        </v-layout>
                        <v-layout
                            row
                            wrap
                            class="mb-3"
                        >
                            <v-flex
                                xs12
                                sm12
                                md12
                            >
                                <b>Justificativa da Secretaria</b><br>
                                <salic-texto-simples :texto="props.item.justificativaSecretaria" />
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                </v-card>
            </template>

            <template slot="no-data">
                <div class="text-xs-center">
                    Sem produtos devolvidos
                </div>
            </template>
        </v-data-table>
    </div>
</template>

<script>

import MxUtils from '@/mixins/utils';
import MxUtilsParecer from '@/modules/parecer/mixins/UtilsParecer';

import SalicTextoSimples from '@/components/SalicTextoSimples';
import TdNomeProduto from '@/modules/parecer/components/gerenciar-paineis/TdNomeProduto';
import TdTipoProduto from '@/modules/parecer/components/gerenciar-paineis/TdTipoProduto';
import TdNumeroPronac from '@/modules/parecer/components/gerenciar-paineis/TdNumeroPronac';

export default {
    name: 'GerenciarListaDevolvidasSecult',
    components: {
        TdNumeroPronac, TdTipoProduto, TdNomeProduto, SalicTextoSimples,
    },
    mixins: [MxUtils, MxUtilsParecer],

    props: {
        produtos: {
            type: Array,
            default: () => [],
        },
        search: {
            type: String,
            default: '',
        },
    },

    data() {
        return {
            dialogConfirmarEnvio: false,
            loading: false,
            produto: {},
            expand: false,
            headers: [
                {
                    text: '#',
                    sortable: false,
                },
                {
                    text: 'Pronac',
                    value: 'pronac',
                    width: '1',
                },
                {
                    text: 'Nome do Projeto',
                    align: 'left',
                    value: 'nomeProjeto',
                },
                {
                    text: 'Produto',
                    align: 'left',
                    value: 'nomeProduto',
                },
                {
                    text: 'Tipo',
                    value: 'stPrincipal',
                    width: '2',
                },
                {
                    text: 'Segmento',
                    align: 'left',
                    value: 'segmento',
                },
                {
                    text: 'Parecerista',
                    align: 'left',
                    value: 'parecerista',
                },
                { text: 'Vl. Incentivo', value: 'valor', width: '2' },
                { text: 'Ações', width: '4', value: 'stPrincipal' },
            ],
        };
    },
};
</script>

<style>
    .table__expand table.v-table thead th:first-child,
    .table__expand table.v-table tbody tr:first-child td:first-child {
        max-width: 40px;
        text-align: center !important;
        padding: 0;
    }
</style>
