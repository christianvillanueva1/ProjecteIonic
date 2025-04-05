<template>
  <ion-page>
    <Navbar title="Pujar un fitxer" />

    <ion-content>
      <div class="filepond-container">
        <!-- Inicialitzar FilePond amb la configuració del servidor -->
        <FilePond
            name="file"
            allow-multiple="true"
            :server="filePondServerConfig"
            :onprocessfile="onFileProcess"
        />
      </div>
      <Footer />
    </ion-content>
  </ion-page>
</template>

<script setup>
import vueFilePond from 'vue-filepond';
import 'filepond/dist/filepond.min.css';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';

import { ref, onMounted,  } from 'vue';
import Navbar from '../components/Navbar.vue';
import Footer from '../components/Footer.vue';

const FilePond = vueFilePond(FilePondPluginImagePreview);

// Definir una referència per al token
const token = ref(null);

// Definir la configuració de FilePond
const filePondServerConfig = ref({});

// Obtenir el token del localStorage després de muntar el component
onMounted(() => {
  // Obtenim el token del localStorage al muntar el component
  token.value = localStorage.getItem('token');

  // Comprovem si el token està disponible i configurem FilePond
  if (token.value) {
    // Actualitzar la configuració de FilePond amb el token
    filePondServerConfig.value = {
      process: {
        url: 'http://127.0.0.1:8000/api/multimedia',
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token.value}`,
        }
      },
      revert: 'http://127.0.0.1:8000/api/multimedia/revert',  // Si vols revertir la pujada
      fetch: 'http://127.0.0.1:8000/api/multimedia/fetch'  // Si necessites obtenir fitxers existents
    };
  } else {
    console.error('No s\'ha trobat el token. Assegura\'t que estiguis autenticat.');
  }
});

// Funció per capturar el fitxer seleccionat
const onFileProcess = (error, file) => {
  if (error) {
    console.error('Error en el procés del fitxer:', error);
  } else {
    console.log('Fitxer processat:', file);
  }
};
</script>

<style scoped>
.filepond-container {
  margin-top: 20px;
  width: 100%;
}
</style>
