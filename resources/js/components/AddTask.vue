<template>
    <div>
      <h1>Add New Task</h1>
      <form @submit.prevent="addTask">
        <div>
          <label>Title</label>
          <input v-model="newTaskTitle" type="text" required />
        </div>
        <div>
          <label>Description</label>
          <textarea v-model="newTaskDescription" required></textarea>
        </div>
        <button type="submit">Add Task</button>
      </form>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        newTaskTitle: '',
        newTaskDescription: '',
      };
    },
    methods: {
      async addTask() {
        try {
          let response = await fetch('http://127.0.0.1:8000/api/tasks', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              title: this.newTaskTitle,
              description: this.newTaskDescription,
              completed: false,
            }),
          });
          let data = await response.json();
          this.$router.push({ name: 'TaskList' });
        } catch (error) {
          console.error("Failed to add task", error);
        }
      },
    },
  };
  </script>
  