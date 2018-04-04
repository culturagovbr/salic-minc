(function (global, factory) {
    if (typeof define === 'function' && define.amd) {
        define(['../numeral'], factory);
    } else if (typeof module === 'object' && module.exports) {
        factory(require('../numeral'));
    } else {
        factory(global.numeral);
    }
}(this, function (numeral) {
    numeral.register('locale', 'pt-br', {
        delimiters: {
            thousands: '.',
            decimal: ','
        },
        abbreviations: {
            thousand: 'mil',
            million: 'milhões',
            billion: 'b',
            trillion: 't'
        },
        ordinal: function (number) {
            return 'º';
        },
        currency: {
            symbol: 'R$'
        }
    });
}));

// switch between locales
numeral.locale('pt-br');
numeral.defaultFormat('0,0.00');

// register
Vue.component('select-percent', {
    template: '<select @change="valorSelecionado($event.target.value)" ref="combo" tabindex="-1"><option v-for="item in items">{{ item }}%</option></select>',
    props: {
        disabled: {
            type: Boolean,
            default: false
        },
        maximoCombo: {}
    },
    data:function(){
        return {
            retorno: 1
        }
    },
    computed: {
        items: function() {
            var total = [];
            for ( var i = this.maximoCombo ; i >= 0; i--){
                total.push(parseInt(i));
            }
            return total;
        }
    },
    watch: {
        disabled: function (){
            this.$refs.combo.disabled = this.disabled;
            if (this.disabled){
                this.value= 0;
            }
        }
    },
    methods: {
        valorSelecionado: function(value) {
            this.retorno = value;
            this.$emit('evento', parseInt(this.retorno))
        }
    },
    mounted: function () {
        this.$refs.combo.disabled = this.disabled;
    }
});

// register
Vue.component('input-money', {
    template: '<div>\
                <input\
                    class="right-align"\
                    v-bind:disabled="false"\
                    v-bind:value="value"\
                    ref="input"\
                    v-on:input="updateMoney($event.target.value)"\
                    v-on:blur="formatValue">\
                </div>',
    props: {
        value: {
          default: 0
        },
        disabled: {
          type: Boolean,
          default: false
        }
    },
    data:function(){
        return {
           val:1
        }
    },
    mounted: function () {
        this.formatValue();
        this.$refs.input.disabled = this.disabled;
    },
    methods:{
        formatValue: function () {
          this.$refs.input.value = numeral(this.$refs.input.value).format();
        },
        updateMoney: function(value) {
            // console.log(value);
            this.val = value;
            this.$emit('ev', this.val)
        }
    },
    watch: {
        disabled: function (){
            this.$refs.input.disabled = this.disabled;
            if (this.disabled){
                this.value= 0;
            }
        }
    }
});

