<template>
  <header class="flex w-full py-5 justify-center">
    <nav>
      <ul class="flex space-x-7">
        <li>
          <router-link to="/" class="text-dm">Главная</router-link>
        </li>
        <li>
          <router-link to="/login" class="text-dm">Авторизация</router-link>
        </li>
        <li>
          <router-link to="/register" class="text-dm">Регистрация</router-link>
        </li>
        <li>
          <router-link to="/dashboard" class="text-dm">Личный кабинет</router-link>
        </li>
        <li>
          <a @click.prevent="authProvider('google')">Регистрация через Google</a>
        </li>
      </ul>
    </nav>
  </header>
</template>

<script>
export default {
  methods: {
    authProvider(provider) {
      let self = this;
      this.$auth.authenticate(provider).then(response => {
        self.socialLogin(provider, response);
      }).catch(err => {
        console.log(err);
      });
    },
    socialLogin(provider, response) {
      this.$http.post('/social/' + provider, response).then(response => {
        return response.data.token;
      }).catch(err => {
        console.log(err);
      })
    }
  }
}
</script>