<template>
    <v-container grid-list-md >
        <v-layout row justify-center>
            <v-dialog v-model="dialog" width="600">
                <v-btn slot="activator" color="green darken-4" dark>Click Me</v-btn>
                <v-flex>
                    <v-card >
                        <v-card-title primary-title>
                            Tipo Avaliação
                        </v-card-title>

                        <v-card-text>

                            <v-list two-line subheader>
                                <v-subheader>Valores</v-subheader>
                                <v-list-tile @click="">
                                    <v-list-tile-action>
                                        <v-icon color="indigo">attach_money</v-icon>
                                    </v-list-tile-action>

                                    <v-list-tile-content>
                                        <v-list-tile-title>{{tipoAvaliacao.vlAprovado}}</v-list-tile-title>
                                        <v-list-tile-sub-title>Aprovado</v-list-tile-sub-title>
                                    </v-list-tile-content>
                                </v-list-tile>

                                <v-list-tile @click="">
                                    <v-list-tile-action>
                                        <v-icon color="indigo">attach_money</v-icon>
                                    </v-list-tile-action>

                                    <v-list-tile-content>
                                        <v-list-tile-title>{{tipoAvaliacao.vlCaptado}}</v-list-tile-title>
                                        <v-list-tile-sub-title>Captado</v-list-tile-sub-title>
                                    </v-list-tile-content>
                                </v-list-tile>

                                <v-list-tile @click="">
                                    <v-list-tile-action>
                                        <v-icon color="indigo">attach_money</v-icon>
                                    </v-list-tile-action>

                                    <v-list-tile-content>
                                        <v-list-tile-title>{{tipoAvaliacao.vlComprovado}}</v-list-tile-title>
                                        <v-list-tile-sub-title>Comprovado</v-list-tile-sub-title>
                                    </v-list-tile-content>
                                </v-list-tile>

                                <v-list-tile @click="">
                                    <v-list-tile-action>
                                        <v-icon color="indigo">attach_money</v-icon>
                                    </v-list-tile-action>

                                    <v-list-tile-content>
                                        <v-list-tile-title>{{tipoAvaliacao.qtComprovacao}}</v-list-tile-title>
                                        <v-list-tile-sub-title>Todos</v-list-tile-sub-title>
                                    </v-list-tile-content>
                                </v-list-tile>



                                <v-divider inset></v-divider>
                                <v-subheader>Quantidade de Comprovantes por nivel de confiança</v-subheader>

                                <v-list-tile @click="">
                                    <v-list-tile-action>
                                        <v-icon color="indigo">bar_chart</v-icon>
                                    </v-list-tile-action>

                                    <v-list-tile-content>
                                        <v-list-tile-title>{{tipoAvaliacao.qtNC_90}}</v-list-tile-title>
                                        <v-list-tile-sub-title>90%</v-list-tile-sub-title>
                                    </v-list-tile-content>
                                </v-list-tile>

                                <v-list-tile @click="">
                                    <v-list-tile-action>
                                        <v-icon color="indigo">bar_chart</v-icon>
                                    </v-list-tile-action>

                                    <v-list-tile-content>
                                        <v-list-tile-title>{{tipoAvaliacao.qtNC_95}}</v-list-tile-title>
                                        <v-list-tile-sub-title>95%</v-list-tile-sub-title>
                                    </v-list-tile-content>
                                </v-list-tile>

                                <v-list-tile @click="">
                                    <v-list-tile-action>
                                        <v-icon color="indigo">bar_chart</v-icon>
                                    </v-list-tile-action>

                                    <v-list-tile-content>
                                        <v-list-tile-title>{{tipoAvaliacao.qtNC_99}}</v-list-tile-title>
                                        <v-list-tile-sub-title>99%</v-list-tile-sub-title>
                                    </v-list-tile-content>
                                </v-list-tile>

                                <v-divider inset></v-divider>
                                <v-subheader>Tipo de avaliação</v-subheader>
                            </v-list>

                             <v-radio-group row column v-model="percentual" :click="redirecionarEncaminhar()">
                                <v-radio color="green darken-4"  label="Todos Comprovantes" :value="0"></v-radio>
                                 <v-radio color="green darken-4" label="90%"  :value="90"></v-radio>
                                 <v-radio color="green darken-4" label="95%"  :value="95"></v-radio>
                                 <v-radio color="green darken-4" label="99%"  :value="99"></v-radio>

                            </v-radio-group>


                        </v-card-text>

                        <v-divider></v-divider>

                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn dark large color="green darken-4" :href="redirect">AVALIAR</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-flex>
            </v-dialog>
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
            encaminhar: '',
            aprovado: 300,
            captado: 200,
            comprovado: 100,
            todos: 75,
            valor1: 80,
            valor2: 10,
            valor3: 20,
            dialog: false,
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
            redirectLink: 'avaliacaoResultados/redirectLinkAvaliacaoResultadoTipo',
        }),

        getTipoAvaliacaoResultado(id) {
            this.getTipo(id);
        },
        fecharModal() {
            // eslint-disable-next-line
            $3('#modalTemplate').modal('close');
            this.modalClose();
        },
        redirecionarEncaminhar() {
            const data = { idPronac: this.idPronac, percentual: this.percentual };
            this.redirectLink(data);
        },
    },
    computed: {
        ...mapGetters({
            modalVisible: 'modal/default',
            tipoAvaliacao: 'avaliacaoResultados/tipoAvaliacao',
            redirect: 'avaliacaoResultados/redirectLink',
        }),
    },
    mounted() {
        this.getTipoAvaliacaoResultado(this.idPronac);
    },
};
</script>
