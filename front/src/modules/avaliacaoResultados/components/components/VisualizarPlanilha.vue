<template>
    <carregando
        v-if="!dadosProjeto"
        :text="'Carregando ...'"
    />
    <v-container
        v-else
        fluid
    >
        <v-toolbar>
            <v-btn
                icon
                class="hidden-xs-only"
                @click="goBack()"
            >
                <v-icon>arrow_back</v-icon>
            </v-btn>
            <v-toolbar-title>Planilha</v-toolbar-title>
        </v-toolbar>
        <v-card>
            <v-card-title primary-title>
                <h2>{{ dadosProjeto.items.pronac }} &#45; {{ dadosProjeto.items.nomeProjeto }}</h2>
            </v-card-title>
            <v-card-text>
                <p v-if="dadosProjeto.items.diligencia">
                    Existe Diligência para esse projeto. Acesse <a
                        :href="'/proposta/diligenciar/listardiligenciaanalista/idPronac/' + idPronac"
                    >aqui</a>.
                </p>
                <p v-else-if="documento != 0">
                    Existe Documento para assinar nesse projeto.
                </p>
                <p v-else-if="estado.estadoId == 5">
                    Projeto em analise.
                </p>
                <p v-else>
                    Sem Observações.
                </p>
                <div class="mt-4 mb-3">
                    <div class="d-inline-block">
                        <h4>Valor Aprovado</h4>
                        {{ moeda(dadosProjeto.items.vlAprovado) }}
                    </div>
                    <div class="d-inline-block ml-5">
                        <h4>Valor Comprovado</h4>
                        {{ moeda(dadosProjeto.items.vlComprovado) }}
                    </div>
                    <div class="d-inline-block ml-5">
                        <h4>Valor a Comprovar</h4>
                        {{ moeda(dadosProjeto.items.vlTotalComprovar) }}
                    </div>
                </div>
            </v-card-text>
            <v-card-actions>
                <v-btn
                    :href="'/consultardadosprojeto/index?idPronac=' + idPronac"
                    color="success"
                    target="_blank"
                    class="mr-2"
                >
                    VER PROJETO
                </v-btn>

                <consolidacao-analise
                    :id-pronac="idPronac"
                    :nome-projeto="dadosProjeto.items.nomeProjeto"
                />
            </v-card-actions>
        </v-card>
        <template v-if="Object.keys(planilha).length > 0">
            <v-card
                class="mt-3"
                flat
            >
                <!-- PRODUTO -->
                <v-expansion-panel
                    :v-if="getPlanilha != undefined && Object.keys(getPlanilha)"
                    :value="expandir(getPlanilha)"
                    expand
                >
                    <v-expansion-panel-content
                        v-for="(produto,i) in getPlanilha"
                        :key="i"
                    >
                        <v-layout
                            slot="header"
                            class="green--text"
                        >
                            <v-icon class="mr-3 green--text">
                                perm_media
                            </v-icon>
                            {{ produto.produto }}
                        </v-layout>
                        <!-- ETAPA -->
                        <v-expansion-panel
                            :value="expandir(produto)"
                            class="pl-3 elevation-0"
                            expand
                        >
                            <v-expansion-panel-content
                                v-for="(etapa,i) in produto.etapa"
                                :key="i"
                            >
                                <v-layout
                                    slot="header"
                                    class="orange--text"
                                >
                                    <v-icon class="mr-3 orange--text">
                                        label
                                    </v-icon>
                                    {{ etapa.etapa }}
                                </v-layout>
                                <!-- UF -->
                                <v-expansion-panel
                                    :value="expandir(etapa)"
                                    class="pl-3 elevation-0"
                                    expand
                                >
                                    <v-expansion-panel-content
                                        v-for="(uf,i) in etapa.UF"
                                        :key="i"
                                    >
                                        <v-layout
                                            slot="header"
                                            class="blue--text"
                                        >
                                            <v-icon class="mr-3 blue--text">
                                                place
                                            </v-icon>
                                            {{ uf.Uf }}
                                        </v-layout>
                                        <!-- CIDADE -->
                                        <v-expansion-panel
                                            :value="expandir(uf)"
                                            class="pl-3 elevation-0"
                                            expand
                                        >
                                            <v-expansion-panel-content
                                                v-for="(cidade,i) in uf.cidade"
                                                :key="i"
                                            >
                                                <v-layout
                                                    slot="header"
                                                    class="blue--text"
                                                >
                                                    <v-icon class="mr-3 blue--text">
                                                        place
                                                    </v-icon>
                                                    {{ cidade.cidade }}
                                                </v-layout>
                                                <template v-if="typeof cidade.itens !== 'undefined'">
                                                    <v-tabs
                                                        slider-color="green"
                                                    >
                                                        <v-tab
                                                            v-for="tab in Object.keys(cidade.itens)"
                                                            :key="tab"
                                                            ripple
                                                        >
                                                            {{ tabs[tab] }}
                                                        </v-tab>
                                                        <v-tab-item
                                                            v-for="(item, index) in cidade.itens"
                                                            :key="index"
                                                        >
                                                            <v-data-table
                                                                :headers="headers"
                                                                :items="Object.values(item)"
                                                                hide-actions
                                                            >
                                                                <template
                                                                    slot="items"
                                                                    slot-scope="props"
                                                                >
                                                                    <td>{{ props.item.item }}</td>
                                                                    <td>{{ moeda(props.item.varlorAprovado) }}</td>
                                                                    <td>{{ moeda(props.item.varlorComprovado) }}</td>
                                                                    <td>
                                                                        {{ moeda(props.item.varlorAprovado -
                                                                            props.item.varlorComprovado) }}
                                                                    </td>
                                                                    <td>
                                                                        <v-btn
                                                                            v-if="props.item.varlorComprovado !== 0"
                                                                            slot="activator"
                                                                            color="blue lighten-2"
                                                                            dark
                                                                            small
                                                                            @click="visualizarComprovantes(
                                                                                uf.Uf,
                                                                                cidade.cdCidade,
                                                                                produto.cdProduto,
                                                                                props.item.stItemAvaliado,
                                                                                etapa.cdEtapa,
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
                                                        </v-tab-item>
                                                    </v-tabs>
                                                </template>
                                            </v-expansion-panel-content>
                                        </v-expansion-panel>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                            </v-expansion-panel-content>
                        </v-expansion-panel>
                    </v-expansion-panel-content>
                </v-expansion-panel>
            </v-card>
            <ModalDetalheItens
                :id-pronac="idPronac.toString()"
                :uf="itemEmVisualizacao.Uf"
                :codigo-cidade="itemEmVisualizacao.cdCidade"
                :codigo-produto="itemEmVisualizacao.cdProduto"
                :st-item-avaliado="itemEmVisualizacao.stItemAvaliado"
                :codigo-etapa="itemEmVisualizacao.cdEtapa"
                :id-planilha-itens="itemEmVisualizacao.idPlanilhaItens"
                :item="itemEmVisualizacao.item "
            />
        </template>
        <carregando
            v-else
            :text="'Carregando planilha...'"
        />
    </v-container>
