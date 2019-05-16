import _ from 'lodash';

export default {
    methods: {
        verificarPerfil(perfil, perfisAceitos) {
            if (!_.isEmpty(perfisAceitos)) {
                if (perfisAceitos.includes(perfil)) {
                    return true;
                }
                return false;
            }
            return true;
        },
    },
};
