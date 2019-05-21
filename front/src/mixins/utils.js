import moment from 'moment';
import cnpjFilter from '@/filters/cnpj';
import moneyFilter from '@/filters/money';
import filtersQuantidade from '@/filters/quantidade';

export const utils = {
    methods: {
        mensagemSucesso(msg) {
            // eslint-disable-next-line
            Materialize.toast(msg, 8000, 'green white-text');
        },
        mensagemErro(msg) {
            // eslint-disable-next-line
            Materialize.toast(msg, 8000, 'red darken-1 white-text');
        },
        mensagemAlerta(msg) {
            // eslint-disable-next-line
            Materialize.toast(msg, 8000, 'mensagem1 orange darken-3 white-text');
        },
        formatarValor(valor) {
            return moneyFilter(parseFloat(valor));
        },
        label_sim_ou_nao(valor) {
            if (valor === 1 || valor === '1') {
                return 'Sim';
            }

            return 'N\xE3o';
        },
        isObject(el) {
            return typeof el === 'object';
        },
        converterParaReal(value) {
            value = parseFloat(value);
            return numeral(value).format('0,0.00');
        },
        isDataExpirada(date) {
            return moment().diff(date, 'days') > 0;
        },
        converterParaMoedaAmericana(valor) {
            if (!valor) {
                return 0;
            }

            let novoValor = String(valor);
            novoValor = novoValor.replace(/\./g, '');
            // eslint-disable-next-line
            novoValor = novoValor.replace(/\,/g, '.');
            novoValor = parseFloat(novoValor);
            novoValor = novoValor.toFixed(2);
            // eslint-disable-next-line
            if (isNaN(novoValor)) {
                novoValor = 0;
            }

            return novoValor;
        },
        stripTags(string) {
            if (typeof string !== 'string') {
                return string;
            }
            return string.replace(/(<([^>]+)>)/ig, '');
        },
    },
    filters: {
        formatarData(date) {
            if ((date && date.length === 0) || date === null) {
                return '-';
            }
            return moment(date)
                .format('DD/MM/YYYY');
        },
        formatarAgencia(agencia) {
            // formato: 9999-9
            if (agencia && agencia.length === 5) {
                return agencia.replace(/(\d{4})(\d)/, '$1-$2');
            }
            return agencia;
        },
        formatarConta(conta) {
            // formato: 99.999.999-x
            const regex = /^(?:0{1,})(\w+)(\w{1})$/;
            const s = conta.toString().match(regex);
            const n = s[1]; const
                t = n.length - 1;
            let novo = '';
            for (let i = t, a = 1; i >= 0; i -= 1, a += 1) {
                const ponto = a % 3 === 0 && i > 0 ? '.' : '';
                novo = ponto + n.charAt(i) + novo;
            }
            return `${novo}-${s[2]}`;
        },
        formatarLabelSimOuNao(valor) {
            if (valor === 1 || valor === '1') {
                return 'Sim';
            }
            return 'N\xE3o';
        },
        formatarLabelMecanismo(valor) {
            switch (valor) {
            case '1':
            case 1:
                return 'Mecenato';
            default:
                return 'Inv\xE1lido';
            }
        },
        formatarLabelEsfera(esfera) {
            let string;

            switch (esfera) {
            case '1':
                string = 'Municipal';
                break;
            case '2':
                string = 'Estadual';
                break;
            case '3':
                string = 'Federal';
                break;
            default:
                string = 'N\xE3o informada';
                break;
            }

            return string;
        },
        formatarCpfOuCnpj(value) {
            if (typeof value === 'undefined') {
                return '';
            }
            return cnpjFilter(value);
        },
        formatarTipoPessoa(tipo) {
            let string = 'Pessoa F\xEDsica';

            if (tipo === '1') {
                string = 'Pessoa Jur\xEDdica';
            }

            return string;
        },
        formatarCep(v) {
            if (v.length !== 8) {
                return '';
            }

            let value = v.replace(/\D/g, '');
            value = value.replace(/(\d{2})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1-$2');
            return value;
        },
        formatarParaReal(v) {
            return moneyFilter(v);
        },
        filtroFormatarParaReal(v) {
            const parsedValue = parseFloat(v);
            return moneyFilter(parsedValue);
        },
        filtroDiminuirTexto(value, size = 150) {
            if (value === null || value.length < size) {
                return value;
            }

            return value.replace(/(<([^>]+)>)/ig, '').slice(0, size).concat('...');
        },
        filtroFormatarQuantidade(value) {
            const parsedValue = parseFloat(value);
            return filtersQuantidade(parsedValue);
        },
    },
};

export default utils;
