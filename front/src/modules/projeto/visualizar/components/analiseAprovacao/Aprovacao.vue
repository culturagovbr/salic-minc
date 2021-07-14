<template>
    <div>
        <div v-if="loading">
            <Carregando :text="'Carregando Aprovação'"/>
        </div>
        <div v-else-if="Object.keys(gruposAprovacao).length > 0">
            <v-expansion-panel popout>
                <v-expansion-panel-content
                    v-for="(dadoAgrupado, titulo) in gruposAprovacao"
                    :key="dadoAgrupado[0].CodTipoAprovacao"
                    class="elevation-1"
                >
                    <v-layout
                        slot="header"
                        class="primary--text">
                        <v-icon class="mr-3 primary--text">
                            {{ dadoAgrupado[0].CodTipoAprovacao | filtrarIcone }}
                        </v-icon>
                        <span
                            class="mr-1"
                            v-html="titulo"/>
                        <span>({{ dadoAgrupado.length }})</span>
                    </v-layout>

                    <v-data-table
                        :headers="headers"
                        :items="dadoAgrupado"
                        class="elevation-1"
                    >
                        <template
                            slot="items"
                            slot-scope="props">
                            <td
                                class="text-xs-left"
                                v-html="props.item.TipoAprovacao"/>
                            <td class="text-xs-center pl-5">
                                {{ props.item.DtAprovacao | formatarData }}
                            </td>
                            <td class="text-xs-right">
                                {{ props.item.PortariaAprovacao }}
                            </td>


                            <td class="text-xs-right">
                                {{ props.item.DtPortariaAprovacao | formatarData }}
                            </td>
                            <td class="text-xs-right">
                                {{ props.item.DtPublicacaoAprovacao | formatarData }}
                            </td>

                            <td class="text-xs-center pl-5">
                                <v-tooltip bottom>
                                    <v-btn
                                        slot="activator"
                                        flat
                                        icon
                                        @click="showItem(props.item)"
                                    >
                                        <v-icon>visibility</v-icon>
                                    </v-btn>
                                    <span>Visualizar Aprovação</span>
                                </v-tooltip>
                            </td>
                        </template>
                        <template
                            slot="pageText"
                            slot-scope="props">
                            Items {{ props.pageStart }} -
                            {{ props.pageStop }} de
                            {{ props.itemsLength }}
                        </template>
                    </v-data-table>
                </v-expansion-panel-content>
            </v-expansion-panel>
            <v-dialog
                v-model="dialog"
                @keydown.esc="dialog = false">
                <v-card>
                    <v-card-text class="pl-5">
                        <v-container
                            grid-list-md
                            text-xs-left>
                            <v-layout
                                justify-space-around
                                row
                                wrap>
                                <v-flex
                                    lg12
                                    dark
                                    class="text-xs-left">
                                    <b><h4>PORTARIA / DATAS</h4></b>
                                    <v-divider class="pb-2"/>
                                </v-flex>
                                <v-flex>
                                    <p><b>Tipo</b></p>
                                    <p v-html="itemEmVisualizacao.TipoAprovacao"/>
                                </v-flex>
                                <v-flex>
                                    <p><b>Dt. Aprovação</b></p>
                                    <p>
                                        {{ itemEmVisualizacao.DtAprovacao | formatarData }}
                                    </p>
                                </v-flex>
                                <v-flex>
                                    <p><b>Portaria</b></p>
                                    <p>
                                        {{ itemEmVisualizacao.PortariaAprovacao }}
                                    </p>
                                </v-flex>
                                <v-flex>
                                    <p><b>Dt. Portaria</b></p>
                                    <p>
                                        {{ itemEmVisualizacao.DtPortariaAprovacao | formatarData }}
                                    </p>
                                </v-flex>
                                <v-flex>
                                    <p><b>Dt. Publicação</b></p>
                                    <p>
                                        {{ itemEmVisualizacao.DtPublicacaoAprovacao | formatarData }}
                                    </p>
                                </v-flex>
                            </v-layout>
                            <v-layout
                                v-if="itemEmVisualizacao.CodTipoAprovacao === '1'
                                || itemEmVisualizacao.CodTipoAprovacao === '3'"
                                justify-space-around
                                row
                                wrap>
                                <v-flex
                                    lg12
                                    dark
                                    class="text-xs-left">
                                    <b><h4>PERÍODO DE CAPTAÇÃO</h4></b>
                                    <v-divider class="pb-2"/>
                                </v-flex>
                                <v-flex>
                                    <p><b>Dt. Início</b></p>
                                    <p>
                                        {{ itemEmVisualizacao.DtInicioCaptacao | formatarData }}
                                    </p>
                                </v-flex>
                                <v-flex>
                                    <p><b>Dt. Fim</b></p>
                                    <p>
                                        {{ itemEmVisualizacao.DtFimCaptacao | formatarData }}
                                    </p>
                                </v-flex>
                            </v-layout>
                            <v-layout
                                justify-space-around
                                row
                                wrap>
                                <v-flex
                                    lg12
                                    dark
                                    class="text-xs-left">
                                    <b><h4>SÍNTESE APROVAÇÃO</h4></b>
                                    <v-divider class="pb-2"/>
                                </v-flex>
                                <v-flex>
                                    <p v-html="itemEmVisualizacao.ResumoAprovacao"/>
                                </v-flex>
                            </v-layout>
                            <div v-if="itemEmVisualizacao.AprovadoReal > 0">
                                <v-layout
                                    justify-space-around
                                    row
                                    wrap>
                                    <v-flex
                                        lg12
                                        dark
                                        class="text-xs-left">
                                        <b><h4>MECENATO</h4></b>
                                        <v-divider class="pb-2"/>
                                    </v-flex>
                                    <v-flex>
                                        <p><b>Vl. Aprovação</b></p>
                                        <p> R$ {{ itemEmVisualizacao.AprovadoReal | filtroFormatarParaReal }}</p>
                                    </v-flex>
                                </v-layout>
                            </div>
                        </v-container>
                    </v-card-text>
                    <v-divider/>
                    <v-card-actions>
                        <v-spacer/>
                        <v-btn
                            color="red"
                            flat
                            @click="dialog = false"
                        >
                            Fechar
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </div>
        <div v-else>
            <v-container
                grid-list-md
                text-xs-center>
                <v-layout
                    row
                    wrap>
                    <v-flex>
                        <v-card>
                            <v-card-text class="px-0">
                                Nenhuma Aprovação encontrada
                            </v-card-text>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-container>
        </div>
    </div>
