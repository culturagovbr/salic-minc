<template>
    <div class="pa-3">
        <AgenteProponente :idagente="proposta.idAgente" />
        <AgenteUsuario :idusuario="proposta.idUsuario" />
    </div>
</template>
<script>

import AgenteProponente from '@/modules/proposta/components/AgenteProponente';
import AgenteUsuario from '@/modules/proposta/components/AgenteUsuario';

export default {
    name: 'PropostaProponente',
    components: {
        AgenteProponente,
        AgenteUsuario,
    },
    props: {
        idpreprojeto: {
            type: null,
            default: null,
        },
        proposta: {
            type: Object,
            default() {
                return {};
            },
        },
    },
};
</script>
