<template>
    <v-layout
        v-if="perfilAceito"
    >
        <v-btn
            dark
            icon
            flat
            small
            color="green darken-3"
            @click.stop="abrirEdicao()"
        >
            <v-tooltip bottom>
                <v-icon slot="activator">
                    edit
                </v-icon>
                <span>Editar Readequação</span>
            </v-tooltip>
        </v-btn>
        <template
            v-if="!getTemplateParaTipo"
        >
            <template-redirect
                :dados-readequacao="dadosReadequacao"
                :campo="campoAtual"
                :redirecionar="redirecionar"
            />
        </template>
        <v-dialog
            :persistent="mensagem.ativa"
            v-model="dialog"
            fullscreen
            hide-overlay
            transition="dialog-bottom-transition"
            @keydown.esc="dialog = false"
        >
            <v-card>
                <v-toolbar
                    dark
                    color="primary"
                    fixed
                >
                    <v-btn
                        icon
                        dark
                        @click="dialog = false"
                    >
                        <v-icon>
                            close
                        </v-icon>
                    </v-btn>
                    <v-toolbar-title>Readequação - {{ dadosReadequacao.dsTipoReadequacao }}</v-toolbar-title>
                    <v-spacer/>
                    <v-toolbar-title>{{ dadosProjeto.Pronac }} - {{ dadosProjeto.NomeProjeto }}</v-toolbar-title>
                </v-toolbar>
                <v-layout
                    row
                    wrap
                    class="mt-5"
                >
                    <v-flex
                        v-if="loading"
                        xs10
                        offset-xs1
                    >
                        <carregando
                            :text="'Montando edição de readequação...'"
                            class="mt-5"
                        />
                    </v-flex>
                    <v-flex
                        v-else
                        xs10
                        offset-xs1
                    >
                        <v-card
                            v-if="getTemplateParaTipo"
                        >
                            <component
                                :is="getTemplateParaTipo"
                                :dados-readequacao="dadosReadequacao"
                                :campo="getDadosCampo"
                                :min-char="minChar.solicitacao"
                                :rules="rules"
                                @dados-update="atualizarCampo($event, 'dsSolicitacao')"
                                @editor-texto-counter="atualizarContador($event, 'solicitacao')"
                            />
                        </v-card>
                        <v-card
                            class="mb-5"
                        >
                            <v-card-title
                                class="green lighten-2 title"
                            >
                                Justificativa da readequação
                            </v-card-title>
                            <form-justificativa
                                :dados-readequacao="dadosReadequacao"
                                :min-char="minChar.justificativa"
                                @dados-update="atualizarCampo($event, 'dsJustificativa')"
                                @editor-texto-counter="atualizarContador($event, 'justificativa')"
                            />
                        </v-card>
                        <v-card
                            class="mb-5"
                        >
                            <v-card-title
                                class="green lighten-2 title"
                            >
                                Arquivo anexo
                            </v-card-title>
                            <v-card-actions>
                                <upload-file
                                    :formatos-aceitos="formatosAceitos"
                                    :id-documento="getReadequacao.idDocumento"
                                    :action-done="uploadActionDone"
                                    class="mt-1"
                                    @arquivo-anexado="atualizarArquivo($event)"
                                    @arquivo-removido="removerArquivo()"
                                />
                            </v-card-actions>
                        </v-card>
                        <v-footer
                            id="footer"
                            class="pb-4 pt-4 elevation-18"
                            fixed
                        >
                            <v-flex xs11>
                                <v-layout
                                    row
                                    wrap
                                    justify-end
                                    text-xs-right
                                >
                                    <v-btn
                                        color="green darken-1"
                                        dark
                                        @click="salvarReadequacao()"
                                    >Salvar
                                        <v-icon
                                            right
                                            dark
                                        >
                                            done
                                        </v-icon>
                                    </v-btn>
                                    <v-btn
                                        color="red lighten-2"
                                        dark
                                        @click="dialog = false"
                                    >Fechar
                                        <v-icon
                                            right
                                            dark
                                        >
                                            cancel
                                        </v-icon>
                                    </v-btn>
                                    <finalizar-button
                                        :disabled="!validacao"
                                        :dados-readequacao="dadosReadequacao"
                                        :dados-projeto="dadosProjeto"
                                        :tela-edicao="true"
                                        :readequacao-editada="readequacaoEditada"
                                        dark
                                        @readequacao-finalizada="readequacaoFinalizada()"
                                    />
                                </v-layout>
                            </v-flex>
                        </v-footer>
                    </v-flex>
                </v-layout>
                <v-snackbar
                    :color="mensagem.cor"
                    :timeout="mensagem.timeout"
                    v-model="mensagem.ativa"
                    bottom
                ><span>{{ mensagem.conteudo }}</span>
                    <v-btn
                        dark
                        flat
                        @click="mensagem.ativa = false"
                    >
                        Fechar
                    </v-btn>
                </v-snackbar>
            </v-card>
        </v-dialog>
    </v-layout>
</template>

