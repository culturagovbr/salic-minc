<template>
    <div>
        <planilha-avaliacao-financeira>
            <template slot-scope="slotProps">
                <v-data-table
                    :headers="headers"
                    :items="Object.values(slotProps.itens)"
                    hide-actions
                    disable-initial-sort
                >
                    <template
                        slot="items"
                        slot-scope="props"
                    >
                        <td>{{ props.item.Seq }}</td>
                        <td>{{ props.item.item }}</td>
                        <td>{{ moeda(props.item.vlAprovado) }}</td>
                        <td>{{ moeda(props.item.vlComprovado) }}</td>
                        <td>
                            {{ moeda(props.item.vlAprovado - props.item.vlComprovado) }}
                        </td>
                        <td>
                            <v-btn
                                v-if="props.item.vlComprovado !== 0"
                                slot="activator"
                                color="blue lighten-2"
                                dark
                                icon
                                @click="visualizarComprovantes(
                                    props.item.uf,
                                    props.item.cdCidade,
                                    props.item.cdProduto,
                                    props.item.stItemAvaliado,
                                    props.item.cdEtapa,
                                    props.item.idPlanilhaItens,
                                    props.item.item,
                                )"
                            >
                                <v-icon dark>
                                    visibility
                                </v-icon>
                            </v-btn>
                        </td>
                    </template>
                </v-data-table>
            </template>
        </planilha-avaliacao-financeira>
        <modal-detalhe-itens
            :id-pronac="idPronac.toString()"
            :uf="itemEmVisualizacao.Uf"
            :codigo-cidade="itemEmVisualizacao.cdCidade"
            :codigo-produto="itemEmVisualizacao.cdProduto"
            :st-item-avaliado="itemEmVisualizacao.stItemAvaliado"
            :codigo-etapa="itemEmVisualizacao.cdEtapa"
            :id-planilha-itens="itemEmVisualizacao.idPlanilhaItens"
            :item="itemEmVisualizacao.item "
        />
    </div>
</template>
<script>
import { mapActions } from 'vuex';
import ModalDetalheItens from './ModalDetalheItens';
import CONST from '../../const';
import PlanilhaAvaliacaoFinanceira from '@/modules/avaliacaoResultados/components/planilha/PlanilhaAvaliacaoFinanceira';

export default {
    name: 'VisualizarPlanilha',
    components: {
        PlanilhaAvaliacaoFinanceira,
        ModalDetalheItens,
    },
    data() {
        return {
            CONST,
            headers: [
                { text: '#', value: 'Seq', sortable: true },
                { text: 'Item de Custo', value: 'item', sortable: false },
                { text: 'Valor Aprovado', value: 'varlorAprovado', sortable: false },
                { text: 'Valor Comprovado', value: 'varlorComprovado', sortable: false },
                { text: 'Valor a Comprovar', value: 'valorAComprovar', sortable: false },
                { text: '', value: 'comprovarItem', sortable: false },
            ],
            idPronac: this.$route.params.id,
            itemEmVisualizacao: {},
        };
    },
    methods: {
        ...mapActions({
            modalOpen: 'modal/modalOpen',
        }),
        moeda: (moedaString) => {
            const moeda = Number(moedaString);
            return moeda.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' });
        },
        visualizarComprovantes(
            Uf,
            cdCidade,
            cdProduto,
            stItemAvaliado,
            cdEtapa,
            idPlanilhaItens,
            item,
        ) {
            this.itemEmVisualizacao = {
                Uf,
                cdCidade,
                cdProduto,
                stItemAvaliado,
                cdEtapa,
                idPlanilhaItens,
                item,
            };

            this.modalOpen('visualizar-comprovantes');
        },
    },
};
</script>
