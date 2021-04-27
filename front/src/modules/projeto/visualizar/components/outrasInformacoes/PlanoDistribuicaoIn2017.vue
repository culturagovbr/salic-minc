<template>
    <div>
        <div v-if="loading">
            <Carregando :text="'Carregando Plano de Distribuicao'"/>
        </div>
        <div v-else>
            <v-expansion-panel popout>
                <v-expansion-panel-content
                    v-for="(produto, index) of dadosIn2017.planodistribuicaoproduto"
                    :key="index"
                    class="elevation-1"
                >
                    <v-layout
                        slot="header"
                        class="primary--text">
                        <v-icon class="mr-3 primary--text">subject</v-icon>
                        <span>{{ produto.Produto }}</span>
                    </v-layout>
                    <v-container fluid>
                        <v-card
                            class="elevation-2"
                            color="grey lighten-4">
                            <v-card-text class="pl-5">
                                <v-layout
                                    justify-space-around
                                    row
                                    wrap>
                                    <v-flex
                                        s12
                                        m6
                                        lg2
                                        offset-lg1>
                                        <b>&Aacute;REA</b>
                                        <p>{{ produto.DescricaoArea }}</p>
                                    </v-flex>
                                    <v-flex
                                        s12
                                        m6
                                        lg3>
                                        <b>SEGMENTO</b>
                                        <p>{{ produto.DescricaoSegmento }}</p>
                                    </v-flex>
                                    <v-flex
                                        s12
                                        m6
                                        lg3>
                                        <b>PRINCIPAL</b>
                                        <P>{{ label_sim_ou_nao(produto.stPrincipal) }}</P>
                                    </v-flex>
                                    <v-flex
                                        s12
                                        m6
                                        lg3>
                                        <b>CANAL ABERTO?</b>
                                        <P>{{ label_sim_ou_nao(produto.canalAberto) }}</P>
                                    </v-flex>
                                </v-layout>

                                <v-layout
                                    justify-space-around
                                    row
                                    wrap>
                                    <v-flex
                                        lg12
                                        class="text-xs-center">
                                        <b>QUANTIDADE DISTRIBUI&Ccedil;&Atilde;O GRATUITA</b>
                                    </v-flex>
                                    <v-flex
                                        s6
                                        m6
                                        lg3
                                        offset-lg1>
                                        <p>
                                            <b>Divulga&ccedil;&atilde;o</b><br>
                                            {{ produto.QtdeProponente }}
                                        </p>
                                    </v-flex>
                                    <v-flex
                                        s12
                                        m6
                                        lg4>
                                        <p>
                                            <b>Patrocinador</b><br>
                                            {{ produto.QtdePatrocinador }}
                                        </p>
                                    </v-flex>
                                    <v-flex
                                        s12
                                        m6
                                        offset-xlg10>
                                        <p>
                                            <b>Popula&ccedil;&atilde;o</b><br>
                                            {{ produto.QtdeOutros }}
                                        </p>
                                    </v-flex>
                                </v-layout>

                                <v-layout
                                    justify-space-around
                                    row
                                    wrap>
                                    <v-flex
                                        lg12
                                        class="text-xs-center">
                                        <b>PRE&Ccedil;O POPULAR</b>
                                    </v-flex>
                                    <v-flex
                                        s6
                                        m6
                                        lg3
                                        offset-lg1>
                                        <p>
                                            <b>Quantidade Inteira</b><br>
                                            {{ produto.QtdeVendaPopularNormal }}
                                        </p>
                                    </v-flex>
                                    <v-flex
                                        s12
                                        m6
                                        lg4>
                                        <p>
                                            <b>Quantidade Meia</b><br>
                                            {{ produto.QtdeVendaPopularPromocional }}
                                        </p>
                                    </v-flex>
                                    <v-flex
                                        s12
                                        m6
                                        lg4>
                                        <p>
                                            <b>Valor m&eacute;dio</b><br>
                                            {{ produto.ReceitaPopularNormal }}
                                        </p>
                                    </v-flex>
                                </v-layout>

                                <v-layout
                                    justify-space-around
                                    row
                                    wrap>
                                    <v-flex
                                        lg12
                                        class="text-xs-center">
                                        <b>PROPONENTE</b>
                                    </v-flex>
                                    <v-flex
                                        s6
                                        m6
                                        lg3
                                        offset-lg1>
                                        <p>
                                            <b>Quantidade Inteira</b><br>
                                            {{ produto.QtdeVendaNormal }}
                                        </p>
                                    </v-flex>
                                    <v-flex
                                        s12
                                        m6
                                        lg4>
                                        <p>
                                            <b>Quantidade Meia</b><br>
                                            {{ produto.QtdeVendaPromocional }}
                                        </p>
                                    </v-flex>
                                    <v-flex
                                        s12
                                        m6
                                        lg4>
                                        <p>
                                            <b>Valor m&eacute;dio</b><br>
                                            {{ produto.PrecoUnitarioNormal }}
                                        </p>
                                    </v-flex>
                                </v-layout>

                                <v-layout
                                    justify-space-around
                                    row
                                    wrap>
                                    <v-flex
                                        lg5
                                        offset-lg1
                                        class="text-xs-center pl-4">
                                        <p><b>QUANTIDADE TOTAL</b></p>
                                        <p>{{ produto.QtdeProduzida }}</p>
                                    </v-flex>
                                    <v-flex
                                        lg6
                                        class="text-xs-center">
                                        <p><b>RECEITA PREVISTA TOTAL</b></p>
                                        <p>{{ produto.Receita }}</p>
                                    </v-flex>
                                </v-layout>
                            </v-card-text>
                            <DetalhamentoPlanoDistribuicao
                                :array-detalhamentos="
                                    detalhamentosByID(dadosIn2017.tbdetalhaplanodistribuicao,
                                                      produto.idPlanoDistribuicao)"/>
                        </v-card>
                    </v-container>
                </v-expansion-panel-content>
            </v-expansion-panel>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';
