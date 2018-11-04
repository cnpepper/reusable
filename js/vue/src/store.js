import Vue from 'vue'
import Vuex from 'vuex'
import Cookies from 'js-cookie'
Vue.use(Vuex)

// 加载API接口
import {
  axUserLogin
} from '@/api/userLogin'

const store = new Vuex.Store({
  state: {
    token: ''
  },
  getters: {
    authToken: state => {
      console.log('state-token:' + state.token)
      return state.token
    }
  },
  mutations: {
    SET_TOKEN(state, token) {
      console.log('toke:' + token)
      state.token = token
      console.log('set-token:' + state.token)
    }
  },
  actions: {
    UserLogin(context, login_form) {
      return new Promise((resolve, reject) => {
        //cookie 已经存在token直接返回
        /*if (Cookies.get('USER_LOGIN_TOKEN')) {
          resolve(0)
        }*/
        //请求登录API
        axUserLogin(login_form.email, login_form.password).then(response => {
          console.log(response);
          let status = response.data.status
          if (0 == status) {
            let user_token = response.data.info.token
            //Cookies.set('USER_LOGIN_TOKEN', user_token)
            context.commit('SET_TOKEN', user_token)
          }
          resolve(status)
        }).catch(error => {
          reject(error)
        })
      })
    }
  }
})

export default store