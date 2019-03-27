Vue.component('planilha-orcamentaria-alterar-item', {
    template: `
<div>
	<table class="bordered">
		<thead>
			<tr>
				<th class="left-align">Produto</th>
				<th class="left-align">Etapa</th>
				<th class="left-align">Item</th>
				<th class="center-align">Vl. Comprovado</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="left-align">{{dadosPlanilhaAtiva.descProduto}}</td>
				<td class="left-align">{{dadosPlanilhaAtiva.descEtapa}}</td>
				<td class="left-align">{{dadosPlanilhaAtiva.descItem}}</td>
				<td class="right-align">{{valoresDoItem.vlComprovadoDoItem}}</td>
			</tr>
		</tbody>
	</table>

	<br/>
	<h6>Valores solicitados</h6>
	<table class="bordered">
		<thead>
			<tr>
				<th class="left-align">Unidade</th>
				<th class="center-align">Qtd</th>
				<th class="center-align">Ocorr&ecirc;ncia</th>
				<th class="center-align">Vl. Unit&aacute;rio</th>
				<th class="center-align">Dias</th>
				<th class="center-align">Total</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td id="vlSolUnidade" class="left-align">{{dadosPlanilhaAtiva.descUnidade}}</td>
				<td id="vlSolQtd" class="right-align">{{dadosPlanilhaAtiva.Quantidade}}</td>
				<td id="vlSolOcor" class="right-align">{{dadosPlanilhaAtiva.Ocorrencia}}</td>
				<td id="vlSolVlUnit" class="right-align">{{dadosPlanilhaAtiva.ValorUnitario}}</td>
				<td id="vlSolDias" class="right-align">{{dadosPlanilhaAtiva.QtdeDias}}</td>
				<td id="vlSolTotal" class="right-align">{{dadosPlanilhaAtiva.TotalSolicitado}}</td>
			</tr>
		</tbody>
	</table>

	<br/>
	<h6>Dados para a readequa&ccedil;&atilde;o</h6>
	<form class="col s12">
		<div class="row">
			<div class="col s3">
				<span>Unidade</span>
			  <select
					class="browser-default"
					ref="itemPlanilhaUnidade"
					v-model="dadosPlanilhaEditavel.idUnidade"
			    @change="atualizarUnidade"
					>
					<option v-for="unidade in unidades" v-bind:value="unidade.idUnidade">{{unidade.Descricao}}</option>
				</select>
			</div>	

			<div class="input-field col s2">
				<input placeholder="Qtd"
							 id="qtd"
							 type="text"
							 class="validate"
							 ref="itemQtd"
							 v-model="dadosPlanilhaEditavel.Quantidade">
				<label for="qtd">Qtd</label>
			</div>
			
			<div class="input-field col s2">
				<input placeholder="Ocorr\xEAncia"
							 id="ocorrencia"
							 type="text"
							 class="validate"
							 ref="itemOcorrencia"
							 v-model="dadosPlanilhaEditavel.Ocorrencia">
				<label for="ocorrencia">Ocorr&ecirc;ncia</label>
			</div>
			
			<div class="input-field col s2">
        <input-money
					ref="itemValorUnitario"
          v-bind:value="dadosPlanilhaEditavel.ValorUnitario"
					v-on:ev="dadosPlanilhaEditavel.ValorUnitario = $event">
        </input-money>
				<label for="vl_unitario">Vl. Unit&aacute;rio</label>
			</div>
			
			<div class="input-field col s1">
				<input placeholder="Dias"
							 id="dias"
							 type="text"
							 class="validate"
							 ref="itemDias"
							 v-model="dadosPlanilhaEditavel.QtdeDias">
				<label for="dias">Dias</label>
			</div>
			
			<div class="input-field col s2">
				<span>Total</span><br/>
				<span>R$ {{totalItemFormatado}}</span>
			</div>
			
		</div>
		<div class="row">
			<div class="input-field col s12">
				<textarea id="dsJustificativa"
									ref="itemJustificativa"
									v-model="dadosPlanilhaEditavel.Justificativa"
									class="materialize-textarea"></textarea>
				<label for="dsJustificativa">Justificativa</label>
			</div>
		</div>
		
		<div class="row">
		  <div class="right-align padding20 col s12">
				<a
					v-on:click="alterarItem"				
					title="Alterar item"
					class="waves-effect waves-light btn white-text">
					salvar
				</a>

				<a
					v-on:click="cancelar"				
					title="cancelar"
					class="waves-effect waves-light btn white-text">
					cancelar
				</a>				
				
			</div>
		</div>				
	</form>
</div>
	`,
    props: {
	idPronac: '',
	idPlanilhaAprovacao: '',
	item: {},
	unidades: {}
    },
    data: function() {
	return {
	    itemForm: Object.assign({}, this.item),
	    dadosPlanilhaAtiva: {
		Justificativa: '',
		Ocorrencia: '',
		QtdeDias: '',
		Quantidade: '',
		TotalSolicitado: '',
		ValorUnitario: '',
		descEtapa: '',
		descItem: '',
		descProduto: '',
		descUnidade: '',
		idEtapa: '',
		idPlanilhaAprovacao: '',
		idPlanilhaItem: '',
		idProduto: '',
		idUnidade: '',
	    },
	    dadosPlanilhaEditavel: {
		Justificativa: '',
		Ocorrencia: 0,
		QtdeDias: '',
		Quantidade: 0,
		TotalSolicitado: 0,
		ValorUnitario: '',
		descEtapa: '',
		descItem: '',
		descProduto: '',
		descUnidade: '',
		idAgente: '',
		idEtapa: '',
		idPlanilhaAprovacao: '',
		idPlanilhaItem: '',
		idProduto: '',
		idUnidade: '',
	    },
	    dadosProjeto: {
		IdPRONAC: '',
		NomePRojeto: '',
		PRONAC: ''
	    },
	    valoresDoItem: {
		vlComprovadoDoItem: '',
		vlComprovadoDoItemValidacao: ''
	    }
	}
    },
    mixins: [utils],
    methods: {
	obterDadosItem: function() {
	    let self = this;
	    $3.ajax({
		type: 'POST',
		url: '/readequacao/readequacoes/alterar-item-solicitacao',
		data: {
		    idPronac: self.idPronac,
		    idPlanilha: self.idPlanilhaAprovacao
		}
	    }).done(function(response) {
		self.dadosPlanilhaAtiva = response.dadosPlanilhaAtiva;
		self.dadosPlanilhaEditavel = response.dadosPlanilhaEditavel;
		self.dadosProjeto = response.dadosProjeto;
		self.valoresDoItem = response.valoresDoItem;
	    });
	},
	alterarItem: function() {
	    if (this.dadosPlanilhaEditavel.Quantidade == '') {
		this.mensagemAlerta("\xC9 obrigat\xF3rio informar a quantidade!");
		this.$refs.itemQtd.focus();
                return;		
	    }
	    if (this.dadosPlanilhaEditavel.Ocorrencia == '') {
		this.mensagemAlerta("\xC9 obrigat\xF3rio informar a ocorr&ecirc;ncia!");
		this.$refs.itemOcorrencia.focus();
                return;		
	    }
	    if (this.dadosPlanilhaEditavel.ValorUnitario == '') {
		this.mensagemAlerta("\xC9 obrigat\xF3rio informar o valor unit&aacute;rio!");
		this.$refs.itemValorUnitario.focus();
                return;		
	    }
	    if (this.dadosPlanilhaEditavel.Justificativa == '') {
		this.mensagemAlerta("\xC9 obrigat\xF3rio informar a justificativa!");
		this.$refs.itemJustificativa.focus();
                return;
	    }
	    
            if (this.totalItem < parseInt(this.valoresDoItem.vlComprovadoDoItem)){
		this.mensagemAlerta("O valor total do item n\xE3o pode ser menor do que o valor comprovado de " + this.valoresDoItem.vlComprovadoDoItem);
		return;
	    }
            
	    let self = this;	    
	    $3.ajax({
                type: 'POST',
                url: '/readequacao/readequacoes/salvar-avaliacao-do-item',
                data: {
		    idPronac: self.idPronac,
                    idPlanilha: self.idPlanilhaAprovacao,
                    Unidade: self.dadosPlanilhaEditavel.idUnidade,
                    Quantidade: self.dadosPlanilhaEditavel.Quantidade,
                    Ocorrencia: self.dadosPlanilhaEditavel.Ocorrencia,
                    ValorUnitario: self.dadosPlanilhaEditavel.ValorUnitario,
                    QtdeDias: self.dadosPlanilhaEditavel.QtdeDias,
                    Justificativa: self.dadosPlanilhaEditavel.Justificativa,
                    valorSolicitado: self.dadosPlanilhaAtiva.TotalSolicitado,
                    nrFonteRecurso: self.dadosPlanilhaAtiva.nrFonteRecurso
                }
	    }).done(function() {
		self.$emit('atualizarItem');
		self.$emit('atualizarSaldoEntrePlanilhas');
		self.$emit('fecharAlterar');
	    });
	},
	cancelar: function() {
	    this.resetData();
	    this.$emit('fecharAlterar');
	},
	atualizarUnidade: function(e){
	    this.dadosPlanilhaEditavel.descUnidade = this.unidades[e.target.options.selectedIndex].Descricao;
	},
	resetData: function() {
	    this.dadosPlanilhaAtiva = {
		Justificativa: '',
		Ocorrencia: '',
		QtdeDias: '',
		Quantidade: '',
		TotalSolicitado: '',
		ValorUnitario: '',
		descEtapa: '',
		descItem: '',
		descProduto: '',
		descUnidade: '',
		idEtapa: '',
		idPlanilhaAprovacao: '',
		idPlanilhaItem: '',
		idProduto: '',
		idUnidade: '',
	    };

	    this.dadosPlanilhaEditavel = {
		Justificativa: '',
		Ocorrencia: 0,
		QtdeDias: '',
		Quantidade: 0,
		TotalSolicitado: 0,
		ValorUnitario: '',
		descEtapa: '',
		descItem: '',
		descProduto: '',
		descUnidade: '',
		idAgente: '',
		idEtapa: '',
		idPlanilhaAprovacao: '',
		idPlanilhaItem: '',
		idProduto: '',
		idUnidade: '',
	    };

	    this.dadosProjeto = {
		    IdPRONAC: '',
		    NomePRojeto: '',
		    PRONAC: ''
	    };
	    
	    this.valoresDoItem = {
		vlComprovadoDoItem: '',
		vlComprovadoDoItemValidacao: ''
	    }
	}
    },
    watch: {
	idPlanilhaAprovacao: function() {
	    this.resetData();
	    if (this.idPlanilhaAprovacao != '') {
		this.obterDadosItem();
	    }
	}
    },
    computed: {
	totalItem: function() {
	    if (this.dadosPlanilhaEditavel.Ocorrencia > 0
		&& this.dadosPlanilhaEditavel.Quantidade > 0
		&& this.dadosPlanilhaEditavel.ValorUnitario != ''
	    ) {
		return this.dadosPlanilhaEditavel.Ocorrencia * this.dadosPlanilhaEditavel.Quantidade * numeral(this.dadosPlanilhaEditavel.ValorUnitario).value();
	    } else {
		return 0;
	    }
	},
	totalItemFormatado: function() {
	    return this.converterParaMoedaPontuado(this.totalItem);
	}
    }
});
