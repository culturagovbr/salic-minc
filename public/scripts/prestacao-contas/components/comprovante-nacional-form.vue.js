Vue.component('sl-comprovante-nacional-form',
{
    template: `
        <form>
            <fieldset>
                <legend>Identifica&ccedil;&atilde;o do Contratado</legend>
                <div class="row">
                    <div class=" col s6">
                        <p>Tipo do Fornecedor</p>
                        <p>
                            <input
                                v-model="comprovante.fornecedor.tipoPessoa"
                                type="radio"
                                name="tipoPessoa"
                                value="1"
                                :id="'tipoPessoa_1_' + _uid"
                                v-on:change="resetTipoPessoa"
                            />
                            <label :for="'tipoPessoa_1_' + _uid">CPF</label>
                            <input
                                v-model="comprovante.fornecedor.tipoPessoa"
                                type="radio"
                                name="tipoPessoa"
                                value="2"
                                :id="'tipoPessoa_2' + _uid"
                                v-on:change="resetTipoPessoa"
                            />
                            <label :for="'tipoPessoa_2' + _uid">CNPJ</label>
                        </p>
                    </div>
                </div>
                    <div class="row">
                        <div
                            :class="[this.c.fornecedor.CNPJCPF.css, 'input-field col s6']"
                        >
                            <input
                                type="text"
                                ref="CNPJCPF"
                                v-on:keyup.enter="pesquisarFornecedor()"
                                :class="[this.c.fornecedor.CNPJCPF.css]"
                                v-mask="maskCNPJCPF"
                                v-model="comprovante.fornecedor.cnpjcpfMask"
                                v-on:input="inputCNPJCPF($event.target.value)"
                            />
                            <template v-if="comprovante.fornecedor.tipoPessoa == 1">
                                <label for="CNPJCPF"
                                    :class="[this.c.fornecedor.CNPJCPF.css]"
                                >
                                    CPF *
                                </label>
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
                    <div class="row" v-show="novoFornecedor">
                        <div class="col s12">
                            <a
                                target="blank"
                                :href="'/prestacao-contas/fornecedor/index/cpfcnpj/'+comprovante.fornecedor.CNPJCPF"
                                class="btn red">
                                Cadastrar Fornecedor
                            </a>
                        </div>
                    </div>
            </fieldset>
            <template v-if="(!comprovante.fornecedor.eInternacional)">
                <fieldset>
                    <legend>Dados do Comprovante de Despesa</legend>
                    <div class="row">
                        <div class="col s2">
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
                        <div class="input-field col s2">
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
                            >Data de Emiss&atilde;o</label>
                        </div>
                        <div class="input-field col s1">
                            <i
                                class="material-icons
                                tooltipped"
                                data-position="bottom"
                                data-delay="50"
                                :data-tooltip="'Inicio em: ' + dataInicio + ' at\xe9 ' + dataFim"
                            >
                                help
                            </i>
                        </div>
                        <div class="input-field col s2">
                            <input
                               type="text"
                               name="nrComprovante"
                               id="nrComprovante"
                               maxlength="50"
                               placeholder="00000000"
                               value=""
                               v-model="comprovante.numero"
                               :class="c.numero.css"
                               ref="numero"
                               v-on:input="inputNumero($event.target.value)"
                            />
                            <label
                                for="nrComprovante"
                                :class="[c.numero.css]"
                            >N&uacute;mero * </label>
                        </div>
                        <div class="input-field col s2">
                           <input type="text"
                                name="nrSerie"
                                id="nrSerie"
                                maxlength="8"
                                v-model="comprovante.serie"
                                placeholder="00000000000"
                           />
                            <label for="nrSerie">S&eacute;rie</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="file-field input-field col s4">
                            <div :class="['btn small', c.arquivo.css] ">
                                <input name="arquivo" id="arquivo" ref="arquivo" type="file" @change="file">
                                <span>Comprovante *</span>
                            </div>
                            <div class="file-path-wrapper">
                                <input class="file-path validate" type="text"
                                       v-model="comprovante.arquivo.nome"
                                    placeholder="Selecionar arquivo">
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Dados do Comprovante Bancario</legend>
                    <div class="row">
                        <div class=" col s3 m3">
                            <label>Forma de Pagamento<span style='color:red'>*</span></label>
                            <select
                                class="browser-default"
                                name="tpFormaDePagamento"
                                id="tpFormaDePagamento"
                                v-model="comprovante.forma"
                            >
                                <option value="1">Cheque</option>
                                <option value="2">Transfer&ecirc;ncia Banc&aacute;ria</option>
                                <option value="3">Saque/Dinheiro</option>
                            </select>
                        </div>
                        <div class="input-field col s3">
                            <input
                                placeholder="DD/MM/AAAA"
                                type="text"
                                name="dtPagamento"
                                id="dtPagamento"
                                v-model="comprovante.dataPagamento"
                                ref="dataPagamento"
                                :class="c.dataPagamento.css"
                                v-on:input="inputDataPagamento($event.target.value)"
                            />
                            <label
                                :class="c.dataPagamento.css"
                            >Data do pagamento *</label>
                        </div>
                        <div class="input-field col s2">
                            <input
                               type="text"
                               name="nrDocumentoDePagamento"
                               v-model="comprovante.numeroDocumento"
                               :class="c.numeroDocumento.css"
                               placeholder="00000000000"
                               id="nrDocumentoDePagamento"
                               maxlength="10"
                               ref="numeroDocumento"
                               v-on:input="inputNumeroDocumento($event.target.value)"
                           />
                           <label for="nrDocumentoDePagamento"
                               :class="[c.numeroDocumento.css]"
                           >N&ordm; Documento Pagamento*</label>
                        </div>
                        <div class="input-field col s4">
                            <input
                               type="text"
                               ref="valor"
                               name="vlComprovado"
                               size="10"
                               id="vlComprovado"
                               v-model="comprovante.valor"
                               :class="c.valor.css"
                               v-money="money"
                            />
                            <label :class="c.valor.css">
                               Valor (atual: {{valorantigo}})(max: {{(valorMaxItem)}})<span style='color:red'>*</span></label>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Justificativa</legend>
                    <div class="row">
                        <div class="input-field col s12">
                            <textarea class="materialize-textarea" rows="5"
                                   v-model="comprovante.justificativa"
                                      name="dsJustificativa"
                                      id="dsJustificativa">
                            </textarea>
                        </div>
                    </div>
                </fieldset>
            </template>
            <button type="button" class="btn" @click="salvar">salvar</button>
            <button
                type="button" class="btn white black-text"
                @click="cancelar()">cancelar</button>
        </form>
    `,
    mounted: function() {
        this.comprovante.item = this.item;
        // this.comprovante.idPlanilhaAprovacao = this.idPlanilhaAprovacao;
        if (this.dados) {
            if (this.dados.idComprovantePagamento) {
                this.comprovante.id = this.dados.idComprovantePagamento;
                this.comprovante.idComprovantePagamento = this.dados.idComprovantePagamento;
            }

            this.comprovante.id = this.dados.idComprovantePagamento;

            if (this.dados.fornecedor.CNPJCPF.length == 11) {
                this.comprovante.fornecedor.tipoPessoa = 1;
            } else {
                this.comprovante.fornecedor.tipoPessoa = 2;
            }

            this.comprovante.fornecedor.CNPJCPF = this.dados.fornecedor.CNPJCPF;
            this.pesquisarFornecedor();

            this.inputCNPJCPF(this.dados.fornecedor.CNPJCPF);
            this.comprovante.tipo = parseInt(this.dados.tipo);
            this.comprovante.numero = this.dados.numero;
            this.comprovante.serie = this.dados.serie;

            this.comprovante.dataEmissao = moment(this.dados.dataEmissao).format('DD/MM/YYYY');
            this.comprovante.dataPagamento = moment(this.dados.dataPagamento).format('DD/MM/YYYY');

            this.comprovante.valor = numeral(parseFloat(this.dados.valor)).format('0,0.00');

            this.comprovante.numeroDocumento = this.dados.numeroDocumento;
            this.comprovante.arquivo = { nome: this.dados.arquivo.nome };
            this.comprovante.justificativa = this.dados.justificativa;
        }
        $3('textarea').trigger('autoresize');
    },
    updated(){
        $3('textarea').trigger('autoresize');
    },
    props: {
        dados: null,
        url:null,
        messages:null,
        tipoform:null,
        item:null,
        idplanilhaaprovacao:null,
        index:null,
        datainicio:null,
        datafim:null,
        valoraprovado: Number,
        valorcomprovado: Number,
        valorantigo: Number,
        status:null
    },
    computed:{
        dataInicio() {
            return moment(this.datainicio).format('DD/MM/YYYY');
        },
        dataFim() {
            return moment(this.datafim).format('DD/MM/YYYY');
        },
        valorMaxItem: function() {
            let valorAprovado = numeral(parseFloat(this.valoraprovado)).value();
            let valorComprovado = numeral(parseFloat(this.valorcomprovado)).value();
            let valorAntigo = numeral(this.valorantigo ? parseFloat(this.valorantigo) : 0.0).value();
            let value = numeral(valorAprovado - (valorComprovado - valorAntigo)).format('0,0.00');

            return value;
        },
        valorAntigo() {
            return numeral(parseFloat(this.valorantigo)).format('0,0.00');
        }
    },
    data() {
        return {
            maskCNPJCPF: '###.###.###-##',
            money:{
             decimal: ',',
                thousands: '.',
                precision: 2,
            },
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
                arquivo: {
                    nome: '',
                    file: ''
                },
                item: this.item,
                idPlanilhaAprovacao: this.idplanilhaaprovacao,
                tipo: 1,
                numero: '',
                serie: '',
                dataEmissao: '',
                dataPagamento:'',
                forma: 1,
                numeroDocumento: '',
                valor: 0,
                valorAntigo: this.valorantigo,
                valorPermitido: (parseFloat(this.valoraprovado) - parseFloat(this.valorcomprovado)),
                justificativa: '',
                foiAtualizado: false,
                _index: this.index,
                status: this.status
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
            },
            random: '',
            novoFornecedor: false
        }
    },
    methods: {
        salvar: function() {
            if (this.validar()) {
                var vue = this;
                var url = this.url;

                let formData = new FormData();

                if (this.comprovante.foiAtualizado) {
                    formData.append('arquivo', this.comprovante.arquivo.file);
                }

                // let c = JSON.parse(JSON.stringify(this.comprovante))
                let c = Object.assign({}, this.comprovante);
                c.valor = numeral(c.valor).value();
                formData.append('comprovante', JSON.stringify(c));

                $3.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    contentType: 'multipart/form-data',
                    processData: false,
                    contentType: false,
               }).done(function(data) {
                    Materialize.toast('Salvo com sucesso!', 4000, 'green');
                    $3('#modal1').modal('close');

                    if (vue.tipoform == 'cadastro') {

                       c._index = data.idComprovantePagamento;
                       c.idComprovantePagamento = data.idComprovantePagamento;

                       c.dataEmissao = (moment(c.dataEmissao, 'DD/MM/YYYY').format('YYYY-MM-DD'));
                       c.dataPagamento = (moment(c.dataPagamento, 'DD/MM/YYYY').format('YYYY-MM-DD'));
                       c.valor = numeral(c.valor).value();

                       vue.$root.$emit('novo-comprovante-nacional', c);

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
                            idPlanilhaAprovacao: vue.idplanilhaaprovacao,
                            item: vue.item,
                            tipo: 1,
                            numero: '',
                            serie: '',
                            dataEmissao: '',
                            dataPagamento:'',
                            forma: 1,
                            numeroDocumento: '',
                            valor: '',
                            arquivo: {
                                nome:'',
                                file: ''
                            },
                            justificativa: '',
                            foiAtualizado: false,
                        }
                    }

                    if (vue.tipoform == 'edicao'){
                        vue.comprovante.dataEmissao = (moment(c.dataEmissao, 'DD/MM/YYYY').format('YYYY-MM-DD'));
                        vue.comprovante.dataPagamento = (moment(c.dataPagamento, 'DD/MM/YYYY').format('YYYY-MM-DD'));
                        vue.comprovante.valor = numeral(c.valor).value();

                        vue.$root.$emit('atualizado-comprovante-nacional', vue.comprovante);
                    }
                });
            }
        },
        validar: function() {
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

            if(!moment(this.comprovante.dataEmissao, 'D/M/YYYY').isValid()) {
                this.$refs.dataEmissao.focus();
                this.c.dataEmissao.css = 'active invalid red-text';
                return false;
            }

            if(!this.comprovante.dataEmissao) {
                this.$refs.dataEmissao.focus();
                this.c.dataEmissao.css = 'active invalid red-text';
                return false;
            }

            if(
                moment(this.comprovante.dataEmissao,'D/M/YYYY' ) < moment(this.datainicio)
                || moment(this.comprovante.dataEmissao) > moment(this.datafim)
            ) {
                this.$refs.dataEmissao.focus();
                this.c.dataEmissao.css = 'active invalid red-text';
                return false;
            }

            if(!this.comprovante.dataPagamento) {
                this.$refs.dataPagamento.focus();
                this.c.dataPagamento.css = 'active invalid red-text';
                return false;
            }

            if(!moment(this.comprovante.dataPagamento, 'D/M/YYYY').isValid()) {
                this.$refs.dataPagamento.focus();
                this.c.dataPagamento.css = 'active invalid red-text';
                return false;
            }

            if(!this.comprovante.numeroDocumento) {
                this.$refs.numeroDocumento.focus();
                this.c.numeroDocumento.css = 'active invalid red-text';
                return false;
            }

            if(!this.validarValor()) {
               return false;
            }


            if(!this.comprovante.arquivo.file && this.tipoform == 'cadastro') {
                this.$refs.arquivo.focus();
                this.c.arquivo.css = 'active red';
                return false;
            }

            return true;
        },
        validarValor: function() {
            let result = true;
            let valor = numeral(this.comprovante.valor);
            let valorAntigo = this.valorantigo ? parseFloat(this.valorantigo) : 0;
            let valorcomprovado = parseFloat(this.valorcomprovado);
            let valorComprovadoAtual = (valorcomprovado - valorAntigo) + (valor.value());
            let valoraprovado = numeral(parseFloat(this.valoraprovado));
            let valorPermitido = parseFloat(this.valoraprovado) - parseFloat(this.valorcomprovado);

            if(this.comprovante.valor == '') {
                this.$refs.valor.focus();
                this.c.valor.css = 'active invalid red-text';

                return false;
            }

            if(numeral(this.comprovante.valor).value() == 0 ) {
                this.$refs.valor.focus();
                this.c.valor.css = 'active invalid red-text';

                return false;
            }

            if(valorComprovadoAtual > valoraprovado.value()) {
                this.$refs.valor.focus();
                this.c.valor.css = 'active invalid red-text';
                alert(
                    'Valor acima do permitido:' + this.comprovante.valor
                    + ', maximo a ser acrescentado e: ' + valorPermitido
                );

                return false;
            }
            return result;
        },
        file: function() {
            this.comprovante.arquivo.file = this.$refs.arquivo.files[0];
            this.comprovante.arquivo.nome = this.comprovante.arquivo.file.name;
            this.comprovante.foiAtualizado = true;
            this.c.arquivo.css = '';
        },
        pesquisarFornecedor: function(){
           var vue = this;
           var url = '/agente/agentes/agentecadastrado';
// console.log(this.comprovante.fornecedor.CNPJCPF.length == 14);
           if (
               (this.comprovante.fornecedor.tipoPessoa == 1
                && this.comprovante.fornecedor.CNPJCPF.length == 11)
               || ( this.comprovante.fornecedor.tipoPessoa == 2
                    && this.comprovante.fornecedor.CNPJCPF.length == 14)
           ) {
               $3.ajax({
                    url: url,
                    method: 'POST',
                    data: {cpf: this.comprovante.fornecedor.CNPJCPF},
                    dataType: "json",
               }).done(function(data){
                   vue.comprovante.fornecedor.nome = '';
                   if (data.length > 0 && data[0]['msgCPF'] == 'cadastrado') {
                        vue.comprovante.fornecedor.nome= data[0]['agente']['nome'];
                        vue.comprovante.fornecedor.idAgente = data[0]['idAgente'];
                        vue.c.fornecedor.CNPJCPF.css = {};
                        vue.novoFornecedor = false;
                    } else {
                        alert('Fornecedor n\xe3o cadastrado! Cadastre antes esse fornecedor.');
                        vue.novoFornecedor = true;
                    }
               });
           }
        },
        paises: function() {
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
            // console.log(e.replace(/[.-]/g,""));
            this.comprovante.fornecedor.CNPJCPF = e.replace(/[.\-\/]/g,"");
            // if (e.length < 15) {
            //     if (e.length == 11 || e.length == 14) {
            //        this.comprovante.fornecedor.CNPJCPF = e.replace(/[.-]/g,"");
            //        this.comprovante.fornecedor.cnpjcpfMask = this.cnpjcpfMask();
            //     }
            // }
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
            this.paises();
            this.comprovante.fornecedor.nacionalidade = 0;
            this.comprovante.fornecedor.eInternacional = true;
        },
        resetTipoPessoa: function(e) {
            this.comprovante.fornecedor.nome = '';
            this.comprovante.fornecedor.CNPJCPF = '';
            this.comprovante.fornecedor.cnpjcpfMask = '';

            if (this.comprovante.fornecedor.tipoPessoa == 1 ) {
                this.maskCNPJCPF = '###.###.###-##';
            } else {
                this.maskCNPJCPF = '##.###.###/####-##';
            }
        },
        inputNumero: function(e) {
            if (e.length > 0) {
               this.c.numero.css = {};
            }
        },
        inputDataEmissao: function (e) {
            if (e.length > 0) {
               this.c.dataEmissao.css = {};
            }

            if ( e.length == 8 ) {
               this.comprovante.dataEmissao = this.comprovante.dataEmissao.replace(/(\d{2})(\d{2})(\d{4})/,"$1/$2/$3")
            }
        },
        inputDataPagamento: function(e) {
            if (e.length > 0) {
               this.c.dataPagamento.css = {};
            }

            if ( e.length == 8 ) {
               this.comprovante.dataPagamento = this.comprovante.dataPagamento.replace(/(\d{2})(\d{2})(\d{4})/,"$1/$2/$3")
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
            $3('#modal1').modal('close');

            if (this.tipoform == 'edicao') {
                this.$root.$emit('cancelar-comprovante-nacional', this.comprovante);
            }
        },
    }
});
