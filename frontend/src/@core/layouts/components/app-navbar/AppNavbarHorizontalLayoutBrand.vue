<template>
  <div class="navbar-header d-xl-block d-none">
    <ul class="nav navbar-nav">
      <li class="nav-item">
        <b-link
          class="navbar-brand"
          to="/"
        >
          <span class="">
            <b-img
              :src="skin === 'dark' ? appLogoImageDark : appLogoImage"
              alt="logo"
              class="logo-img"
            />
          </span>
          <!-- <h2 class="brand-text mb-0">
            {{ appName }}
          </h2> -->
        </b-link>
      </li>
    </ul>
  </div>
</template>

<script>
import { BLink, BImg } from 'bootstrap-vue'
import { $themeConfig } from '@themeConfig'

export default {
  components: {
    BLink,
    BImg,
  },
  setup() {
    // App Name
    const { appName, appLogoImage, appLogoImageDark } = $themeConfig.app
    return {
      appName,
      appLogoImage,
      appLogoImageDark,
    }
  },
}
</script>

<style>
.logo-img {
    width: 55px;
    height: 20px;
  }
</style>
