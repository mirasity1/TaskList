<template>
    <div class="flex items-center justify-center min-h-screen px-4 py-12 bg-gray-100">
      <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-md">
        <h2 class="mb-6 text-2xl font-bold text-center text-blue-600">Registrar</h2>
        <form @submit.prevent="register">
          <div class="mb-4">
            <label for="name" class="block mb-2 text-sm font-medium text-gray-700">Nome</label>
            <input
              type="text"
              v-model="name"
              id="name"
              class="block w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
              required
            />
          </div>
          <div class="mb-4">
            <label for="email" class="block mb-2 text-sm font-medium text-gray-700">Email</label>
            <input
              type="email"
              v-model="email"
              id="email"
              class="block w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
              required
            />
          </div>
          <div class="mb-4">
            <label for="password" class="block mb-2 text-sm font-medium text-gray-700">Senha</label>
            <input
              type="password"
              v-model="password"
              id="password"
              class="block w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
              required
            />
          </div>
          <div class="mb-6">
            <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-700">Confirme a Senha</label>
            <input
              type="password"
              v-model="password_confirmation"
              id="password_confirmation"
              class="block w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
              required
            />
          </div>
          <button type="submit" class="w-full px-4 py-2 font-bold text-white transition duration-200 bg-blue-600 rounded-lg hover:bg-blue-500">
            Registrar
          </button>
        </form>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  export default {
    data() {
      return {
        name: '',
        email: '',
        password: '',
        password_confirmation: '', // Adicionado
      };
    },
    methods: {
      register() {
        axios.post('/register', {
          name: this.name,
          email: this.email,
          password: this.password,
          password_confirmation: this.password_confirmation, // Adicionado
        })
        .then(response => {
          console.log('Registrado com sucesso:', response.data);
          localStorage.setItem('token', response.data.token);
          this.$router.push({ name: '' });

        })
        .catch(error => {
          console.error('Erro ao registrar:', error.response.data);
        });
      },
    },
  };
  </script>
  
  <style scoped>
  /* Adicione estilos personalizados, se necessário */
  </style>
  