<template>
    <v-container fluid v-if="dadosProjeto">
        <v-card>
            <v-card-title primary-title>
                <h2>{{ dadosProjeto.items.pronac }} &#45; {{ dadosProjeto.items.nomeProjeto }}</h2>
            </v-card-title>
            <v-card-text>
                <p v-if="dadosProjeto.items.diligencia">Existe Diligência para esse projeto. Acesse <a :href="'/proposta/diligenciar/listardiligenciaanalista/idPronac/' + idPronac">aqui</a>.</p>
                <p v-else-if="documento != 0">Existe Documento para assinar nesse projeto.</p>
                <p v-else-if="estado.estadoId == 5">Projeto em analise.</p>
                <p v-else>Sem Observações.</p>
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
                    color="success"
                    :href="'/consultardadosprojeto/index?idPronac=' + idPronac" target="_blank"
                    class="mr-2"
                >VER PROJETO</v-btn>

                <consolidacao-analise
                    :idPronac="idPronac"
                    :nomeProjeto="dadosProjeto.items.nomeProjeto"
                ></consolidacao-analise>

            </v-card-actions>
        </v-card>
        <v-card class="mt-3" flat>
            <!-- PRODUTO -->
            <v-expansion-panel
                expand
                v-if="this.getPlanilha != undefined && Object.keys(this.getPlanilha)"
            >
                <v-expansion-panel-content
                    v-for="(produto,i) in getPlanilha"
                    :key="i"
                >
                    <v-layout slot="header" class="green--text">
                        <v-icon class="mr-3 green--text">perm_media</v-icon>
                        {{ produto.produto }}
                    </v-layout>
                        <!-- ETAPA -->
                        <v-expansion-panel
                            class="pl-3 elevation-0"
                            expand
                        >
                            <v-expansion-panel-content
                                v-for="(etapa,i) in produto.etapa"
                                :key="i"
                            >
                                <v-layout slot="header" class="orange--text">
                                    <v-icon class="mr-3 orange--text">label</v-icon>
                                    {{ etapa.etapa }}
                                </v-layout>
                                <!-- UF -->
                                <v-expansion-panel
                                    class="pl-3 elevation-0"
                                    expand
                                >
                                    <v-expansion-panel-content
                                        v-for="(uf,i) in etapa.UF"
                                        :key="i"
                                    >
                                        <v-layout slot="header" class="blue--text">
                                            <v-icon class="mr-3 blue--text">place</v-icon>
                                            {{ uf.Uf }}
                                        </v-layout>
                                        <!-- CIDADE -->
                                        <v-expansion-panel
                                            class="pl-3 elevation-0"
                                            expand
                                        >
                                            <v-expansion-panel-content
                                                v-for="(cidade,i) in uf.cidade"
                                                :key="i"
                                            >
                                                <v-layout slot="header" class="blue--text">
                                                    <v-icon class="mr-3 blue--text">place</v-icon>
                                                    {{ cidade.cidade }}
                                                </v-layout>
                                                <template v-if="typeof cidade.itens !== 'undefined'">
                                                    <v-tabs
                                                        slider-color="green"
                                                    >
                                                        <v-tab ripple v-for="tab in Object.keys(cidade.itens)" :key="tab">{{ tabs[tab] }}</v-tab>
                                                        <v-tab-item v-for="item in cidade.itens" :key="item.stItemAvaliado">
                                                            <v-data-table
                                                                :headers="headers"
                                                                :items="Object.values(item)"
                                                                hide-actions
                                                            >
                                                                <template slot="items" slot-scope="props">
                                                                    <td>{{ props.item.item }}</td>
                                                                    <td>{{ moeda(props.item.varlorAprovado) }}</td>
                                                                    <td>{{ moeda(props.item.varlorComprovado) }}</td>
                                                                    <td>{{ moeda(props.item.varlorAprovado - props.item.varlorComprovado) }}</td>
                                                                    <td >
                                                                        <template
                                                                            v-if="podeEditar(props.item.varlorComprovado)"
                                                                        >
                                                                            <v-btn
                                                                                :href="'/prestacao-contas/analisar/comprovante/idPronac/' + idPronac + '/uf/' + uf.Uf + '/produto/' + produto.cdProduto + '/idmunicipio/' + cidade.cdCidade + '/idPlanilhaItem/' + props.item.idPlanilhaItens + '/etapa/' + etapa.cdEtapa"
                                                                                replace
                                                                                color="red"
                                                                                small
                                                                                dark
                                                                                title="Comprovar Item"
                                                                            >
                                                                                <v-icon>gavel</v-icon>
                                                                            </v-btn>
                                                                        </template>
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

        <v-speed-dial
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
            <v-tooltip left v-if="(documento != 0)">

                <v-btn
                    fab
                    dark
                    small
                    color="green"
                    slot="activator"
                    :href="'/assinatura/index/visualizar-projeto?idDocumentoAssinatura=' + documento.idDocumentoAssinatura"

                >
                    <v-icon>edit</v-icon>
                </v-btn>
                <span>Assinar</span>
            </v-tooltip>
            <v-tooltip left v-if="(documento == 0)">
                <v-btn
                    fab
                    dark
                    small
                    color="teal"
                    slot="activator"
                    :to="'/emitir-parecer/' + idPronac"
                >
                    <v-icon>gavel</v-icon>
                </v-btn>
                <span>Emitir Parecer</span>
            </v-tooltip>
            <v-tooltip left v-if="(documento == 0)">
                <v-btn
                    fab
                    dark
                    small
                    color="red ligthen-4"
                    slot="activator"
                >
                    <v-icon>warning</v-icon>
                </v-btn>
                <span>Diligenciar</span>
            </v-tooltip>
        </v-speed-dial>
     </v-container>
</template>

<script>
    import { mapActions, mapGetters } from 'vuex';
    import ModalTemplate from '@/components/modal';
    import ConsolidacaoAnalise from './ConsolidacaoAnalise';
    import AnalisarItem from './AnalisarItem';

    export default {
        name: 'Painel',
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
            };
        },
        computed: {
            ...mapGetters({
                getPlanilha: 'avaliacaoResultados/planilha',
                getProjetoAnalise: 'avaliacaoResultados/projetoAnalise',
            }),
            dadosProjeto() {
                return this.getProjetoAnalise.data;
            },
            documento() {
                let documento = this.getProjetoAnalise.data.items.documento;
                documento = documento !== null ? this.getProjetoAnalise.data.items.documento : 0;
                return documento;
            },
            estado() {
                let estado = this.getProjetoAnalise.data.items.estado;
                estado = (estado !== null) ? this.getProjetoAnalise.data.items.estado : 0;
                return estado;
            },
        },
        mounted() {
            this.setPlanilha(this.idPronac);
            this.setProjetoAnalise(this.idPronac);
        },
        components: {
            ModalTemplate,
            ConsolidacaoAnalise,
            AnalisarItem,
        },
        methods: {
            ...mapActions({
                setPlanilha: 'avaliacaoResultados/planilha',
                setProjetoAnalise: 'avaliacaoResultados/projetoAnalise',
            }),
            moeda: (moedaString) => {
                const moeda = Number(moedaString);
                return moeda.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' });
            },
            podeEditar(varlorComprovado) {
                if (varlorComprovado !== 0
                    && !this.dadosProjeto.items.diligencia
                    && this.documento == 0) {
                    return true;
                }

                return false;
            },
        },
    };
</script>
