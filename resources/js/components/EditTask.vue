<template>
    <div>
      <h1>Edit Task</h1>
      <form @submit.prevent="updateTask">
        <div>
          <label>Title</label>
          <input v-model="taskTitle" type="text" required />
        </div>
        <div>
          <label>Description</label>
          <textarea v-model="taskDescription" required></textarea>
        </div>
        <button type="submit">Update Task</button>
      </form>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        taskTitle: '',
        taskDescription: '',
      };
    },
    async mounted() {
      const taskId = this.$route.params.id;
      try {
        let response = await fetch(`http://127.0.0.1:8000/api/tasks/${taskId}`);
        let task = await response.json();
        this.taskTitle = task.title;
        this.taskDescription = task.description;
      } catch (error) {
        console.error("Failed to fetch task", error);
      }
    },
    methods: {
      async updateTask() {
        const taskId = this.$route.params.id;
        try {
          await fetch(`http://127.0.0.1:8000/api/tasks/${taskId}`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              title: this.taskTitle,
              description: this.taskDescription,
              completed: false,
            }),
          });
          this.$router.push({ name: 'TaskList' });
        } catch (error) {
          console.error("Failed to update task", error);
        }
      },
    },
  };
  </script>
  