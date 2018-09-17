<template>
    <div>
        <div class="text-xs-center">
            <v-dialog
                    v-model="dialog"
                    width="650"
            >
                <v-btn
                        slot="activator"
                        color="green lighten-2"
                        text="white"
                        dark
                >
                    Encaminhar

                </v-btn>

                <v-card>
                    <v-form
                            v-model="form"
                            ref="form"
                    >
                        <v-card-title
                                class="headline green"
                                primary-title
                        >
                            <span class="white--text">
                                Encaminhamento do projeto
                            </span>
                        </v-card-title>
                        <v-card-text>
                            <v-list three-line subheader>
                                <v-subheader>
                                    Área de Encaminhamento
                                </v-subheader>
                                <v-list-tile>
                                    <v-list-tile-action>
                                        <v-icon color="green">group</v-icon>
                                    </v-list-tile-action>
                                    SEFIC/DEIPC/CGARE
                                </v-list-tile>
                                <v-divider></v-divider>
                                <v-subheader>
                                    Informações do encaminhamento
                                </v-subheader>
                                <v-list-tile>
                                    <v-list-tile-action>
                                        <v-icon color="green">perm_identity</v-icon>
                                    </v-list-tile-action>
                                    <v-select
                                            v-model="destinatarioEncaminhamento"
                                            height="10px"
                                            solo
                                            single-line
                                            :items="dadosProjeto.tecnicos"
                                            label="-- Escolha um técnico  --"
                                            :rules="[rules.required]"
                                    ></v-select>
                                </v-list-tile>
                                <v-list-tile>
                                    <v-textarea
                                            ref="justificativa"
                                            label="Justificativa de encaminhamento para análise"
                                            prepend-icon="create"
                                            color="green"
                                            autofocus
                                            :rules="[rules.required]"
                                    ></v-textarea>
                                </v-list-tile>
                            </v-list>
                        </v-card-text>
                        <v-divider></v-divider>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn
                                    color="primary"
                                    flat
                                    @click="enviarEncaminhamento()"
                                    :disabled="!form"
                            >
                                Encaminhar
                            </v-btn>
                            <v-btn
                                    color="red"
                                    flat
                                    @click="dialog = false, $refs.form.reset()"
                            >
                                Fechar
                            </v-btn>
                        </v-card-actions>
                    </v-form>
                </v-card>
            </v-dialog>
        </div>
    </div>
</template>

<script>
    import { mapActions, mapGetters } from 'vuex';
    import Modal from '@/components/modal';

    export default {
        name: 'ComponenteEncaminhar',
        created() {
            this.obterDadosTabela();
        },
        props: ['idPronac'],
        data() {
            return {
                dialog: false,
                dadosProjeto: {
                    idPronac: 123456,
                    nomeProjeto: 'dfdfgdfg',
                    tecnicos: ['Foo', 'Bar', 'Fizz', 'Buzz']
                },
                rules: {
                    required: v => !!v
                },
                destinatarioEncaminhamento: null,
                justificativa: null,
                form: null
            }
        },
        watch: {
            dialog: function (val) {
                if(!val) this.$refs.form.reset()
            },
        },
        components: {
            Modal,
        },
        computed: {
            ...mapGetters({
                dadosTabela: 'foo/dadosTabela',
                modalVisible: 'modal/default',
            }),
        },
        methods: {
            ...mapActions({
                obterDadosTabela: 'foo/obterDadosTabela',
                setRegistroAtivo: 'foo/setRegistroAtivo',
                removerRegistro: 'foo/removerRegistro',
                modalOpen: 'modal/modalOpen',
                modalClose: 'modal/modalClose',
            }),
            enviarEncaminhamento(){
                this.dialog = false;
                this.$refs.form.reset();
            }
        },
    };
</script>