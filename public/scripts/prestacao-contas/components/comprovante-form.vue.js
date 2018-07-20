Vue.component('sl-comprovar-form',
{
    template: `
        <form>
            <fieldset>
                <legend>Identifica&ccedil;&atilde;o do Contratado</legend>
                <div class="row">
                    <div class=" col s8">
                        <p>Nacionalidade do Fornecedor</p>
                        <p>
                            <input
                                v-model="comprovante.fornecedor.eInternacional"
                                type="radio"
                                name="nacionalidade"
                                :value="false"
                                :id="'nacionalidade_1_' + comprovante.id"
                                v-on:click="forncedorNacional($event.target.value)"
                            />
                            <label 
                                :for="'nacionalidade_1_' + comprovante.id"
                            >Brasil</label>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class=" col s1">
                        <input
                            v-model="comprovante.fornecedor.eInternacional"
                            :value="true"
                            type="radio"
                            name="nacionalidade"
                            :id="'nacionalidade_2_' + comprovante.id"
                            v-on:click="fornecedorInternacional($event.target.value)"
                        />
                        <label
                            :for="'nacionalidade_2_' + comprovante.id"
                        >Outros</label>
                    </div>
                    <template v-if="(comprovante.fornecedor.eInternacional)">
                        <div class="col s4">
                            <label class="" for="pais">Nacionalidade do Fornecedor</label>
                            <select
                                v-model="comprovante.fornecedor.nacionalidade"
                                name="pais"
                                class=" browser-default "

                             >
                                <option v-for="p in pais" :value="p.id">
                                    {{p.nome}}
                                </option>
                            </select>
                        </div>
                    </template>
                </div>
            <template v-if="(comprovante.fornecedor.nacionalidade == 31)">
                <div class="row">
                    <div class=" col s6">
                        <p>Tipo do Fornecedor</p>
                        <p>
                            <input
                                v-model="comprovante.fornecedor.tipoPessoa"
                                type="radio"
                                name="tipoPessoa"
                                value="1"
                                id="tipoPessoa_1"
                                v-on:change="resetTipoPessoa"
                            />
                            <label for="tipoPessoa_1">CPF</label>
                            <input
                                v-model="comprovante.fornecedor.tipoPessoa"
                                type="radio"
                                name="tipoPessoa"
                                value="2"
                                id="tipoPessoa_2"
                                v-on:change="resetTipoPessoa"
                            />
                            <label for="tipoPessoa_2">CNPJ</label>
                        </p>
                    </div>
                </div>
            </template>
                <template v-if="(!comprovante.fornecedor.eInternacional)">
                    <div class="row">
                        <div
                            :class="[this.c.fornecedor.CNPJCPF.css, 'input-field col s6']" >
                            <input
                                type="text"
                                ref="CNPJCPF"
                                :value="comprovante.fornecedor.cnpjcpfMask"
                                v-on:keyup.enter="pesquisarFornecedor()"
                                v-on:input="inputCNPJCPF($event.target.value)"
                                :class="[this.c.fornecedor.CNPJCPF.css]"
                            />
                            <template v-if="comprovante.fornecedor.tipoPessoa == 1">
                            <label for="CNPJCPF"
                                :class="['active', this.c.fornecedor.CNPJCPF.css]"
                            >
                            CPF *</label>
                            </template>
                            <template v-else >
                                <label for="CNPJCPF"
                                    :class="[this.c.fornecedor.CNPJCPF.css]"
                                >CNPJ *</label>
                            </template>
                        </div>
                        <div class="input-field col s1">
                            <button v-on:click.prevent="pesquisarFornecedor()" class="btn">
                                <i class="material-icons">search</i>
                            </button>
                        </div>
                        <div class="input-field col s5">
                            <input
                                v-model="comprovante.fornecedor.nome"
                                type="text"
                                name="Descricao"
                                id="Descricao"
                                null="false"
                                value=""
                                disabled="true"
                            />
                            <template v-if="comprovante.fornecedor.tipoPessoa == 1">
                                <label>Nome</label>
                            </template>
                            <template v-else >
                                <label>Raz&atilde;o Social</label>
                            </template>
                        </div>
                    </div>
                </template>
            </fieldset>
            <template v-if="(!comprovante.fornecedor.eInternacional)">
                <fieldset>
                    <legend>Dados da Comprova&ccedil;&atilde;o de Pagamento</legend>
                    <div class="row">
                        <div class="col s4">
                            <label for="tpDocumento">Tipo Comprovante *</label>
                            <select name="tpDocumento" id="tpDocumento"
                                v-model="comprovante.tipo"
                                class="browser-default"
                            >
                                <option value="1">Cupom Fiscal</option>
                                <option value="2">Guia de Recolhimento</option>
                                <option value="3" selected="selected">Nota Fiscal/Fatura</option>
                                <option value="4">Recibo de Pagamento</option>
                                <option value="5">RPA</option>
                            </select>
                        </div>
                        <div class="input-field col s4">
                            <input type="text" name="nrComprovante"
                                   id="nrComprovante"
                                   maxlength="50" null="false"
                                   value=""
                                   v-model="comprovante.numero"
                                   :class="c.numero.css"
                                   ref="numero"
                                   v-on:input="inputNumero($event.target.value)"
                            />
                            <label
                                for="nrComprovante"
                                :class="c.numero.css"
                            >N&uacute;mero * </label>
                        </div>
                        <div class="input-field col s4">
                           <input type="text"
                                name="nrSerie"
                                id="nrSerie"
                                maxlength="8"
                                v-model="comprovante.serie"
                           />
                            <label>S&eacute;rie</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s3">
                            <input
                                placeholder="DD/MM/AAAA"
                                type="text"
                                name="dtEmissao"
                                id="dataEmissao"
                                v-model="comprovante.dataEmissao"
                                ref="dataEmissao"
                                :class="c.dataEmissao.css"
                                v-on:input="inputDataEmissao($event.target.value)"
                            />
                            <label
                                for="dataEmissao"
                                :class="c.dataEmissao.css"
                            >Dt. de Emiss&atilde;o do comprovante de despesa</label>
                        </div>
                        <div class="input-field col s3">
                            <input placeholder="DD/MM/AAAA" type="text"
                                name="dtPagamento" id="dtPagamento"
                                v-model="comprovante.dataPagamento"
                                ref="dataPagamento"
                                :class="c.dataPagamento.css"
                                v-on:input="inputDataPagamento($event.target.value)"
                            />
                            <label
                                :class="c.dataPagamento.css"
                            >Data do pagamento *</label>
                        </div>
                        <div class=" col s3">
                            <label>Forma de Pagamento<span style='color:red'>*</span></label>
                            <select class="browser-default" name="tpFormaDePagamento" id="tpFormaDePagamento"
                                    v-model="comprovante.forma"
                                    >
                                    <option value="1">Cheque</option>
                                    <option value="2">Transfer&ecirc;ncia Banc&aacute;ria</option>
                                    <option value="3">Saque/Dinheiro</option>
                            </select>
                        </div>
                        <div class="input-field col s3">
                            <input
                               type="text"
                               name="nrDocumentoDePagamento"
                               v-model="comprovante.numeroDocumento"
                               :class="c.numeroDocumento.css"
                               id="nrDocumentoDePagamento"
                               maxlength="10"
                               ref="numeroDocumento"
                               v-on:input="inputNumeroDocumento($event.target.value)"
                           />
                           <label for="nrDocumentoDePagamento"
                               :class="c.numeroDocumento.css"
                           >N&ordm; Documento Pagamento*</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s3">
                            <input type="text" name="vlComprovado" size="10" id="vlComprovado"
                                   v-model="comprovante.valor"
                                   :class="c.valor.css"
                                   v-on:input="inputValor($event.target.value)"
                                   ref="valor"
                           />
                           <label :class="c.valor.css">
                                   Valor do Item <span style='color:red'>*</span></label>
                        </div>
                        <div class="file-field input-field col s4">
                            <div :class="['btn small', c.arquivo.css] ">
                                <input name="arquivo" id="arquivo" ref="arquivo" type="file" @change="file">
                                <span>Arquivo *</span>
                            </div>
                            <div class="file-path-wrapper">
                                <input class="file-path validate" type="text"
                                       v-model="comprovante.arquivo.name"
                                    placeholder="Selecionar arquivo">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <textarea class="materialize-textarea" rows="5"
                                   v-model="comprovante.justificativa"
                                      name="dsJustificativa"
                                      id="dsJustificativa">
                                      </textarea>
                            <label>
                                Justificativa
                            </label>
                        </div>
                    </div>
                </fieldset>
            </template>
            <!-- Fonecedor Internacional -->
            <template v-else>
                <fieldset>
                    <legend>Dados da Comprova&ccedil;&atilde;o de Pagamento Internacional</legend>
                    <div class="row">
                        <div class="input-field col s6">
                            <input
                                v-model="comprovante.fornecedor.nome"
                                type="text"
                            />
                            <label for="nomeRazaoSocialInternacional">Nome da Empresa</label>
                        </div>
                        <div class="input-field col s6">
                            <input
                                v-model="comprovante.fornecedor.endereco"
                                type="text"
                            />
                            <label for="enderecoInternacional">Endere&ccedil;o</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="input-field col s5">
                            <input type="text" name="nif"
                                v-model="comprovante.numeroDocumento"
                            />
                            <label> N&uacute;mero do documento</label>
                        </div>
                        <div class="input-field col s4">
                            <input type="text" name="nrSerie"
                                id="nrSerie" maxlength="8"
                                    v-model="comprovante.serie"
                                   value=""/>
                            <label>S&eacute;rie</label>
                        </div>
                        <div class="col s3">
                            <p>
                                <label>Tipo de Documento</label>
                            </p>
                            <p>
                                <input type="radio" value="6"
                                    id="tipo_documento_invoice"
                                    v-model="comprovante.tipo"
                                />
                                <label for="tipo_documento_invoice">Invoice</label>
                            </p>
                            <p>
                                <input type="radio" value="7"
                                    id="tipo_documento_outros"
                                        v-model="comprovante.tipo" />
                                <label for="tipo_documento_outros">Outros</label>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s6">
                            <input
                                type="text"
                                name="dtEmissaoInternacional"
                                v-model="comprovante.dataEmissao"
                            />
                            <label for="dtEmissaoInternacional">Dt. do Documento</label>
                        </div>
                        <div class="input-field col s6">
                            <input
                                type="text"
                                id="dtPagamentoInternacional"
                                v-model="comprovante.dataPagamento"
                            />
                            <label for="dtPagamentoInternacional">Dt. do Pagamento</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s4">
                            <input type="text" name="vlComprovadoInternacional"
                                v-model="comprovante.valor"
                                :class="c.valor.css"
                            size="10" value=""/>
                            <label for="vlComprovadoInternacional">Valor do Item</label>
                        </div>
                        <div class="file-field input-field col s4"
                            :class="c.arquivo.css"
                        >
                            <div class="btn">
                                <span>Anexar comprovante</span>
                                <input name="arquivo" id="arquivo" type="file" @change="file">
                            </div>
                            <div
                                :class="[c.arquivo.css, 'file-path-wrapper']"
                            >
                                <input class="file-path validate" type="text">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <textarea id="dsJustificativaInternacionalId"
                                class="materialize-textarea" rows="5"
                                v-model="comprovante.justificativa"
                                name="dsJustificativaInternacional">
                            </textarea>
                            <label for="dsJustificativaInternacionalId">Justificativa</label>
                        </div>
                    </div>
                </fieldset>
            </template>
            <button type="button" class="btn" @click.prevent="salvar">salvar</button>
            <button type="button" class="btn white black-text" @click.prevent="cancelar()">cancelar</button>
        </form>
    `,
    mounted: function() {
        this.paises();

        this.comprovante.item = this.item;
        if (this.dados) {
            if (this.dados.idComprovantePagamento) {
                this.comprovante.id = this.dados.idComprovantePagamento;
            }

            this.comprovante.id = this.dados.idComprovantePagamento;
            this.comprovante.fornecedor.CNPJCPF = this.dados.CNPJCPF;
            this.pesquisarFornecedor();
            this.inputCNPJCPF(this.dados.CNPJCPF);
            this.comprovante.numero = this.dados.nrComprovante;
            this.comprovante.serie = this.dados.nrSerie;

            this.comprovante.dataEmissao = moment(this.dados.dtEmissao).format('DD/MM/YYYY');
            this.comprovante.dataPagamento = moment(this.dados.dtPagamento).format('DD/MM/YYYY');

            this.comprovante.valor = this.dados.vlComprovacao;
            this.comprovante.numeroDocumento = this.dados.nrDocumentoDePagamento;
            this.comprovante.arquivo = { name: this.dados.nmArquivo };
            this.comprovante.justificativa = this.dados.dsJustificativaProponente;
        }
    },
    props: ['dados', 'url', 'messages', 'tipoform', 'item'],
    data: function() {
        return this.data();
    },
    computed: {
    },
    methods: {
        salvar: function() {
            if (this.validar()) {
                var vue = this;
                var url = this.url;

                let formData = new FormData();

                if (this.comprovante.foiAtualizado) {
                    formData.append('arquivo', this.comprovante.arquivo);
                }

                formData.append('comprovante', JSON.stringify(this.comprovante));

                $3.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    contentType: 'multipart/form-data',
                    processData: false,
                    contentType: false,
               }).done(function(data){
                   Materialize.toast('Salvo com sucesso!', 4000, 'green');
                    if (vue.tipoform == 'cadastro') {
                       vue.$root.$emit('comprovante-novo', vue.comprovante);

                       vue.c = {
                           fornecedor: {
                               CNPJCPF: {
                                   css:'',
                               },
                           },
                           numero: {
                               css: ''
                           },
                           serie: '',
                           dataEmissao: {
                               css:'',
                           },
                           dataPagamento:{
                               css:'',
                           },
                           numeroDocumento: {
                               css:'',
                           } ,
                           valor: {
                               css:'',
                           },
                           arquivo: {
                               css: '',
                           },
                       }

                       vue.comprovante = {
                            fornecedor: {
                                nacionalidade: 31,
                                tipoPessoa: 1,
                                CNPJCPF: '',
                                cnpjcpfMask: '',
                                nome: '',
                                idAgente: '',
                                eInternacional: false,
                            },
                            item: vue.item,
                            tipo: 1,
                            numero: '',
                            serie: '',
                            dataEmissao: '',
                            dataPagamento:'',
                            forma: 1,
                            numeroDocumento: '',
                            valor: '',
                            arquivo: '',
                            justificativa: '',
                            foiAtualizado: false,
                        }
                    }

                    if (vue.tipoform == 'edicao'){
                        vue.$root.$emit('comprovante-atualizado', vue.comprovante);
                    }
                });
            }
        },
        validar: function() {
            if(this.comprovante.eInternacional ) {
            }

            if(!this.comprovante.fornecedor.CNPJCPF) {
                this.c.fornecedor.CNPJCPF.css = 'invalid red-text';
                this.$refs.CNPJCPF.focus();
                return false;
            }

            if(!this.comprovante.numero) {
                this.$refs.numero.focus();
                this.c.numero.css = 'active invalid red-text';
                return false;
            }
            if(!this.comprovante.dataEmissao) {
                this.$refs.dataEmissao.focus();
                this.c.dataEmissao.css = 'active invalid red-text';
                return false;
            }
            if(!this.comprovante.dataPagamento) {
                this.$refs.dataPagamento.focus();
                this.c.dataPagamento.css = 'active invalid red-text';
                return false;
            }
            if(!this.comprovante.numeroDocumento) {
                this.$refs.numeroDocumento.focus();
                this.c.numeroDocumento.css = 'active invalid red-text';
                return false;
            }

            if(!this.comprovante.valor) {
                this.$refs.valor.focus();
                this.c.valor.css = 'active invalid red-text';
                return false;
            }

            if(!this.comprovante.arquivo) {
                this.$refs.arquivo.focus();
                this.c.arquivo.css = 'active red';
                return false;
            }

            return true;
        },
        file: function() {
            this.comprovante.arquivo = this.$refs.arquivo.files[0];
            this.comprovante.foiAtualizado = true;
            this.c.arquivo.css = '';
        },
        pesquisarFornecedor: function(){
           var vue = this;
           var url = '/prestacao-contas/gerenciar/fornecedor' ;

           if (
               (this.comprovante.fornecedor.tipoPessoa == 1
                && this.comprovante.fornecedor.CNPJCPF.length == 11)
               || ( this.comprovante.fornecedor.tipoPessoa == 2
                    && this.comprovante.fornecedor.CNPJCPF.length == 14)
           ) {
               $3.ajax({
                    url: url,
                    method: 'POST',
                    data: {cnpjcpf: this.comprovante.fornecedor.CNPJCPF},
                    dataType: "json",
               }).done(function(data){
                    if (data.retorno){
                        vue.comprovante.fornecedor.nome= data.nome;
                        vue.comprovante.fornecedor.idAgente = data.idAgente;
                        vue.c.fornecedor.CNPJCPF.css = {};
                    }
               });
           }
        },
        paises: function(){
           var vue = this;
           var url = '/prestacao-contas/gerenciar/pais' ;

           $3.ajax({
                url: url,
                dataType: "json",
           }).done(function(data){
                if (data){
                    vue.pais = data;
                }
           });
        },
        inputCNPJCPF: function(e) {
            if (e.length < 15) {
                if (e.length == 11 || e.length == 14) {
                   this.comprovante.fornecedor.CNPJCPF = e;
                   this.comprovante.fornecedor.cnpjcpfMask = this.cnpjcpfMask();
                } else {
                   this.comprovante.fornecedor.CNPJCPF = '';
                }
            }
        },
        cnpjcpfMask: function() {
            if ( this.comprovante.fornecedor.tipoPessoa == 1
                && this.comprovante.fornecedor.CNPJCPF.length == 11) {
                return this.comprovante.fornecedor.CNPJCPF.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/,"$1.$2.$3-$4")
            } else if(this.comprovante.fornecedor.tipoPessoa == 2
                && this.comprovante.fornecedor.CNPJCPF.length == 14) {
                return this.comprovante.fornecedor.CNPJCPF.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/,"$1.$2.$3/$4-$5")
            }
        },
        forncedorNacional: function(e) {
            this.comprovante.fornecedor.nacionalidade = 31;
            this.comprovante.fornecedor.eInternacional = false;
        },
        fornecedorInternacional: function(e) {
            this.comprovante.fornecedor.nacionalidade = 0;
            this.comprovante.fornecedor.eInternacional = true;
        },
        resetTipoPessoa: function(e) {
            this.comprovante.fornecedor.nome = '';
            this.comprovante.fornecedor.CNPJCPF = '';
            this.comprovante.fornecedor.cnpjcpfMask = '';
        },
        inputNumero: function(e) {
            if (e.length > 0) {
               this.c.numero.css = {};
            }
        },
        inputDataEmissao: function(e) {
            if (e.length > 0) {
               this.c.dataEmissao.css = {};
            }
        },
        inputDataPagamento: function(e) {
            if (e.length > 0) {
               this.c.dataPagamento.css = {};
            }
        },
        inputNumeroDocumento(e) {
            if (e.length > 0) {
               this.c.numeroDocumento.css = {};
            }
        },
        inputValor(e) {
            if (e > 0) {
               this.c.valor.css = {};
            }
        },
        cancelar: function () {
            vue.$root.$emit('comprovante-atualizado');
        },
        data: function () {
            return {
                comprovante: {
                    fornecedor: {
                        nacionalidade: 31,
                        tipoPessoa: 1,
                        CNPJCPF: '',
                        cnpjcpfMask: '',
                        nome: '',
                        idAgente: '',
                        eInternacional: false,
                    },
                    item: this.item,
                    tipo: 1,
                    numero: '',
                    serie: '',
                    dataEmissao: '',
                    dataPagamento:'',
                    forma: 1,
                    numeroDocumento: '',
                    valor: '',
                    arquivo: '',
                    justificativa: '',
                    foiAtualizado: false,
                },
                pais: '',
                c: {
                    fornecedor: {
                        CNPJCPF: {
                            css:'',
                        },
                    },
                    numero: {
                        css: ''
                    },
                    serie: '',
                    dataEmissao: {
                        css:'',
                    },
                    dataPagamento:{
                        css:'',
                    },
                    numeroDocumento: {
                        css:'',
                    } ,
                    valor: {
                        css:'',
                    },
                    arquivo: {
                        css: '',
                    },
                }
            }
        }
    }
});
