// authMixin.js
import axios from 'axios';

export const authMixin = {
  methods: {
    async isAuthenticated() {
      const token = localStorage.getItem('token');
      // jwt auth token ver se é valido
      if (token) {
        // Verificar se o token expirou
        const payload = JSON.parse(atob(token.split('.')[1]));
        if (payload.exp < Date.now() / 1000) {
          // Token expirado
          console.log('Token expirado');
          // Tente renovar o token
          await this.refreshToken();
        }
        return true; // Retorna verdadeiro se estiver autenticado
      }
      return false; // Retorna falso se não houver token
    },

    async refreshToken() {
      try {
        const response = await axios.post('/api/auth/refresh', {}, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });
        // Armazenar o novo token no localStorage
        localStorage.setItem('token', response.data.token);
        console.log('Token renovado com sucesso');
      } catch (error) {
        console.error('Erro ao renovar o token:', error);
        // Você pode redirecionar para a página de login se falhar ao renovar o token
        this.$router.push('/login');
      }
    },

    checkAuthentication() {
      if (!this.isAuthenticated()) {
        // Redirecionar ou tomar ação se não autenticado
        console.log("Usuário não autenticado.");
        // Exemplo: redirecionar para a página de login
        // this.$router.push('/login');
        return false; // Retorna falso se não estiver autenticado
      }
      return true; // Retorna verdadeiro se estiver autenticado
    },
  },
};
