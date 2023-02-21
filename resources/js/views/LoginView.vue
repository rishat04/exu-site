<template>
  <h1 class="font-semibold text-3xl">Авторизация</h1>
  <form class="max-w-md mx-auto mt-16" @submit.prevent="submitForm">
    <div class="flex flex-col space-y-7">
      <div class="flex flex-col">
        <label for="login" class="text-gray-300 mb-1">Логин</label>
        <input id="login" type="email" class="p-3 bg-gray-100 rounded-md outline-transparent" v-model="login"/>
      </div>
      <div class="flex flex-col">
        <label for="password" class="text-gray-300 mb-1">Пароль</label>
        <input id="password" type="password" class="p-3 bg-gray-100 rounded-md outline-transparent" v-model="password"/>
      </div>
      <div class="flex">
        <button class="bg-green-800/50 px-5 py-2 rounded-md text-white">Отправить</button>
      </div>
    </div>
  </form>
</template>

<script>
export default {
  data() {
    return {
      login: "",
      password: "",
    }
  },

  methods: {
    async submitForm() {
      await fetch('/api/auth/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json;charset=utf-8'
          },
          body: JSON.stringify({
            email: this.login,
            password: this.password
          })
        }).then((response) => {
          return response.json();
        }).then((json) => {
          if (json.status) {
            localStorage.setItem("token", json.token);
          }
          else {
            console.log(json.message)
          }
          this.$router.push('/');
        })
    }
  },
};
</script>
