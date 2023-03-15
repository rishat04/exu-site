<template>
  <div class="max-w-screen-xl w-full m-auto min-h-screen">
    <Navbar />
    <div class="main-layout">
      <slot />
    </div>
    <form >
      <input type="text" v-model="id">
      <button v-on:click="trim">Trim</button>
    </form>
  </div>
</template>

<style lang="scss">
.container {
  margin: auto;
  max-width: 1230px;
  padding: 0 15px;
}
</style>

<script>
import Navbar from "../components/Navbar.vue";
export default {
  components: { Navbar },
  data: {
    id: ''
  },
  methods: {
    trim: function(event) {
      fetch('/api/trim', {
        method: 'post',
        data: { 
          v: this.id,
          s: '0',
          d: '0',
          q: '720'        
        }
      }).then(response => {
        return response.json();
      }).then(json => {
        console.log(json);
      })
    }
  }
}
</script>
