<template>
  <div class="container p-6 mx-auto">
    <!-- Botão de Voltar -->
    <div class="mb-4">
      <button
        @click="goBack"
        class="px-4 py-2 text-white transition duration-200 bg-blue-500 rounded hover:bg-blue-600"
      >
        <font-awesome-icon icon="fa-solid fa-arrow-left" class="mr-2" />
        Voltar
      </button>
    </div>

    <div v-if="task" class="p-4 bg-white rounded-lg shadow-md">
      <h2 class="text-2xl font-semibold text-gray-800">
        <font-awesome-icon icon="fa-solid fa-tasks" class="mr-2" />
        {{ task.title }}
      </h2>
      <p class="mt-2 text-gray-600">
        <font-awesome-icon icon="fa-solid fa-align-left" class="mr-2" />
        {{ task.description }}
      </p>
      <p class="mt-4 text-lg">
        Status: 
        <span :class="{'text-green-600': task.is_completed, 'text-red-600': !task.is_completed}">
          <font-awesome-icon 
            :icon="task.is_completed ? 'fa-solid fa-check-circle' : 'fa-solid fa-times-circle'" 
            class="mr-1"
          />
          {{ task.is_completed ? 'Completa' : 'Incompleta' }}
        </span>
      </p>
    </div>
    
    <div v-else-if="error" class="flex items-center p-4 text-red-700 bg-red-100 rounded-md">
      <font-awesome-icon icon="fa-solid fa-exclamation-triangle" class="mr-2" />
      <p class="font-medium">{{ error }}</p>
    </div>
    
    <div v-else class="text-center text-gray-500">
      <font-awesome-icon icon="fa-solid fa-spinner fa-spin" class="text-3xl" />
      <p>Carregando tarefa...</p>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

export default {
  components: {
    FontAwesomeIcon
  },
  data() {
    return {
      task: null,
      error: null,
    };
  },
  
  methods: {
    async fetchTask() {
      const uuid = this.$route.params.uuid;

      try {
        const token = localStorage.getItem('token');
        const config = token
          ? { headers: { Authorization: `Bearer ${token}` } }
          : {};

        const response = await axios.get(`/api/tasksv1/${uuid}`, config);
        this.task = response.data;
      } catch (error) {
        this.handleError(error);
      }
    },
    
    handleError(error) {
      if (error.response) {
        if (error.response.status === 401) {
          this.error = "Você não tem autorização para acessar esta tarefa.";
        } else if (error.response.status === 404) {
          this.error = "Tarefa não encontrada.";
        } else {
          this.error = "Erro ao buscar detalhes da tarefa.";
        }
      } else {
        this.error = "Erro de rede ou servidor.";
      }
      console.error("Erro ao buscar detalhes da tarefa:", error);
    },

    // Método para voltar à página anterior
    goBack() {
      this.$router.go(-1); // Volta uma página no histórico
    }
  },
  
  mounted() {
    this.fetchTask();
  }
};
</script>

<style scoped>
.container {
  max-width: 600px;
}
.text-red-500 {
  color: #f56565;
}
.bg-red-100 {
  background-color: #fed7d7;
}
</style>
