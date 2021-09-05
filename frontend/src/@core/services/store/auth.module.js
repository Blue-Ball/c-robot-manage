import ApiService from "@core/services/api.service";
import JwtService from "@core/services/jwt.service";

// action types
export const VERIFY_AUTH = "verifyAuth";
export const LOGIN = "login";
export const LOGOUT = "logout";
export const REGISTER = "register";
export const UPDATE_PASSWORD = "updateUser";

// mutation types
export const PURGE_AUTH = "logOut";
export const SET_AUTH = "setUser";
export const SET_PASSWORD = "setPassword";
export const SET_ERROR = "setError";

const state = {
  errors: null,
  user: {},
  isAuthenticated: !!JwtService.getToken()
};

const getters = {
  currentUser(state) {
    return state.user;
  },
  isAuthenticated(state) {
    return state.isAuthenticated;
  }
};

const actions = {
  [LOGIN](context, credentials) {
    console.log("LOGIN : ");
    console.log(context);
    return new Promise((resolve, reject) => {
      ApiService.post("/api/user/login", credentials)
        .then(({ data }) => {
          console.log(data);
          context.commit(SET_AUTH, data);
          resolve(data);
        })
        .catch(error => {
          context.commit(SET_ERROR, error.response.data.errors);
          return reject(error);
        });
    });
  },

  [LOGOUT](context) {
    context.commit(PURGE_AUTH);
  },
  
  [REGISTER](context, credentials) {
    return new Promise((resolve, reject) => {
      ApiService.post("signup", credentials)
        .then(({ data }) => {
          //console.log("regist-data", data);
          //context.commit(SET_AUTH, data);
          resolve(data);
        })
        .catch(error => {
          //console.log("regist-error", error.response);
          context.commit(SET_ERROR, error.response.data.errors);
          return reject(error);
        });
    });
  }
};

const mutations = {
  [SET_ERROR](state, error) {
    state.errors = error;
  },
  [SET_AUTH](state, user) {
    state.isAuthenticated = true;
    state.user = user;
    state.errors = {};
    JwtService.saveToken(state.user.token);
  },
  [SET_PASSWORD](state, password) {
    state.user.password = password;
  },
  [PURGE_AUTH](state) {
    state.isAuthenticated = false;
    state.user = {};
    state.errors = {};
    JwtService.destroyToken();
  }
};

export default {
  state,
  actions,
  mutations,
  getters
};
