<template>
    <div class="container p-6 mx-auto bg-white rounded-lg shadow-md">
        <!-- Barra de Filtros e Ordenação -->
        <div class="flex flex-wrap items-center justify-between p-4 mb-6 rounded-lg shadow-sm bg-gray-50">
            <div class="flex space-x-4">
                <!-- Botões de Filtro -->
                <button class="px-4 py-2 font-medium transition-all duration-200 rounded-md"
                    :class="filterStatusClass('all')" @click="filterTasks('all')">
                    Todos
                </button>
                <button class="px-4 py-2 font-medium transition-all duration-200 rounded-md"
                    :class="filterStatusClass('is_completed')" @click="filterTasks('is_completed')">
                    Completos
                </button>
                <button class="px-4 py-2 font-medium transition-all duration-200 rounded-md"
                    :class="filterStatusClass('incomplete')" @click="filterTasks('incomplete')">
                    Incompletos
                </button>
                <button class="px-4 py-2 font-medium transition-all duration-200 rounded-md"
                    :class="filterStatusClass('my_tasks')" @click="filterTasks('my_tasks')">
                    Minhas Tarefas
                </button>
            </div>

            <!-- Dropdown de Ordenação -->
            <div class="mt-4 md:mt-0">
                <label class="block mb-1 font-semibold text-gray-600">Ordenar por:</label>
                <select v-model="sortOrder" @change="sortTasks"
                    class="w-full px-4 py-2 border rounded-md shadow-sm focus:outline-none focus:ring focus:border-blue-300">
                    <option value="">Selecione...</option>
                    <option value="asc">Mais antigo</option>
                    <option value="desc">Mais recente</option>
                </select>
            </div>
        </div>
        <div v-if="loading" class="loading-indicator">
            <div class="spinner"></div>
            <p>Carregando tarefas...</p>
        </div>
        <!-- Lista de Tarefas -->
        <ul class="space-y-4 task-list">
            <li v-for="task in filteredTasks" :key="task.uuid"
                :class="[
                    'p-4 transition-colors duration-200 border rounded-lg shadow-sm cursor-pointer',
                    task.user_id === loggedUserId ? 'bg-green-100' : 'bg-gray-50'
                ]"
                @click="task.user_id === loggedUserId ? openEditModal(task) : showTaskDetails(task.uuid)">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            {{ task . title }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            Responsável: {{ task . user_name }}
                        </p>
                    </div>
                    <!-- butao para copiar o link da tarefa -->
                    <div class="flex items-center">
                        <span class="ml-2 mr-2 text-sm"
                            :class="{
                                'text-green-600': task.is_public,
                                'text-red-600': !task.is_public,
                            }">
                            {{ task . is_public ? '🌐 Público' : '🔑 Privado' }}
                        </span>
                        <button v-if="task.is_public" @click.stop="copyTaskLink(task.uuid)"
                            class="text-gray-500 hover:text-blue-600">
                            <font-awesome-icon icon="fa-solid fa-link" />
                        </button>
                        <span class="ml-4 text-xl">{{ task . is_completed ? '✅' : '❌' }}</span>
                    </div>
                </div>
                <p class="mt-2 text-gray-600">{{ task . description }}</p>
            </li>
        </ul>

        <!-- Botão para Criar Nova Tarefa -->
        <div class="mt-6">
            <button @click="openModal"
                class="px-4 py-2 font-medium text-white transition duration-200 bg-blue-500 rounded hover:bg-blue-600">
                + Criar Nova Tarefa
            </button>
            <!--  adiciona um botao para ir para / -->
            <router-link to="/" class="px-4 py-2 ml-2 font-medium text-white transition duration-200 bg-red-500 rounded hover:bg-red-600">
                Voltar
            </router-link>
        </div>

        <!-- Modal para Criar Nova Tarefa -->
        <transition name="modal">
            <div v-if="showModal" class="modal-overlay">
                <div class="modal">
                    <h3 class="text-lg font-semibold">Criar Nova Tarefa</h3>
                    <div class="mt-4">
                        <label>Título</label>
                        <input v-model="newTask.title" class="w-full px-3 py-2 border rounded-md" type="text" />
                    </div>
                    <div class="mt-4">
                        <label>Descrição</label>
                        <textarea v-model="newTask.description" class="w-full px-3 py-2 border rounded-md"></textarea>
                    </div>
                    <div class="flex items-center mt-4">
                        <input v-model="newTask.is_public" type="checkbox" class="mr-2" />
                        <label>Visível para todos</label>
                    </div>
                    <div class="flex items-center mt-4">
                        <input v-model="newTask.is_completed" type="checkbox" class="mr-2" />
                        <label>Completa</label>
                    </div>
                    <div class="mt-6">
                        <button @click="createTask"
                            class="px-4 py-2 font-medium text-white transition duration-200 bg-green-500 rounded hover:bg-green-600">
                            Criar Tarefa
                        </button>
                        <button @click="closeModal"
                            class="px-4 py-2 ml-2 font-medium text-white transition duration-200 bg-red-500 rounded hover:bg-red-600">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </transition>
        <transition name="modal">
            <div v-if="showEditModal" class="modal-overlay">
                <div class="modal">
                    <h3 class="text-lg font-semibold">Editar Tarefa</h3>
                    <div class="mt-4">
                        <label>Título</label>
                        <input v-model="editTask.title" class="w-full px-3 py-2 border rounded-md" type="text" />
                    </div>
                    <div class="mt-4">
                        <label>Descrição</label>
                        <textarea v-model="editTask.description" class="w-full px-3 py-2 border rounded-md"></textarea>
                    </div>
                    <div class="flex items-center mt-4">
                        <input v-model="editTask.is_public" type="checkbox" class="mr-2" />
                        <label>Visível para todos</label>
                    </div>
                    <div class="flex items-center mt-4">
                        <input v-model="editTask.is_completed" type="checkbox" class="mr-2" />
                        <label>Completa</label>
                    </div>
                    <div class="mt-6">
                        <button @click="updateTask"
                            class="px-4 py-2 font-medium text-white transition duration-200 bg-blue-500 rounded hover:bg-blue-600">
                            Atualizar Tarefa
                        </button>
                        <button @click="closeEditModal"
                            class="px-4 py-2 ml-2 font-medium text-white transition duration-200 bg-red-500 rounded hover:bg-red-600">
                            Cancelar
                        </button>
                        <button @click="deleteTask(editTask.uuid)"
                            class="px-4 py-2 ml-2 font-medium text-white transition duration-200 bg-red-700 rounded hover:bg-red-800">
                            Apagar
                        </button>
                    </div>
                </div>
            </div>
        </transition>
        <div v-if="loadingEdit" class="loading-indicator">
            <div class="spinner"></div>
            <p>Editando tarefa...</p>
        </div>

        <!-- Loading para exclusão de tarefa -->
        <div v-if="loadingDelete" class="loading-indicator">
            <div class="spinner"></div>
            <p>Excluindo tarefa...</p>
        </div>
        <div v-if="loadingAdd" class="loading-indicator">
            <div class="spinner"></div>
            <p>Adicionando tarefa...</p>
        </div>
    </div>
