<template>
    <div class="container px-6 mx-auto mt-8">
        <h1 class="mb-6 text-4xl font-bold text-center text-blue-600">
            Lista de Tarefas
        </h1>

        <!-- Indicador de Carregamento -->
        <div v-if="loading" class="flex items-center justify-center h-64">
            <div class="w-16 h-16 border-b-2 border-blue-600 rounded-full animate-spin"></div>
            <p class="ml-4 text-lg">...</p>
        </div>

        <!-- Filtro e Ordenação -->
        <div v-else>
            <!-- Botão de login no canto superior direito -->
            <div v-if="!isAuth" class="absolute top-4 right-6">
                <router-link to="/login"
                    class="flex items-center px-4 py-2 text-white transition-all bg-blue-600 rounded-lg shadow-md hover:bg-blue-700">
                    <!-- Ícone de login (font awesome) -->
                    <font-awesome-icon :icon="['fas', 'sign-in-alt']" class="mr-2" />
                    Login
                </router-link>
            </div>
            <!-- else logout -->
            <div v-else class="absolute flex items-center justify-between top-4 right-6">
                <!-- Botão para voltar -->
                <router-link to="/"
                    class="flex items-center px-4 py-2 text-white transition-all bg-blue-600 rounded-lg shadow-md hover:bg-blue-700">
                    <!-- Ícone de voltar (font awesome) -->
                    <font-awesome-icon :icon="['fas', 'arrow-left']" class="mr-2" />
                    Voltar
                </router-link>

                <!-- Botão para logout -->
                <button @click="logout"
                    class="flex items-center px-4 py-2 ml-4 text-white transition-all bg-red-600 rounded-lg shadow-md hover:bg-red-700">
                    <!-- Ícone de logout (font awesome) -->
                    <font-awesome-icon :icon="['fas', 'sign-out-alt']" class="mr-2" />
                    Logout
                </button>
            </div>


            <div class="flex flex-wrap gap-6">
                <draggable :list="sortedStatuses" group="statuses" @end="onStatusDragEnd" item-key="id"
                    :disabled="!isAuth" class="flex gap-6 flex-nowrap">
                    <template v-slot:item="{ element }">
                        <div class="flex-shrink-0 w-64 h-full p-4 bg-gray-100 rounded-lg column" :data-id="element.id">
                            <h2 class="mb-2 text-2xl font-bold text-center text-blue-600">
                                {{ element . name }}
                            </h2>
                            <!-- atribuir id do status para conseguir adicionar a tarefa ao status por drag and drop -->
                            <draggable :list="tasks.filter(task => task.status_id === element.id)" group="tasks"
                                @end="onTaskDragEnd" item-key="id" class="task-container" :data-id="element.id"
                                :disabled="!isAuth">
                                
                                <template v-slot:item="{ element: task }" 
                                    :class="task.is_public ? 'bg-green-100' : 'bg-red-100'"
                                >
                                <!-- atribuir o id da tarefa á div para conseguir fazer  o drag and drop para ordem-->

                                    <div :data-id="task.id" @click="showDetails(task.id)"
                                        class="relative p-4 mt-2 transition-transform transform bg-white rounded-lg shadow-lg cursor-pointer hover:scale-105 task">
                                        <div
                                            class="absolute inset-0 flex items-center justify-center bg-gray-200 opacity-50">
                                        </div>
                                        <h2 class="text-lg font-semibold text-gray-600">Titulo</h2>
                                        <h5 class="mb-2 text-xl font-semibold text-blue-800">{{ task . title }}</h5>
                                        <h2 class="text-lg font-semibold text-gray-600">Descrição</h2>
                                        <p class="mb-4 text-gray-700">{{ task . description }}</p>
                                        <h2 class="text-lg font-semibold text-gray-600">Criada por:</h2>
                                        <p class="mb-4">
                                            <span
                                                class="font-semibold">{{ task . user_name || 'Usuário desconhecido' }}</span>
                                        </p>
                                        <p class="mb-4">Visibilidade:
                                            <span :class="task.is_public ? 'text-blue-600' : 'text-gray-600'">
                                                {{ task . is_public ? 'Pública' : 'Privada' }}
                                            </span>
                                        </p>
                                        <button v-if="this.isAuth" @click.stop="deleteTask(task.id)"
                                            class="absolute text-red-600 top-2 right-2 hover:text-red-800">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        <span class="text-gray-600">Clique para ver</span>
                                    </div>
                                </template>
                            </draggable>

                            <button v-if="this.isAuth" @click="addTask(element.id)"
                                class="flex items-center justify-center w-full py-2 mt-2 text-lg font-bold text-blue-600 bg-white border rounded-lg shadow hover:bg-blue-50 add-task-button">
                                + Adicionar Tarefa
                            </button>

                            <div v-if="!tasks.filter(task => task.status_id === element.id).length"
                                class="p-4 mt-2 text-center rounded-lg bg-yellow-50">
                                <p class="font-semibold text-yellow-700">Nenhuma tarefa nesta coluna.</p>
                            </div>
                        </div>
                    </template>
                </draggable>


                <div v-if="isAuthenticated()" class="flex-shrink-0 w-64 p-4 bg-gray-200 rounded-lg">
                    <button @click="addStatus"
                        class="flex items-center justify-center w-full py-8 text-xl font-bold text-blue-600 bg-white border rounded-lg shadow hover:bg-blue-50">
                        + Adicionar Status
                    </button>
                </div>
            </div>
            <!-- Modal para mostrar os alertas ao utilizador -->
            <div v-if="alertModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-50"
                @click.self="alertModal = false">
                <div class="relative w-full max-w-md p-6 bg-white rounded-lg shadow-lg">
                    <h2 class="mb-4 text-2xl font-bold">Alerta</h2>
                    <p class="text-lg">{{ alertMessage }}</p>
                </div>
            </div>

            <!-- Modal para mostrar mensagem de sucesso com icon de fechar e ok ao utilizador -->
            <div v-if="successModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-50"
                @click.self="successModal = false">
                <div class="relative w-full max-w-md p-6 bg-white rounded-lg shadow-lg">
                    <h2 class="mb-4 text-2xl font-bold">Sucesso</h2>
                    <p class="text-lg">{{ successMessage }}</p>
                    <button @click="successModal = false"
                        class="px-4 py-2 mt-4 text-white bg-blue-600 rounded-md hover:bg-blue-700">
                        OK
                    </button>
                </div>
            </div>

            <div v-if="showAddTaskModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-50"
                @click.self="closeAddTaskModal">
                <div class="relative w-full max-w-md p-6 bg-white rounded-lg shadow-lg">
                    <!-- Botão de Fechar no canto superior direito -->
                    <button @click="closeAddTaskModal" class="absolute text-gray-500 top-2 right-2 hover:text-gray-700">
                        <font-awesome-icon :icon="['fas', 'times']" class="text-xl"></font-awesome-icon>
                    </button>

                    <h2 class="mb-4 text-2xl font-bold">
                        Adicionar Nova Tarefa
                    </h2>

                    <!-- Formulário de Criação de Tarefa -->
                    <div>
                        <label class="block mb-2 text-lg font-semibold">Título</label>
                        <input v-model="newTaskTitle" type="text" class="w-full p-2 mb-4 border rounded-md shadow-md"
                            placeholder="Título da tarefa" />

                        <label class="block mb-2 text-lg font-semibold">Descrição</label>
                        <textarea v-model="newTaskDescription" class="w-full p-2 mb-4 border rounded-md shadow-md"
                            placeholder="Descrição da tarefa"></textarea>
                        <!-- definir se é public ou nao sendo privada a preselecionada -->
                        <label class="block mb-2 text-lg font-semibold">Visibilidade</label>
                        <select v-model="newTaskVisibility" class="w-full p-2 mb-4 border rounded-md shadow-md">
                            <option value="0">Privada</option>
                            <option value="1">Pública</option>
                        </select>

                        <!-- Status Pré-Selecionado -->
                        <p>
                            Status selecionado:
                            <span class="font-bold text-blue-600">{{ selectedStatusName }}</span>
                        </p>
                    </div>

                    <!-- Botão para Adicionar a Tarefa -->
                    <button @click="submitTask"
                        class="px-4 py-2 mt-4 text-white bg-blue-600 rounded-md hover:bg-blue-700">
                        Adicionar Tarefa
                    </button>
                </div>
            </div>
            <!-- Modal para Detalhes da Tarefa -->
            <div v-if="selectedTask"
                class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-50"
                @click.self="closeDetails">
                <div class="relative w-full max-w-md p-6 bg-white rounded-lg shadow-lg">
                    <!-- Botão de Fechar no canto superior direito -->
                    <button @click="closeDetails" class="absolute text-gray-500 top-2 right-2 hover:text-gray-700">
                        <font-awesome-icon :icon="['fas', 'times']" class="text-xl"></font-awesome-icon>
                    </button>
                    <h2 class="text-lg font-semibold text-gray-600">Titulo</h2>

                    <!-- Se o usuário for o dono da tarefa, exibir campos de edição -->
                    <template v-if="isAuth && selectedTask.user_id === userId">
                        <input v-model="selectedTask.title" class="w-full p-2 mb-4 border rounded-md shadow-md"
                            placeholder="Editar título..." />
                        <h2 class="text-lg font-semibold text-gray-600">
                            Descrição
                        </h2>
                        <textarea v-model="selectedTask.description" class="w-full p-2 mb-4 border rounded-md shadow-md"
                            placeholder="Editar descrição..."></textarea>
                        <div class="flex items-center mt-2">
                            <label class="mr-2">Visibilidade:</label>
                            <select v-model="selectedTask.is_public" class="border rounded-md">
                                <option :value="true">Pública</option>
                                <option :value="false">Privada</option>
                            </select>
                        </div>
                        <div class="mt-2">
                            <label class="mr-2">Estado:</label>
                            <select v-model="selectedTask.status_id" class="border rounded-md">
                                <option v-for="status in statuses" :key="status.id" :value="status.id">
                                    {{ status . name }}
                                </option>
                            </select>
                        </div>
                        <button @click="updateTask(selectedTask)"
                            class="px-4 py-2 mt-4 text-white bg-blue-600 rounded-md hover:bg-blue-700">
                            Atualizar Tarefa
                        </button>
                    </template>

                    <template v-else>
                        <p class="mb-4">{{ selectedTask . description }}</p>
                        <p>
                            Visibilidade:
                            {{ selectedTask . is_public ? 'Pública' : 'Privada' }}
                        </p>
                    </template>

                    <!-- Comentários to be implemented no time... -->
                    <!-- <div
                        v-if="
                            selectedTask.comments &&
                            selectedTask.comments.length
                        ">
                        <h3 class="mt-4 mb-2 text-xl font-semibold">
                            Comentários
                        </h3>
                        <ul>
                            <li v-for="comment in selectedTask.comments" :key="comment.id"
                                class="p-2 mb-2 bg-gray-100 rounded-lg">
                                <p class="text-gray-800">{{ comment . text }}</p>
                            </li>
                        </ul>
                    </div>
                    <div v-else>
                        <p class="mt-4 text-gray-600">
                            Nenhum comentário ainda.
                        </p>
                    </div> -->

                    <!-- Adicionar Comentário -->
                    <!-- <div v-if="isAuth">
                        <textarea v-model="newComment" class="w-full p-2 mt-4 border rounded-md shadow-md"
                            placeholder="Adicionar comentário..."></textarea>
                        <button @click="addComment(selectedTask.id)"
                            class="px-4 py-2 mt-4 text-white bg-blue-600 rounded-md hover:bg-blue-700">
                            Adicionar Comentário
                        </button>
                    </div> -->

                    <button v-if="isAuth && selectedTask.user_id === userId" @click="deleteTask(selectedTask.id)"
                        class="px-4 py-2 mt-4 ml-3 text-white bg-red-600 rounded-md hover:bg-red-700">
                        Excluir Tarefa
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from "axios";
    import draggable from "vuedraggable";
    import {
        authMixin
    } from "../authMixin";
    import {
        FontAwesomeIcon
    } from "@fortawesome/vue-fontawesome";
    
    export default {
        mixins: [authMixin],
        components: {
            draggable,
        },
        data() {
            return {
                tasks: [],
                statuses: [],
                filter: "",
                selectedStatus: "",
                sortOrder: "",
                selectedTask: null,
                newComment: "",
                showAddTaskModal: false,
                newTaskTitle: "",
                newTaskDescription: "",
                loading: true, // Adiciona a propriedade de carregamento
                isAuth: false,
                alertModal: false,
                alertMessage: "",
                // model de sucesso
                successModal: false,
                successMessage: "",
                newTaskVisibility: 0, // 0 para Privada
                // userId
                userId: null,
            };
        },
        methods: {
            // fetchComments(taskId) {
            //     axios
            //         .get(`/api/tasks/${taskId}/comments`, {
            //             headers: {
            //                 Authorization: `Bearer ${localStorage.getItem(
            //                 "token"
            //             )}`,
            //             },
            //         })
            //         .then((response) => {
            //             // Atribui os comentários à tarefa selecionada
            //             this.selectedTask.comments = response.data;
            //         })
            //         .catch((error) => {
            //             console.error("Erro ao buscar comentários:", error);
            //         });
            // },
            // fetchtasks
            fetchTasks() {
                // se tiver autenticado faz pedido com bearertoken
                if (this.isAuth) {
                    axios
                        .get("/api/tasks", {
                            headers: {
                                Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                            },
                        })
                        .then((response) => {
                            this.tasks = response.data;
                        })
                        .catch((error) => {
                            console.error("Erro ao buscar tarefas:", error);
                        });
                } else {
                    axios
                        .get("/api/tasks")
                        .then((response) => {
                            this.tasks = response.data;
                        })
                        .catch((error) => {
                            console.error("Erro ao buscar tarefas:", error);
                        });
                }
            },

            fetchStatuses() {
                this.loading = true; // Inicia o carregamento
                axios
                    .get("/api/statuses")
                    .then((response) => {
                        this.statuses = response.data;
                    })
                    .catch((error) => {
                        console.error("Erro ao buscar statuses:", error);
                    })
                    .finally(() => {
                        this.loading = false; // Termina o carregamento
                    });
            },
            onTaskDragEnd(evt) {
                this.loading = true;
                // Se estiver logado e for o dono da tarefa pode fazer isto
                if (this.isAuth) {

                    const taskId = evt.item.dataset.id;
                    // Encontre a tarefa correspondente ao ID
                    const task = this.tasks.find((task) => task.id === taskId);
                    if (task) {

                        // Obtenha o novo status ID (assumindo que o status é identificado pelo id do elemento da coluna)
                        const newStatusId = evt.to.dataset.id; // ID do novo status

                        // Atualizar a tarefa via API
                        axios
                            .put(
                                `/api/tasks/${taskId}`, {
                                    status_id: newStatusId, // Atualizar o status da tarefa
                                    order: evt.newIndex, // Atualizar a ordem se necessário
                                }, {
                                    headers: {
                                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                                    },
                                }
                            )
                            .then(() => {
                                this.fetchTasks(); // Recarregar as tarefas
                            })
                            .catch((error) => {
                                // Se for erro 403 modal a dizer que não tem permissão
                                if (error.response && error.response.status === 403) {
                                    this.alertMessage = "Não tem permissão para fazer esta ação";
                                    this.alertModal = true;
                                } else {
                                    console.error("Erro ao atualizar a tarefa:", error);
                                }
                            }).finally(() => {
                                // timeout
                                setTimeout(() => {
                                    this.loading = false;
                                }, 1000);
                            });
                    } else {
                        console.error("Tarefa não encontrada para o id:", taskId);
                    }
                }
            },

            // addComment(taskId) {
            //     if (this.newComment.trim() === "") return;

            //     axios
            //         .post(
            //             `/api/tasks/${taskId}/comments`, {
            //                 text: this.newComment,
            //             }, {
            //                 headers: {
            //                     Authorization: `Bearer ${localStorage.getItem(
            //                     "token"
            //                 )}`,
            //                 },
            //             }
            //         )
            //         .then(() => {
            //             this.newComment = "";
            //             this.fetchTasks();
            //         })
            //         .catch((error) => {
            //             console.error("Erro ao adicionar comentário:", error);
            //         });
            // },

            showDetails(taskId) {
                this.selectedTask = this.tasks.find((task) => task.id === taskId);

                // Busca os comentários da tarefa
                this.fetchComments(taskId);
            },

            closeDetails() {
                this.selectedTask = null;
            },
            addTask(statusId) {
                // Abrir o modal e definir o status selecionado
                this.selectedStatus = statusId;
                this.selectedStatusName = this.statuses.find(
                    (status) => status.id === statusId
                ).name;
                this.showAddTaskModal = true;
            },
            closeAddTaskModal() {
                // Fechar o modal de adição de tarefa
                this.showAddTaskModal = false;
                this.newTaskTitle = "";
                this.newTaskDescription = "";
            },
            // create task
            submitTask() {
                if (!this.newTaskTitle.trim() || !this.newTaskDescription.trim()) {
                    this.alertMessage = "Por favor, preencha todos os campos.";
                    this.alertModal = true;
                    return;
                }

                axios
                    .post(
                        "/api/tasks", {
                            title: this.newTaskTitle,
                            description: this.newTaskDescription,
                            status_id: this.selectedStatus,
                            is_public: this.newTaskVisibility,
                        }, {
                            headers: {
                                Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                            },
                        }
                    )
                    .then(() => {
                        this.closeAddTaskModal();
                        this.fetchTasks();
                    })
                    .catch((error) => {
                        console.error("Erro ao adicionar tarefa:", error);
                    });
            },
            // onStatusDragEnd
            onStatusDragEnd(evt) {
                // set loading
                this.loading = true;

                // Acessar o id usando o dataset
                const statusId = evt.item.dataset.id; // Acessando o data-id que foi definido
                const oldIndex = evt.oldIndex; // Índice antigo
                const newIndex = evt.newIndex; // Novo índice

                // Encontre o status correspondente
                const status = this.statuses.find(
                    (status) => status.id == statusId
                ); // Use == para comparação de tipos

                if (!status) {
                    console.error("Status não encontrado para o id:", statusId);
                    this.loading = false;

                    return; // Saia da função se o status não for encontrado
                }

                // Atualizar a ordem do status movido
                const updatedOrder = newIndex + 1; // nova ordem do status movido
                const oldOrder = oldIndex + 1; // ordem antiga do status

                // Atualizar a ordem do status movido via API
                axios
                    .put(
                        `/api/statuses/${statusId}`, {
                            order: updatedOrder,
                        }, {
                            headers: {
                                Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                            },
                        }
                    )
                    .then((response) => {


                        // Agora, precisamos atualizar a ordem do status que estava na nova posição
                        const statusAtNewIndex = this.statuses[newIndex]; // Status que ocupa a nova posição

                        if (statusAtNewIndex) {

                            // Atualizar a ordem do status que estava na nova posição
                            axios
                                .put(
                                    `/api/statuses/${statusAtNewIndex.id}`, {
                                        order: oldOrder,
                                    }, {
                                        headers: {
                                            Authorization: `Bearer ${localStorage.getItem(
                                            "token"
                                        )}`,
                                        },
                                    }
                                )
                                .then((response) => {

                                    this.loadStatuses(); // Recarregue os statuses
                                    // mensagem de sucesso e depois para o loading
                                    this.successMessage =
                                        "Status atualizado com sucesso!";
                                    this.successModal = true;
                                    this.loading = false;
                                })
                                .catch((error) => {
                                    console.error(
                                        "Erro ao atualizar o status na nova posição:",
                                        error.response ?
                                        error.response.data :
                                        error.message
                                    );
                                    this.loading = false;
                                });
                        } else {
                            this.loadStatuses(); // Recarregue os statuses se não houver status na nova posição
                            // mensagem de sucesso e depois para o loading
                            this.successMessage = "Status atualizado com sucesso!";
                            this.successModal = true;
                            this.loading = false;
                        }
                    })
                    .catch((error) => {
                        console.error(
                            "Erro ao atualizar o status:",
                            error.response ? error.response.data : error.message
                        );
                        this.loading = false;
                    });
            },
            loadStatuses() {
                axios
                    .get("/api/statuses", {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem(
                            "token"
                        )}`,
                        },
                    })
                    .then((response) => {
                        this.statuses = response.data.sort(
                            (a, b) => a.order - b.order
                        );
                    })
                    .catch((error) => {
                        console.error(
                            "Erro ao carregar status:",
                            error.response ? error.response.data : error.message
                        );
                    });
            },

            // logout
            logout() {
                localStorage.removeItem("token");
                this.isAuth = false;
                // push /
                this.$router.push("/");
            },
            // get userId
            getUserId() {
                return localStorage.getItem("userId");
            },
            // updateTask
            updateTask(task) {
                this.loading = true;

                axios
                    .put(`/api/tasks/${task.id}`, task, {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem(
                            "token"
                        )}`,
                        },
                    })
                    .then(() => {
                        this.selectedTask = null;
                        this.loading = false;
                        this.successMessage = "Tarefa atualizada com sucesso!";
                        this.successModal = true;
                        this.fetchTasks();
                    })
                    .catch((error) => {
                        console.error("Erro ao atualizar a tarefa:", error);
                    });
            },
            // isOwner
            isOwner(taskId) {
                return this.tasks.find((task) => tasks.id === taskId).user_id === this.userId;
            },
            // deleteTask
            deleteTask(taskId) {
                this.loading = true;

                axios
                    .delete(`/api/tasks/${taskId}`, {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem(
                            "token"
                        )}`,
                        },
                    })
                    .then(() => {
                        this.selectedTask = null;
                        this.loading = false;
                        this.successMessage = "Tarefa excluída com sucesso!";
                        this.successModal = true;
                        this.fetchTasks();
                    })
                    .catch((error) => {
                        console.error("Erro ao excluir a tarefa:", error);
                    });
            },
        },
        computed: {
            sortedStatuses() {
                return this.statuses.sort((a, b) => a.order - b.order);
            },
        },
        mounted() {
            this.checkAuthentication(); // Verifique a autenticação ao montar o componente
            // set is authenticado
            this.isAuth = this.checkAuthentication();
            // set userId
            this.userId = this.getUserId();
            // timeout and fetch tasks and status
            setTimeout(() => {
                this.fetchTasks();
                this.fetchStatuses();
            }, 1000);
        },
    };
</script>

<style scoped>
    /* Estilo para o indicador de carregamento */
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }

    .column {
        position: relative;
        /* Permite posicionar o botão em relação à coluna */
        height: 100vh;
        /* Coluna ocupa toda a altura da tela */
        overflow-y: auto;
        /* Permite rolagem vertical */
    }

    .add-task-button {
        margin-top: 1rem;
        /* Ajuste conforme necessário */
    }

    /* Defina uma altura mínima para o contêiner de tarefas para manter espaço */
    .task-container {
        min-height: 200px;
        /* Ajuste conforme necessário para acomodar suas tarefas */
    }

    .task {
        margin: 10px 0;
        /* Espaçamento entre as tarefas */
    }
</style>
