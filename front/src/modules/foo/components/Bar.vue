<template>
    <div>
        <h1>Componente Bar</h1>
        <div>
            <h3>Exemplo Requisição Ajax</h3>
            <div
                v-for="(linha, index) in tabela"
                :key="index">
                <div>{{ linha }}</div>
            </div>
        </div>
        <div class="card">
            <h3>Exemplo Computed</h3>
            <input
                v-model="nome"
                type="text">
            <input
                v-model="sobrenome"
                type="text">
            <h1 v-html="nomeCompleto"/>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Bar',
    data() {
        return {
            tabela: {},
            nome: 'Nome',
            sobrenome: 'Sobrenome',
        };
    },
    computed: {
        nomeCompleto() {
            return `${this.nome} ${this.sobrenome}`;
        },
    },
    created() {
        this.obterDados();
    },
    methods: {
        obterDados() {
            const self = this;
            /* eslint-disable-next-line */
                $3.ajax({
                url: '/foo/foo-rest',
                data: {},
            }).done(
                (response) => {
                    self.tabela = response.data;
                },
            );
        },
    },
};
</script>
