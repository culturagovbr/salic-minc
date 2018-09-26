<template>
    <div v-if="arrayPlanilha" class="planilha-orcamentaria card">
        <CollapsibleRecursivo :planilha="arrayPlanilha">
            <template slot-scope="slotProps">
                <slot v-bind:itens="slotProps.itens">
                    <PlanilhaItensPadrao :table="slotProps.itens"></PlanilhaItensPadrao>
                </slot>
            </template>
        </CollapsibleRecursivo>
        <div class="card-action right-align">
            <span><b>Valor total do projeto:</b> R$ {{ arrayPlanilha.total | filtroFormatarParaReal }}</span>
        </div>
    </div>
    <div v-else>Nenhuma planilha encontrada</div>
</template>

<script>
    import PlanilhaItensPadrao from '@/components/Planilha/PlanilhaItensPadrao';
    import PlanilhaConsolidacao from '@/components/Planilha/PlanilhaConsolidacao';
    import planilhas from '@/mixins/planilhas';

    const CollapsibleRecursivo = {
        name: 'CollapsibleRecursivo',
        props: {
            planilha: {},
            contador: {
                default: 1,
                type: Number,
            },
        },
        mixins: [planilhas],
        render(h) {
            const self = this;
            if (this.isObject(self.planilha) && typeof self.planilha.itens === 'undefined') {
                return h('ul',
                    { class: 'collapsible no-margin', attrs: { 'data-collapsible': 'expandable' } },
                    Object.keys(this.planilha).map((key) => {
                        if (self.isObject(self.planilha[key])) {
                            return h('li', [
                                h('div',
                                    { class: 'collapsible-header active' },
                                    [
                                        h('i', { class: 'material-icons' }, [self.obterIconeHeader(self.contador)]),
                                        h('div', key),
                                        h('span', { class: 'badge' }, [`R$ ${self.formatarParaReal(self.planilha[key].total)} `]),
                                    ],
                                ),
                                h('div',
                                    { class: 'collapsible-body no-padding' },
                                    [
                                        h(CollapsibleRecursivo, {
                                            props: {
                                                planilha: self.planilha[key],
                                                contador: self.contador + 1,
                                            },
                                            scopedSlots: { default: self.$scopedSlots.default },
                                        }),
                                        h(PlanilhaConsolidacao, {
                                            props: {
                                                planilha: self.planilha[key],
                                            },
                                        }),
                                    ],
                                ),
                            ]);
                        }

                        return true;
                    }),
                );
            } else if (self.$scopedSlots.default !== 'undefined') {
                return h('div', { class: 'margin20 scroll-x' }, [
                    self.$scopedSlots.default({ itens: self.planilha.itens }),
                ]);
            }
            return true;
        },
        methods: {
            obterIconeHeader(tipo) {
                let icone = '';
                switch (tipo) {
                case 1:
                    icone = 'beenhere';
                    break;
                case 2:
                    icone = 'perm_media';
                    break;
                case 3:
                    icone = 'label';
                    break;
                case 4:
                    icone = 'place';
                    break;
                default:
                    icone = '';
                }
                return icone;
            },
        },
    };

    export default {
        name: 'Planilha',
        props: {
            arrayPlanilha: {},
        },
        mixins: [planilhas],
        components: {
            CollapsibleRecursivo,
            PlanilhaItensPadrao,
        },
        mounted() {
            this.$nextTick(function () {
                this.iniciarCollapsible();
            })
        },
        watch: {
            arrayPlanilha() {
                this.$nextTick(function () {
                    this.iniciarCollapsible();
                })
            },
        },
        methods: {
            iniciarCollapsible() {
                // eslint-disable-next-line
                $3(".collapsible").each(function () {
                    // eslint-disable-next-line
                    $3(this).collapsible();
                });
            },
        },
    };
</script>
