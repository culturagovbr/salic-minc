<template>
    <div id="conteudo">
        <div
            v-if="loading"
            class="row">
            <Carregando :text="'Carregando proponente'"/>
        </div>
        <div
            v-else
            id="proponente">
            <Identificacao :projeto="dadosProjeto"/>
            <fieldset>
                <Endereco :enderecos="dadosProponente.enderecos"/>
            </fieldset>
            <fieldset>
                <Telefone :telefones="dadosProponente.telefones"/>
            </fieldset>
            <fieldset>
                <Email :emails="dadosProponente.emails"/>
            </fieldset>
            <fieldset>
                <Natureza :natureza="dadosProponente.dados"/>
            </fieldset>
            <fieldset>
                <Dirigente :dirigentes="dadosProponente.dirigentes"/>
            </fieldset>
            <fieldset>
                <Procurador :procuradores="dadosProponente.procuradores"/>
            </fieldset>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import Carregando from '@/components/Carregando';
import Identificacao from '@/components/Projeto/ProjetoIdentificacao';
import Endereco from '@/components/Agente/AgenteEndereco';
import Telefone from '@/components/Agente/AgenteTelefone';
import Email from '@/components/Agente/AgenteEmail';
import Natureza from '@/components/Agente/AgenteNatureza';
import Dirigente from '@/components/Agente/AgenteDirigente';
import Procurador from '@/components/Agente/AgenteProcurador';

export default {
    components: {
        Carregando,
        Identificacao,
        Endereco,
        Telefone,
        Email,
        Natureza,
        Dirigente,
        Procurador,
    },
    data() {
        return {
            loading: true,
        };
    },
    computed: {
        ...mapGetters({
            dadosProponente: 'projeto/proponente',
            dadosProjeto: 'projeto/projeto',
        }),
    },
    watch: {
        dadosProponente() {
            if (Object.keys(this.dadosProponente).length > 0) {
                this.loading = false;
            }
        },
    },
    created() {
        this.buscaProponente(this.dadosProjeto.idPronac);

        if (Object.keys(this.dadosProponente).length > 0) {
            this.loading = false;
        }
    },
    methods: {
        ...mapActions({
            buscaProponente: 'projeto/buscaProponente',
        }),
    },
};
</script>
