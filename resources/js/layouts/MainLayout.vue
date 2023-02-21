<template>
  <div class="max-w-screen-xl w-full m-auto">
    <Navbar />
    <div class="main-layout">
      <slot />
    </div>
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
  components: { Navbar }
}
</script>