</template>
<script>
import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';
import ModalDetalheItens from './ModalDetalheItens';
import ConsolidacaoAnalise from './ConsolidacaoAnalise';

export default {
    name: 'Painel',
    components: {
        ModalDetalheItens,
        ConsolidacaoAnalise,
        Carregando,
    },
    data() {
        return {
            headers: [
                { text: 'Item de Custo', value: 'item', sortable: false },
                { text: 'Valor Aprovado', value: 'varlorAprovado', sortable: false },
                { text: 'Valor Comprovado', value: 'varlorComprovado', sortable: false },
                { text: 'Valor a Comprovar', value: 'valorAComprovar', sortable: false },
                { text: '', value: 'comprovarItem', sortable: false },
            ],
            tabs: {
                1: 'AVALIADO',
                3: 'IMPUGNADOS',
                4: 'AGUARDANDO ANÁLISE',
                todos: 'TODOS',
            },
            fab: false,
            idPronac: this.$route.params.id,
            itemEmVisualizacao: {},
        };
    },
    computed: {
        ...mapGetters({
            getPlanilha: 'avaliacaoResultados/planilha',
            getProjetoAnalise: 'avaliacaoResultados/projetoAnalise',
            isModalVisible: 'modal/default',
        }),
        dadosProjeto() {
            return this.getProjetoAnalise.data;
        },
        documento() {
            let { documento } = this.getProjetoAnalise.data.items;
            documento = documento !== null ? this.getProjetoAnalise.data.items.documento : 0;
            return documento;
        },
        estado() {
            let { estado } = this.getProjetoAnalise.data.items;
            estado = (estado !== null) ? this.getProjetoAnalise.data.items.estado : 0;
            return estado;
        },
        planilha() {
            let planilha = this.getPlanilha;
            planilha = (planilha !== null && Object.keys(planilha).length) ? this.getPlanilha : 0;
            return planilha;
        },
        getPronac() {
            return parseInt(this.idPronac, 10);
        },
    },
    mounted() {
        this.setPlanilha(this.idPronac);
        this.setProjetoAnalise(this.idPronac);
    },
    methods: {
        ...mapActions({
            setPlanilha: 'avaliacaoResultados/planilha',
            setProjetoAnalise: 'avaliacaoResultados/projetoAnalise',
            modalOpen: 'modal/modalOpen',
            modalClose: 'modal/modalClose',
            // buscarDetalhamentoItens: 'avaliacaoResultados/buscarDetalhamentoItens',
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
        expandir(obj) {
            const arr = [];
            const items = Object.keys(obj).length;
            for (let i = 0; i < items; i += 1) {
                arr.push(true);
            }
            return arr;
        },
        codigoStItemAvaliado(stItemAvaliado) {
            let response = null;

            switch (stItemAvaliado) {
            case 'AVALIADO':
                response = '1';
                break;
            case 'IMPUGNADOS':
                response = '3';
                break;
            case 'AGUARDANDO ANÁLISE':
                response = '4';
                break;
            default:
                response = '';
            }

            return response;
        },
        goBack() {
            if (window.history.length > 1) {
                this.$router.go(-1);
            } else {
                this.$router.push('/');
            }
        },
    },
};
</script>
