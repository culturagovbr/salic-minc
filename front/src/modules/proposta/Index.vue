<template>
    <div>
        {{id}}
    </div>
</template>

<script>
    export default {
        name: 'Index',
        data() {
            return {
                id: this.$route.params.idPreProjeto,
            };
        },
    };
</script>
