<template>
    <v-container grid-list-md >
        <v-layout  column>
            <v-flex xs12>
                <v-layout row xs12  wrap align-space-between  >
                    <v-flex  xs12 >
                        <v-card>
                            <v-card-text>
                                <table class="v-datatable v-table">
                                    <tr>
                                        <th class="text-xs-left">
                                            Dt.Envio Prestacao de Contas
                                        </th>
                                        <th class="text-xs-left">
                                            Resultado da Avaliacao do Objeto
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>{{tipoAvaliacao.DtEnvioDaPrestacaoContas}}</td>
                                        <td class="text-xs-left">{{tipoAvaliacao.ResultadoAvaliacaoObjeto}}</td>
                                    </tr>
                                </table>
                            </v-card-text>
                        </v-card>
                    </v-flex>

                    <v-flex xs12 >
                        <v-card>
                            <v-card-text>
                                <table class="v-datatable v-table">
                                    <tr>
                                        <th colspan="3" class="text-xs-center">
                                            Valores
                                        </th>
                                        <th colspan="4" class="text-xs-center">
                                            Quantidade de Comprovantes por nivel de confianca
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="text-xs-center">Aprovado</th>
                                        <th class="text-xs-center">Captado</th>
                                        <th class="text-xs-center">Comprovado</th>
                                        <th>Todos</th>
                                        <th>90%</th>
                                        <th>95%</th>
                                        <th>99%</th>
                                    </tr>
                                    <tr>
                                        <td class="text-xs-center">{{tipoAvaliacao.vlAprovado}}</td>
                                        <td class="text-xs-center">{{tipoAvaliacao.vlCaptado}}</td>
                                        <td class="text-xs-center">{{tipoAvaliacao.vlComprovado}}</td>
                                        <td class="text-xs-center">{{tipoAvaliacao.qtComprovacao}}</td>
                                        <td class="text-xs-center">{{tipoAvaliacao.qtNC_90}}</td>
                                        <td class="text-xs-center">{{tipoAvaliacao.qtNC_95}}</td>
                                        <td class="text-xs-center">{{tipoAvaliacao.qtNC_99}}</td>
                                    </tr>
                                </table>
                            </v-card-text>
                        </v-card>
                    </v-flex>

                    <v-flex xs12>
                        <v-card>
                            <v-card-text>
                                <fieldset>
                                    <legend>SELECIONAR O TIPO DE AVALIACAO FINANCERIA</legend>
                                    <p>Avaliar comprovantes por nivel de confianca:</p>
                                    <v-flex xs12 sm6 md6>
                                        <v-radio-group  column v-model="percentual" :click="redirecionarEncaminhar()">

                                            <v-radio
                                                    label="Todos Comprovantes"
                                                    color="cyan darken-2"
                                                    :value="0"
                                            ></v-radio>
                                            <v-radio
                                                    label="90%"
                                                    color="teal darken-1"
                                                    :value="90"
                                            ></v-radio>
                                            <v-radio
                                                    label="95%"
                                                    color="teal darken-1"
                                                    :value="95"
                                            ></v-radio>
                                            <v-radio
                                                    label="99%"
                                                    color="teal darken-1"
                                                    :value="99"
                                            ></v-radio>
                                        </v-radio-group>
                                        <v-btn dark large color="teal darken-1" :href="redirect">AVALIAR</v-btn>
                                    </v-flex>
                                </fieldset>
                            </v-card-text>
                        </v-card>
                    </v-flex>

                </v-layout>

            </v-flex>

        </v-layout>
    </v-container>


</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import ModalTemplate from '@/components/modal';

export default {
    name: 'Painel',
    data() {
        return {
            idPronac: this.$route.params.id,
            percentual: '',
            encaminhar:'',
            aprovado:300,
            captado:200,
            comprovado:100,
            todos:75,
            valor1:80,
            valor2:10,
            valor3:20,
        };
    },
    components: {
        ModalTemplate,
    },
    methods: {
        ...mapActions({
            criarRegistro: 'foo/criarRegistro',
            modalOpen: 'modal/modalOpen',
            modalClose: 'modal/modalClose',
            getTipo: 'avaliacaoResultados/getTipoAvaliacao',
            redirectLink: 'avaliacaoResultados/redirectLinkAvaliacaoResultadoTipo'
        }),

        getTipoAvaliacaoResultado(id)
        {
            this.getTipo(id);
        },
        fecharModal() {
            // eslint-disable-next-line
            $3('#modalTemplate').modal('close');
            this.modalClose();
        },
        redirecionarEncaminhar(){
            const data = {idPronac: this.idPronac , percentual: this.percentual};
            this.redirectLink(data);
        }
    },
    computed: {
        ...mapGetters({
            modalVisible: 'modal/default',
            tipoAvaliacao: 'avaliacaoResultados/tipoAvaliacao',
            redirect: 'avaliacaoResultados/redirectLink'
        }),
    },
    mounted()
    {
        this.getTipoAvaliacaoResultado(this.idPronac);
    },
};
</script>

