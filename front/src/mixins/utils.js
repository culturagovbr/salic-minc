import moment from 'moment';
import numeral from 'numeral';
import 'numeral/locales';
import filtersQuantidade from '@/filters/quantidade';

numeral.locale('pt-br');
numeral.defaultFormat('0,0.00');

/* eslint-disable */
export const utils = {
    methods: {
        converterParaMoedaAmericana: function (valor) {
            if (!valor) {
                return 0;
            }

            valor = String(valor);
            valor = valor.replace(/\./g, '');
            valor = valor.replace(/\,/g, '.');
            valor = parseFloat(valor);
            valor = valor.toFixed(2);

            if (isNaN(valor)) {
                valor = 0;
            }

            return valor;
        },
        converterParaMoedaPontuado: function (num) {
            // funcao salic de conversao pontuada - trazida de moeda.js
            var x = 0;

            if (num < 0) {
                num = Math.abs(num);
                x = 1;
            }

            if (isNaN(num)) {
                num = "0";
            }
            var cents = Math.floor((num * 100 + 0.5) % 100);
            num = Math.floor((num * 100 + 0.5) / 100).toString();

            if (cents < 10) {
                cents = "0" + cents;
            }
            for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
                num = num.substring(0, num.length - (4 * i + 3)) + '.' +
                    num.substring(num.length - (4 * i + 3));

            var ret = num + ',' + cents;
            if (x == 1) {
                ret = ' - ' + ret;
            }

            return ret;
        },
        mensagemSucesso: function (msg) {
            Materialize.toast(msg, 8000, 'green white-text');
        },
        mensagemErro: function (msg) {
            Materialize.toast(msg, 8000, 'red darken-1 white-text');
        },
        mensagemAlerta: function (msg) {
            Materialize.toast(msg, 8000, 'mensagem1 orange darken-3 white-text');
        },
        formatarValor: function (valor) {
            valor = parseFloat(valor);
            return numeral(valor).format();
        },
        label_sim_ou_nao: function (valor) {
            if (valor == 1) {
                return 'Sim';
            }

            return 'N\xE3o';
        },
	isObject: function (el) {
            return typeof el === "object";
        },
	converterParaReal: function (value) {
            value = parseFloat(value);
            return numeral(value).format('0,0.00');
        },
        isDataExpirada(date) {
            return moment().diff(date, 'days') > 0;
        },
    },
    filters: {
        formatarData(date) {
            if (date && date.length === 0 || date === null || date === '0.00') {
                return '-';
            }
            return moment(date)
                .format('DD/MM/YYYY');
        },
        formatarAgencia(agencia) {
            // formato: 9999-9
            if (agencia && agencia.length === 5) {
                agencia = agencia.replace(/(\d{4})(\d)/, '$1-$2');
            }
            return agencia;
        },
        formatarConta(conta) {
            if (!conta) {
                return '';
            }

            // formato: 99.999.999-x
            const regex = /^(?:0{1,})(\w+)(\w{1})$/;
            const s = conta.toString().match(regex);
            const n = s[1], t = n.length -1;
            let novo = '';
            for( var i = t, a = 1; i >=0; i--, a++ ){
                var ponto = a % 3 === 0 && i > 0 ? '.' : '';
                novo = ponto + n.charAt(i) + novo;
            }
            return `${novo}-${s[2]}`;
        },
        filtroFormatarParaReal(value) {
            const parsedValue = parseFloat(value);
            return numeral(parsedValue).format('0,0.00');
        },
        filtroFormatarQuantidade(value) {
            const parsedValue = parseFloat(value);
            return filtersQuantidade(parsedValue);
        }
    },
};
