<template>
    <div
        class="flex flex-col items-center justify-center min-h-screen px-4 py-12 bg-gray-100 sm:px-6 lg:px-8"
    >
        <div v-if="loading" class="flex items-center justify-center mb-6">
            <div
                class="w-16 h-16 border-b-2 border-blue-600 rounded-full animate-spin"
            ></div>
            <p class="ml-4 text-lg">Carregando...</p>
        </div>
        <div v-else>
            <div class="w-full max-w-3xl p-8 bg-white rounded-lg shadow-md">
                <h1 class="mb-4 text-4xl font-bold text-center text-blue-600">
                    Bem-vindo ao Task Manager
                </h1>
                <p class="mb-6 text-lg text-center text-gray-700">
                    Organize suas tarefas de forma simples e eficiente. Com o
                    nosso site, você pode criar, gerenciar e compartilhar suas
                    listas de tarefas com facilidade.
                </p>

                <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2">
                    <div class="p-4 rounded-lg shadow bg-blue-50">
                        <h2 class="text-lg font-semibold text-blue-600">
                            Como Funciona?
                        </h2>
                        <p class="text-gray-600">
                            - Crie uma nova lista de tarefas e adicione suas
                            tarefas. <br />
                            - Classifique suas tarefas como privadas ou
                            públicas. <br />
                            - Filtre e ordene suas tarefas com base nas suas
                            preferências. <br />
                            - Compartilhe listas com amigos e familiares usando
                            links gerados.
                        </p>
                    </div>
                    <div class="p-4 rounded-lg shadow bg-green-50">
                        <h2 class="text-lg font-semibold text-green-600">
                            Recursos
                        </h2>
                        <ul class="pl-5 text-gray-600 list-disc">
                            <li>Interface amigável e intuitiva</li>
                            <li>Organização eficiente das suas tarefas</li>
                            <li>Compatível com dispositivos móveis</li>
                            <li>Armazenamento seguro na nuvem</li>
                        </ul>
                    </div>
                </div>

                <!-- Exibe o nome do usuário se estiver logado -->
                <div class="mb-6 text-center">
                    <div v-if="isLoggedIn">
                        <p class="text-lg text-gray-700">
                            Bem-vindo, {{ userName }}!
                        </p>

                        <router-link to="/tasks">
                            <button
                                class="px-4 py-2 mr-4 font-bold text-white bg-blue-500 rounded hover:bg-blue-700"
                            >
                                Ver as tarefas V1
                            </button>
                        </router-link>

                        <router-link to="/tasks2">
                            <button
                                class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700"
                            >
                                Ver as tarefas V2
                            </button>
                        </router-link>

                        <button
                            class="px-4 py-2 ml-4 font-bold text-white transition duration-200 bg-red-600 rounded hover:bg-red-500"
                            @click="logout"
                        >
                            Sair
                        </button>
                    </div>
                    <div v-else>
                        <p class="mb-4 text-lg text-gray-700">
                            Já tem uma conta?
                        </p>
                        <div class="flex justify-center gap-4">
                            <router-link to="/login">
                                <button
                                    class="px-4 py-2 font-bold text-white transition duration-200 bg-green-600 rounded hover:bg-green-500"
                                >
                                    Fazer Login
                                </button>
                            </router-link>
                            <router-link to="/register">
                                <button
                                    class="px-4 py-2 font-bold text-white transition duration-200 bg-blue-600 rounded hover:bg-blue-500"
                                >
                                    Registrar
                                </button>
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import axios from "axios";

export default {
    name: "HomePage",
    data() {
        return {
            userName: "",
            isLoggedIn: false,
            loading: true, // Adiciona estado de carregamento
        };
    },
    created() {
        // Simula um atraso de 3 segundos para a transição suave
        setTimeout(() => {
            this.checkAuth();
        }, 125);
    },
    methods: {
        checkAuth() {
            const token = localStorage.getItem("token"); // Obtenha o token do localStorage

            if (token) {
                // Adicione o token ao cabeçalho de autorização
                axios.defaults.headers.common[
                    "Authorization"
                ] = `Bearer ${token}`;

                axios
                    .get("/api/user")
                    .then((response) => {
                        if (response.data) {
                            this.userName = response.data.name;
                            this.isLoggedIn = true;
                        } else {
                            this.isLoggedIn = false;
                        }
                    })
                    .catch((error) => {
                        console.error(
                            "Erro ao verificar autenticação:",
                            error.response ? error.response.data : error.message
                        );
                        this.isLoggedIn = false;
                    })
                    .finally(() => {
                        this.loading = false; // Finaliza o loading
                    });
            } else {
                this.isLoggedIn = false;
                this.loading = false; // Finaliza o loading
            }
        },
        logout() {
            localStorage.removeItem("token");
            this.isLoggedIn = false;
            this.$router.push("/");
        },
    },
};
</script>

<style scoped>
/* Adicione estilos personalizados, se necessário */
</style>
