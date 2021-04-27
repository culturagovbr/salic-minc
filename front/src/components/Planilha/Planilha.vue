<template>
    <div
        v-if="arrayPlanilha.length > 0"
        class="planilha-orcamentaria"
    >
        <slot
            :planilha-montada="planilhaMontada"
            :totais="totaisProjeto"
            :array-planilha="arrayPlanilha"
            name="header"
        />
        <s-collapsible-recursivo
            v-if="!listItems"
            :planilha="planilhaMontada"
            :headers="headers"
            :expand-all="expandAll"
        >
            <template
                slot="badge"
                slot-scope="slotProps"
            >
                <slot
                    :planilha="slotProps.planilha"
                    name="badge"
                >
                    <v-chip
                        v-if="slotProps.planilha.vlSolicitado"
                        outline="outline"
                        label="label"
                        color="#565555"
                    >
                        R$ {{ formatarParaReal(slotProps.planilha.vlSolicitado) }}
                    </v-chip>
                </slot>
            </template>
            <template slot-scope="slotProps">
                <slot :itens="slotProps.itens">
                    <s-planilha-itens-padrao :table="slotProps.itens" />
                </slot>
            </template>
        </s-collapsible-recursivo>
        <slot
            v-else
            :itens="arrayPlanilha"
        >
            <s-planilha-itens-padrao :table="arrayPlanilha" />
        </slot>
        <slot
            :planilha-montada="planilhaMontada"
            :totais="totaisProjeto"
            :array-planilha="arrayPlanilha"
            name="footer"
        >
            <div
                v-for="(total, key) in totaisProjeto"
                :key="key"
                class="text-xs-right pa-3"
            >
                <span>
                    <b>{{ total.label }} total:</b>
                    R$ {{ total.value| filtroFormatarParaReal }}
                </span>
            </div>
        </slot>
    </div>
    <div
        v-else
        class="text-xs-center"
    >
        Nenhuma planilha encontrada
    </div>
</template>

<script>
import SPlanilhaItensPadrao from '@/components/Planilha/PlanilhaItensPadrao';
import SPlanilhaConsolidacao from '@/components/Planilha/PlanilhaConsolidacao';
import MxPlanilhas from '@/mixins/planilhas';

const SCollapsibleRecursivo = {
    name: 'SCollapsibleRecursivo',
    props: {
        planilha: {
            type: Object,
            default: () => {},
        },
        contador: {
            type: Number,
            default: 1,
        },
        headers: {
            type: Array,
            required: true,
        },
        expandAll: {
            type: Boolean,
            required: true,
        },
    },
    mixins: [MxPlanilhas],
    render(h) {
        const self = this;
        if (this.isObject(self.planilha) && typeof self.planilha.itens === 'undefined') {
            return h('VExpansionPanel',
                { props: { value: self.toggleExpand(this.planilha, self.contador) }, attrs: { expand: 'expand' } },
                Object.keys(this.planilha).map((key) => {
                    if (self.isObject(self.planilha[key])) {
                        const badgeHeader = (self.$scopedSlots.badge)
                            ? self.$scopedSlots.badge({ planilha: self.planilha[key] }) : '';
                        return h('VExpansionPanelContent',
                            [
                                h('VLayout',
                                    {
                                        props: {
                                            row: true,
                                            'justify-space-between': true,
                                        },
                                        slot: 'header',
                                        style: { color: self.getHeader(self.contador).color },
                                    },
                                    [
                                        h('i',
                                            { class: `material-icons mt-2 pl-${self.contador * 1}` },
                                            [self.getHeader(self.contador).icon]),
                                        h('span', { class: 'ml-2 mt-2' }, key),
                                        h('VSpacer'),
                                        badgeHeader,
                                    ]),
                                h('div',
                                    [
                                        h(SCollapsibleRecursivo, {
                                            props: {
                                                planilha: self.planilha[key],
                                                contador: self.contador + 1,
                                                headers: self.headers,
                                                expandAll: self.expandAll,
                                            },
                                            scopedSlots: {
                                                badge: self.$scopedSlots.badge,
                                                default: self.$scopedSlots.default,
                                            },
                                        }),
                                        h(SPlanilhaConsolidacao, {
                                            props: {
                                                planilha: self.planilha[key],
                                            },
                                        }),
                                    ]),
                            ]);
                    }
                    return true;
                }));
        } if (self.$scopedSlots.default !== 'undefined') {
            return h('div', { class: 'scroll-x pa-2 elevation-1', style: { border: '1px solid #ddd' } }, [
                self.$scopedSlots.default({ itens: self.planilha.itens }),
            ]);
        }
        return true;
    },
    methods: {
        getHeader(id) {
            return this.headers.find(element => element.id === id);
        },
        toggleExpand(table, contador) {
            const lastItem = this.headers.slice(-1)[0];
            if (this.expandAll !== true && lastItem.id === contador) {
                return [];
            }

            return [...Object.keys(table)].map(() => true);
        },
    },
};

