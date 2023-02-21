<template>
  <h1 class="font-semibold text-3xl">Регистрация</h1>
  <form class="max-w-md mx-auto mt-16" @submit.prevent="register" method="post">
    <div class="flex flex-col space-y-7">
      <div class="flex flex-col">
        <label for="login" class="text-gray-300 mb-1">Логин</label>
        <input id="login" type="text" class="p-3 bg-gray-100 rounded-md outline-transparent" v-model="login" />
      </div>
      <div class="flex flex-col">
        <label for="password" class="text-gray-300 mb-1">Пароль</label>
        <input id="password" type="password" class="p-3 bg-gray-100 rounded-md outline-transparent" v-model="password" />
      </div>
      <div class="flex flex-col">
        <label for="password" class="text-gray-300 mb-1">Повторите Пароль</label>
        <input id="password" type="password" class="p-3 bg-gray-100 rounded-md outline-transparent" v-model="confirm" />
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
        confirm: "",
      };
    },
    methods: {
      async register() {
        if (this.password !== this.confirm) {
          return;
        }

        await fetch('/api/auth/register', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json;charset=utf-8'
          },
          body: JSON.stringify({
            email: this.login,
            password: this.password,
            confirm: this.confirm
          })
        }).then((response) => {
          console.log(response.json());
        })
      }
    }
  }
</script>
