<template>
    <carregando
        v-if="Object.keys(dadosProjeto).length == 0"
        text="Carregando ..."
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
            <v-toolbar-title>
                Planilha: {{ dadosProjeto.items.pronac }} &#45; {{ dadosProjeto.items.nomeProjeto }}
            </v-toolbar-title>
            <v-spacer />

            <v-chip v-if="isProjetoDiligenciado">
                Projeto diligenciado
            </v-chip>
            <v-chip v-else-if="estado.estadoId == 5">
                Projeto em analise
            </v-chip>
            <historico-diligencias
                :id-pronac="idPronac"
                :obj="dadosProjeto.items.diligencia"
            />
        </v-toolbar>
        <v-card>
            <v-card-text>
                <v-alert
                    v-if="documento != 0"
                    :value="true"
                    color="info"
                >
                    Existe Documento para assinar nesse projeto.
                </v-alert>
                <parecer-tecnico-planilha-header :dados="dadosProjeto" />
            </v-card-text>
            <v-card-actions>
                <v-btn
                    :href="'/consultardadosprojeto/index?idPronac=' + idPronac"
                    color="success"
                    target="_blank"
                    class="mr-2"
                    dark
                >
                    VER PROJETO
                </v-btn>

                <consolidacao-analise
                    :id-pronac="idPronac"
                    :nome-projeto="dadosProjeto.items.nomeProjeto"
                />
            </v-card-actions>
        </v-card>
        <template v-if="Object.keys(planilha).length > 0 && planilha.error">
            <v-alert
                :value="true"
                color="error"
            >
                {{ planilha.error.message }}
            </v-alert>
        </template>
        <template v-else-if="Object.keys(planilha).length">
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
                                                <template
                                                    v-if="typeof cidade.itens !== 'undefined'"
                                                >
                                                    <v-tabs
                                                        slider-color="green"
                                                    >
                                                        <v-tab
                                                            v-for="(tab, index) in Object.keys(cidade.itens)"
                                                            :key="index"
                                                            ripple
                                                        >
                                                            <b>{{ tabs[tab] }}</b>
                                                        </v-tab>
                                                        <v-tab-item
                                                            v-for="item in cidade.itens"
                                                            :key="item.stItemAvaliado"
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
                                                                    <td>
                                                                        {{ props.item.item }}
                                                                    </td>
                                                                    <td class="text-xs-right">
                                                                        {{ (props.item.quantidade) }}
                                                                    </td>
                                                                    <td class="text-xs-right">
                                                                        {{ (props.item.numeroOcorrencias) }}
                                                                    </td>
                                                                    <td class="text-xs-right">
                                                                        {{ props.item.valor | moedaMasc }}
                                                                    </td>
                                                                    <td class="text-xs-right">
                                                                        {{ props.item.varlorAprovado | moedaMasc }}
                                                                    </td>
                                                                    <td class="text-xs-right">
                                                                        {{ props.item.varlorComprovado | moedaMasc }}
                                                                    </td>
                                                                    <td class="text-xs-right">
                                                                        {{ (props.item.varlorAprovado - props.item.varlorComprovado) | moedaMasc }}
                                                                    </td>
                                                                    <td>
                                                                        <v-btn
                                                                            v-if="podeEditar(props.item.varlorComprovado)"
                                                                            color="primary"
                                                                            dark
                                                                            small
                                                                            title="Avaliar comprovantes do item"
                                                                            @click="avaliarItem(
                                                                                props.item,
                                                                                produto.produto,
                                                                                etapa.etapa,
                                                                                uf.Uf,
                                                                                produto.cdProduto,
                                                                                cidade.cdCidade,
                                                                                etapa.cdEtapa,
                                                                                uf.cdUF)"
                                                                        >
                                                                            <v-icon>gavel</v-icon>
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
            <analisar-item
                v-if="isModalVisible === 'avaliacao-item'"
                :item="itemEmAvaliacao.item"
                :descricao-produto="itemEmAvaliacao.produto"
                :descricao-etapa="itemEmAvaliacao.etapa"
                :id-pronac="idPronac"
                :uf="itemEmAvaliacao.Uf"
                :produto="itemEmAvaliacao.cdProduto"
                :idmunicipio="itemEmAvaliacao.cdCidade"
                :etapa="itemEmAvaliacao.cdEtapa"
                :cd-produto="itemEmAvaliacao.cdProduto"
                :cd-uf="itemEmAvaliacao.cdUF"
            />
        </template>
        <template v-else>
            <Carregando :text="'Carregando planilha ...'" />
        </template>
        <v-speed-dial
            v-if="(!isProjetoDiligenciado)"
            v-model="fab"
            bottom
            right
            direction="top"
            open-on-hover
            transition="slide-y-reverse-transition"
            fixed
        >
            <v-btn
                slot="activator"
                v-model="fab"
                color="red darken-2"
                dark
                fab
            >
                <v-icon>menu</v-icon>
                <v-icon>close</v-icon>
            </v-btn>
            <v-tooltip
                v-if="(documento != 0)"
                left
            >
                <v-btn
                    slot="activator"
                    :href="'/assinatura/index/visualizar-projeto?idDocumentoAssinatura=' + documento.idDocumentoAssinatura"
                    fab
                    dark
                    small
                    color="green"
                >
                    <v-icon>edit</v-icon>
                </v-btn>
                <span>Assinar</span>
            </v-tooltip>
            <v-tooltip
                v-if="(documento == 0 && !isProjetoDiligenciado)"
                left
            >
                <v-btn
                    slot="activator"
                    :to="'/emitir-parecer/' + idPronac"
                    fab
                    dark
                    small
                    color="teal"
                    @click.native="getConsolidacao(idPronac)"
                >
                    <v-icon>gavel</v-icon>
                </v-btn>
                <span>Emitir Parecer</span>
            </v-tooltip>
            <v-tooltip
                v-if="(documento == 0) && !isProjetoDiligenciado"
                left
            >
                <v-btn
                    slot="activator"
                    :to="'/diligenciar/' + idPronac"
                    fab
                    dark
                    small
                    color="red ligthen-4"
                >
                    <v-icon>warning</v-icon>
                </v-btn>
                <span>Diligenciar</span>
            </v-tooltip>
        </v-speed-dial>
    </v-container>
