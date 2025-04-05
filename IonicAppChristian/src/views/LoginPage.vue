<template>
  <ion-page>
    <Navbar title="Accedeix al teu compte"/>
    <ion-content :fullscreen="true">
      <ion-card v-if="!isLoggedIn" class="auth-card">
        <ion-card-header>
          <ion-card-title>{{ isRegister ? 'Crear un compte' : 'Inicia sessió al teu compte' }}</ion-card-title>
        </ion-card-header>
        <ion-card-content>
          <ion-item>
            <ion-label position="floating">Nom</ion-label>
            <ion-input v-model="form.name" v-if="isRegister" placeholder="Introdueix el teu nom complet" />
          </ion-item>
          <ion-item>
            <ion-label position="floating">Correu electrònic</ion-label>
            <ion-input v-model="form.email" type="email" placeholder="Introdueix el teu correu electrònic" />
          </ion-item>
          <ion-item>
            <ion-label position="floating">Contrasenya</ion-label>
            <ion-input v-model="form.password" type="password" placeholder="Tria una contrasenya segura" />
          </ion-item>
          <!-- Campo per Confirmar Contrasenya -->
          <ion-item v-if="isRegister">
            <ion-label position="floating">Confirmar contrasenya</ion-label>
            <ion-input v-model="form.confirmPassword" type="password" placeholder="Confirma la teva contrasenya" />
          </ion-item>
          <ion-button expand="block" @click="submit" class="action-btn">
            {{ isRegister ? 'Registrar-se' : 'Iniciar sessió' }}
          </ion-button>
          <ion-button expand="block" fill="clear" @click="isRegister = !isRegister" class="switch-btn">
            {{ isRegister ? 'Vés a Iniciar Sessió' : 'Vés a Registrar-se' }}
          </ion-button>
        </ion-card-content>
      </ion-card>
      <Footer/>
    </ion-content>
  </ion-page>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue';
import { IonPage, IonContent, IonCard, IonCardHeader, IonCardTitle, IonCardContent, IonItem, IonLabel, IonInput, IonButton } from '@ionic/vue';
import api from '../services/api';
import { useRouter } from 'vue-router';
import Navbar from "@/components/Navbar.vue";
import Footer from "@/components/Footer.vue";

export default defineComponent({
  name: 'LoginPage',
  components: {
    Footer,
    Navbar,
    IonPage,
    IonContent,
    IonCard,
    IonCardHeader,
    IonCardTitle,
    IonCardContent,
    IonItem,
    IonLabel,
    IonInput,
    IonButton
  },
  setup() {
    const router = useRouter();
    const isRegister = ref(false);
    const isLoggedIn = ref(!!localStorage.getItem('token'));
    const form = ref({ name: '', email: '', password: '', confirmPassword: '' });

    // Redirigir a la pàgina de /user si l'usuari ja està autenticat
    onMounted(() => {
      if (isLoggedIn.value) {
        router.push('/user');
      }
    });

    const submit = async () => {
      try {
        // Validació del frontend
        if (isRegister.value) {
          if (!form.value.name.trim()) {
            alert('Si us plau, introdueix un nom.');
            return;
          }
          if (!form.value.confirmPassword || form.value.password !== form.value.confirmPassword) {
            alert('Les contrasenyes no coincideixen.');
            return;
          }
        }
        if (!form.value.email.trim() || !form.value.password.trim()) {
          alert('Si us plau, introdueix tant el correu electrònic com la contrasenya.');
          return;
        }

        const endpoint = isRegister.value ? '/register' : '/login';
        const payload = {
          email: form.value.email,
          password: form.value.password,
          device_name: 'web',
          ...(isRegister.value && { name: form.value.name })
        };

        // Debug payload
        console.log('Enviant dades:', payload);

        const response = await api.post(endpoint, payload);
        localStorage.setItem('token', response.data.token);
        isLoggedIn.value = true;
        router.push('/home');
      } catch (error) {
        console.error('Error d\'autenticació:', error.response?.data || error.message);
        alert('Error d\'autenticació: ' + (error.response?.data?.message || 'Error desconegut'));
      }
    };

    const logout = () => {
      localStorage.removeItem('token');
      isLoggedIn.value = false;
      router.push('/login');
    };

    return { isRegister, isLoggedIn, form, submit, logout };
  }
});
</script>

<style scoped>
ion-content {
  --background: #f4f7f6;
}

.auth-card {
  margin: 20px;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
  background-color: white;
}

ion-card-header {
  text-align: center;
  margin-bottom: 20px;
}

ion-card-title {
  font-size: 22px;
  font-weight: 600;
  color: #333;
}

ion-button {
  margin-top: 15px;
  border-radius: 10px;
  background: #3498db;
  color: white;
  font-weight: bold;
  transition: background 0.3s ease;
  box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.1);
}

ion-button:hover {
  background: #2980b9;
}

.switch-btn {
  color: white !important; /* Assegura que el text sigui blanc */
  font-weight: 600;
  text-align: center;
}

ion-item {
  margin-bottom: 15px;
}

ion-label {
  color: #333;
}

ion-input {
  border-radius: 10px;
  background-color: #f4f7f6;
  padding: 10px;
  font-size: 16px;
}

ion-input::part(input) {
  padding: 12px;
}

footer {
  margin-top: 40px;
  text-align: center;
}
</style>
