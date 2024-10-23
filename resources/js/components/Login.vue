<template>
    <div
        class="flex items-center justify-center min-h-screen px-4 py-12 bg-gray-100 sm:px-6 lg:px-8"
    >
        <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-md">
            <h2 class="mb-6 text-2xl font-bold text-center text-blue-600">
                Login
            </h2>
            <form @submit.prevent="login">
                <div class="mb-4">
                    <label
                        for="email"
                        class="block text-sm font-medium text-gray-700"
                        >Email</label
                    >
                    <input
                        type="email"
                        v-model="email"
                        id="email"
                        required
                        class="block w-full p-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-500"
                    />
                </div>
                <div class="mb-4">
                    <label
                        for="password"
                        class="block text-sm font-medium text-gray-700"
                        >Senha</label
                    >
                    <input
                        type="password"
                        v-model="password"
                        id="password"
                        required
                        class="block w-full p-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-500"
                    />
                </div>
                <button
                    type="submit"
                    class="w-full px-4 py-2 font-bold text-white transition duration-200 bg-blue-600 rounded hover:bg-blue-500"
                >
                    Entrar
                </button>
                <!-- Mensagem de erro -->
                <div
                    v-if="error"
                    class="p-4 mt-4 text-center bg-red-100 rounded-md"
                >
                    <p class="font-semibold text-red-700">{{ error }}</p>
                </div>
            </form>
            <p class="mt-4 text-center text-gray-600">
                Não tem uma conta?
                <router-link
                    to="/register"
                    class="text-blue-600 hover:underline"
                    >Registrar</router-link
                >
            </p>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            email: "",
            password: "",
            // error if there is any
            error: "",
        };
    },
    methods: {
        login() {
            axios
                .post("/login", {
                    email: this.email,
                    password: this.password,
                })
                .then((response) => {
                    // Armazenar o token no localStorage
                    localStorage.setItem("token", response.data.token); // Salvar o token
                    localStorage.setItem('userId', response.data.user.id); // Armazena o ID do usuário logado
                    // console log response.data

                    this.checkAuth(); // Verifica se o usuário está autenticado
                    this.$router.push({ path: "/" });
                })
                .catch((error) => {
                    if (error.response) {
                        console.error("Erro ao logar:", error.response.data);
                        // show erro
                        this.error =
                            "Erro ao logar: " + error.response.data.message;
                    } else if (error.request) {
                        console.error(
                            "Erro: A requisição foi feita, mas não houve resposta"
                        );
                        // show erro
                        this.error =
                            "Erro: A requisição foi feita, mas não houve resposta";
                    } else {
                        console.error("Erro:", error.message);
                        // show erro
                        this.error = "Erro: " + error.message;
                    }
                });
        },
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
                        // erro
                        this.error =
                            "Erro ao verificar autenticação: " +
                            (error.response
                                ? error.response.data
                                : error.message);
                    });
            }
        },
    },
};
</script>

<style scoped>
/* Adicione estilos personalizados, se necessário */
</style>