// register
Vue.component('my-component', {
  template: '#app-6',
    data: function() {
       return {
            produto:{ }, // produto sendo manipulado
            produtos:  [], // lista de produtos
            active : false,
            visualizarFormulario: false,
            icon : 'add',
            "tpVenda" : 'i',
            "distribuicaoGratuita" : 'n',
            "tpLocal" : 'a',
            "tpEspaco" : 'n',
            "dsProduto": '', // Categoria
            "qtExemplares": 0, // Quantidade de exemplar / Ingresso
            "qtGratuitaDivulgacao" : 0,
            "qtGratuitaPatrocinador": 0,
            "qtGratuitaPopulacao": 0,
            "vlUnitarioPopularIntegral": 0.0, // Preço popular: Preço Unitario do Ingresso
            "qtPrecoPopularValorIntegral" : 0, //Preço Popular: Quantidade de Inteira
            "qtPrecoPopularValorParcial": 0,//Preço Popular: Quantidade de meia entrada
            "vlUnitarioProponenteIntegral": 0,
            "qtPopularIntegral": 0,
            "qtPopularParcial": 0,
            "percentualGratuitoPadrao" : 0.3,
            "percentualGratuito" : 0.3,
            "percentualPrecoPopularPadrao" : 0.2,
            "percentualPrecoPopular" : 0.2,
            "percentualProponentePadrao" : 0.5,
            "percentualProponente" : 0.5,
            "labelInteira" : 'Inteira',
            activeForm : false
        }
    },
    props:['idpreprojeto','idplanodistribuicao', 'idmunicipioibge', 'iduf', 'disabled', 'canalaberto'],
    computed:{
        // Limite: preço popular: Quantidade de Inteira
        qtPrecoPopularValorIntegralLimite: function() {

            var percentualPopularIntegral = 0.6;

            if(this.tpVenda == 'e') {
                percentualPopularIntegral = 1;
            }

            return parseInt((this.qtExemplares * this.percentualPrecoPopular) * percentualPopularIntegral);
        },
        // Limite: preço popular: Quantidade de meia entrada 40% de 50%
        qtPrecoPopularValorParcialLimite: function () {

            var percentualPopularParcial = 0.4;

            if(this.tpVenda == "e") {
                percentualPopularParcial = 0
            }
            return parseInt((this.qtExemplares * this.percentualPrecoPopular) * percentualPopularParcial);
        },
        qtGratuitaPopulacaoMinimo : function() {
            return  parseInt(( parseInt(this.qtExemplares)  * this.percentualGratuito - (parseInt(this.qtGratuitaPatrocinador) + parseInt(this.qtGratuitaDivulgacao))));
        },
        quantidadePopularIntegral: function() {
            if (this.distribuicaoGratuita == 'n') {

                var percentualPopularIntegral = 0.6;

                if(this.tpVenda == "e") {
                    percentualPopularIntegral = 1;
                }

                return parseInt((this.qtExemplares * this.percentualPrecoPopular) * percentualPopularIntegral) ;

            }
            return 0;
        },
        quantidadePopularParcial: function() {
            if (this.distribuicaoGratuita == 'n') {

                var percentualPopularParcial = 0.4;

                if(this.tpVenda == "e") {
                    percentualPopularParcial = 0;
                }

                return parseInt((this.qtExemplares * this.percentualPrecoPopular) * percentualPopularParcial) ;

            }
            return 0;
        },
        /*verificar esse calculo com mais cuidado*/
        qtProponenteIntegral: function() {
            if (this.distribuicaoGratuita == 'n') {

                this.percentualPrecoPopular = this.percentualMaximoPrecoPopular;

                var percentualProponenteIntegral = 0.5;

                if(this.tpVenda == 'e') {
                    percentualProponenteIntegral = 1
                }

                return parseInt((this.qtExemplares * this.percentualProponente) * percentualProponenteIntegral) ;
            }
            return 0;
        },
        qtProponenteParcial: function() {
            if (this.distribuicaoGratuita == 'n') {

                var percentualProponenteParcial = 0.5;

                if(this.tpVenda == 'e') {
                    percentualProponenteParcial = 0
                }

                return parseInt((this.qtExemplares * this.percentualProponente) * percentualProponenteParcial) ;
            }
            return 0;
        },
        percentualMaximoDistribuicaoGratuita: function() {
            return this.percentualGratuitoPadrao + (this.percentualMaximoPrecoPopular - this.percentualPrecoPopular);
        },
        percentualMaximoPrecoPopular: function() {
            if (this.distribuicaoGratuita == 'n') {
                return this.percentualPrecoPopularPadrao + (this.percentualProponentePadrao - this.percentualProponente);
            }
            return 0;
        },
        percentualPrecoPopularSoma: function(val)  {
            this.qtPopularIntegral = this.quantidadePopularIntegral;
            this.qtPopularParcial = this.quantidadePopularParcial;
            this.percentualGratuito = this.percentualGratuitoPadrao + (this.percentualPrecoPopularPadrao - this.percentualPrecoPopular);

            return this.percentualPrecoPopular;
        },
        //Preço Popular: Valor da inteira
        vlReceitaPopularIntegral: function() {
            if (this.distribuicaoGratuita == 'n') {
                return numeral(parseInt(this.qtPopularIntegral) * this.converterParaMoedaAmericana(this.vlUnitarioPopularIntegral)).format();
            }
            return 0;

        },
        vlReceitaPopularParcial: function() {
            return numeral(this.qtPopularParcial * this.converterParaMoedaAmericana(this.vlUnitarioPopularIntegral) * 0.5).format();
        },
        vlReceitaProponenteIntegral: function() {
            if (this.distribuicaoGratuita == 'n') {
                return numeral(this.converterParaMoedaAmericana(this.vlUnitarioProponenteIntegral) * parseInt(this.qtProponenteIntegral)).format();
            }
            return 0;
        },
        vlReceitaProponenteParcial: function(){
            if (this.distribuicaoGratuita == 'n'){
                return numeral( ( this.converterParaMoedaAmericana(this.vlUnitarioProponenteIntegral) * 0.5 ) * this.qtProponenteParcial).format();
            }
            return 0;
        },
        vlReceitaPrevista: function() {

            var soma = numeral();

            soma.add(this.converterParaMoedaAmericana(this.vlReceitaPopularIntegral));
            soma.add(this.converterParaMoedaAmericana(this.vlReceitaPopularParcial));
            soma.add(this.converterParaMoedaAmericana(this.vlReceitaProponenteIntegral));
            soma.add(this.converterParaMoedaAmericana(this.vlReceitaProponenteParcial));

            return numeral(soma).format();
        },
        // Total de exemplares
        qtExemplaresTotal: function() {
            total = 0 ;
            for ( var i = 0 ; i < this.produtos.length; i++){
                total += parseInt(this.produtos[i]['qtExemplares']);
            }
            return total;
        },
        // Total de divulgação gratuita.
        qtGratuitaDivulgacaoTotal: function(){
            total = 0 ;
            for ( var i = 0 ; i < this.produtos.length ; i++){
                total += parseInt(this.produtos[i]['qtGratuitaDivulgacao']);
            }
            return total;
        },
        // Total de divulgação Patrocinador
        qtGratuitaPatrocinadorTotal: function() {
            total = 0 ;
            for ( var i = 0 ; i < this.produtos.length ; i++) {
                total += parseInt(this.produtos[i]['qtGratuitaPatrocinador']);
            }
            return total;
        },
        // Total de divulgação gratuita.
        qtGratuitaPopulacaoTotal: function() {
            total = 0 ;
            for ( var i = 0 ; i < this.produtos.length ; i++) {
                total += parseInt(this.produtos[i]['qtGratuitaPopulacao']);
            }
            return total;
        },
        //Preço Popular: Quantidade de Inteira
        qtPopularIntegralTotal: function() {
            total = 0 ;
            for ( var i = 0 ; i < this.produtos.length ; i++){
                total += parseInt(this.produtos[i]['qtPopularIntegral']);
            }
            return total;
        },
        //Preço Popular: Quantidade de meia entrada
        qtPopularParcialTotal:function() {
            total = 0 ;
            for ( var i = 0 ; i < this.produtos.length ; i++){
                total += parseInt(this.produtos[i]['qtPopularParcial']);
            }
            return total;
        },
        vlReceitaPopularIntegralTotal :function() {
            total = 0 ;
            for ( var i = 0 ; i < this.produtos.length ; i++){
                var vl = (this.produtos[i]['vlReceitaPopularIntegral']);
                total += numeral(vl).value();
            }
            return numeral(total).format();
        },
        vlReceitaPopularParcialTotal: function() {
            total = 0 ;
            for ( var i = 0 ; i < this.produtos.length ; i++) {
                var vl = (this.produtos[i]['vlReceitaPopularParcial']);
                total += numeral(vl).value();
            }
            return numeral(total).format();
        },
        qtProponenteIntegralTotal:function(){
            total = 0 ;
            for ( var i = 0 ; i < this.produtos.length ; i++){
                total += parseInt(this.produtos[i]['qtProponenteIntegral']);
            }
            return total;
        },
        qtProponenteParcialTotal:function(){
            total = 0 ;
            for ( var i = 0 ; i < this.produtos.length ; i++){
                total += parseInt(this.produtos[i]['qtProponenteParcial']);
            }
            return total;
        },
        vlReceitaProponenteIntegralTotal:function(){
            total = 0 ;
            for ( var i = 0 ; i < this.produtos.length ; i++){
                vl = (this.produtos[i]['vlReceitaProponenteIntegral']);
                total += this.converterParaMoedaAmericana(vl);
            }
            return numeral(total).format();
        },
        vlReceitaProponenteParcialTotal: function() {
            total = 0 ;
            for ( var i = 0 ; i < this.produtos.length ; i++){
                var vl = (this.produtos[i]['vlReceitaProponenteParcial']);
                total += this.converterParaMoedaAmericana(vl);
            }
            return numeral(total).format();
        },
        receitaPrevistaTotal: function() {
            var total = numeral();

            for ( var i = 0 ; i < this.produtos.length ; i++){
                var vl = this.produtos[i]['vlReceitaPrevista'];
                total.add(parseFloat(vl));
            }
            return total.format();
        },
        valorMedioProponente: function() {
            var vlReceitaProponenteIntegral = numeral();
            var vlReceitaProponenteParcial = numeral();
            var qtProponenteIntegral = numeral();
            var qtProponenteParcial = numeral();

            for ( var i = 0 ; i < this.produtos.length ; i++){
                vlReceitaProponenteIntegral.add(this.produtos[i]['vlReceitaProponenteIntegral']);
                vlReceitaProponenteParcial.add(parseFloat(this.produtos[i]['vlReceitaProponenteParcial']))  ;
                qtProponenteIntegral.add(parseFloat(this.produtos[i]['qtProponenteIntegral']));
                qtProponenteParcial.add(parseFloat(this.produtos[i]['qtProponenteParcial']));
            }

            var media = numeral(parseFloat(vlReceitaProponenteIntegral.value() + vlReceitaProponenteParcial.value()) / (qtProponenteIntegral.value() +qtProponenteParcial.value()));

            return media;
        },
        valorMedioProponenteFormatado: function() {
            return this.valorMedioProponente.format();
        }
    },
    watch:{
        //Quantidade de exemplar / Ingresso
        qtExemplares: function(val)  {
            if (this.distribuicaoGratuita == 'n'){
                this.qtGratuitaDivulgacao = parseInt(this.qtExemplares * 0.1);
                this.qtGratuitaPatrocinador = parseInt(this.qtExemplares * 0.1);
                this.percentualGratuito = this.percentualMaximoDistribuicaoGratuita;
                this.qtGratuitaPopulacao = this.qtGratuitaPopulacaoMinimo;
                this.percentualPrecoPopular = this.percentualMaximoPrecoPopular;
                this.qtPopularIntegral = this.quantidadePopularIntegral;
                this.qtPopularParcial = this.quantidadePopularParcial;
            } else {
                this.qtGratuitaPopulacao = this.qtExemplares;
            }
        },
        percentualPrecoPopular: function(val) {
            this.percentualGratuito = this.percentualMaximoDistribuicaoGratuita;
            this.qtGratuitaPopulacao = this.qtGratuitaPopulacaoMinimo;
            this.qtPopularIntegral = this.quantidadePopularIntegral;
            this.qtPopularParcial = this.quantidadePopularParcial;
        },
        //Distribuição Gratuita: Divulgação
        qtGratuitaDivulgacao: function(val)  {
            if (this.distribuicaoGratuita == 'n'){
                quantidade = this.qtExemplares * 0.1;
                if(val > quantidade) {
                    alert("Valor n\xE3o pode passar de: "+quantidade);
                    this.qtGratuitaDivulgacao = this.qtExemplares * 0.1;
                }
                return;
            }
            this.qtGratuitaDivulgacao = 0;
        },
        tpVenda: function() {
            this.qtPopularParcial = this.quantidadePopularParcial;
            this.qtPopularIntegral = this.quantidadePopularIntegral;

            if( this.tpVenda == 'e') {
                this.labelInteira = '';
            }else {
                this.labelInteira = 'Inteira';
            }
        },
        //Distribuição Gratuita: Patrocinador
        patrocinador: function(val)  {
            quantidade = this.qtExemplares * 0.1;

            if (this.distribuicaoGratuita == 'n') {
                if(val > quantidade) {
                    alert("Valor n\xE3o pode passar de: "+quantidade);
                    this.qtGratuitaPatrocinador = this.qtExemplares * 0.1;
                }
                return;
            }
            this.qtGratuitaPatrocinador = 0;
        },
        vlUnitarioPopularIntegral: function() {

            if (this.distribuicaoGratuita == 'n') {
                if (this.converterParaMoedaAmericana(this.vlUnitarioPopularIntegral) > 75.00) {
                    this.vlUnitarioPopularIntegral = numeral(75.00).format();
                    alert('O valor n\xE3o pode ser maior que 75.00');
                }
                return;
            }
            this.vlUnitarioPopularIntegral = 0;
        },
        qtPrecoPopularValorIntegral: function(val){
            if (this.distribuicaoGratuita == 'n') {
                if (this.qtPrecoPopularValorIntegral > this.qtPrecoPopularValorIntegralLimite) {
                    alert('O valor n\xE3o pode ser maior que ' + this.qtPrecoPopularValorIntegralLimite);
                }
                return ;
            }
            this.qtPrecoPopularValorIntegral = 0;
        },
        qtPrecoPopularValorParcial: function(val){
            if (this.distribuicaoGratuita == 'n') {
                if (this.qtPrecoPopularValorParcial > this.qtPrecoPopularValorParcialLimite) {
                    alert('O valor n\xE3o pode ser maior que ' + this.qtPrecoPopularValorParcialLimite);
                }
                return;
            }
            this.qtPrecoPopularValorParcial = 0;
        },
        distribuicaoGratuita : function(val){
            if (this.distribuicaoGratuita == 's') {

                this.qtGratuitaPopulacao = this.qtExemplares;
                this.qtGratuitaPopulacaoMinimo = this.qtExemplares;
                this.percentualProponente = 0;
                this.percentualPrecoPopular = 0;

                this.$refs.populacao.disabled = true;
                this.$refs.divulgacao.disabled = true;
                this.$refs.patrocinador.disabled = true;
                this.$refs.qtPopularIntegral.disabled = true;

                if(typeof this.$refs.qtPopularParcial !== 'undefined') {
                    this.$refs.qtPopularParcial.disabled = true;
                }

                this.qtGratuitaDivulgacao = 0;
                this.qtGratuitaPatrocinador = 0;
                this.vlUnitarioPopularIntegral = 0.0; // Preço popular: Preço Unitario do Ingresso
                this.qtPrecoPopularValorIntegral = 0; //Preço Popular: Quantidade de Inteira
                this.qtPrecoPopularValorParcial =  0;//Preço Popular: Quantidade de meia entrada
                this.vlUnitarioProponenteIntegral =  0;
                this.qtPopularIntegral = 0;
                this.qtPopularParcial = 0;
                this.vlReceitaPopularIntegral = 0;



            } else {
                this.$refs.populacao.disabled = false;
                this.$refs.divulgacao.disabled = false;
                this.$refs.patrocinador.disabled = false;
                // this.$refs.qtPopularIntegral.disabled = false;
                // this.$refs.qtPopularParcial.disabled = false;

                this.percentualGratuito  = this.percentualGratuitoPadrao;
                this.percentualProponente = this.percentualProponentePadrao;
                this.percentualPrecoPopular = this.percentualPrecoPopularPadrao;
            }
        }
    },
    mounted: function() {
        this.t();
        this.$refs.add.disabled = !this.disabled;
    },
    methods: {
        t: function(){
            var vue = this;

            this.$data.produtos = [];
            url = "/proposta/plano-distribuicao/detalhar-mostrar/idPreProjeto/"+this.idpreprojeto+"?idPlanoDistribuicao=" + this.idplanodistribuicao + "&idMunicipio=" + this.idmunicipioibge +"&idUF=" + this.iduf
            $3.ajax({
              type: "GET",
              url:url
            })
            .done(function(data) {
                vue.$data.produtos = data.data;
            })
            .fail(function(){ vue.mensagemErro('Erro ao buscar detalhamento'); });

        },
        salvar: function (event) {

            if( this.dsProduto == '' && this.tpVenda == 'i' ) {
                this.mensagemAlerta("\xC9 obrigat\xF3rio informar a categoria");
                this.$refs.dsProduto.focus();
                return;
            }

            if( this.qtExemplares == 0) {
                this.mensagemAlerta("Quantidade \xE9 obrigat\xF3rio!");
                this.$refs.qtExemplares.focus();
                return;
            }

            if (this.distribuicaoGratuita == 'n'){

                if(this.vlUnitarioProponenteIntegral == 0 && this.percentualProponente > 0) {
                    this.mensagemAlerta("Pre\xE7o unit\xE1rio no Proponente \xE9 obrigat\xF3rio!");
                    return;
                }

                if(this.vlUnitarioPopularIntegral == 0 && this.percentualPrecoPopular > 0) {
                    this.mensagemAlerta("Pre\xE7o unit\xE1rio no Pre\xE7o Popular \xE9 obrigat\xF3rio!");
                    return;
                }
            }

            if(this.qtGratuitaPopulacao < this.qtGratuitaPopulacaoMinimo) {
                this.mensagemAlerta("Quantidade para popula\xE7\xE3o n\xE3o pode ser menor que "+ this.qtGratuitaPopulacaoMinimo);
                this.qtGratuitaPopulacao = this.qtGratuitaPopulacaoMinimo;
                this.$refs.populacao.focus();
                return;
            }



            p = {
                idPlanoDistribuicao: this.idplanodistribuicao,
                idUF: this.iduf,
                idMunicipio: this.idmunicipioibge,
                dsProduto: this.dsProduto,
                qtExemplares : this.qtExemplares,
                qtGratuitaDivulgacao :this.qtGratuitaDivulgacao,
                qtGratuitaPatrocinador : this.qtGratuitaPatrocinador,
                qtGratuitaPopulacao : this.qtGratuitaPopulacao,
                qtPopularIntegral :this.qtPopularIntegral,
                qtPopularParcial : this.qtPopularParcial,
                vlUnitarioPopularIntegral : this.converterParaMoedaAmericana(this.vlUnitarioPopularIntegral),
                vlReceitaPopularIntegral : this.converterParaMoedaAmericana(this.vlReceitaPopularIntegral),
                vlReceitaPopularParcial : this.converterParaMoedaAmericana(this.vlReceitaPopularParcial),
                qtProponenteIntegral : this.qtProponenteIntegral,
                qtProponenteParcial : this.qtProponenteParcial,
                vlUnitarioProponenteIntegral : this.converterParaMoedaAmericana(this.vlUnitarioProponenteIntegral),
                vlReceitaProponenteIntegral : this.converterParaMoedaAmericana(this.vlReceitaProponenteIntegral),
                vlReceitaProponenteParcial : this.converterParaMoedaAmericana(this.vlReceitaProponenteParcial),
                vlReceitaPrevista : this.converterParaMoedaAmericana(this.vlReceitaPrevista),
                tpVenda: this.tpVenda,
                tpLocal: this.tpLocal,
                tpEspaco: this.tpEspaco
            };

            this.$data.produtos.push(p);

            if((numeral(this.valorMedioProponente).value() > 225
		&& (this.canalaberto == 0))) {
                this.mensagemAlerta("O valor medio:" + this.valorMedioProponenteFormatado + ", n\xE3o pode ultrapassar: 225,00");
                this.$data.produtos.splice(-1,1)
            }

            this.visualizarFormulario = false;

            var vue = this;
            $3.ajax({
              type: "POST",
              url: "/proposta/plano-distribuicao/detalhar-salvar/idPreProjeto/" + this.idpreprojeto,
              data: p
            })
            .done(function() {
                vue.t();
                vue.limparFormulario();
                vue.mensagemSucesso('Salvo com sucesso');
            })
            .fail(function(){ vue.mensagemErro('Erro ao salvar!'); });

        },
        excluir: function(index){

            var vue = this;
            $3.ajax({
                type: "POST",
                url: "/proposta/plano-distribuicao/detalhar-excluir/idPreProjeto/" + this.idpreprojeto,
                data: {idDetalhaPlanoDistribuicao: index, idPlanoDistribuicao: this.idplanodistribuicao},
            })
            .done(function() {
                vue.t();
                vue.mensagemSucesso("Excluido com sucesso");
            });
        },
        populacaoValidate: function(val){
            if (this.distribuicaoGratuita == 'n'){

                var quantidadeMinima = this.qtGratuitaPopulacaoMinimo;

                if(val < quantidadeMinima) {
                    vue.mensagemAlerta("Quantidade para popula\xE7\xE3o n\xE3o pode ser menor que "+ quantidadeMinima);
                     this.qtGratuitaPopulacao = quantidadeMinima;
                }

            } else {

                this.qtGratuitaPopulacao = this.qtExemplares;
            }
        },
        converterParaMoedaAmericana: function(valor) {
            if( !valor )
                valor = '0';

            valor = valor.replace( /\./g, '' );
            valor = valor.replace( /\,/g, '.' );
            valor = parseFloat( valor );
            valor = valor.toFixed( 2 );

            if( isNaN( valor ) )
                valor = 0;

            return valor;
        },
        mostrar: function() {
            this.active = this.active == true ? false: true ;
            this.icon = this.icon == 'visibility_off' ? 'add': 'visibility_off';
        },
        mostrarFormulario: function(el) {
            this.visualizarFormulario = this.visualizarFormulario == true ? false: true;

            if( this.visualizarFormulario == true ) {
                var element = $('#' + el).offset().top - 60;
                $('body').animate({
                    scrollTop: element
                }, 500);
            }
        },
        formatarValor: function (valor) {
            valor = parseFloat(valor);
            return numeral(valor).format();
        },
        limparFormulario: function() {
            this.qtExemplares = 0;
            this.qtGratuitaDivulgacao = 0;
            this.qtGratuitaPatrocinador = 0;
            this.vlUnitarioPopularIntegral = 0; // Preço popular: Preço Unitario do Ingresso
            this.qtPrecoPopularValorIntegral = 0; //Preço Popular: Quantidade de Inteira
            this.qtPrecoPopularValorParcial =  0;//Preço Popular: Quantidade de meia entrada
            this.vlUnitarioProponenteIntegral =  0;
            this.qtPopularIntegral = 0;
            this.qtPopularParcial = 0;
            this.vlReceitaPopularIntegral = 0;
            this.dsProduto = '';
            this.tpVenda = 'i';
            this.distribuicaoGratuita = 'n';
        },
        mensagemSucesso: function(msg) {
            Materialize.toast(msg, 8000, 'green white-text');
        },
        mensagemErro: function(msg) {
            Materialize.toast(msg, 8000, 'red darken-1 white-text');
        },
        mensagemAlerta: function(msg) {
            Materialize.toast(msg, 8000, 'mensagem1 orange darken-3 white-text');
        }
    }
});

var app6 = new Vue({
        el: '#example'
    });


$3(document).ready(function () {
    $3('#modal-pre-loader').show();
});

$3(document).ajaxStart(function() {
    $3('#modal-pre-loader').show();
});

$3(document).ajaxComplete(function () {
    $3('#modal-pre-loader').hide();
});
