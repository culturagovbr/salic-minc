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
//numeral.locale('pt-br');

// register
Vue.component('input-money', {
    template: '<div>\
                <input\
                    class="right-align"\
                    v-bind:disabled="false"\
                    v-bind:value="value"\
                    ref="input"\
                    v-on:input="updateMoney($event.target.value)"\
                    v-on:blur="formatValue"\
                >\
                </div>',
    props: {
        value: {
          type: Number,
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
        this.formatValue()
        console.log(this.disabled);
        this.$refs.input.disabled = this.disabled;
    },
    methods:{
        formatValue: function () {
          this.$refs.input.value = numeral(this.$refs.input.value).format('0,0.00');
        },
        updateMoney: function(value) {
            console.log(value);
            this.val = value;
            this.$emit('ev', this.val)
        }
    },
    watch: {
        disabled: function (){
            console.log(this.disabled)
            this.$refs.input.disabled = this.disabled;
            if (this.disabled){
                this.value= 0;
            }
        }
    }
})

// register
Vue.component('visualizar-plano-distribuicao', {
  template: '#visualizar-plano-distribuicao',
    data: function() {
       return {
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
            produto:{ }, // produto sendo manipulado
            produtos:  [], // lista de produtos
            active : false,
            icon : 'add',
            radio : 'n'
        }
    },
    props:['idpreprojeto','idplanodistribuicao', 'idmunicipioibge', 'iduf', 'disabled'],
    computed:{
        // Limite: preço popular: Quantidade de Inteira
        qtPrecoPopularValorIntegralLimite: function() {
            return ((this.qtExemplares * 0.5)  - (parseInt(this.qtGratuitaDivulgacao) + parseInt(this.qtGratuitaPatrocinador) + parseInt(this.qtGratuitaPopulacao))) * 0.6 ;
        },
        // Limite: preço popular: Quantidade de meia entrada
        qtPrecoPopularValorParcialLimite: function () {
            return  ( ((this.qtExemplares * 0.5) - (parseInt(this.qtGratuitaDivulgacao) + parseInt(this.qtGratuitaPatrocinador) + parseInt(this.qtGratuitaPopulacao))) *0.4 );
        },
        //Preço Popular: Valor da inteira
        vlReceitaPopularIntegral: function() {
            if (this.radio == 'n') {
                return parseInt(this.qtPopularIntegral) * parseFloat(this.vlUnitarioPopularIntegral);
            }
            return 0;
        },
        vlReceitaPopularParcial: function() {
            return this.qtPopularParcial * ( this.vlUnitarioPopularIntegral * 0.5);
        },
        /*verificar esse calculo com mais cuidado*/
        qtProponenteIntegral: function() {
            if (this.radio == 'n') {
                return (this.qtExemplares * 0.5) * 0.5 ;
            }
            return 0;
        },
        qtProponenteParcial: function() {
            if (this.radio == 'n') {
                return (this.qtExemplares * 0.5) * 0.5 ;
            }
            return 0;
        },
        vlReceitaProponenteIntegral: function() {
            if (this.radio == 'n') {
                return parseFloat( this.vlUnitarioProponenteIntegral * this.qtProponenteIntegral ).toFixed(2);
            }
            return 0;
        },
        vlReceitaProponenteParcial: function(){
            if (this.radio == 'n'){
                return parseFloat( ( this.vlUnitarioProponenteIntegral * 0.5 ) * this.qtProponenteParcial).toFixed(2);
            }
            return 0;
        },
        vlReceitaPrevista: function() {
            var total =  (parseFloat(this.vlReceitaPopularIntegral) + parseFloat(this.vlReceitaPopularParcial)
                + parseFloat(this.vlReceitaProponenteIntegral) + parseFloat(this.vlReceitaProponenteParcial)).toFixed(2);
            return numeral(total).format('0,0.00');
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
        //Preço Popular: Quantidade de mei entrada
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
            return numeral(total).format('0,0.00');
        },
        vlReceitaPopularParcialTotal: function() {
            total = 0 ;
            for ( var i = 0 ; i < this.produtos.length ; i++) {
                var vl = (this.produtos[i]['vlReceitaPopularParcial']);
                total += numeral(vl).value();
            }
            return numeral(total).format('0,0.00');
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
                total += numeral(vl).value();
            }
            return numeral(total).format('0,0.00');
        },
        vlReceitaProponenteParcialTotal: function() {
            total = 0 ;
            for ( var i = 0 ; i < this.produtos.length ; i++){
                var vl = (this.produtos[i]['vlReceitaProponenteParcial']);
                total += numeral(vl).value();
            }
            return numeral(total).format('0,0.00');
        },
        receitaPrevistaTotal: function() {
            var total = 0 ;
            for ( var i = 0 ; i < this.produtos.length ; i++){
                var vl = this.produtos[i]['vlReceitaPrevista'];
                total += numeral(vl).value();
            }
            return numeral(total).format('0,0.00');
        },
    },
    watch:{
        //Quantidade de exemplar / Ingresso
        qtExemplares: function(val)  {
            if (this.radio == 'n'){
                this.qtGratuitaDivulgacao = this.qtExemplares * 0.1;
                this.qtGratuitaPatrocinador = this.qtExemplares * 0.1;
                this.qtGratuitaPopulacao = parseInt(this.qtExemplares * 0.1);
                this.qtPopularIntegral = ((this.qtExemplares * 0.5)  - (parseInt(this.qtGratuitaDivulgacao) + parseInt(this.qtGratuitaPatrocinador) + parseInt(this.qtGratuitaPopulacao))) * 0.6 ;
                this.qtPopularParcial =  ( ((this.qtExemplares * 0.5) - (parseInt(this.qtGratuitaDivulgacao) + parseInt(this.qtGratuitaPatrocinador) + parseInt(this.qtGratuitaPopulacao))) *0.4 );
            } else {
                this.qtGratuitaPopulacao = this.qtExemplares;
            }
        },
        //Distribuição Gratuita: Divulgação
        qtGratuitaDivulgacao: function(val)  {
            if (this.radio == 'n'){
                quantidade = this.qtExemplares * 0.1;
                if(val > quantidade) {
                    alert("Valor não pode passar de: "+quantidade);
                    this.qtGratuitaDivulgacao = this.qtExemplares * 0.1;
                }
                return;
            }
            this.qtGratuitaDivulgacao = 0;
        },
        //Distribuição Gratuita: Patrocinador
        patrocinador: function(val)  {
            quantidade = this.qtExemplares * 0.1;

            if (this.radio == 'n') {
                if(val > quantidade) {
                    alert("Valor não pode passar de: "+quantidade);
                    this.qtGratuitaPatrocinador = this.qtExemplares * 0.1;
                }
                return;
            }
            this.qtGratuitaPatrocinador = 0;
        },
        vlUnitarioPopularIntegral: function() {
            if (this.radio == 'n') {
                if (this.vlUnitarioPopularIntegral > 50.00) {
                    alert('O valor não pode ser maior que 50.00');
                    this.vlUnitarioPopularIntegral = 50.00;
                }
                return;
            }
            this.vlUnitarioPopularIntegral = 0;
        },
        qtPrecoPopularValorIntegral: function(val){
            if (this.radio == 'n') {
                if (this.qtPrecoPopularValorIntegral > this.qtPrecoPopularValorIntegralLimite) {
                    alert('O valor não pode ser maior que ' + this.qtPrecoPopularValorIntegralLimite);
                }
                return ;
            }
            this.qtPrecoPopularValorIntegral = 0;
        },
        qtPrecoPopularValorParcial: function(val){
            if (this.radio == 'n') {
                if (this.qtPrecoPopularValorParcial > this.qtPrecoPopularValorParcialLimite) {
                    alert('O valor não pode ser maior que ' + this.qtPrecoPopularValorParcialLimite);
                }
                return;
            }
            this.qtPrecoPopularValorParcial = 0;
        },
        radio : function(val){
            if (this.radio == 's') {

                console.log(this.radio);
                this.qtGratuitaPopulacao = this.qtExemplares;

                this.$refs.populacao.disabled = true;
                this.$refs.divulgacao.disabled = true;
                this.$refs.patrocinador.disabled = true;
                this.$refs.qtPopularIntegral.disabled = true;
                this.$refs.qtPopularParcial.disabled = true;

                this.qtGratuitaDivulgacao = 0;
                this.qtGratuitaPatrocinador = 0;
                this.vlUnitarioPopularIntegral = 0.0; // Preço popular: Preço Unitario do Ingresso
                this.qtPrecoPopularValorIntegral = 0; //Preço Popular: Quantidade de Inteira
                this.qtPrecoPopularValorParcial =  0;//Preço Popular: Quantidade de meia entrada
                this.vlUnitarioProponenteIntegral =  0;
                this.qtPopularIntegral = 0,
                this.qtPopularParcial = 0;
                this.vlReceitaPopularIntegral = 0;

            } else {
                this.$refs.populacao.disabled = false;
                this.$refs.divulgacao.disabled = false;
                this.$refs.patrocinador.disabled = false;
                this.$refs.qtPopularIntegral.disabled = false;
                this.$refs.qtPopularParcial.disabled = false;
            }
        }
    },
    mounted: function() {
        this.t();
        //console.log(this.disabled);
        //this.$refs.add.disabled = !this.disabled;
    },
    methods: {
        t: function(){
            var vue = this;

            this.$data.produtos = [];
            url = "/proposta/visualizar-plano-distribuicao/detalhar/idPreProjeto/"+this.idpreprojeto+"?idPlanoDistribuicao=" + this.idplanodistribuicao + "&idMunicipio=" + this.idmunicipioibge +"&idUF=" + this.iduf
            console.log(url);
            $3.ajax({
              type: "GET",
              url:url
            })
            .done(function(data) {
                vue.$data.produtos = data.data;
            })
            .fail(function(){ alert('error'); });

        },
        populacaoValidate: function(val){
            if (this.radio == 'n'){
                quantidade = this.qtExemplares * 0.1;
                if(val < quantidade) {
                    alert("Valor não pode ser menor que: "+quantidade);
                    this.qtGratuitaPopulacao = this.qtExemplares * 0.1;
                }

                if((parseInt( this.qtGratuitaDivulgacao ) + parseInt(this.qtGratuitaPatrocinador) + parseInt(this.qtGratuitaPopulacao)) > (this.qtExemplares * 0.3)) {
                    alert("A soma dos valores de divulgação, patrocinador e população não pode passar de 30%");
                    this.$refs.patrocinador.focus();
                }
            } else {

                this.qtGratuitaPopulacao = this.qtExemplares;
            }
        },
        mostrar: function() {
            this.active = this.active == true ? false: true ;
            this.icon = this.icon == 'visibility_off' ? 'add': 'visibility_off';
        }
    }
})

var app6 = new Vue({
        el: '#example',
    })
