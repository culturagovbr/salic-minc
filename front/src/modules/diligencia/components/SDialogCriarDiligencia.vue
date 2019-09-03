<template>
    <v-dialog
        v-model="dialog"
        scrollable
        max-width="1200px"
        min-width="500px"
        @keydown.esc="dialog = false"
    >
        <v-card>
            <v-toolbar
                dark
                color="primary"
            >
                <v-btn
                    icon
                    dark
                    @click="dialog = false"
                >
                    <v-icon>close</v-icon>
                </v-btn>
                <v-toolbar-title>Criar nova diligência</v-toolbar-title>
                <v-spacer />
            </v-toolbar>
            <v-card-text>
                <v-container>
                    <v-card>
                        <v-card-text>
                            <v-form
                                ref="form"
                                v-model="valid"
                            >
                                <s-editor-texto
                                    v-model="diligenciaEmEdicao.solicitacao"
                                    :min-char="minChar"
                                    placeholder="Texto da diligência"
                                    @editor-texto-counter="validateText($event)"
                                />
                            </v-form>
                        </v-card-text>
                        <v-card-actions class="justify-center">
                            <v-btn
                                :loading="saveLoading"
                                :disabled="!valid || !textIsValid || saveLoading"
                                color="primary"
                                @click.native="enviarDiligencia()"
                            >
                                <v-icon left>
                                    send
                                </v-icon>
                                Enviar
                            </v-btn>
                            <v-btn
                                @click="dialog = false"
                            >
                                <v-icon left>
                                    clear
                                </v-icon>
                                Cancelar
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-container>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import SEditorTexto from '@/components/SalicEditorTexto';
import mixinUtils from '@/mixins/utils';

export default {
    name: 'SDialoagCriarDiligencia',
    components: {
        SEditorTexto,
    },
    mixins: [mixinUtils],
    props: {
        value: {
            type: Boolean,
            default: false,
        },
        idPronac: {
            type: Number,
            required: true,
        },
        tpDiligencia: {
            type: Number,
            required: true,
        },
        idProduto: {
            type: Number,
            default: null,
        },
        situacao: {
            type: String,
            default: null,
        },
    },
    data() {
        return {
            saveLoading: false,
            solicitacao: '',
            minChar: 10,
            valid: false,
            textIsValid: false,
            dialog: false,
            diligenciaRules: [
                v => !!v || 'Tipo de diligencia é obrigatório!',
            ],
            diligenciaEmEdicao: {
                idPronac: this.idPronac,
                tpDiligencia: this.tpDiligencia,
                solicitacao: '',
                idProduto: this.idProduto,
                situacao: this.situacao,
            },
        };
    },
    computed: {
        ...mapGetters({
            projeto: 'avaliacaoResultados/projeto',
        }),
    },
    watch: {
        value(val) {
            this.dialog = val;
        },
        dialog(val) {
            this.$emit('input', val);
        },
    },
    methods: {
        ...mapActions({
            requestEmissaoParecer: 'avaliacaoResultados/getDadosEmissaoParecer',
            salvar: 'diligencia/salvarDiligencia',
        }),
        validateText(e) {
            this.textIsValid = e >= this.minChar;
        },
        enviarDiligencia() {
            this.saveLoading = true;
            this.salvar(this.diligenciaEmEdicao).then(() => {
                this.dialog = false;
            }).finally(() => {
                this.saveLoading = false;
            });
        },
    },
};
</script>
