<template>
    <div id="conteudo">
        <div class="row">
            <CarregarTemplateAjax :urlAjax="url"></CarregarTemplateAjax>
        </div>
    </div>
</template>
<script>
    import CarregarTemplateAjax from '@/components/CarregarTemplateAjax';

    export default {
        data() {
            return {
                url: `/default/consultardadosprojeto/dados-relacao-pagamentos?idPronac=${this.$route.params.idPronac}`,
            };
        },
        components: {
            CarregarTemplateAjax,
        },
    };
</script>
