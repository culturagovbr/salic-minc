<template>
    <div
        v-if="proponente"
        id="proponente">
        <agente-identificacao :identificacao="identificacao"/>
        <agente-endereco :enderecos="proponente.enderecos"/>
        <agente-telefone :telefones="proponente.telefones"/>
        <agente-email :emails="proponente.emails"/>
        <agente-natureza :natureza="proponente.natureza"/>
        <agente-dirigente :dirigentes="proponente.dirigentes"/>
    </div>
</template>
<script>
import axios from 'axios';
import { utils } from '@/mixins/utils';
import AgenteIdentificacao from './AgenteIdentificacao';
import AgenteEndereco from './AgenteEndereco';
import AgenteTelefone from './AgenteTelefone';
import AgenteEmail from './AgenteEmail';
import AgenteNatureza from './AgenteNatureza';
import AgenteDirigente from './AgenteDirigente';

export default {
    name: 'AgenteProponente',
    components: {
        AgenteDirigente,
        AgenteNatureza,
        AgenteEmail,
        AgenteTelefone,
        AgenteEndereco,
        AgenteIdentificacao,
    },
    mixins: [utils],
    props: {
        idagente: {
            type: [String, Number],
            default: '',
        },
    },
    data() {
        return {
            proponente: [],
            identificacao: {},
        };
    },
    watch: {
        idagente(value) {
            this.fetch(value);
        },
    },
    mounted() {
        if (typeof this.idagente !== 'undefined') {
            this.fetch(this.idagente);
        }
    },
    methods: {
        fetch(id) {
            if (id) {
                const self = this;
                axios.get(`/agente/visualizar/obter-dados-proponente/idAgente/${id}`)
                    .then((response) => {
                        self.proponente = response.data.data;

                        if (self.proponente && self.proponente.identificacao) {
                            self.identificacao = self.proponente.identificacao;
                        }
                    });
            }
        },
    },
};
</script>
