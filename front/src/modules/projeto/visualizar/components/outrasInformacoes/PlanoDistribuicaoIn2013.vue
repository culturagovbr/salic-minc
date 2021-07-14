<template>
    <div>
        <div v-if="loading">
            <Carregando :text="'Carregando Plano de Distribuicao'"/>
        </div>
        <div v-else-if="dadosIn2013">
            <v-expansion-panel popout>
                <v-expansion-panel-content
                    v-for="(dado, index) in dadosIn2013"
                    :key="index"
                    class="elevation-1"
                >
                    <v-layout
                        slot="header"
                        class="primary--text">
                        <v-icon class="mr-3 primary--text">subject</v-icon>
                        <span>{{ dado.Produto }}</span>
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
                                        lg3
                                        offset-lg2>
                                        <p><b>Produto</b></p>
                                        <p>{{ dado.Produto }}</p>
                                    </v-flex>
                                    <v-flex
                                        s6
                                        m6
                                        lg3>
                                        <p><b>&Aacute;REA</b></p>
                                        <p>{{ dado.Area }}</p>
                                    </v-flex>
                                    <v-flex
                                        s12
                                        m6
                                        offset-xlg10>
                                        <p><b>SEGMENTO</b></p>
                                        <p>{{ dado.Segmento }}</p>
                                    </v-flex>
                                </v-layout>
                                <v-layout
                                    justify-space-around
                                    row
                                    wrap>
                                    <v-flex
                                        lg12
                                        class="text-xs-center">
                                        <p><b>DISTRIBUI&Ccedil;&Atilde;O GRATUITA (QTDE)</b></p>
                                    </v-flex>
                                    <v-flex
                                        s12
                                        m6
                                        lg3
                                        offset-lg2>
                                        <p>
                                            <b>Patrocinador</b><br>
                                            {{ dado.QtdePatrocinador }}
                                        </p>
                                    </v-flex>
                                    <v-flex
                                        s6
                                        m6
                                        lg3>
                                        <p>
                                            <b>Divulga&ccedil;&atilde;o</b><br>
                                            {{ dado.QtdeProponente }}
                                        </p>
                                    </v-flex>
                                    <v-flex
                                        s12
                                        m6
                                        offset-xlg10>
                                        <p>
                                            <b>Popula&ccedil;&atilde;o Baixa Renda</b><br>
                                            {{ dado.QtdeOutros }}
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
                                        <p><b>TOTAL PARA VENDA (QTDE)</b></p>
                                    </v-flex>
                                    <v-flex
                                        lg6
                                        class="text-xs-center">
                                        <p><b>TOTAL PRODUZIDO</b></p>
                                    </v-flex>
                                    <v-flex
                                        s12
                                        m6
                                        lg3
                                        offset-lg2>
                                        <p>
                                            <b>Normal</b><br>
                                            {{ dado.QtdeVendaNormal }}
                                        </p>
                                    </v-flex>
                                    <v-flex
                                        s6
                                        m6
                                        lg3>
                                        <p>
                                            <b>Promocional</b><br>
                                            {{ dado.QtdeVendaPromocional }}
                                        </p>
                                    </v-flex>
                                    <v-flex
                                        s12
                                        m6
                                        offset-xlg10
                                        class="pl-5">
                                        <p>
                                            <b>N&ordm; Exemplares / Ingressos</b><br>
                                            {{ dado.QtdeProduzida }}
                                        </p>
                                    </v-flex>
                                </v-layout>
                                <v-layout
                                    justify-space-around
                                    row
                                    wrap>
                                    <v-flex
                                        lg12
                                        offset-lg4
                                        class="text-xs-left">
                                        <p><b>PRE&Ccedil;O UNIT&Aacute;RIO (R$)</b></p>
                                    </v-flex>
                                    <v-flex
                                        s12
                                        m6
                                        lg3
                                        offset-lg1
                                        class="pl-3">
                                        <p>
                                            <b>Normal</b><br>
                                            {{ dado.PrecoUnitarioNormal }}
                                        </p>
                                    </v-flex>
                                    <v-flex
                                        s6
                                        m6
                                        lg3>
                                        <p>
                                            <b>Promocional</b><br>
                                            {{ dado.PrecoUnitarioPromocional }}
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
                                        <p><b>RECEITA PREVISTA (R$)</b></p>
                                    </v-flex>
                                    <v-flex
                                        lg6
                                        class="text-xs-center">
                                        <p><b>TOTAL RECEITA PREVISTA (R$)</b></p>
                                    </v-flex>
                                    <v-flex
                                        s12
                                        m6
                                        lg3
                                        offset-lg2>
                                        <p>
                                            <b>Normal</b><br>
                                            {{ dado.ReceitaNormal }}
                                        </p>
                                    </v-flex>
                                    <v-flex
                                        s6
                                        m6
                                        lg3>
                                        <p>
                                            <b>Promocional</b><br>
                                            {{ dado.ReceitaPro }}
                                        </p>
                                    </v-flex>
                                    <v-flex
                                        s12
                                        m6
                                        offset-xlg10
                                        class="pl-5">
                                        <p>
                                            <b>Total Receita Prevista (R$)</b><br>
                                            {{ dado.ReceitaPrevista }}
                                        </p>
                                    </v-flex>
                                </v-layout>
                            </v-card-text>
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

export default {
    name: 'PlanoDistribuicaoIn2013',
    components: {
        Carregando,
    },
    props: {
        idPronac: {
            type: Number,
            default: 0,
        },
    },
    data() {
        return {
            informacoes: {},
            loading: true,
            activeTab: -1,
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            dadosIn2013: 'outrasInformacoes/planoDistribuicaoIn2013',
        }),
    },
    watch: {
        dados(value) {
            this.informacoes = value.informacoes;
        },
        dadosIn2013() {
            this.loading = false;
        },
    },
    mounted() {
        if (typeof this.$route.params.idPronac !== 'undefined') {
            this.buscarPlanoDistribuicaoIn2013(this.dadosProjeto.idPronac);
        }
    },
    methods: {
        ...mapActions({
            buscarPlanoDistribuicaoIn2013: 'outrasInformacoes/buscarPlanoDistribuicaoIn2013',
        }),
        setActiveTab(index) {
            if (this.activeTab === index) {
                this.activeTab = -1;
            } else {
                this.activeTab = index;
            }
        },
    },
};
</script>
