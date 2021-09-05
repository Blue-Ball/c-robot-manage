<template>
  <div class="auth-wrapper auth-v1 px-2">
    <div class="auth-inner py-2">
      <!-- Login v1 -->
      <b-card class="mb-0">
        <b-link class="brand-logo">
          <vuexy-logo />

          <h2 class="brand-text text-primary ml-1">Vuexy</h2>
        </b-link>

        <!-- form -->
        <validation-observer ref="loginForm" #default="{ invalid }">
          <b-form class="auth-login-form mt-2" @submit.prevent="login">
            <!-- email -->
            <b-form-group label-for="email" label="Email">
              <validation-provider
                #default="{ errors }"
                name="Email"
                rules="required|email"
              >
                <b-form-input
                  id="email"
                  v-model="userEmail"
                  name="email"
                  :state="errors.length > 0 ? false : null"
                  placeholder="john@example.com"
                  autofocus
                />
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>

            <!-- password -->
            <b-form-group label-for="password" label="Password">
              <!-- <div class="d-flex justify-content-between">
                <label for="password">Password</label>
                <b-link :to="{ name: 'auth-forgot-password' }">
                  <small>Forgot Password?</small>
                </b-link>
              </div> -->
              <validation-provider #default="{ errors }" name="Password" rules="required">
                <b-input-group
                  class="input-group-merge"
                  :class="errors.length > 0 ? 'is-invalid' : null"
                >
                  <b-form-input
                    id="password"
                    v-model="password"
                    :type="passwordFieldType"
                    class="form-control-merge"
                    :state="errors.length > 0 ? false : null"
                    name="password"
                    placeholder="Password"
                  />

                  <b-input-group-append is-text>
                    <feather-icon
                      class="cursor-pointer"
                      :icon="passwordToggleIcon"
                      @click="togglePasswordVisibility"
                    />
                  </b-input-group-append>
                </b-input-group>
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>

            <!-- checkbox -->
            <!-- <b-form-group>
              <b-form-checkbox id="remember-me" v-model="status" name="checkbox-1">
                Remember Me
              </b-form-checkbox>
            </b-form-group> -->

            <!-- submit button -->
            <b-button
              ref="signin_submit"
              variant="primary"
              type="submit"
              block
              :disabled="invalid"
            >
              Sign in
            </b-button>
          </b-form>
        </validation-observer>

        <b-card-text class="text-center mt-2">
          <span>New on our platform? </span>
          <b-link :to="{ name: 'auth-register' }">
            <span>Create an account</span>
          </b-link>
        </b-card-text>
      </b-card>
      <!-- /Login v1 -->
    </div>
  </div>
</template>

<script>
import { ValidationProvider, ValidationObserver } from "vee-validate";
import {
  BButton,
  BForm,
  BFormInput,
  BFormGroup,
  BCard,
  BLink,
  BCardTitle,
  BCardText,
  BInputGroup,
  BInputGroupAppend,
  BFormCheckbox,
} from "bootstrap-vue";
import VuexyLogo from "@core/layouts/components/Logo.vue";
import { required, email } from "@validations";
import { togglePasswordVisibility } from "@core/mixins/ui/forms";

import { LOGIN } from "@core/services/store/auth.module";
import { getHomeRouteForLoggedInUser } from "@/auth/utils";

import ToastificationContent from "@core/components/toastification/ToastificationContent.vue";

export default {
  components: {
    // BSV
    BButton,
    BForm,
    BFormInput,
    BFormGroup,
    BCard,
    BCardTitle,
    BLink,
    VuexyLogo,
    BCardText,
    BInputGroup,
    BInputGroupAppend,
    BFormCheckbox,
    ValidationProvider,
    ValidationObserver,
  },
  mixins: [togglePasswordVisibility],
  data() {
    return {
      userEmail: "",
      password: "",
      status: "",
      // validation rules
      required,
      email,
    };
  },
  computed: {
    passwordToggleIcon() {
      return this.passwordFieldType === "password" ? "EyeIcon" : "EyeOffIcon";
    },
  },
  methods: {
    login() {
      let email = this.userEmail;
      let password = this.password;

      this.$refs.loginForm.validate().then((success) => {
        if (success) {
          this.$store
            .dispatch(LOGIN, { email, password })
            .then((response) => {
              const { userData } = response.data;
              // useJwt.setToken(response.data.accessToken);
              // useJwt.setRefreshToken(response.data.refreshToken);
              localStorage.setItem("userData", JSON.stringify(userData));
              this.$ability.update(userData.ability);

              this.$router
                .replace(getHomeRouteForLoggedInUser(userData.role))
                .then(() => {
                  this.$toast({
                    component: ToastificationContent,
                    position: "top-right",
                    props: {
                      title: `Welcome ${userData.fullName || userData.username}`,
                      icon: "CoffeeIcon",
                      variant: "success",
                      text: `You have successfully logged in as ${userData.role}. Now you can start to explore!`,
                    },
                  });
                });
            })
            .catch((error) => {
              this.$refs.loginForm.setErrors(error.response.data.error);
            });
        }
      });
    },
  },
};
</script>

<style lang="scss">
@import "@core/scss/vue/pages/page-auth.scss";
</style>
