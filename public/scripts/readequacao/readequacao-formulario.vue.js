Vue.component('readequacao-formulario', {
    template: `
    <div class="card">
      <div v-if="!disabled" class="card-content">
        <span class="card-title">Solicita&ccedil;&atilde;o de readequa&ccedil;&atilde;o</span>
        <input type="hidden" v-model="readequacao.idReadequacao"/>
        <div class="row">
          <div class="input-field col s12">
            <textarea
              :disabled="disabled"
              id="textarea1"
              class="materialize-textarea"
              ref="readequacaoJustificativa"
              v-model="readequacao.justificativa"></textarea>
            <label for="textarea1">Justificativa *</label>
          </div>
        </div>
        <component
          v-bind:is="componenteDsSolicitacao"
          :ds-solicitacao="readequacao.dsSolicitacao"
          :disabled="disabled"
          v-on:eventoAtualizarDsSolicitacao="atualizarDsSolicitacao($event)"
          ></component>
        <div class="row">
          <div class="col s12">
            <div v-if="!disabled" class="file-field input-field">
              <div class="btn">
                <span>Selecionar arquivo</span>
                <input type="file"
                       name="arquivo"
                       id="arquivo"
                                   @change="subirDocumento">
              </div>
              <div class="file-path-wrapper">
                <input class="file-path validate" type="text">
              </div>
              <input type="hidden" v-model="readequacao.idDocumento"/>
            </div>
            <div id="carregando-arquivo" class="progress sumir">
              <div class="indeterminate"></div>
            </div>
            <div v-if="readequacao.idDocumento" class="col s12">
              Arquivo anexado: <a v-bind:href="'/readequacao/readequacoes/abrir-documento-readequacao?id=' + readequacao.idDocumento">
                            {{readequacao.nomeArquivo }}
              </a>
              <a
                v-if="!disabled"
                v-show="readequacao.idDocumento"
                v-on:click="excluirDocumento"
                title="Remover aquivo"
                class=" small waves-effect waves-light red-text lighten-2">
                <i class="material-icons">delete</i>
              </a>
            </div>
          </div>
        </div>
        <div v-if="!disabled" class="row">
          <div class="right-align padding20 col s12">
            <button
              v-on:click="salvarReadequacao"
              class="waves-effect waves-light btn btn-primary green">
              <i class="material-icons right">save</i>Salvar
            </button>
          </div>
        </div>
      </div>
			<template v-if="disabled && exibirInfo">
				<div class="card-content">
					<span class="card-title">Solicita&ccedil;&atilde;o de readequa&ccedil;&atilde;o</span>
					<p>
						{{readequacao.justificativa}}
					</p>
				</div>
				<div class="card-content" v-if="readequacao.idDocumento">
					<span class="card-title">Arquivo anexado</span>
					<a v-bind:href="'/readequacao/readequacoes/abrir-documento-readequacao?id=' + readequacao.idDocumento">
            {{readequacao.nomeArquivo }}
          </a>
				</div>
			</template>
		</div>
    `,
    data() {
        return {
            readequacao: {},
            exibirInfo: false,
            minCaracteresJustificativa: 10,
            arquivo: {
                tamanhoMaximo: 500000,
                tiposAceitos: ['pdf']
            }
        }
    },
    props: {
        idPronac: {
            type: [Number, String],
            default: '',
        },
        idTipoReadequacao: {
            type: [Number, String],
            default: '',
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        objReadequacao: {
            type: [Array, Object],
            default: () => {},
        },
        componenteDsSolicitacao: {
            type: String,
            default: '',
        },
    },
    mixins: [utils],
    watch: {
        objReadequacao: {
            handler(value) {
                this.readequacao = value;
            },
            deep: true,
        },
    },
    created() {
        if (_.isEmpty(this.objReadequacao)) {
            this.obterDadosReadequacao();
        } else {
            this.readequacao = this.objReadequacao;
        }
    },
    methods: {
        obterDadosReadequacao() {
            let self = this;
            $3.ajax({
                type: "GET",
                url: "/readequacao/readequacoes/obter-dados-readequacao",
                data: {
                    idTipoReadequacao: self.idTipoReadequacao,
                    idPronac: self.idPronac
                }
            }).done((response) => {
                if (response.readequacao != null) {
                    self.readequacao = response.readequacao;
                }
            });
        },
        salvarReadequacao() {
            if (this.readequacao.justificativa.length < this.minCaracteresJustificativa) {
                this.mensagemAlerta("\xC9 obrigat\xF3rio preencher a justificativa da readequa\xE7\xE3o. M\xEDnimo de " + this.minCaracteresJustificativa + " caracteres.");
                this.$refs.readequacaoJustificativa.focus();
                return;
            }

            this.$emit('eventoSalvarReadequacao', this.readequacao);
        },
        subirDocumento() {
            let arquivo = $('#arquivo')[0].files[0],
                self = this;

            if (!this.validarDocumento(arquivo)) {
                return;
            }
            var formData = new FormData();
            formData.append('arquivo', arquivo);
            formData.append('idPronac', self.idPronac);
            formData.append('idTipoReadequacao', self.idTipoReadequacao);
            formData.append('idDocumentoAtual', self.readequacao.idDocumento);
            if (self.readequacao.idReadequacao) {
                formData.append('idReadequacao', self.readequacao.idReadequacao);
            }

            $3('#carregando-arquivo').fadeIn('slow');
            $3.ajax(
                Object.assign(
                    {},
                    {
                        type: "POST",
                        url: "/readequacao/readequacoes/salvar-documento/idPronac/" + self.idPronac,
                        processData: false,
                        contentType: false,
                    },
                    {
                        data: formData,
                    }
                )
            ).done((response) => {
                self.readequacao.idDocumento = response.documento.idDocumento;
                self.readequacao.nomeArquivo = response.documento.nomeArquivo;
                self.readequacao.idReadequacao = response.readequacao.idReadequacao;
                self.$emit('eventoAtualizarReadequacao', self.readequacao);
                $3('#carregando-arquivo').fadeOut('slow');
            });
        },
        excluirDocumento() {
            $3('#carregando-arquivo').fadeIn('slow');

            let self = this;
            $3.ajax({
                type: "GET",
                url: "/readequacao/readequacoes/excluir-documento",
                data: {
                    idDocumento: self.readequacao.idDocumento,
                    idPronac: self.idPronac,
                    idReadequacao: self.readequacao.idReadequacao
                }
            }).done((response) => {
                self.mensagemSucesso("Documento excluido com sucesso.");
                self.readequacao.nomeArquivo = '';
                self.readequacao.idDocumento = '';
                self.$emit('eventoAtualizarReadequacao', self.readequacao);
                $3('#carregando-arquivo').fadeOut('slow');
            }).fail((response) => {
                self.mensagemAlerta(response.mensagem);
                $3('#carregando-arquivo').fadeOut('slow');
            });
        },
        validarDocumento(arquivo) {
            if (!this.arquivo.tiposAceitos.includes(arquivo.name.split(".").pop().toLowerCase())) {
                this.mensagemAlerta("Extens\xE3o de arquivo inv\xE1lida. Envie arquivos nos tipos: " + this.arquivo.tiposAceitos.join(','));
                return;
            }

            if (arquivo.size > this.arquivo.tamanhoMaximo) {
                this.mensagemAlerta("Arquivo ultrapassou o limite de " + this.arquivo.tamanhoMaximo);
                return;
            }
            return true;
        },
        atualizarDsSolicitacao(valor) {
            this.readequacao.dsSolicitacao = valor;
            this.$emit('eventoAtualizarReadequacao', this.readequacao);
        }
    }
});
