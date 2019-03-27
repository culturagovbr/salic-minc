<template>
    <table class="tabela">
        <thead>
            <tr class="destacar">
                <th>PRONAC</th>
                <th>Nome do Projeto</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ pronac }}</td>
                <td>{{ nomeProjeto }}</td>
            </tr>
        </tbody>
    </table>
</template>
<script>
export default {
    name: 'IdentificacaoProjeto',
    props: ['pronac', 'nomeProjeto'],
};
</script>
