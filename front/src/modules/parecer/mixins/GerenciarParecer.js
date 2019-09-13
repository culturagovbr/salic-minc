export default {
    methods: {
        mxObterClasseItem(item) {
            let classe = {};
            switch (true) {
            case item.tipoAnalise === 1:
                classe = { 'purple lighten-5': true };
                break;
            default:
                classe = {};
                break;
            }
            return classe;
        },
    },
};
