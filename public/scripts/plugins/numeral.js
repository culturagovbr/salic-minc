(function (global, factory) {
    if (typeof define === 'function' && define.amd) {
        define(['../numeral'], factory);
    } else if (typeof module === 'object' && module.exports) {
        factory(require('../numeral'));
    } else {
        factory(global.numeral);
    }
}(this, function (numeral) {
    if (numeral.locales['pt-br'] === undefined) {
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
    }
}));

// switch between locales
numeral.locale('pt-br');
numeral.defaultFormat('0,0.00');