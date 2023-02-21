<template>
  <header class="header">
    <nav class="nav">
      <ul class="nav__list">
        <li class="nav__item">
          <router-link to="/" class="nav__link">Главная</router-link>
        </li>
        <li class="nav__item">
          <router-link to="/login" class="nav__link">Авторизация</router-link>
        </li>
        <li class="nav__item">
          <router-link to="/register" class="nav__link">Регистрация</router-link>
        </li>
      </ul>
    </nav>
  </header>
</template>

<style lang="scss">
.header {
  display: flex;
  justify-content: center;
  padding: 15px 0px;
}
.nav {
  &__list {
    display: flex;
    list-style: none;
  }
  &__item {
    & + & {
      margin-left: 20px;
    }
  }
  &__link {
    text-decoration: none;
    color: #424242;
    font-size: 1.6rem;
  }
}
</style>