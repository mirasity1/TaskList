import { createApp } from 'vue';
import App from './App.vue'; // O componente principal
import router from './routes';
import '../assets/tailwind.css'

import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';


import { faTimes,faSignInAlt, faSignOutAlt, faLink,faArrowLeft,faSpinner,faTasks, faAlignLeft,faCheckCircle,faTimesCircle} from '@fortawesome/free-solid-svg-icons';


library.add(faTimes,faSignInAlt,faSignOutAlt,faLink,faArrowLeft,faSpinner,faTasks,faAlignLeft,faCheckCircle,faTimesCircle);

const app = createApp(App);
app.component('font-awesome-icon', FontAwesomeIcon)
app.use(router);
app.mount('#app');
