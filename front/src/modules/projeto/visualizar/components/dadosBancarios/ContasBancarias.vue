<template>
    <div>
        <div v-if="loading">
            <Carregando :text="'Contas Bancárias'"/>
        </div>
        <div v-else-if="Object.keys(dadosConta).length > 0">
            <v-card>
                <v-card-text>
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
                                <b><h4>DADOS DA CONTA</h4></b>
                                <v-divider class="pb-2"/>
                            </v-flex>
                            <v-flex>
                                <p><b>Banco</b></p>
                                <p>
                                    {{ dadosConta.Banco }}
                                </p>
                            </v-flex>
                            <v-flex>
                                <p><b>Agência</b></p>
                                <p>
                                    {{ dadosConta.Agencia | formatarAgencia }}
                                </p>
                            </v-flex>
                            <v-flex/>
                        </v-layout>
                        <v-layout
                            justify-space-around
                            row
                            wrap>
                            <v-flex
                                lg12
                                dark
                                class="text-xs-left">
                                <b><h4>CONTA CAPTAÇÃO</h4></b>
                                <v-divider class="pb-2"/>
                            </v-flex>
                            <v-flex>
                                <p><b>Número</b></p>
                                <p>
                                    {{ dadosConta.ContaBloqueada | formatarConta }}
                                </p>
                            </v-flex>
                            <v-flex>
                                <p><b>Dt. Abertura</b></p>
                                <p>
                                    {{ dadosConta.DtLoteRemessaCB | formatarData }}
                                </p>
                            </v-flex>
                            <v-flex>
                                <p><b>Ocorrência</b></p>
                                <p v-if="dadosConta.OcorrenciaCB">{{ dadosConta.OcorrenciaCB }}</p>
                                <p v-else> - </p>
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
                                <b><h4>CONTA MOVIMENTO</h4></b>
                                <v-divider class="pb-2"/>
                            </v-flex>
                            <v-flex>
                                <p><b>Número</b></p>
                                <p>
                                    {{ dadosConta.ContaLivre | formatarConta }}
                                </p>
                            </v-flex>
                            <v-flex>
                                <p><b>Dt. Abertura</b></p>
                                <p>
                                    {{ dadosConta.DtLoteRemessaCL | formatarData }}
                                </p>
                            </v-flex>
                            <v-flex>
                                <p><b>Ocorrência</b></p>
                                <p v-if="dadosConta.OcorrenciaCL ">
                                    {{ dadosConta.OcorrenciaCL }}
                                </p>
                                <p v-else> - </p>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card-text>
            </v-card>
        </div>
        <v-layout v-else>
            <v-container
                grid-list-md
                text-xs-center>
                <v-layout
                    row
                    wrap>
                    <v-flex>
                        <v-card>
                            <v-card-text class="px-0">
                                Nenhuma Contas Bancárias encontrada
                            </v-card-text>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-layout>
    </div>
</template>
<script>

import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';
import { utils } from '@/mixins/utils';

export default {
    name: 'ContasBancarias',
    components: {
        Carregando,
    },
    mixins: [utils],
    data() {
        return {
            loading: true,
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            dadosConta: 'dadosBancarios/contasBancarias',
        }),
    },
    watch: {
        dadosConta() {
            this.loading = false;
        },
        dadosProjeto(value) {
            this.loading = true;
            this.buscarContasBancarias(value.idPronac);
        },
    },
    mounted() {
        if (typeof this.dadosProjeto.idPronac !== 'undefined') {
            this.buscarContasBancarias(this.dadosProjeto.idPronac);
        }
    },
    methods: {
        ...mapActions({
            buscarContasBancarias: 'dadosBancarios/buscarContasBancarias',
        }),
    },
};
</script>
