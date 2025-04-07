<template>
  <ion-page>
    <Navbar title="Llista d'Arxius"/>

    <ion-content>
      <!-- Botó flotant per pujar un fitxer -->
      <ion-button
          expand="full"
          class="upload-button"
          @click="goToUploadPage">
        <img src="@/assets/add.png" alt="Pujar un fitxer" class="upload-icon" />
      </ion-button>

      <!-- Llista d'arxius multimèdia en mode grid -->
      <ion-grid>
        <ion-row>
          <ion-col size="6" size-sm="4" size-md="3" v-for="(item, index) in multimedia" :key="index">
            <ion-card class="media-card">
              <ion-card-header>
                <img :src="getPreviewSrc(item)" alt="Previsualització" />
              </ion-card-header>

              <!-- Imatge de l'usuari en un cercle i el seu nom -->
              <ion-card-footer>
                <ion-item lines="none">
                  <ion-avatar slot="start">
                    <img :src="item.user.profile_photo_url" />
                  </ion-avatar>
                  <ion-label>{{ item.user.name }}</ion-label>
                </ion-item>
                <ion-item>
                  <ion-label>
                    <p>{{ formatDate(item.created_at) }} - Fa {{ getTimeAgo(item.created_at) }}</p>
                  </ion-label>
                </ion-item>
              </ion-card-footer>
            </ion-card>
          </ion-col>
        </ion-row>
      </ion-grid>

      <Footer/>
    </ion-content>
  </ion-page>
</template>

<script setup>
import Navbar from '../components/Navbar.vue';
import Footer from '../components/Footer.vue';
import {ref, onMounted} from 'vue';
import {useRouter} from 'vue-router';
import api from '../services/api';

// Funció per formatar la data
const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleString(); // Potser vulguis personalitzar més el format
};

// Funció per mostrar "Fa X hores" o altres unitats de temps
const getTimeAgo = (dateString) => {
  const now = new Date();
  const diffInSeconds = Math.floor((now - new Date(dateString)) / 1000);

  const minutes = Math.floor(diffInSeconds / 60);
  const hours = Math.floor(diffInSeconds / 3600);
  const days = Math.floor(diffInSeconds / 86400);

  if (hours < 1) {
    return `${minutes} minut${minutes === 1 ? '' : 's'}`;
  } else if (hours < 24) {
    return `${hours} hora${hours === 1 ? '' : 's'}`;
  } else {
    return `${days} dia${days === 1 ? '' : 's'}`;
  }
};

const router = useRouter();
const multimedia = ref([]);

// Funció per obtenir els arxius multimèdia
const getMultimedia = async () => {
  try {
    const response = await api.get('/multimedia');
    multimedia.value = response.data;
  } catch (error) {
    console.error('Error getting multimedia:', error);
  }
};

// Funció per redirigir a la pàgina de pujada
const goToUploadPage = () => {
  router.push('/upload').then(() => {
    window.location.reload();  // Forçar recàrrega
  });
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


// Carregar la llista d'arxius en muntar la pàgina
onMounted(getMultimedia);
</script>

<style scoped>
ion-content {
  --background: #f4f7f6;
  padding-bottom: 60px; /* Espai per al botó flotant */
}

.upload-button {
  position: fixed;
  bottom: 70px;
  right: 20px;
  background-color: #3498db;
  border-radius: 10px;
  height: 60px;
  width: 60px;
  display: flex;
  justify-content: center;
  align-items: center;
  box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.2);
  z-index: 10;
}

.upload-button:hover {
  background-color: #2980b9;
}

.upload-icon {
  width: 30px;
  height: 30px;
}

ion-grid {
  padding: 10px;
}

ion-card {
  border-radius: 12px;
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
  background-color: white;
}

ion-card-header {
  background-color: #f9f9f9;
  padding: 10px;
  border-top-left-radius: 12px;
  border-top-right-radius: 12px;
  text-align: center;
}

ion-card-title {
  font-size: 16px;
  color: #333;
}

ion-card-content {
  padding: 15px;
}

ion-item {
  padding: 0;
}

ion-label p {
  margin: 5px 0;
  font-size: 14px;
  color: #666;
}

ion-card-footer {
  padding: 10px;
}

ion-avatar img {
  border-radius: 50%;
  width: 40px;
  height: 40px;
}

ion-card-footer ion-item {
  display: flex;
  align-items: center;
}

ion-card-footer ion-label {
  margin-left: 10px;
  font-size: 14px;
  color: #333;
}

@media (max-width: 767px) {
  ion-col {
    padding: 5px;
  }
}
</style>
