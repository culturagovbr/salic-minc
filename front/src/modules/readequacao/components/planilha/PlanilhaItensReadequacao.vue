<template>
    <div class="itens">
        <v-data-table
            :headers="headers"
            :items="table"
            :rows-per-page-items="[-1]"
            :loading="loading"
            item-key="idPlanilhaAprovacao"
            class="elevation-1"
            hide-actions
        >
            <v-progress-linear
                slot="progress"
                color="blue"
                indeterminate/>
            <template
                slot="items"
                slot-scope="props"
            >
                <tr
                    :class="getClassItem(props.item)"
                    style="cursor: pointer"
                    @click="props.expanded = !props.expanded"
                >
                    <td class="text-xs-left">
                        <a
                            v-if="isItemDisponivelEdicao(props.item) && !readonly"
                            href="javascript:void(0);"
                        >
                            {{ props.item.Item }}
                        </a>
                        <span v-else>
                            {{ props.item.Item }}
                        </span>
                    </td>
                    <td class="text-xs-center">{{ props.item.Unidade }}</td>
                    <td class="text-xs-center">{{ props.item.QtdeDias }}</td>
                    <td class="text-xs-center">{{ props.item.Quantidade }}</td>
                    <td class="text-xs-center">{{ props.item.Ocorrencia }}</td>
                    <td class="text-xs-right">
                        <v-tooltip
                            v-if="validarValorPraticado(props.item).valid === false"
                            bottom
                        >
                            <v-badge
                                slot="activator"
                                right
                                color="orange darken-4"
                            >
                                <span slot="badge">
                                    !
                                </span>
                                {{ props.item.vlUnitario | filtroFormatarParaReal }}
                            </v-badge>
                            <span> {{ validarValorPraticado(props.item).message }}</span>
                        </v-tooltip>
                        <span v-else>
                            {{ props.item.vlUnitario | filtroFormatarParaReal }}
                        </span>
                    </td>
                    <td class="text-xs-right">{{ props.item.vlAprovado | filtroFormatarParaReal }}</td>
                    <td class="text-xs-right">{{ props.item.vlComprovado | filtroFormatarParaReal }}</td>
                </tr>
            </template>
            <template
                slot="expand"
                slot-scope="props">
                <v-layout
                    wrap
                    column
                    class="blue-grey lighten-5 pa-2">
                    <v-card>
                        <v-card-title class="py-1">
                            <h3>Visualizando item: {{ props.item.Item }} </h3>
                        </v-card-title>
                        <v-divider/>
                        <v-card-text>
                            <div
                                v-if="isItemDisponivelEdicao(props.item) && !readonly"
                            >
                                <v-card>
                                    <visualizar-item-planilha
                                        :item="props.item"
                                        :original="true"
                                    >
                                        <v-layout
                                            slot="header"
                                            row
                                        >
                                            <v-flex
                                                xs12
                                            >
                                                <h3
                                                    class="subheading"
                                                >
                                                    Dados originais
                                                </h3>
                                            </v-flex>
                                        </v-layout>
                                    </visualizar-item-planilha>
                                </v-card>
                                <v-card>
                                    <v-card-text>
                                        <v-alert
                                            :value="isCustoPraticado(props.item)"
                                            class="py-2"
                                            type="warning"
                                            dismissible
                                            outline
                                            color="orange darken-4"
                                        >
                                            {{ obterMensagemCustoPraticado(props.item) }}
                                        </v-alert>
                                        <div
                                            v-if="isCustoPraticado(props.item)"
                                            class="text-xs-center"
                                        >
                                            <v-btn
                                                v-if="exibirMediana(props.item)"
                                                color="blue-grey"
                                                class="white--text"
                                                @click="buscarMediana(props.item)"
                                            >
                                                Ver mediana
                                                <v-icon
                                                    right
                                                    dark
                                                >
                                                    monetization_on
                                                </v-icon>
                                            </v-btn>
                                        </div>
                                    </v-card-text>
                                </v-card>
                                <editar-item-planilha
                                    :item="props.item"
                                    @fechar-item="props.expanded = false"
                                />
                            </div>
                            <div v-else>
                                <visualizar-item-planilha
                                    :item="props.item"
                                />
                            </div>
                        </v-card-text>
                    </v-card>
                </v-layout>
            </template>
        </v-data-table>
        <s-planilha-dialog-dados-mediana
            ref="mediana"
            v-model="modalMediana"
        />
    </div>
</template>

<script>
import { utils } from '@/mixins/utils';
import EditarItemPlanilha from './EditarItemPlanilha';
import VisualizarItemPlanilha from './VisualizarItemPlanilha';
import MxPlanilhaReadequacao from '../../mixins/PlanilhaReadequacao';
import SPlanilhaDialogDadosMediana from '@/components/Planilha/PlanilhaDialogDadosMediana';

export default {
    name: 'PlanilhaItensReadequacao',
    components: {
        EditarItemPlanilha,
        VisualizarItemPlanilha,
        SPlanilhaDialogDadosMediana,
    },
    mixins: [
        MxPlanilhaReadequacao,
        utils,
    ],
    props: {
        table: {
            type: Array,
            required: true,
        },
        readonly: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            valid: false,
            expand: false,
            loading: false,
            headers: [
                { text: 'Item', align: 'left', value: 'Item' },
                { text: 'Unidade', align: 'left', value: 'Unidade' },
                { text: 'Dias', align: 'center', value: 'QtdeDias' },
                { text: 'Qtde', align: 'center', value: 'Quantidade' },
                { text: 'Ocor.', align: 'center', value: 'Ocorrencia' },
                { text: 'Vl. Unitário', align: 'right', value: 'vlUnitario' },
                { text: 'Vl. Total', align: 'right', value: 'vlAprovado' },
                { text: 'Vl. Comprovado', align: 'right', value: 'vlComprovado' },
            ],
            itemEmEdicao: {},
            select: {},
            minChar: 10,
            modalMediana: false,
        };
    },
    methods: {
        getClassItem(row) {
            return this.obterClasseItem(row);
        },
        isCustoPraticado(item) {
            const idEtapasNaoExibir = [8, 10];
            if (!idEtapasNaoExibir.includes(item.idEtapa)) {
                return (item.stCustoPraticado === true
                        || parseInt(item.stCustoPraticado, 10) === 1);
            }
            return false;
        },
        obterMensagemCustoPraticado(item) {
            return `O valor unitário (${this.formatarParaReal(item.vlUnitario)}) deste item para ${item.Municipio},
                    ultrapassa o valor aprovado por este orgão. Faça uma nova sugestão de valor e justifique`;
        },
        buscarMediana(item) {
            this.modalMediana = true;
            this.$refs.mediana.obterMediana({
                idProduto: item.idProduto,
                idUnidade: item.idUnidade,
                idPlanilhaItem: item.idPlanilhaItem,
                idUfDespesa: item.idUF,
                idMunicipioDespesa: item.idMunicipio,
            });
        },
        validarValorPraticado(item) {
            let validacao = {
                valid: true,
                message: '',
            };
            if (this.isCustoPraticado(item)) {
                validacao = {
                    valid: false,
                    message: this.obterMensagemCustoPraticado(item),
                };
            }
            return validacao;
        },
        exibirMediana(item) {
            const idEtapasNaoExibir = [8, 10];
            if (!idEtapasNaoExibir.includes(item.idEtapa)) {
                return true;
            }
            return false;
        },
    },
};
</script>
