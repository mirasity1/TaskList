<template>
    <div>
      <h2 class="text-2xl font-bold text-blue-600">Gerenciar Status</h2>
      <form @submit.prevent="addStatus">
        <input v-model="newStatus" placeholder="Novo Status" class="p-2 border rounded-md" />
        <button type="submit" class="px-4 py-2 ml-2 text-white bg-green-600 rounded-md">Adicionar</button>
      </form>
      <ul class="mt-4">
        <li v-for="status in statuses" :key="status.id" class="flex items-center justify-between p-2 mb-2 bg-gray-100 rounded-md">
          <span>{{ status.name }}</span>
          <button @click="deleteStatus(status.id)" class="px-2 py-1 text-white bg-red-600 rounded-md">Excluir</button>
        </li>
      </ul>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        statuses: [],
        newStatus: '',
      };
    },
    methods: {
      fetchStatuses() {
        axios.get('/api/statuses')
          .then(response => {
            this.statuses = response.data;
          })
          .catch(error => {
            console.error('Erro ao buscar status:', error);
          });
      },
      addStatus() {
        axios.post('/api/statuses', { name: this.newStatus })
          .then(response => {
            this.statuses.push(response.data);
            this.newStatus = ''; // Limpa o campo
          })
          .catch(error => {
            console.error('Erro ao adicionar status:', error);
          });
      },
      deleteStatus(id) {
        axios.delete(`/api/statuses/${id}`)
          .then(() => {
            this.statuses = this.statuses.filter(status => status.id !== id);
          })
          .catch(error => {
            console.error('Erro ao excluir status:', error);
          });
      },
    },
    mounted() {
      this.fetchStatuses();
    },
  };
  </script>
  