</template>
<script>

import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';
import { utils } from '@/mixins/utils';

export default {
    name: 'Aprovacao',
    components: {
        Carregando,
    },
    filters: {
        filtrarIcone(tipo) {
            let icone = '';
            switch (tipo) {
            case '1':
                icone = 'home';
                break;
            case '2':
                icone = 'trending_up';
                break;
            case '3':
                icone = 'date_range';
                break;
            case '4':
                icone = 'trending_down';
                break;
            case '8':
                icone = 'update';
                break;
            default:
                icone = '';
            }
            return icone;
        },
    },
    mixins: [utils],
    data() {
        return {
            dialog: false,
            itemEmVisualizacao: {},
            gruposAprovacao: {},
            loading: true,
            headers: [
                {
                    text: 'TIPO',
                    align: 'left',
                    value: 'stAtendimento',
                },
                {
                    text: 'DT. APROVAÇÃO',
                    align: 'center',
                    value: 'DtAprovacao',
                },
                {
                    text: 'PORTARIA',
                    align: 'right',
                    value: 'PortariaAprovacao',
                },
                {
                    text: 'DT. PORTARIA',
                    align: 'right',
                    value: 'DtPortariaAprovacao',
                },
                {
                    text: 'DT. PUBLICAÇÃO',
                    align: 'right',
                    value: 'DtPublicacaoAprovacao',
                },
                {
                    text: 'VISUALIZAR',
                    align: 'center',
                    value: '',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            dados: 'analise/aprovacao',
        }),
    },
    watch: {
        dadosProjeto(value) {
            this.loading = true;
            this.buscarAprovacao(value.idPronac);
        },
        dados() {
            this.loading = false;
            this.gruposAprovacao = this.obterGrupoAprovacoes();
        },
    },
    mounted() {
        if (typeof this.dadosProjeto.idPronac !== 'undefined') {
            this.buscarAprovacao(this.dadosProjeto.idPronac);
        }
    },
    methods: {
        ...mapActions({
            buscarAprovacao: 'analise/buscarAprovacao',
        }),
        showItem(item) {
            this.itemEmVisualizacao = Object.assign({}, item);
            this.dialog = true;
        },
        obterGrupoAprovacoes() {
            const gruposAprovacao = {};
            const { Aprovacao } = this.dados;

            Aprovacao.forEach((aprovacao) => {
                const { TipoAprovacao } = aprovacao;

                if (gruposAprovacao[TipoAprovacao] == null
                        || gruposAprovacao[TipoAprovacao].length < 1) {
                    gruposAprovacao[TipoAprovacao] = [];
                }
                gruposAprovacao[TipoAprovacao].push(
                    aprovacao,
                );
            });

            return gruposAprovacao;
        },
    },
};
</script>
