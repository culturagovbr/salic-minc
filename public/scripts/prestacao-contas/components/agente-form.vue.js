Vue.component('agente-form', {
    template: `
    <form id="formulario" >
        <h4>Novo Fornecedor</h4>
        <div v-if="css == true " class="erro"> <h6>Os campos com '*' são obrigatorios!</h6></div>
        <fieldset>
            <legend>DADOS PRINCIPAIS</legend>
            <div class="row">
                <label>
                    <input class="with-gap" type="radio" id="cpf" value="0" v-model="TipoPessoa"/>
                    <label for="cpf">CPF</label>
                    <input class="with-gap" type="radio" id="cnpj" value="1" v-model="TipoPessoa">
                    <label for="cnpj">CNPJ</label>
                </label>
            </div>
            <div class="row">
                <div class="input-field col s3">
                    <input
                        id="first_name"
                        :class="[Erro.CpfCnpj ? 'erro': '']"
                        type="text"
                        ref="CpfCnpj"
                        @input="inputValidacao($event.target.value)"
                        @blur="inputCNPJCPF($event.target.value)"
                        v-model="cnpjcpfMask"
                    >
                    <label :class="[Erro.CpfCnpj ? 'erro': '']" for="first_name">
                        CPF/CNPJ *
                    </label>
                </div>
                <div class="input-field col s6">
                    <input :class="[Erro.Nome ? 'erro': '']" @input="inputValidacao($event.target.value)" id="last_name" type="text" class="validate" v-model="Nome">
                    <label :class="[Erro.Nome ? 'erro': '']" for="last_name">Nome *</label>
                </div>
            </div>
        </fieldset>
        <fieldset>
            <legend>NOVO ENDERÇO</legend>
            <div class="row">
                <div class="input-field col s3">
                    <input
                        :class="[Erro.Cep ? 'erro': '']"
                        type="text"
                        name="cep"
                        id="cep"
                        class="validate"
                        :value="CEPmask"
                        maxlength="8"
                        @keyup="cepMask(CEP)"
                        @blur="buscarcep(CEPmask)"
                        @input="inputCEP($event.target.value)"
                    >
                    <span id="erroCep" class="spanError"></span>
                    <label :class="[Erro.Cep ? 'erro': '']" for="first_name">CEP *</label>
                </div>
                <div class="col s3">
                    <label :class="[Erro.Tipo ? 'erro': '']">Tipo *</label>
                    <select :class="[Erro.Tipo ? 'erro': '']"" class="browser-default" v-model="Tipo" :disabled="disabled.Tipo" ref="tipo">
                        <option
                            v-for="logradouro in TiposLogradouros"
                            :value="logradouro.id"
                        >
                            {{logradouro.descricao}}
                        </option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input
                        :disabled="disabled.Logradouro"
                        :class="[Erro.Logradouro ? 'erro': '']"
                        @input="inputValidacao($event.target.value)"
                        type="text"
                        id="logradouro"
                        name="logradouro"
                            class="validate" v-model.lazy="Logradouro" ref="logradouro"
                    >
                    <label :class="[Erro.Logradouro ? 'erro': '']" for="last_name">Logradouro *</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s6">
                    <input :class="[Erro.Numero ? 'erro': '']" @input="inputValidacao($event.target.value)" type="text"
                    id="last_name" class="validate" v-model="Numero">
                    <label :class="[Erro.Numero ? 'erro': '']" for="last_name">Número *</label>
                </div>
                <div class="input-field col s6">
                    <input type="text" id="last_name" class="validate" v-model="Complemento">
                    <label for="last_name">Complemento</label>
                </div>
            </div>
            <div class="row">
                <div class="col s3">
                    <label :class="[Erro.Uf ? 'erro': '']">UF *</label>
                    <select :class="[Erro.Uf ? 'erro': '']"
                        class="browser-default"
                        v-on:change="combo()"
                        v-model="uf"
                        ref="uf"
                        :disabled="disabled.Uf"
                    >
                        <option v-for="uf in UFs" :value="uf.id">{{uf.descricao}}</option>
                    </select>
                </div>
                <div class="col s3">
                    <label :class="[Erro.Cidade ? 'erro': '']">Cidade *</label>
                    <select
                        :disabled="disabled.Cidade"
                        :class="[Erro.Cidade ? 'erro': '']"
                        class="browser-default"
                        v-model="Cidade"
                        ref="cidade"
                    >
                        <option v-for="cidade in Cidades" :value="cidade.id">{{cidade.descricao}}</option>
                    </select>
                </div>
                <div class="input-field col s4">
                    <input
                        :class="[Erro.Bairro ? 'erro': '']"
                        @input="inputValidacao($event.target.value)"
                        type="text" id="last_name"
                        class="validate"
                        :disabled="disabled.Bairro"
                        v-model="Bairro"
                        ref="bairro">
                    <label :class="[Erro.Bairro ? 'erro': '']" for="last_name">Bairro *</label>
                </div>
            </div>
            <div class="row">
                <div class="col s3">
                    <label :class="[Erro.TipoEndereco ? 'erro': '']">Tipo de Endereço *</label>
                    <select :class="[Erro.TipoEndereco ? 'erro': '']" class="browser-default" v-model="TipoDeEndereco">
                        <option v-for="(endereco, index) in TiposEnderecos" :value="endereco.id" >{{endereco.descricao}}</option>
                    </select>
                </div>
                <div class="col s3">
                    <label>Autoriza Divulgar?</label>
                    <div class="align-bottom">
                        <input class="with-gap" type="radio" id="sim" value="1" v-model="Autorizar" />
                        <label for="sim">Sim</label>
                        <input class="with-gap" type="radio" id="nao" value="0" v-model="Autorizar"/>
                        <label for="nao">Não</label>
                    </div>
                </div>
            </div>
        </fieldset>
           <div class="row center-align">
        <div class="col s4 offset-s4">
            <button class="btn" type="button" @click="salvar()">Salvar</button>
        </div>
    </div>
    </form>
    ` ,
    data () {
       return {
           TipoPessoa: "0",
           CpfCnpj: '' ,
           cnpjcpfMask: '',
           Nome: "",
           Fornecedor: "248",
           CEP: "",
           CEPmask: "",
           Tipo: "",
           Logradouro: "",
           Numero: "",
           Complemento: "",
           Cidade: "",
           Bairro: "",
           TipoDeEndereco: "",
           Autorizar: "0",
           uf: "",
           UFs: "",
           Cidades: "",
           Fornecedores: "",
           TiposEnderecos: "",
           TiposLogradouros: "",
           visao: 248,
           css: false,
           Erro: {
               CpfCnpj: false,
               Nome: false,
               Cep: false,
               Tipo: false,
               Logradouro: false,
               Numero: false,
               Uf: false,
               Cidade: false,
               Bairro: false,
               TipoEndereco: false,
           },
           disabled: {
               Logradouro: false,
               Bairro: false,
               Uf: false,
               Cidade: false,
           }
       }
    },
    props:['cpfcnpj'],
    mounted: function () {
        this.carregarInfoFornecedores();
        this.inputCNPJCPF(this.cpfcnpj); 

        if (this.cpfcnpj.length == 11) {
           this.TipoPessoa = "0";
        } else if(this.cpfcnpj.length == 14) {
           this.TipoPessoa = "1";
        }

    },
    methods:
    {
        combo: function () {
             let self = this;
             let url = '/prestacao-contas/fornecedor/cidade/id/'+self.uf;
             $3.ajax({
                 type: "GET",
                 url: url,
            })
             .done( (cidade) => self.Cidades = cidade );
        },
        carregarInfoFornecedores: function () {
            let self = this;

            let url = ['/prestacao-contas/fornecedor/uf',
                '/prestacao-contas/fornecedor/fornecedores-tipo',
                '/prestacao-contas/fornecedor/logradouro-tipo',
                '/prestacao-contas/fornecedor/endereco-tipo'];

            $3.ajax({
                type: "GET",
                url: url[0],
            })
                .done((data) => self.UFs = data)
                .fail((jqXHR) => alert('error'));

            $3.ajax({
                type: "GET",
                url: url[1],
            })
                .done((fornecedor) => self.Fornecedores = fornecedor)
                .fail((jqXHR) => alert('error'));

            $3.ajax({
                type: "GET" ,
                url: url[2]
            })
                .done((logradouro) => self.TiposLogradouros = logradouro)
                .fail((jqXHR) => alert('error'));

            $3.ajax({
                type: "GET" ,
                url: url[3]
            })
                .done((endereco) => self.TiposEnderecos = endereco)
                .fail((jqXHR) => alert('error'));
        },
        cepMask: function() {
            return this.CEP.replace(/(\d{2})(\d{3})(\d{2})/,"$1.$2-$3")
        },
        inputCEP: function(e) {
                if (e.length == 8) {
                    this.CEP = e;
                    this.CEPmask = this.cepMask();
                } else {
                    this.CEP = '';
                }
        },
        buscarcep(cep) {
            let self = this;

            $3.ajax({
                url: "/default/cep/cep-ajax",
                data: {
                    cep: cep
                }
            }).done(function (result) {

                if (result.status === true) {

                    self.Logradouro = result.logradouro.trim();
                    self.UFs.forEach( uf => {
                        if (uf.descricao === result.uf){
                            self.uf = uf.id;
                            self.Cidade = result.idCidade;
                            self.combo();
                            self.Bairro = result.bairro;
                        }
                    });

                    let tipoLogradouro =  Object.keys(self.TiposLogradouros).map(key => self.TiposLogradouros[key]);
                    tipoLogradouro.forEach( tipo => {
                        if (tipo.descricao === result.tipoLogradouro){
                            self.Tipo = tipo.id;
                            self.Erro.Cep = false
                        }
                    })

                    if(self.Tipo != '' || self.Tipo != undefined){
                        self.Erro.Tipo = false;
                    }

                    if(self.Logradouro){
                        self.Erro.Logradouro = false
                        self.disabled.Logradouro = true;
                    } else {
                        self.disabled.Logradouro = false;
                    }

                    if(self.Bairro.trim()){
                        self.disabled.Bairro = true;
                        self.Erro.Bairro = false
                    } else {
                        self.disabled.Logradouro = false;
                    }

                    if(self.uf) {
                        self.disabled.Uf = true;
                        self.Erro.Uf = false
                    }else {
                        self.disabled.Uf = false;
                    }

                    if(self.Cidade){
                        self.Erro.Cidade = false
                        self.disabled.Cidade = true;
                    }else {
                        self.disabled.Cidade = false;
                    }

                } else {
                   self.Tipo= "";
                   self.Logradouro= "";
                   self.Numero= "";
                   self.Complemento= "";
                   self.Cidade= "";
                   self.Bairro= "";
                   self.TipoDeEndereco= "";
                   self.uf= "";
                   self.Cidades= "";

                   self.disabled.Uf = false;
                   self.disabled.Cidade = false;
                   self.disabled.Bairro = false;
                   self.disabled.Logradouro = false;
                }
            });
        },
        salvar: function(e) {
            let vue = this;

            // let dados = this.$data;

            let dados = JSON.parse(JSON.stringify(this.$data))

            dados.cpf = dados.CpfCnpj;
            dados.Tipo = dados.TipoPessoa;
            dados.tipoEndereco = dados.TipoDeEndereco;
            dados.tipoLogradouro = dados.Tipo;
            dados.logradouro = dados.Logradouro;
            dados.divulgarEndereco = dados.Autorizar;
            dados.nome = dados.Nome;
            dados.UFs = '';
            dados.Cidades = '';

            if(this.validarForm()) {
                $3.ajax({
                     type: 'POST',
                     url: '/agente/agentes/salvaagentegeral',
                    data:dados
                })
                .done(function(data) {
                    alert('Fornecedor cadastrado, retorne a tela anterior e pesquise novamente o CPF/CNPJ ou cadastre outro fornecedor.');

                       vue.TipoPessoa = "0";
                       vue.CpfCnpj= "";
                       vue.Nome= "";
                       vue.Fornecedor= "248";
                       vue.CEP= "";
                       vue.CEPmask= "";
                       vue.Tipo= "";
                       vue.Logradouro= "";
                       vue.Numero= "";
                       vue.Complemento= "";
                       vue.Cidade= "";
                       vue.Bairro= "";
                       vue.TipoDeEndereco= "";
                       vue.Autorizar= "0";
                       vue.uf= "";
                       vue.Cidades= "";
                       vue.Fornecedores= "";
                       vue.visao= 248;
                       vue.css= false;
                       vue.Erro= {
                           CpfCnpj: false,
                           Nome: false,
                           Cep: false,
                           Tipo: false,
                           Logradouro: false,
                           Numero: false,
                           Uf: false,
                           Cidade: false,
                           Bairro: false,
                           TipoEndereco: false,
                       }
                })
            }
        },
        validarForm: function() {
            let vue = this;
            let valido = true;

            if(!this.CpfCnpj){
               this.css = this.Erro.CpfCnpj = true;
               return false;
            }

            $3.ajax({
                type: 'POST',
                url: '/agente/agentes/agentecadastrado',
                data: {'cpf': vue.CpfCnpj},
                async: false
            })
            .done(function(data) {
                if(data[0].msgCPF == 'cadastrado') {
                    alert('Esse CPF/CNPJ j\xE1 est\xE1 cadastrado.');
                    valido = false;
                    vue.CpfCnpj = '';
                }
            });

            if(!valido){
                return false;
            }

            if(!this.Nome){
                this.css = this.Erro.Nome = true;
               return false;
            }
            if(!this.CEP){
               this.css = this.Erro.Cep = true;
               return false;
            }
            if(!this.Tipo){
                this.css = this.Erro.Tipo = true;
               return false;
            }
            if(!this.Logradouro){
                this.css = this.Erro.Logradouro = true;
               return false;
            }
            if(!this.Numero){
                this.css = this.Erro.Numero = true;
               return false;
            }
            if(!this.uf){
               this.css = this.Erro.Uf = true;
               return false;
            }
            if(!this.Cidade){
                this.css = this.Erro.Cidade = true;
               return false;
            }
            if(!this.Bairro){
                this.css = this.Erro.Bairro = true;
               return false;
            }
            if(!this.TipoDeEndereco){
                this.css = this.Erro.TipoEndereco = true;
               return false;
            }
            return true;
        },
        inputValidacao: function(event){
           if(event.length > 0) {
               this.css = false
               this.Erro.CpfCnpj = false;
               this.Erro.Nome = false;
               this.Erro.Cep = false;
               this.Erro.Tipo = false;
               this.Erro.Logradouro = false;
               this.Erro.Numero = false;
               this.Erro.Uf = false;
               this.Erro.Cidade = false;
               this.Erro.Bairro = false;
               this.Erro.TipoEndereco = false;
           }
        },
        cnpjcpfFormat (e) {
            if (e.length == 11) {
                return e.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/,"$1.$2.$3-$4");
            } else if(e.length == 14) {
                return e.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/,"$1.$2.$3/$4-$5");
            }
        },
        inputCNPJCPF(e) {
            let vue = this;
            e = e.replace(/[.-]/g, '');

            if (e.length < 15) {
                if (e.length == 11 || e.length == 14) {
                   this.CpfCnpj = e;
                   this.cnpjcpfMask = this.cnpjcpfFormat(e);

                    $3.ajax({
                        type: 'POST',
                        url: '/agente/agentes/agentecadastrado',
                        data: { 'cpf': vue.CpfCnpj },
                        async: false
                    })
                    .done(function(data) {
                        if(data[0]['msgCPF'] == 'novo') {
                           vue.Nome = data[0]['Nome'];
                           vue.inputCEP(data[0]['Cep']);
                        }

                        if(data[0]['msgCPF'] == 'invalido') {
                            alert('CPF/CNPJ invalido');
                        }

                        if(data[0]['msgCPF'] == 'cadastrado') {
                            alert('Esse CPF/CNPJ j\xE1 est\xE1 cadastrado.');
                        }
                    });

                } 
            }
        },
    }
});
