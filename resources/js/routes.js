import { createRouter, createWebHistory } from 'vue-router';
import TaskList from './components/TaskList.vue';
import Login from './components/Login.vue';
import Register from './components/Register.vue';
import Homepage from './components/HomePage.vue';
import TaskDetail from './components/TaskDetail.vue';
import TaskListV1 from './components/TaskListV1.vue';

const routes = [
    // home route is home page.vue 
    {
        path: '/',
        name: 'Homepage',
        component: Homepage,
    },
    {
        path: '/tasks2',
        name: 'TaskList',
        component: TaskList,
    },{
        path: '/tasks',
        name: 'TaskListV1',
        component: TaskListV1,
    },
    {
        path: '/tasksv1/:uuid',
        name: 'TaskDetails',
        component: TaskDetail
      },
    {
        path: '/login',
        name: 'Login',
        component: Login,
    },
    {
        path: '/register',
        name: 'Register',
        component: Register,
    },
    {
        path: '/tasks/:id',
        name: 'TaskDetail',
        component: TaskDetail,
        props: true
      },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
