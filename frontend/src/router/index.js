import Vue from 'vue'
import VueRouter from 'vue-router'
import { canNavigate } from '@/libs/acl/routeProtection'
import { isUserLoggedIn, getUserData, getHomeRouteForLoggedInUser } from '@/auth/utils'

Vue.use(VueRouter)

const DEFAULT_TITLE = 'C-Bot';
const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  scrollBehavior() {
    return { x: 0, y: 0 }
  },
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('@/views/Home.vue'),
      meta: {
        pageTitle: 'Home',
        breadcrumb: [
          {
            text: 'Home',
            active: true,
          },
        ],
      },
    },
    {
      path: '/hospital-map',
      name: 'hospital-map',
      component: () => import('@/views/HospitalMap.vue'),
      meta: {
        pageTitle: 'Hospital Map',
        breadcrumb: [
          {
            text: 'Hospital Map',
            active: true,
          },
        ],
      },
    },
    {
      path: '/mapdetail/:unit/:floor/:room',
      name: 'mapdetail',
      component: () => import('@/views/MapDetail.vue'),
      props: true,
      meta: {
        pageTitle: 'Map Detail',
        breadcrumb: [
          {
            text: 'Hospital Map',
            to:'/hospital-map'
          },
          {
            text: 'Map Detail',
            active: true,
          },
        ],
      },
    },
    {
      path: '/export/:startDate/:endDate',
      name: 'export',
      component: () => import('@/views/Export.vue'),
      props: true,
      meta: {
        pageTitle: 'Export PDF',
        breadcrumb: [
          {
            text: 'Home',
            to:'/'
          },
          {
            text: 'Export PDF',
            active: true,
          },
        ],
      },
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/authentication/Login.vue'),
      meta: {
        pageTitle: 'Login',
        layout: 'full',
        resource: 'Auth',
        redirectIfLoggedIn: true,
      },
    },
    {
      path: '/register',
      name: 'auth-register',
      component: () => import('@/views/authentication/Register.vue'),
      meta: {
        pageTitle: 'Register',
        layout: 'full',
        resource: 'Auth',
        redirectIfLoggedIn: true,
      },
    },
    
    {
      path: '/forgot-password',
      name: 'auth-forgot-password',
      component: () => import('@/views/authentication/ForgotPassword.vue'),
      meta: {
        layout: 'full',
        resource: 'Auth',
        redirectIfLoggedIn: true,
      },
    },
    {
      path: '/error-404',
      name: 'error-404',
      component: () => import('@/views/error/Error404.vue'),
      meta: {
        layout: 'full',
      },
    },
    {
      path: '*',
      redirect: 'error-404',
    },
  ],
})

router.beforeEach((to, _, next) => {
  const isLoggedIn = isUserLoggedIn()
  if (!canNavigate(to)) {
    // Redirect to login if not logged in
    if (!isLoggedIn) return next({ name: 'login' })

    // If logged in => not authorized
    return next()
  }

  // Redirect if logged in
  if (to.meta.redirectIfLoggedIn && isLoggedIn) {
    const userData = getUserData()
    next(getHomeRouteForLoggedInUser(userData ? userData.role : null))
  }

  return next()
})

// ? For splash screen
// Remove afterEach hook if you are not using splash screen
router.afterEach((to) => {
  // Remove initial loading
  const appLoading = document.getElementById('loading-bg')
  if (appLoading) {
    appLoading.style.display = 'none'
  }
  Vue.nextTick(() => {
    document.title = to.meta.pageTitle || DEFAULT_TITLE;
});
})

export default router
