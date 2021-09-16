<template>
  <div class="auth-wrapper auth-v1 px-2">
    <div class="auth-inner py-2">
      <!-- Register v1 -->
      <b-card class="mb-0">
        <b-link class="brand-logo">
          <!-- <vuexy-logo />
          <h2 class="brand-text text-primary ml-1">Vuexy</h2> -->
          <b-img 
            :src="skin === 'dark' ? appLogoImageDark : appLogoImage"
            alt="logo" 
            class="logo-img" fluid 
          />
        </b-link>

        <!-- form -->
        <validation-observer ref="registerForm">
          <b-form
            ref="signup_form"
            class="auth-register-form mt-2"
            @submit.prevent="register"
          >
            <!-- api response error -->
            <p v-if="api_errors.length">
              <small class="text-danger">{{ api_errors[0] }}</small>
            </p>
            <!-- username -->
            <b-form-group label="Username" label-for="username">
              <validation-provider #default="{ errors }" name="Username" rules="required">
                <b-form-input
                  id="username"
                  v-model="username"
                  :state="errors.length > 0 ? false : null"
                  name="register-username"
                  placeholder="johndoe"
                />
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>

            <!-- email -->
            <b-form-group label="Email" label-for="email">
              <validation-provider
                #default="{ errors }"
                name="Email"
                rules="required|email"
              >
                <b-form-input
                  id="email"
                  v-model="regEmail"
                  :state="errors.length > 0 ? false : null"
                  name="register-email"
                  placeholder="john@example.com"
                />
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>

            <!-- password -->
            <b-form-group label="Password" label-for="password">
              <validation-provider #default="{ errors }" name="Password" rules="required">
                <b-input-group
                  class="input-group-merge"
                  :class="errors.length > 0 ? 'is-invalid' : null"
                >
                  <b-form-input
                    id="password"
                    v-model="password"
                    :type="passwordFieldType"
                    :state="errors.length > 0 ? false : null"
                    class="form-control-merge"
                    name="register-password"
                    placeholder="············"
                  />
                  <b-input-group-append is-text>
                    <feather-icon
                      :icon="passwordToggleIcon"
                      class="cursor-pointer"
                      @click="togglePasswordVisibility"
                    />
                  </b-input-group-append>
                </b-input-group>
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>

            <!-- re_password -->
            <b-form-group label="Password Confirm" label-for="re_password">
              <validation-provider
                #default="{ errors }"
                name="re_password"
                rules="required"
              >
                <b-input-group
                  class="input-group-merge"
                  :class="errors.length > 0 ? 'is-invalid' : null"
                >
                  <b-form-input
                    id="re_password"
                    v-model="re_password"
                    :type="passwordFieldType"
                    :state="errors.length > 0 ? false : null"
                    class="form-control-merge"
                    name="register-password-confirm"
                    placeholder="············"
                  />
                  <b-input-group-append is-text>
                    <feather-icon
                      :icon="passwordToggleIcon"
                      class="cursor-pointer"
                      @click="togglePasswordVisibility"
                    />
                  </b-input-group-append>
                </b-input-group>
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>

            <!-- checkbox -->
            <!-- <b-form-group>
              <b-form-checkbox
                id="register-privacy-policy"
                v-model="status"
                name="checkbox-1"
              >
                I agree to
                <b-link>privacy policy & terms</b-link>
              </b-form-checkbox>
            </b-form-group> -->

            <!-- submit button -->
            <b-button variant="primary" block type="submit"> Sign up </b-button>
          </b-form>
        </validation-observer>

        <b-card-text class="text-center mt-2">
          <span>Already have an account? </span>
          <b-link :to="{ name: 'login' }">
            <span>Sign in instead</span>
          </b-link>
        </b-card-text>
      </b-card>
      <!-- /Register v1 -->
    </div>
  </div>
</template>

<script>
import { ValidationProvider, ValidationObserver } from "vee-validate";
import {
  BCard,
  BLink,
  BCardTitle,
  BCardText,
  BForm,
  BButton,
  BFormInput,
  BFormGroup,
  BInputGroup,
  BInputGroupAppend,
  BFormCheckbox,
  BImg,
} from "bootstrap-vue";
import VuexyLogo from "@core/layouts/components/Logo.vue";
import { required, email } from "@validations";
import { togglePasswordVisibility } from "@core/mixins/ui/forms";
import ToastificationContent from "@core/components/toastification/ToastificationContent.vue";
import { getHomeRouteForRegister } from "@/auth/utils";
import axios from "axios";
import { $themeConfig } from '@themeConfig'
import useAppConfig from '@core/app-config/useAppConfig'

export default {
  components: {
    VuexyLogo,
    // BSV
    BCard,
    BLink,
    BCardTitle,
    BCardText,
    BForm,
    BButton,
    BFormInput,
    BFormGroup,
    BInputGroup,
    BInputGroupAppend,
    BFormCheckbox,
    // validations
    ValidationProvider,
    ValidationObserver,
    BImg,
  },
  setup() {
    const { skin } = useAppConfig()
    const { appName, appLogoImage, appLogoImageDark  } = $themeConfig.app
    return {
      skin,
      appName,
      appLogoImage,
      appLogoImageDark,
    }
  },
  mixins: [togglePasswordVisibility],
  data() {
    return {
      regEmail: "",
      username: "",
      password: "",
      re_password: "",
      status: "",

      // validation rules
      required,
      email,
      api_errors: [],
    };
  },
  computed: {
    passwordToggleIcon() {
      return this.passwordFieldType === "password" ? "EyeIcon" : "EyeOffIcon";
    },
  },
  methods: {
    register() {
      const formData = {
        name: this.username,
        email: this.regEmail,
        password: this.password,
        re_password: this.re_password,
      };

      this.$refs.registerForm.validate().then((success) => {
        if (success) {
          // send register request
          axios
            .post("/api/user/register", formData)
            .then((response) => {
              if(response.data.status == 1){
                this.$router.replace(getHomeRouteForRegister()).then(() => {
                  this.$toast({
                    component: ToastificationContent,
                    props: {
                      title: "Form Submitted",
                      icon: "EditIcon",
                      variant: "success",
                    },
                  });
                });
              }else{
                if(response.data.error != null){
                  this.api_errors.pop();
                  this.api_errors.push(response.data.error);
                }                
              }
            })
            .catch((error) => {
              console.log(error);
              this.api_errors.pop();
              this.api_errors.push(error);
            });
        }
      });
    },
  },
};
</script>

<style lang="scss">
@import "@core/scss/vue/pages/page-auth.scss";
.logo-img {
    width: 65px;
    height: 20px;
  }
</style>
