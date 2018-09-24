<template>
    <v-container fluid>
        <v-card>
            <v-card-title primary-title>
                <h3>TÍTULO A SER DEFINIDO</h3>
            </v-card-title>
            <v-card-text>
                <p v-if="existeDiligencia">Existe Diligência para esse projeto. Acesse <router-link to="#">aqui</router-link>.</p>
                <p v-else>Sem Observações.</p>
            </v-card-text>
            <v-card-actions>
                <v-btn color="success" to="#">VER PROJETO</v-btn>
                <v-btn color="success" to="#">CONSOLIDAÇÃO</v-btn>
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
                        <v-expansion-panel class="pl-3 elevation-0"
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
                                                                    <td>R$ # valorAprovado - valorComprovado</td>
                                                                    <td>
                                                                        <v-btn color="red" small dark title="Comprovar Item">
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

        <v-card class="mt-3 pa-3" color="teal">
            <table class="white--text font-weight-bold" width="100%">
                <tr>
                    <td>Valor Aprovado</td>
                    <td>R$ 00,00</td>
                </tr>
                <tr>
                    <td>Valor Comprovado</td>
                    <td>R$ 00,00</td>
                </tr>
                <tr>
                    <td>Valor a Comprovar</td>
                    <td>R$ 00,00</td>
                </tr>
            </table>
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
            <v-tooltip left>
                <v-btn
                    fab
                    dark
                    small
                    color="green"
                    slot="activator"
                >
                    <v-icon>check</v-icon>
                </v-btn>
                <span>Finalizar Análise</span>
            </v-tooltip>
            <v-tooltip left>
                <v-btn
                    fab
                    dark
                    small
                    color="teal"
                    slot="activator"
                >
                    <v-icon>gavel</v-icon>
                </v-btn>
                <span>Emitir Parecer</span>
            </v-tooltip>
            <v-tooltip left>
                <v-btn
                    fab
                    dark
                    small
                    color="white"
                    slot="activator"
                >
                    <v-icon>warning</v-icon>
                </v-btn>
                <span>Diligenciar</span>
            </v-tooltip>
            <v-tooltip left>
                <v-btn
                    fab
                    dark
                    small
                    color="yellow"
                    slot="activator"
                >
                    <v-icon>arrow_upward</v-icon>
                </v-btn>
                <span>Ir para o topo</span>
            </v-tooltip>
        </v-speed-dial>
     </v-container>
</template>

<script>
    import { mapActions, mapGetters } from 'vuex';
    import ModalTemplate from '@/components/modal';

    export default {
        name: 'Painel',
        data() {
            return {
                existeDiligencia: true,
                produtos: this.planilha,
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
            };
        },
        computed: {
            ...mapGetters({
                getPlanilha: 'avaliacaoResultados/planilha',
            }),
        },
        props: {
            idpronac: '132451',
        },
        mounted() {
            this.setPlanilha();
        },
        components: {
            ModalTemplate,
        },
        methods: {
            ...mapActions({
                setPlanilha: 'avaliacaoResultados/planilha',
            }),
            moeda: (moedaString) => {
                const moeda = Number(moedaString);
                return moeda.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' });
            },
        },
    };
</script>
