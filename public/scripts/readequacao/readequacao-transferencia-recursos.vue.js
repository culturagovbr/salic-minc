Vue.component('readequacao-transferencia-recursos', {
    template: `
<div class='readequacao-transferencia-recursos'>

            <div class="card">
                <div class="card-content">
		    <span class="card-title">Projeto transferidor</span>
			<div class="row">
                            <div class="col s2">
                            <b>Pronac: </b>{{ projetoTransferidor.pronac }}<br>
                        </div>
                        <div class="col s4">
                            <b>Projeto: </b><span v-html="projetoTransferidor.nome"></span>
                        </div>
                        <div class="col s2">
                            <b>&Aacute;rea: </b><span v-html="projetoTransferidor.area"></span>
                        </div>
                        <div class="col s2">
                            <b>Vl. a Comprovar: </b>{{ projetoTransferidor.valorComprovar }}
                        </div>
                        <div class="col s2">
                            <b>Saldo dispon&iacute;vel: </b>R$ {{ saldoDisponivel }}
                        </div>
                    </div>
		</div>
            </div>

  <ul class="collapsible">
      <li id="collapsible-first">
          <div class="collapsible-header active"><i class="material-icons">assignment</i>Solicita&ccedil;&atilde;o de readequa&ccedil;&atilde;o</div>
	      <readequacao-formulario
		  ref="formulario"
		  :id-pronac="projetoTransferidor.idPronac"
		  :id-tipo-readequacao="idTipoReadequacao"
		  :componente-ds-solicitacao='componente'
		  v-on:eventoSalvarReadequacao="salvarReadequacao">
	      </readequacao-formulario>
      </li>
      <li>
	  <div class="collapsible-header"><i class="material-icons">list</i>Projetos recebedores</div>
	  <div class="collapsible-body card" 
		 v-show="possuiReadequacaoCriada">
		<div class="card-content">
		    <span class="card-title">Projetos recebedores</span>
		    <table v-show="exibeProjetosRecebedores" class="animated fadeIn">
			<thead>
			    <th>Pronac</th>
			    <th>Nome do projeto</th>
			    <th>Vl. transfer&ecirc;ncia</th>
			</thead>
			<tbody>
			    <tr v-for="(projeto, index) in projetosRecebedores" class="animated fadeIn">
				<td>{{ projeto.idPronac }}</td>
				<td>{{ projeto.nome}}</td>
				<td colspan="2">R$ {{ projeto.valorRecebido}}</td>
				<td class="right">
				    <a href="javascript:void(0)"
				       v-on:click="excluirRecebedor(index)"
				       class="btn small">
					<i class="material-icons">delete</i>					
				    </a>
				</td>
			    </tr>
			</tbody>
			<tfoot>
			    <tr>
				<td colspan="2"></td>
				<td class="right">Total transferido: </td>
				<td>R$ {{ totalRecebido }} </td>
			    </tr>
			</tfoot>
		    </table>
		    		    
		<form class="row">
		    <div class="col s6">
			<div v-if="areasEspeciais">
			    <input type="text" v-model="projetoRecebedor.idPronac" />
            		</div>
			<div v-else>
			    <label>Projeto recebedor</label>
			    <select class="browser-default"
				    v-model="projetoRecebedor.idPronac"
				    ref="projetoRecebedorIdPronac"
                                    @change="updateRecebedor"
				    :disabled="!disponivelAdicionarRecebedor">
				<option v-for="(projeto, index) in projetosDisponiveis" v-bind:value="projeto.idPronac" v-bind:data-nome="projeto.nome">{{ projeto.pronac }} - {{ projeto.nome}}</option>
			    </select>
			</div>
			</div>
 			<div class="input-field col s3">
			    <input-money
				ref="projetoRecebedorValorRecebido"
                                v-on:ev="projetoRecebedor.valorRecebido = $event"
                                v-bind:value="projetoRecebedor.valorRecebido"
				:disabled="!disponivelAdicionarRecebedor">
			    </input-money>
			    <label for="valor_recebido">Valor recebido</label>
			</div>		
			<div class="center-align padding20 col s3">
			    <a href="javascript:void(0)"
			       v-on:click="incluirRecebedor"
			       :disabled="!disponivelAdicionarRecebedor"
			       class="btn">Adicionar recebedor</a>
			</div>
		    </form>
		</div>
	    </div>
      </li>
      <div class="card">
      	   <div class="right-align padding20 col s12">
	       <button
	            v-on:click="finalizarReadequacao"
		    :disabled="!disponivelFinalizar"
		    class="btn">Finalizar</button>
	   </div>
      </div>
  </ul>
</div>
	    `,
    props: [
	'idPronac',
	'idTipoReadequacao'
    ],
    mixins: [utils],
    data: function() {
	var readequacao = {
	    'idPronac': null,
	    'idReadequacao': null,
	    'justificativa': '',
	    'arquivo': null,
	    'idTipoReadequacao': null,
	    'dsSolicitacao': '',
	    'idArquivo' : null,
	    'nomeArquivo': null
	},
	    projetoTransferidor = function() {
		return defaultProjetoTransferidor();
	    },	    
	    projetoRecebedor = function() {
		return defaultProjetoRecebedor();
	    },
	    tiposTransferencia = [
		{
		    'id': 1,
		    'nome': 'N\xE3o homologados'
		},
		{
		    'id': 2,
		    'nome': 'Homologados'
		},
		{
		    'id': 3,
		    'nome': 'Recursos remanescentes'
		},
	    ];
	
	return {
	    projetoTransferidor,
	    readequacao,
	    projetoRecebedor,
	    tiposTransferencia,
	    projetosRecebedores: [],
	    projetosDisponiveis: [],
	    tiposAceitos: ['pdf'],
	    tamanhoMaximo: 5000000,
	    componente: 'readequacao-transferencia-recursos-tipo-transferencia'
	}	
    },
    created: function() {
	this.obterDadosProjetoTransferidor();
//		this.obterProjetosDisponiveis();
//	this.obterDadosReadequacao();
    },
    mounted: function() {
    },
    methods: {
	defaultProjetoRecebedor: function() {
	    return {
		nome: "",
		idPronac: "",
		valorRecebido: "0.00"
	    };
	},
	defaultProjetoTransferidor: function() {
	    return {
		pronac: null,
		idPronac: null,
		nome: '',
		area: '',
		valorComprovar: 0.00,
		saldoDisponivel: 0.00
	    }
	},	
	incluirRecebedor: function() {
	    if (this.projetoRecebedor.idPronac == '' ||
		this.projetoRecebedor.idPronac == undefined
	    ) {
		this.mensagemAlerta("\xC9 obrigat\xF3rio informar o projeto recebedor!");
		this.$refs.projetoRecebedorIdPronac.focus();
                return;		
	    }
	    if (this.projetoRecebedor.valorRecebido == '' ||
		this.projetoRecebedor.valorRecebido == undefined
	    ) {
		this.mensagemAlerta("\xC9 obrigat\xF3rio informar o valor a ser recebido pelo projeto!");
		this.$refs.projetoRecebedorValorRecebido.$refs.input.focus();		
                return;
	    }
	    
	    var valorRecebido = parseFloat(this.projetoRecebedor.valorRecebido.replace(",", "."));
	    valorRecebido = valorRecebido.toFixed(2);
	    this.projetoRecebedor.valorRecebido = valorRecebido.replace(".", ",");
	    
	    var somaTransferencia = parseFloat(this.projetoRecebedor.valorRecebido.replace(",", ".")) + parseFloat(this.totalRecebido);
	    somaTransferencia = somaTransferencia.toFixed(2);
	    var saldoDisponivel = parseFloat(this.projetoTransferidor.saldoDisponivel);
	    saldoDisponivel = saldoDisponivel.toFixed(2)
	    
	    if (parseFloat(somaTransferencia) > parseFloat(this.projetoTransferidor.saldoDisponivel)) {
		this.mensagemAlerta("Voc\xEA ultrapassou o saldo dispon\xEDvel para transfer\xEAncia, que \xE9 de R$ " + saldoDisponivel + "!");
		this.$refs.projetoRecebedorValorRecebido.$refs.input.focus();
                return;
	    }
	    var self = this;
	    $3.ajax({
		type: "POST",
		url: "/readequacao/transferencia-recursos/incluir-projeto-recebedor",
		data: {
		    idReadequacao: self.readequacao.idReadequacao,
		    dsSolicitacao: self.readequacao.dsSolicitacao,
		    idPronacTransferidor: self.projetoTransferidor.idPronac,
		    idPronacRecebedor: self.projetoRecebedor.idPronac,
		    valorRecebido: self.projetoRecebedor.valorRecebido
		}		
	    }).done(function(response) {
		self.$data.projetosRecebedores.push(
		    vue.projetoRecebedor
		);
		self.projetoRecebedor = vue.defaultProjetoRecebedor();
		self.obterProjetosDisponiveis();
	    });
	},
	excluirRecebedor: function(id) {
	    Vue.delete(this.projetosRecebedores, id);
	    // TODO remover dado ajax
	},
	salvarReadequacao: function(readequacao) {
	    /* if (readequacao.dsSolicitacao == '' ||
	       readequacao.dsSolicitacao == undefined
	       ) {
	       this.mensagemAlerta("\xC9 obrigat\xF3rio informar o tipo da transfer\xEAncia!");
	       this.$refs.readequacaoTipoTransferencia.focus();
	     *     return;		
	       }*/
	    
	    if (readequacao.justificativa.length == 0) {
		this.mensagemAlerta("\xC9 obrigat\xF3rio preencher a justificativa da readequa\xE7\xE3o!");
		this.$refs.formulario.$refs.readequacaoJustificativa.focus();
		
		return;
	    }

	    let vue = this;
            $3.ajax({
                type: "POST",
                url: "/readequacao/transferencia-recursos/salvar-readequacao",
		data: {
		    idPronac: readequacao.idPronac,
		    idReadequacao: readequacao.idReadequacao,
		    justificativa: readequacao.justificativa,
		    dsSolicitacao: readequacao.dsSolicitacao
		}
            }).done(function (response) {
		var arquivo = $('#arquivo')[0].files[0];

		var callback = function() {
		    $3('.collapsible').collapsible('close', 0);
		    $3('.collapsible').collapsible('open', 1);
		    vue.mensagemSucesso('Readequa\xE7\xE3o gravada com sucesso!');
		}
		
		if (typeof arquivo != 'undefined') {
		    vue.subirArquivo(
			arquivo,
			response,
			callback
		    );
		} else {
		    callback();
		}
            });
	},
	validarArquivo: function(arquivo) {
	    if (!this.tiposAceitos.includes(arquivo.name.split(".").pop().toLowerCase())) {
		this.mensagemAlerta("Extens\xE3o de arquivo inv\xE1lida. Envie arquivos nos tipos: " + this.tiposAceitos.join(','));
		return;
	    }
	    
	    if (arquivo.size > this.tamanhoMaximo) {
		this.mensagemAlerta("Arquivo ultrapassou o limite de " + this.tamanhoMaximo);
		return;
	    }
	    
	    return true;
	},
	subirArquivo: function(arquivo, response, callback) {
	    let self = this;
	    self.readequacao = response.readequacao;

	    if (!this.validarArquivo(arquivo)) {
		return;
	    }
	    	    
	    var formData = new FormData();
	    formData.append('arquivo', arquivo);
	    
	    $3.ajax(
		Object.assign(
		    {},
		    {
			type: "POST",
			url: "/readequacao/readequacoes/upload-anexo/idreadequacao/" + self.readequacao.idReadequacao,
			processData: false, 
			contentType: false, 			
		    },
		    {
			data: formData
		    }
		)
	    ).done(function(response) {
		self.readequacao.idDocumento = response.readequacao.idDocumento;		
		self.readequacao.nomeArquivo = response.readequacao.nomeArquivo;
		callback();
	    });
	},	
	finalizarReadequacao: function() {
	},
	obterDadosProjetoTransferidor: function() {	    
	    let self = this;
	    
	    this.projetoTransferidor = {
		pronac: 164783,
		idPronac: 166121,
		nome: 'Expedição gastronimica',
		area: '7',
		valorComprovar: 5234.00,
		saldoDisponivel: 5234.00
	    }

	    $3.ajax({
                type: "GET",
                url: "/readequacao/transferencia-recursos/dados-projeto-transferidor",
		data: {
		    idPronac: self.idPronac
		}
            }).done(function (response) {
                self.projetoTransferidor = response.projeto;
            });
	},
	obterProjetosDisponiveis: function(idPronac) {
	    // TODO: buscar projetos disponíveis do mesmo proponente ajax
	    var self = this;
	    $3.ajax({
		type: "GET",
		url: "/readequacao/transferencia-recursos/listar-projetos-recebedores-disponiveis",
		data: {
		    idPronac: this.idPronac
		}
	    }).done(function(response) {
		self.projetosDisponiveis = response.projetos;
	    });
	    
	    /*
	       this.projetosDisponiveis = [
	       {
	       'idPronac': '201112',
	       'pronac': '167531',
	       'nome': 'Projeto xyz'
	       },
	       {
	       'idPronac': '261712',
	       'pronac': '167512',
	       'nome': 'Outro projeto'
	       },
	       {
	       'idPronac': '261114',
	       'pronac': '157511',
	       'nome': 'Mais um projeto'
	       }
	       ];
	     */
	},
	obterProjetosRecebedores: function(idReadequacao) {
	    let vue = this;
            $3.ajax({
                type: "GET",
                url: "/readequacao/transferencia-recursos/listar-projetos-recebedores",
		data: {
		    idReadequacao: self.readequacao.idReadequacao
		}
            }).done(function (response) {
                console.log(response);
            });
	},
	obterDadosReadequacao: function(idPronac) {
	    let vue = this;
            $3.ajax({
                type: "GET",
                url: "/readequacao/transferencia-recursos/dados-readequacao",
		data: {
		    idTipoReadequacao: 23, // TODO 
		    idPronac: self.idPronac
		}
            }).done(function (response) {
                self.readequacao = response.readequacao;  // TODO: substituir vue por self
		self.obterProjetosRecebedores();
            });
	},
	updateRecebedor: function(e) {
	    if(e.target.options.selectedIndex > -1) {
		this.projetoRecebedor.nome = e.target.options[e.target.options.selectedIndex].dataset.nome;
            }
	}
    },
    computed: {
	areasEspeciais: function() {    
	    var areasMultiplos = [];
	    areasMultiplos[7] = 'Patrimônio cultural';
	    areasMultiplos[9] = 'Museus e memória';
	    
	    if (areasMultiplos.hasOwnProperty(this.projetoTransferidor.area)) {
		return true;
	    } else {
		return;
	    }
	},	
	totalRecebido: function() {
	    return this.projetosRecebedores.reduce(function (total, projeto) {
                var resultado = parseFloat(total) + parseFloat(projeto.valorRecebido);
		return resultado.toFixed(2);
            }, 0);
	},
	saldoDisponivel: function() {
	    var saldo = parseFloat(this.projetoTransferidor.valorComprovar) - parseFloat(this.totalRecebido);
	    return saldo.toFixed(2);
	},
	possuiReadequacaoCriada: function() {
	    if (this.readequacao.idReadequacao != null) {
		return true;
	    } else {
		return;
	    }
	},
	disponivelAdicionarRecebedores: function() {

	    // parei aqui - estou criando cada caso para a interface

	    // prop diferente: busca de pronac 
	    // prop igual: select 
	    
	    
	    if (!this.areaMultiplosProjeto &&
		this.projetosRecebedores.length == 0) {
		return true;
	    } else if (this.areaMultiplosProjeto &&
		       this.projetosRecebedores.length > 0) {
		return true;		   
	    } else {
		return false;
	    }
	},
	disponivelFinalizar: function() {
	    if (this.projetosRecebedores.length > 0 &&
		this.totalRecebido <= this.projetoTransferidor.saldoDisponivel
	    ) {
		return true;
	    } else {
		return false;
	    }
	},
	disponivelAdicionarRecebedor: function() {
	    if (this.totalRecebido == this.projetoTransferidor.saldoDisponivel) {
		return false;
	    } else {
		if (!this.areaMultiplosProjeto &&
		    this.projetosRecebedores.length == 0) {
		    return true;
		} else if (this.areaMultiplosProjeto &&
			   this.projetosRecebedores.length > 0) {
		    return true;		   
		}
	    }
	},
	exibeProjetosRecebedores: function() {
	    if (this.projetosRecebedores.length > 0) {
		return true;
	    } else {
		return false;
	    }
	}
    }
})