</template>

<script>
import Vue from 'vue';
import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';
import ConsolidacaoAnalise from '../components/ConsolidacaoAnalise';
import AnalisarItem from './AnalisarItem';
import Moeda from '../../../../filters/money';
import HistoricoDiligencias from '@/modules/avaliacaoResultados/components/components/HistoricoDiligencias';
import ParecerTecnicoPlanilhaHeader from '@/modules/avaliacaoResultados/components/ParecerTecnico/PlanilhaHeader';

Vue.filter('moedaMasc', Moeda);

export default {
    name: 'Planilha',
    components: {
        ParecerTecnicoPlanilhaHeader,
        HistoricoDiligencias,
        ConsolidacaoAnalise,
        AnalisarItem,
        Carregando,
    },
    data() {
        return {
            headers: [
                { text: 'Item', value: 'item', sortable: false },
                {
                    text: 'Qtd', value: 'quantidade', sortable: false, align: 'right',
                },
                {
                    text: 'Nº Ocorr.', value: 'numeroOcorrencias', sortable: false, align: 'right',
                },
                {
                    text: 'Valor (R$)', value: 'valor', sortable: false, align: 'right',
                },
                {
                    text: 'Vl. Aprovado (R$)', value: 'varlorAprovado', sortable: false, align: 'right',
                },
                {
                    text: 'Vl. Comprovado (R$)', value: 'varlorComprovado', sortable: false, align: 'right',
                },
                {
                    text: 'Vl. a Comprovar (R$)', value: 'valorAComprovar', sortable: false, align: 'right',
                },
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
            itemEmAvaliacao: {},
        };
    },
    computed: {
        ...mapGetters({
            getPlanilha: 'avaliacaoResultados/planilha',
            getProjetoAnalise: 'avaliacaoResultados/projetoAnalise',
            isModalVisible: 'modal/default',
        }),
        dadosProjeto() {
            if (Object.keys(this.getProjetoAnalise).length > 0) {
                return this.getProjetoAnalise.data;
            }
            return {};
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
        isProjetoDiligenciado() {
            const { diligencia } = this.dadosProjeto.items;
            return diligencia && diligencia.DtSolicitacao && !diligencia.DtResposta;
        },
    },
    mounted() {
        this.setPlanilha(this.idPronac);
        this.setProjetoAnalise(this.idPronac);
    },
    methods: {
        ...mapActions({
            modalOpen: 'modal/modalOpen',
            requestEmissaoParecer: 'avaliacaoResultados/getDadosEmissaoParecer',
            setPlanilha: 'avaliacaoResultados/syncPlanilhaAction',
            setProjetoAnalise: 'avaliacaoResultados/syncProjetoAction',
        }),
        getConsolidacao(id) {
            this.requestEmissaoParecer(id);
        },
        moeda: (moedaString) => {
            const moeda = Number(moedaString);
            return moeda.toLocaleString('pt-br', { currency: 'BRL' });
        },
        podeEditar(varlorComprovado) {
            if (varlorComprovado !== 0
                && !this.dadosProjeto.items.diligencia
                && this.documento.length === 0
            ) {
                return true;
            }

            return false;
        },
        avaliarItem(item,
            produto,
            etapa,
            Uf,
            cdProduto,
            cdCidade,
            cdEtapa,
            cdUF) {
            this.itemEmAvaliacao = {
                item,
                produto,
                etapa,
                Uf,
                cdProduto,
                cdCidade,
                cdEtapa,
                cdUF,
            };
            this.modalOpen('avaliacao-item');
        },
        expandir(obj) {
            const arr = [];
            const items = Object.keys(obj).length;
            for (let i = 0; i < items; i += 1) {
                arr.push(true);
            }
            return arr;
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
