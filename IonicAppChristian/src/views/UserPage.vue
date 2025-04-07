<template>
  <ion-page>
    <Navbar title="Perfil d'Usuari"/>

    <ion-content>
      <!-- Botó per tancar sessió -->
      <ion-button expand="full" class="logout-button" @click="logout">
        Tancar Sessió
      </ion-button>

      <!-- Llista de fitxers de l'usuari -->
      <ion-list class="file-list">
        <ion-item v-for="(item, index) in userFiles" :key="index" class="file-item">
          <ion-label>
            <ion-card-header>
              <img :src="getPreviewSrc(item)" alt="Previsualització"/>
            </ion-card-header>
            <p class="file-date">Creat: {{ formatDate(item.created_at) }}</p>
            <p class="file-date">Modificat: {{ formatDate(item.updated_at) }}</p>
          </ion-label>
          <ion-button color="none" class="delete-button" @click="deleteFile(item.id)">
            Eliminar
          </ion-button>
        </ion-item>
      </ion-list>

      <Footer/>
    </ion-content>
  </ion-page>
</template>

<script setup>
import {ref, onMounted} from 'vue';
import {useRouter} from 'vue-router';
import api from '../services/api';
import Navbar from '../components/Navbar.vue';
import Footer from '../components/Footer.vue';

const router = useRouter();
const userFiles = ref([]);

// Funció per obtenir els fitxers de l'usuari
const getUserFiles = async () => {
  try {
    const response = await api.get(`/multimedia/user`); // Aquí passes el userId
    userFiles.value = response.data;
  } catch (error) {
    console.error('Error obtenint fitxers:', error);
  }
};

// Funció per formatar la data
const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleString(); // Potser vulguis personalitzar més el format
};

// Funció per tancar sessió
const logout = async () => {
  try {
    localStorage.removeItem('token');
    router.push('/login').then(() => {
      window.location.reload();  // Forçar recàrrega
    }); // Redirigeix a la pàgina de login
  } catch (error) {
    console.error('Error logging out:', error);
  }
};

// Funció per eliminar un fitxer
const deleteFile = async (fileId) => {
  try {
    await api.delete(`/multimedia/${fileId}`);
    // Un cop eliminat el fitxer, actualitza la llista
    userFiles.value = userFiles.value.filter(file => file.id !== fileId);
  } catch (error) {
    console.error('Error deleting file:', error);
  }
};

import videoIcon from '@/assets/video.png';
import documentIcon from '@/assets/docs.png';

const getPreviewSrc = (item) => {
  console.log(item);
  if (item.file_type.startsWith('image/')) {
    return `data:image/jpeg;base64,${item.file_path}`;
  } else if (item.file_type.startsWith('video/')) {
    return videoIcon;
  } else {
    return documentIcon;
  }
};


// Carregar els fitxers de l'usuari al muntar la pàgina
onMounted(getUserFiles);
</script>

<style scoped>

ion-label{
  display: flex;
  flex-direction: column;

  img{
    width: 100px;
  }
}
ion-content {
  --background: #f9fafb;
  padding-bottom: 80px; /* Espai per als botons */
}

.logout-button {
  background-color: #e74c3c;
  margin: 10px 0;
  color: white;
  font-size: 16px;
  font-weight: bold;
  border-radius: 8px;
}

.logout-button:hover {
  background-color: #c0392b;
}

ion-header {
  --background: #3498db;
}

ion-title {
  color: white;
}

.file-list {
  margin-top: 20px;
}

.file-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px;
  margin-bottom: 10px;
  background-color: white;
  border-radius: 10px;
  box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
}

.file-title {
  font-size: 16px;
  color: #333;
  font-weight: bold;
}

.file-date {
  font-size: 14px;
  color: #777;
}

.delete-button {
  background-color: #e74c3c;
  color: white;
  font-weight: bold;
  border-radius: 8px;
  padding: 5px 15px;
}

.delete-button:hover {
  background-color: #c0392b;
}

@media (max-width: 767px) {
  .file-item {
    flex-direction: column;
    align-items: flex-start;
  }

  .delete-button {
    margin-top: 10px;
    width: 100%;
  }
}
</style>
