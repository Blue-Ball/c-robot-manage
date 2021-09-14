<template>
  <div class="navbar-container d-flex content align-items-center">
    <!-- Nav Menu Toggler -->
    <ul class="nav navbar-nav d-xl-none">
      <li class="nav-item">
        <b-link class="nav-link" @click="toggleVerticalMenuActive">
          <feather-icon icon="MenuIcon" size="21" />
        </b-link>
      </li>
    </ul>

    <!-- Left Col -->
    <div class="bookmark-wrapper align-items-center flex-grow-1 d-none d-lg-flex"></div>

    <b-navbar-nav class="nav align-items-center ml-auto">
      <b-button
        v-if="$route.meta.pageTitle == 'Home'"
        id="toggle-btn"
        v-ripple.400="'rgba(113, 102, 240, 0.15)'"
        v-b-modal.modal-date-range
        variant="primary"
      >
        {{ $t("Export PDF") }}
      </b-button>
      <dark-Toggler class="d-none d-lg-block" />
      <locale />
      <b-nav-item-dropdown
        right
        toggle-class="d-flex align-items-center dropdown-user-link"
        class="dropdown-user"
      >
        <template #button-content>
          <div class="d-sm-flex d-none user-nav">
            <p class="user-name font-weight-bolder mb-0">{{ userData.name }}</p>
            <span class="user-status">{{
              userData.is_admin == 1 ? "Admin" : "User"
            }}</span>
          </div>
        </template>

        <!-- <b-dropdown-item link-class="d-flex align-items-center">
          <feather-icon size="16" icon="UserIcon" class="mr-50" />
          <span>Profile</span>
        </b-dropdown-item>

        <b-dropdown-item link-class="d-flex align-items-center">
          <feather-icon size="16" icon="MailIcon" class="mr-50" />
          <span>Inbox</span>
        </b-dropdown-item>

        <b-dropdown-item link-class="d-flex align-items-center">
          <feather-icon size="16" icon="CheckSquareIcon" class="mr-50" />
          <span>Task</span>
        </b-dropdown-item>

        <b-dropdown-item link-class="d-flex align-items-center">
          <feather-icon size="16" icon="MessageSquareIcon" class="mr-50" />
          <span>Chat</span>
        </b-dropdown-item>

        <b-dropdown-divider /> -->

        <b-dropdown-item link-class="d-flex align-items-center" @click="logout()">
          <feather-icon size="16" icon="LogOutIcon" class="mr-50" />
          <span>Logout</span>
        </b-dropdown-item>
      </b-nav-item-dropdown>
    </b-navbar-nav>
    <!-- modal -->
    <b-modal
      id="modal-date-range"
      ref="my-modal"
      ok-title="Submit"
      cancel-variant="outline-secondary"
      @show="resetModal"
      @hidden="resetModal"
      @ok="handleOk"
    >
      <form ref="form" @submit.stop.prevent="handleSubmit">
        <b-form-group
          :state="rangeState"
          label="Select range"
          label-for="range-input"
          invalid-feedback="Date range is required"
        >
          <div class="d-flex align-items-center">
            <feather-icon icon="CalendarIcon" size="24" />
            <flat-pickr
              id="range-input"
              v-model="rangeDate"
              :config="dateConfig"
              @on-close="onDateChange"
              class="form-control flat-picker bg-transparent border shadow-none"
              placeholder="YYYY/MM/DD"
              outline
            />
          </div>
        </b-form-group>
      </form>
    </b-modal>
  </div>
</template>

<script>
import {
  BLink,
  BNavbarNav,
  BNavItemDropdown,
  BDropdownItem,
  BDropdownDivider,
  BAvatar,
  BButton,
  BFormGroup,
  BFormInput,
  BModal,
  VBModal,
} from "bootstrap-vue";
import DarkToggler from "@core/layouts/components/app-navbar/components/DarkToggler.vue";
import Locale from "./Locale.vue";
import { getUserData } from "@/auth/utils";
import Ripple from "vue-ripple-directive";
import flatPickr from "vue-flatpickr-component";
import moment from "moment";

export default {
  components: {
    BLink,
    BNavbarNav,
    BNavItemDropdown,
    BDropdownItem,
    BDropdownDivider,
    BAvatar,
    BButton,
    BFormGroup,
    BFormInput,
    BModal,

    // Navbar Components
    DarkToggler,
    Locale,
    flatPickr,
  },
  directives: {
    "b-modal": VBModal,
    Ripple,
  },
  props: {
    toggleVerticalMenuActive: {
      type: Function,
      default: () => {},
    },
    isShow: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      userData: [],
      rangeState: null,
      rangeDate: null,
      startDate: null,
      endDate: null,
      dateConfig: {
        mode: "range",
        wrap: true, // set wrap to true only when using 'input-group'
        altFormat: "d/m/Y",
        altInput: true,
        dateFormat: "Y/m/d",
        defaultDate: [new Date(Date.now() - 6 * 24 * 60 * 60 * 1000), new Date()],
      },
    };
  },
  mounted() {
    this.userData = getUserData();
    this.endDate = new Date().toJSON().slice(0, 10).replace(/-/g, "-");
    this.startDate = new Date(Date.now() - 6 * 24 * 60 * 60 * 1000)
      .toISOString()
      .slice(0, 10);
  },
  methods: {
    logout() {
      localStorage.removeItem("userData");
      this.$router.replace("/login");
    },
    checkFormValidity() {
      const valid = this.$refs.form.checkValidity();
      this.rangeState = valid;
      return valid;
    },
    resetModal() {
      this.range = "";
      this.rangeState = null;
    },
    handleOk(bvModalEvt) {
      // Prevent modal from closing
      bvModalEvt.preventDefault();
      // Trigger submit handler
      this.handleSubmit();
    },
    handleSubmit() {
      // Exit when the form isn't valid
      if (!this.checkFormValidity()) {
        return;
      }

      this.$nextTick(() => {
        this.$refs["my-modal"].toggle("#toggle-btn");
      });
      // console.log(this.startDate, this.endDate);

      this.$router.push("/export/" + this.startDate + "/" + this.endDate);
      // this.$route.params.pathMatch;
    },
    onDateChange: function (selectedDates, dateStr, instance) {
      this.startDate = moment(selectedDates[0]).format("YYYY-MM-DD");
      this.endDate = moment(selectedDates[1]).format("YYYY-MM-DD");
    },
  },
};
</script>