<script>
import _ from 'lodash';
import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';
import FormJustificativa from './FormJustificativa';
import TemplateTextarea from './TemplateTextarea';
import TemplateInput from './TemplateInput';
import TemplateDate from './TemplateDate';
import TemplateRedirect from './TemplateRedirect';
import FinalizarButton from './FinalizarButton';
import UploadFile from './UploadFile';
import validarFormulario from '../mixins/validarFormulario';
import verificarPerfil from '../mixins/verificarPerfil';

export default {
    name: 'EditarReadequacaoButton',
    components: {
        Carregando,
        FinalizarButton,
        FormJustificativa,
        TemplateTextarea,
        TemplateInput,
        TemplateDate,
        TemplateRedirect,
        UploadFile,
    },
    mixins: [
        validarFormulario,
        verificarPerfil,
    ],
    props: {
        dadosReadequacao: {
            type: Object,
            default: () => {},
        },
        dadosProjeto: {
            type: Object,
            default: () => {},
        },
        bindClick: {
            type: Number,
            default: 0,
        },
        minChar: {
            type: Object,
            default: () => {},
        },
        perfisAceitos: {
            type: Array,
            default: () => [],
        },
        perfil: {
            type: [Number, String],
            default: 0,
        },
    },
    data() {
        return {
            dialog: false,
            tiposComponentesReadequacao: {
                textarea: 'TemplateTextarea',
                input: 'TemplateInput',
                date: 'TemplateDate',
            },
            templateEdicao: [],
            formatosAceitos: 'application/pdf',
            panel: [true, true],
            readequacaoEditada: {
                idReadequacao: 0,
                dsSolicitacao: '',
                dsJustificativa: '',
                dtSolicitacao: '',
                documento: {},
                idDocumento: '',
                dsAvaliacao: '',
            },
            redirecionar: false,
            mensagem: {
                ativa: false,
                timeout: 2300,
                conteudo: '',
                cor: '',
                finaliza: false,
            },
            recarregarReadequacoes: false,
            validacao: false,
            contador: {
                solicitacao: 0,
                justificativa: 0,
            },
            rules: {
                required: v => !!v || 'Campo obrigatório.',
                dataExecucaoChars: v => (v && v.length >= this.minChar.dataExecucao) || 'Data em formato inválido',
                dataExecucao: v => (v !== this.getValorCampoAtual()) || 'Data deve ser diferente da original.',
                somenteNumerico: v => (v && /^[\d]*$/.test(v)) || 'Somente dados numéricos',
                solicitacao: v => (v && v.length >= this.minChar.solicitacao) || `Deve ter no mínimo ${this.minChar.solicitacao} caracteres.`,
                justificativa: v => (v && v.length >= this.minChar.justificativa)
                    || `Justificativa ter no mínimo ${this.minChar.justificativa} caracteres.`,
            },
            campos: [
                'dsSolicitacao',
                'dsJustificativa',
            ],
            loading: true,
            arquivo: {},
            uploadActionDone: true,
        };
    },
    computed: {
        ...mapGetters({
            campoAtual: 'readequacao/getCampoAtual',
            getReadequacao: 'readequacao/getReadequacao',
        }),
        getTemplateParaTipo() {
            let templateName = false;
            const chave = `key_${this.dadosReadequacao.idTipoReadequacao}`;
            if (Object.prototype.hasOwnProperty.call(this.campoAtual, chave)) {
                templateName = this.tiposComponentesReadequacao[this.campoAtual[chave].tpCampo];
            }
            return templateName;
        },
        getDadosCampo() {
            const chave = `key_${this.dadosReadequacao.idTipoReadequacao}`;
            if (Object.prototype.hasOwnProperty.call(this.campoAtual, chave)) {
                return {
                    valor: this.campoAtual[chave].dsCampo,
                    titulo: this.campoAtual[chave].descricao,
                    tpCampo: this.campoAtual[chave].tpCampo,
                };
            }
            return {};
        },
        perfilAceito() {
            return this.verificarPerfil(this.perfil, this.perfisAceitos);
        },
    },
    watch: {
        getDadosCampo: {
            handler(value) {
                if (!_.isEmpty(value)) {
                    this.loading = false;
                }
            },
            deep: true,
        },
        bindClick() {
            if (this.bindClick === this.dadosReadequacao.idReadequacao) {
                this.dialog = true;
            }
        },
        mensagem: {
            handler(mensagem) {
                if (mensagem.ativa === false
                    && mensagem.finaliza === true) {
                    this.dialog = false;
                }
            },
            deep: true,
        },
        dadosReadequacao: {
            handler(value) {
                if (value.idPronac && value.idTipoReadequacao) {
                    if (this.bindClick === this.dadosReadequacao.idReadequacao) {
                        this.dialog = true;
                    }
                }
            },
            deep: true,
        },
        dialog() {
            if (this.dialog === false
                && this.recarregarReadequacoes === true) {
                this.$emit('atualizar-readequacao', { idReadequacao: this.readequacaoEditada.idReadequacao });
            } else {
                this.obterDadosIniciais();
            }
        },
    },
    methods: {
        ...mapActions({
            obterCampoAtual: 'readequacao/obterCampoAtual',
            obterDocumento: 'readequacao/obterDocumento',
            obterReadequacao: 'readequacao/obterReadequacao',
            updateReadequacao: 'readequacao/updateReadequacao',
            finalizarReadequacao: 'readequacao/finalizarReadequacao',
        }),
        obterDadosIniciais() {
            if (
                this.dadosReadequacao.idPronac && this.dadosReadequacao.idTipoReadequacao
            ) {
                this.inicializarReadequacaoEditada();
                this.obterReadequacao(this.dadosReadequacao);
                const key = `key_${this.dadosReadequacao.idTipoReadequacao}`;
                if (typeof this.campoAtual[key] === 'undefined') {
                    this.obterCampoAtual({
                        idPronac: this.dadosReadequacao.idPronac,
                        idTipoReadequacao: this.dadosReadequacao.idTipoReadequacao,
                    });
                }
            }
        },
        abrirEdicao() {
            this.inicializarReadequacaoEditada();
            this.obterCampoAtual({
                idPronac: this.dadosReadequacao.idPronac,
                idTipoReadequacao: this.dadosReadequacao.idTipoReadequacao,
            }).then(() => {
                this.loading = false;
                if (typeof this.getTemplateParaTipo === 'undefined') {
                    this.redirecionar = true;
                } else {
                    this.dialog = true;
                }
                this.validar();
            });
        },
        salvarReadequacao() {
            this.updateReadequacao(this.readequacaoEditada).then(() => {
                this.mensagem.conteudo = 'Readequação salva com sucesso!';
                this.mensagem.timeout = 2300;
                this.mensagem.ativa = true;
                this.mensagem.finaliza = true;
                this.mensagem.cor = 'green darken-1';
                this.recarregarReadequacoes = true;
            });
        },
        inicializarReadequacaoEditada() {
            this.readequacaoEditada = {
                idPronac: this.dadosReadequacao.idPronac,
                idReadequacao: this.dadosReadequacao.idReadequacao,
                idTipoReadequacao: this.dadosReadequacao.idTipoReadequacao,
                dsAvaliacao: this.dadosReadequacao.dsAvaliacao,
                idDocumento: this.dadosReadequacao.idDocumento || '',
                dsSolicitacao: this.dadosReadequacao.dsSolicitacao,
                dsJustificativa: this.dadosReadequacao.dsJustificativa,
            };
        },
        atualizarArquivo(arquivo) {
            this.uploadActionDone = false;
            this.readequacaoEditada.documento = arquivo;
            if (this.readequacaoEditada.idReadequacao === 0) {
                this.readequacaoEditada.idReadequacao = this.dadosReadequacao.idReadequacao;
                this.readequacaoEditada.idPronac = this.dadosReadequacao.idPronac;
            }
            this.updateReadequacao(this.readequacaoEditada).then(() => {
                this.mensagem.conteudo = 'Arquivo enviado!';
                this.mensagem.ativa = true;
                this.mensagem.finaliza = false;
                this.mensagem.cor = 'green darken-1';
                this.recarregarReadequacoes = true;
                this.uploadActionDone = true;
            });
        },
        removerArquivo() {
            this.uploadActionDone = false;
            this.readequacaoEditada.documento = '';
            this.readequacaoEditada.idDocumento = '';
            this.updateReadequacao(this.readequacaoEditada).then(() => {
                this.mensagem.conteudo = 'Arquivo removido!';
                this.mensagem.ativa = true;
                this.mensagem.finaliza = false;
                this.mensagem.cor = 'green darken-1';
                this.recarregarReadequacoes = true;
                this.uploadActionDone = true;
            });
        },
        atualizarCampo(valor, campo) {
            if (typeof this.readequacaoEditada.idTipoReadequacao !== 'undefined') {
                if (this.campos.includes(campo)) {
                    this.readequacaoEditada[campo] = valor;
                    this.validar();
                }
            }
        },
        atualizarContador(valor, campo) {
            this.contador[campo] = valor;
        },
        validar() {
            if (typeof this.dadosReadequacao.idTipoReadequacao !== 'undefined') {
                let campo = '';
                const key = `key_${this.dadosReadequacao.idTipoReadequacao}`;
                if (typeof this.campoAtual[key] !== 'undefined') {
                    campo = this.campoAtual[key].dsCampo;
                }
                this.validacao = this.validarFormulario(
                    this.readequacaoEditada,
                    this.contador,
                    this.minChar,
                    campo,
                );
            }
        },
        getValorCampoAtual() {
            if (typeof this.dadosReadequacao.idReadequacao === 'number') {
                const key = `key_${this.dadosReadequacao.idTipoReadequacao}`;
                if (typeof this.campoAtual[key] !== 'undefined') {
                    return this.campoAtual[key].dsCampo;
                }
            }
            return false;
        },
        readequacaoFinalizada() {
            this.dialog = false;
        },
    },
};
</script>
<style>

#footer {
    z-index: 10;
}
</style>
