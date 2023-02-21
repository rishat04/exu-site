<template>
  <form class="form" @submit.prevent="submitForm">
    <div class="form__inner">
      <div class="form__row form__row--column">
        <h1 class="form__title">Авторизация</h1>
      </div>
      <div class="form__row form__row--column">
        <label for="login" class="form__label">Логин</label>
        <input id="login" type="email" class="form__input" v-model="login"/>
      </div>
      <div class="form__row form__row--column">
        <label for="password" class="form__label">Пароль</label>
        <input id="password" type="password" class="form__input" v-model="password"/>
      </div>
      <div class="form__row">
        <button class="form__button">Отправить</button>
      </div>
    </div>
  </form>
</template>

<style lang="scss" scoped>
.form {
  max-width: 400px;
  margin: 20px auto 0;
  &__row {
    display: flex;
    &--column {
      flex-direction: column;
    }
    & + & {
      margin-top: 15px;
    }
  }
  &__title {
    font-size: 2.4rem;
  }
  &__label {
    color: #424242;
    margin-bottom: 10px;
    font-size: 1.6rem;
  }
  &__input {
    padding: 10px;
    outline: none;
    border-radius: 6px;
    border: none;
    background-color: #f5f5f5;
  }
  &__button {
    padding: 1.2rem 2rem;
    font-size: 1.6rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    background-color: #84b89b;
    color: #fff;
  }
}
</style>

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
