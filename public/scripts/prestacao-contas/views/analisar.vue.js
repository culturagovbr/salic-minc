const Analisar = {
    template:
    `
        <div>
            <div class="card">
                <div class="card-content">
                    <span class="card-title">1012121 - Criança é Vida - 15 anos</span> 
                    <div class="card-content">
                        <p> Existe Diligência para esse projeto. Acesse <a href="/proposta/diligenciar/listardiligenciaanalista/idPronac/132451">aqui</a>.</p>
                    </div>
                    <div class="card-action">
                        <a href="/consultardadosprojeto/index?idPronac=132451" target="_blank" class="btn-flat green waves-effect waves-dark white-text">Ver Projeto</a>
                        <button class="btn btn-flat green white-text">
                            Consolidação
                        </button>
                        <router-link :to="{ name: 'completa', params: { id: 123 }}" class="btn">Completa</router-link>
                        <router-link :to="{ name: 'amostragem', params: { id: 123 }}" class="btn">Amostragem</router-link>
                    </div>
                </div>
            </div>
            <router-view></router-view>
        </div>
    `
}

const AnaliseCompleta = {
    template:`
    <div class="card">
        <div class="card-content">
            teste
        </div>
    </div>
    `
}

const AnaliseAmostragem = {
    template:`
    <div class="card">
        <div class="card-content">
           amostragem 
        </div>
    </div>
    `
}
