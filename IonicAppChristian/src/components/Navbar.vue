<template>
  <ion-header>
    <ion-toolbar>
      <ion-buttons slot="start" v-if="token">
        <!-- Botó Home que apareix només si hi ha token -->
        <ion-button @click="goToHome">
          <img src="@/assets/home.png" alt="home" class="icon" />
        </ion-button>
      </ion-buttons>

      <!-- Títol centrat -->
      <ion-title class="center-title">{{ title }}</ion-title>

      <ion-buttons slot="end" v-if="token">
        <!-- Botó Persona que apareix només si hi ha token -->
        <ion-button @click="goToUser">
          <img src="@/assets/user.png" alt="persona" class="icon" />
        </ion-button>
      </ion-buttons>
    </ion-toolbar>
  </ion-header>
</template>

<script setup>
import { defineProps, ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../services/api'; // Assegura't de tenir el teu fitxer de serveis API

const props = defineProps({
  title: {
    type: String,
    default: 'Nom de l\'App'
  }
});

const router = useRouter();

// Almacenarem el token en una variable reactiva
const token = ref(null);

// Funció per verificar el token en l'emmagatzematge local
const checkToken = async () => {
  token.value = localStorage.getItem('token');
  if (token.value) {
    try {
      // Fem una crida per verificar si el token és vàlid
      const response = await api.get('/user', {
        headers: {
          Authorization: `Bearer ${token.value}`,
        },
      });

      // Si la resposta és exitosa, podem continuar
      console.log('Token vàlid', response.data);
    } catch (error) {
      // Si hi ha un error, eliminem el token de localStorage
      console.log('Token no vàlid', error);
      localStorage.removeItem('token');
      token.value = null;
      // Redirigir a la pàgina de login
      router.push('/login').then(() => {
        window.location.reload();  // Forçar recàrrega
      });
    }
  } else {
    // Si no hi ha token, redirigim a login
    if (router.currentRoute.value.path !== '/login') {
      router.push('/login').then(() => {
        window.location.reload();  // Forçar recàrrega
      });
    }
  }
};

// Funció per redirigir a la pàgina Home
const goToHome = () => {
  router.push('/home').then(() => {
    window.location.reload();  // Forçar recàrrega
  });  // Assegura't de tenir aquesta ruta definida
};

// Funció per redirigir a la pàgina de perfil

// Funció per redirigir a la pàgina de l'usuari
const goToUser = () => {
  router.push('/user').then(() => {
    window.location.reload();  // Forçar recàrrega
  });
};

// Comprovem el token quan el component es carrega
onMounted(checkToken);
</script>

<style scoped>
/* Aquí pots afegir estil per personalitzar el navbar */
.icon {
  width: 24px;  /* Ajusta la mida de l'icona */
  height: 24px; /* Ajusta la mida de l'icona */
  object-fit: contain; /* Per mantenir la proporció de l'icona */
}

/* Centrar el títol */
.center-title {
  flex: 1;
  text-align: center;
}
</style>