import PropostaPlanoDistribuicao from '@/modules/proposta/visualizar/components/PropostaPlanoDistribuicao';
import DetalhamentoPlanoDistribuicao from './components/DetalhamentoPlanoDistribuicao';

export default {
    name: 'PlanoDistribuicaoIn2017',
    components: {
        Carregando,
        PropostaPlanoDistribuicao,
        DetalhamentoPlanoDistribuicao,
    },
    props: {
        idPronac: {
            type: Number,
            default: 0,
        },
    },
    data() {
        return {
            detalhamentos: [],
            loading: true,
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            dadosIn2017: 'outrasInformacoes/planoDistribuicaoIn2017',
        }),
    },
    watch: {
        dadosIn2017() {
            this.loading = false;
        },
    },
    mounted() {
        if (typeof this.dadosProjeto.idPreProjeto !== 'undefined') {
            this.buscarPlanoDistribuicaoIn2017(this.dadosProjeto.idPreProjeto);
        }

        if (typeof this.dadosIn2017.tbdetalhaplanodistribuicao !== 'undefined') {
            this.detalhamentos = this.dadosIn2017.tbdetalhaplanodistribuicao;
        }
    },
    methods: {
        ...mapActions({
            buscarPlanoDistribuicaoIn2017: 'outrasInformacoes/buscarPlanoDistribuicaoIn2017',
        }),
        label_sim_ou_nao(valor) {
            if (valor === 1) {
                return 'Sim';
            }
            return 'N\xE3o';
        },
        detalhamentosByID(lista, id) {
            if (typeof lista !== 'undefined') {
                /* eslint-disable */
                let novaLista = [];

                Object.keys(lista).map((key) => {
                    if (lista[key].idPlanoDistribuicao === id) {
                        novaLista.push(lista[key]);
                    }
                    return novaLista;
                });
                return novaLista;
            }
            return lista;
        },
    },
};
</script>