</template>

<script>
    import axios from "axios";
    import {
        authMixin
    } from "../authMixin";

    export default {
        mixins: [authMixin],
        data() {
            return {
                tasks: [],
                filterStatus: "all",
                sortOrder: "",
                loggedUserId: null, // Armazena o ID do usuário logado
                showModal: false, // Controle do modal
                newTask: {
                    title: "",
                    description: "",
                    is_public: false,
                    is_completed: false,
                },
                editTask: {
                    title: "",
                    description: "",
                    is_public: false,
                    is_completed: false,
                },

                showEditModal: false, // Adicione esta linha
                loading: false, // Para carregamento de tarefas
                loadingEdit: false, // Para carregamento de edição de tarefa
                loadingDelete: false, // Para carregamento de exclusão de tarefa
                loadingAdd: false, // para carregamento de adicao de tarefa

            };
        },
        computed: {
            // Filtro dinâmico das tarefas baseado no status selecionado
            filteredTasks() {
                let filtered = this.tasks;

                // Filtrar tarefas por status
                if (this.filterStatus !== "all") {
                    if (this.filterStatus === "is_completed") {
                        filtered = filtered.filter((task) => task.is_completed === true);
                    } else if (this.filterStatus === "incomplete") {
                        filtered = filtered.filter((task) => task.is_completed === false);
                    } else if (this.filterStatus === "my_tasks") {
                        filtered = filtered.filter((task) => task.user_id === this.loggedUserId);
                    }
                }

                // Ordenar tarefas
                if (this.sortOrder) {
                    filtered = filtered.sort((a, b) => {
                        if (this.sortOrder === "asc") {
                            return new Date(a.created_at) - new Date(b.created_at);
                        } else {
                            return new Date(b.created_at) - new Date(a.created_at);
                        }
                    });
                }

                return filtered;
            },
        },
        methods: {
            fetchTasks() {
                this.loading = true; // Começa o loading aqui
                axios
                    .get("/api/tasksv1", {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem("token")}`,
                        },
                    })
                    .then((response) => {
                        this.tasks = response.data;
                        console.log("Tarefas carregadas:", this
                            .tasks); // Verifique se as tarefas estão sendo carregadas
                        this.loading = false;
                    })
                    .catch((error) => {
                        console.error("Erro ao buscar tarefas:", error);
                    })
                    .finally(() => {
                        this.loading = false; // Para sempre esconder o loading, independentemente do resultado
                    });
            },
            getUserNameById(userId) {
                // Você pode armazenar os usuários em uma variável ou realizar uma chamada à API para obter os dados do usuário
                const user = this.users.find(user => user.id === userId);
                return user ? user.name : 'Desconhecido';
            },
            filterTasks(status) {
                this.loading = true;
                this.filterStatus = status;
                this.fetchTasks(); // Chama fetchTasks para obter as tarefas filtradas
            },
            sortTasks() {

                this.loading = true;
                this.fetchTasks();
            },
            showTaskDetails(uuid) {
                this.$router.push(`/tasksv1/${uuid}`);
            },
            filterStatusClass(status) {
                return {
                    "bg-blue-500 text-white": this.filterStatus === status,
                    "hover:bg-blue-600": this.filterStatus !== status,
                };
            },
            copyTaskLink(uuid) {
                const link = `${window.location.origin}/tasksv1/${uuid}`;
                navigator.clipboard.writeText(link).then(() => {
                    alert("Link copiado para a área de transferência!");
                });
            },
            getUserId() {
                return localStorage.getItem("userId");
            },
            openModal() {
                this.showModal = true;
            },
            closeModal() {
                this.showModal = false;
                this.newTask = {
                    title: "",
                    description: "",
                    is_public: false,
                    is_completed: false,
                };
            },
            createTask() {
                this.loadingAdd = true;
                const taskData = {
                    ...this.newTask,
                    user_id: this.loggedUserId, // Definindo o usuário como responsável pela tarefa
                };
                // post with bearer token
                axios
                    .post("/api/tasksv1", taskData, {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem("token")}`,
                        },
                    })
                    .then((response) => {
                        const newTask = response.data.task;
                        this.tasks.unshift(newTask);
                        this.closeModal();
                    })
                    .catch((error) => {
                        console.error("Erro ao criar tarefa:", error);
                    })
                    .finally(() => {
                        this.loadingAdd = false; // Para sempre esconder o loading
                    });
            },
            openEditModal(task) {
                this.editTask = {
                    ...task
                };
                this.showEditModal = true;
            },
            closeEditModal() {
                this.showEditModal = false;
                this.editTask = {
                    uuid: null,
                    title: "",
                    description: "",
                    is_public: false,
                    is_completed: false
                };
            },
            updateTask() {
                this.loadingEdit = true;
                axios.put(`/api/tasksv1/${this.editTask.uuid}`, this.editTask, {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem("token")}`
                        },
                    })
                    .then(response => {
                        // return response()->json([
                        //     'message' => 'Tarefa atualizada com sucesso',
                        //     'task' => $task // Retornando a tarefa atualizada
                        // ]);
                        const updatedTask = response.data.task;
                        const index = this.tasks.findIndex(task => task.uuid === updatedTask.uuid);
                        this.tasks.splice(index, 1, updatedTask);
                        this.closeEditModal();

                    })
                    .catch(error => {
                        console.error("Erro ao atualizar tarefa:", error);
                    })
                    .finally(() => {
                        this.loadingEdit = false; // Para sempre esconder o loading
                    });
            },
            deleteTask(uuid) {
                this.loadingDelete = true; // Começa o loading aqui
                if (confirm("Tem certeza que deseja apagar esta tarefa?")) {
                    axios.delete(`/api/tasksv1/${uuid}`, {
                            headers: {
                                Authorization: `Bearer ${localStorage.getItem("token")}`
                            },
                        })
                        .then(() => {
                            this.tasks = this.tasks.filter(task => task.uuid !== uuid);
                            this.closeEditModal();
                        })
                        .catch(error => {
                            console.error("Erro ao apagar tarefa:", error);
                        })
                        .finally(() => {
                            this.loadingDelete = false; // Para sempre esconder o loading
                        });
                } else {
                    this.loadingDelete = false; // Se o usuário cancelar a ação
                }
            }
        },
        mounted() {
            this.loading = true;
            this.checkAuthentication(); // Verifique a autenticação ao montar o componente
            this.isAuth = this.checkAuthentication();
            this.loggedUserId = this.getUserId();
            setTimeout(() => {
                this.fetchTasks();
            }, 200);
        },
    };
</script>

<style scoped>
    .task-list {
        list-style-type: none;
        padding: 0;
    }

    .task-item {
        transition: background-color 0.3s ease;
    }

    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .modal {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        max-width: 500px;
        width: 100%;
    }

    .spinner {
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-top: 4px solid #3498db;
        /* Cor do spinner */
        border-radius: 50%;
        width: 40px;
        /* Tamanho do spinner */
        height: 40px;
        /* Tamanho do spinner */
        animation: spin 1s linear infinite;
        display: inline-block;
        /* Para centralizar */
        margin: 0 auto;
        /* Para centralizar */
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
    .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10; /* Ajuste o z-index do modal */
}

.modal {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    max-width: 500px;
    width: 100%;
    z-index: 20; /* Z-index do conteúdo do modal */
}

.loading-indicator {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    height: 100%;
    position: fixed; /* Torna o loading fixo na tela */
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.7); /* Fundo semi-transparente para o loading */
    z-index: 30; /* Z-index do loading */
}
</style>
