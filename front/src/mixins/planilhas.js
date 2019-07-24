import numeral from 'numeral';
import moment from 'moment';
import moneyFilter from '@/filters/money';
import 'numeral/locales';

numeral.locale('pt-br');
numeral.defaultFormat('0,0.00');

export default {
    methods: {
        obterValorSolicitadoTotal(table) {
            const soma = numeral();

            Object.entries(table).forEach(([, cell]) => {
                if (cell.vlSolicitado !== undefined) {
                    soma.add(parseFloat(cell.vlSolicitado));
                }
            });

            return soma.format();
        },
        obterValorAprovadoTotal(table) {
            const soma = numeral();
            Object.entries(table).forEach(([, cell]) => {
                if (typeof cell.vlAprovado !== 'undefined') {
                    if (cell.tpAcao && cell.tpAcao === 'E') {
                        return;
                    }
                    soma.add(parseFloat(cell.vlAprovado));
                }
            });

            return soma.format();
        },
        obterValorComprovadoTotal(table) {
            const soma = numeral();
            Object.entries(table).forEach(([, cell]) => {
                if (typeof cell.VlComprovado !== 'undefined') {
                    if (cell.tpAcao && cell.tpAcao === 'E') {
                        return;
                    }
                    soma.add(parseFloat(cell.VlComprovado));
                }
            });

            return soma.format();
        },
        obterValorSugeridoTotal(table) {
            const soma = numeral();

            Object.entries(table).forEach(([, cell]) => {
                if (typeof cell.vlSugerido !== 'undefined') {
                    soma.add(parseFloat(cell.vlSugerido));
                }
            });

            return soma.format();
        },
        isObject(el) {
            return typeof el === 'object';
        },
        definirClasseItem(row) {
            return {
                'orange lighten-4': row.stCustoPraticado === true || row.stCustoPraticado === '1' || row.stCustoPraticado === 1,
                'linha-incluida': row.tpAcao === 'I',
                'linha-excluida': row.tpAcao === 'E',
                'linha-atualizada': row.tpAcao === 'A',
            };
        },
        formatarParaReal(value) {
            return this.$options.filters.filtroFormatarParaReal(value);
        },
        decodeHtml(value) {
            const decoded = document.createElement('span');
            decoded.innerHTML = value;
            return decoded.textContent;
        },
    },
    filters: {
        filtroFormatarParaReal(value) {
            const parsedValue = typeof value !== 'undefined' ? parseFloat(value) : 0;
            return moneyFilter(parsedValue);
        },
        formatarData(value) {
            if (value) {
                return moment(String(value)).format('MM/DD/YYYY');
            }

            return '';
        },
    },
};