export default {
    name: 'Planilha',
    components: {
        SCollapsibleRecursivo,
        SPlanilhaItensPadrao,
    },
    mixins: [MxPlanilhas],
    props: {
        arrayPlanilha: {
            type: [Array],
            default: () => [],
        },
        headers: {
            type: Array,
            default: () => [
                {
                    id: 1,
                    icon: 'beenhere',
                    color: '#F44336',
                },
                {
                    id: 2,
                    icon: 'perm_media',
                    color: '#4CAF50',
                },
                {
                    id: 3,
                    icon: 'label',
                    color: '#ff9800',
                },
                {
                    id: 4,
                    icon: 'place',
                    color: '#2196F3',
                },
                {
                    id: 5,
                    icon: 'location_city',
                    color: '#2196F3',
                },
            ],
        },
        agrupamentos: {
            type: Array,
            default: () => ['FonteRecurso', 'Produto', 'Etapa', 'UF', 'Municipio'],
        },
        totais: {
            type: Array,
            default: () => [
                {
                    label: 'Vl. Solicitado',
                    column: 'vlSolicitado',
                },
            ],
        },
        expandAll: {
            type: Boolean,
            default: true,
        },
        listItems: {
            type: Boolean,
            default: false,
        },
    },
    computed: {
        planilhaMontada() {
            if (this.arrayPlanilha.length === 0) {
                return {};
            }

            const self = this;
            /* eslint-disable no-param-reassign */
            const groupBy = (planilha, agrupamentos) => planilha.reduce((prev, item) => {
                let i = 0;
                function agruparItens(colunas) {
                    const key = agrupamentos[i];
                    i += 1;
                    (colunas[item[key]] = colunas[item[key]] || Object.assign({}, colunas[item[key]]) || {});
                    const isItemExcluido = item.tpAcao && item.tpAcao === 'E';
                    // calculando os totais
                    const qtdTotais = self.totais.length;
                    if (!isItemExcluido) {
                        for (let y = 0; y < qtdTotais; y += 1) {
                            const b = self.totais[y].column;
                            colunas[item[key]] = Object.assign(colunas[item[key]], { [b]: (colunas[item[key]][b] + item[b]) || item[b] });
                        }
                    }

                    if (agrupamentos[agrupamentos.length - 1] === key) {
                        if (!colunas[item[key]].itens) {
                            (colunas[item[key]] = Object.assign(colunas[item[key]], { itens: [] }));
                        }
                        colunas[item[key]].itens.push(item);
                    } else {
                        agruparItens(colunas[item[key]], item, agrupamentos);
                    }
                    return colunas;
                }
                return agruparItens(prev);
            }, {});
            return groupBy(this.arrayPlanilha, this.agrupamentos);
        },
        totaisProjeto() {
            const self = this;
            if (Object.keys(self.planilhaMontada).length === 0) {
                return {};
            }
            return Object.keys(self.planilhaMontada).reduce((prev, key) => {
                self.totais.forEach((current) => {
                    const a = current.column;
                    prev[a] = (prev[a] || { ...current, value: 0 });
                    prev[a].value += self.planilhaMontada[key][a];
                });
                return prev;
            }, {});
        },
    },
};
</script>

<style>
    .planilha-orcamentaria > ul > li > .v-expansion-panel__header {
        border-top: 1px solid #ddd;
    }
    .v-expansion-panel__header {
        padding: 10px !important;
        border-bottom: 1px solid #ddd;
        border-left: 1px solid #ddd;
        border-right: 1px solid #ddd;
    }
</style>
