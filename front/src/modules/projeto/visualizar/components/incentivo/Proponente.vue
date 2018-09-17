<template>
    <div id="conteudo">
        <div v-if="loading" class="row">
            <Carregando :text="'Carregando proponente'"></Carregando>
        </div>
        <div v-else id="proponente">
            <Identificacao :projeto="dadosProjeto"></Identificacao>
            <fieldset>
                <Endereco :enderecos="dadosProponente.enderecos"></Endereco>
            </fieldset>
            <fieldset>
                <Telefone :telefones="dadosProponente.telefones"></Telefone>
            </fieldset>
            <fieldset>
                <Email :emails="dadosProponente.emails"></Email>
            </fieldset>
            <fieldset>
                <Natureza :natureza="dadosProponente.dados"></Natureza>
            </fieldset>
            <fieldset>
                <Dirigente :dirigentes="dadosProponente.dirigentes"></Dirigente>
            </fieldset>
            <fieldset>
                <Procurador :procuradores="dadosProponente.procuradores"></Procurador>
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
        data() {
            return {
                loading: true,
            };
        },
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
        created() {
            this.buscaProponente(this.dadosProjeto.idPronac);

            if (Object.keys(this.dadosProponente).length > 0) {
                this.loading = false;
            }
        },
        watch: {
            dadosProponente() {
                if (Object.keys(this.dadosProponente).length > 0) {
                    this.loading = false;
                }
            },
        },
        methods: {
            ...mapActions({
                buscaProponente: 'projeto/buscaProponente',
            }),
        },
        computed: {
            ...mapGetters({
                dadosProponente: 'projeto/proponente',
                dadosProjeto: 'projeto/projeto',
            }),
        },
    };
</script>
