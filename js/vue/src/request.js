import axios from 'axios'
import store from '@/store'

// 创建axios实例
const service = axios.create({
    baseURL: 'http://homestead.test/api',
    timeout: 0
})

service.interceptors.request.use(
    config=> {
        // 添加token
        console.log('gettes-token:' + store.getters.authToken)
        config.headers['Authorization'] = 'Bearer '+store.getters.authToken
        return config
    },
    error=>{
        // Do something with request error
        //console.log(error) // for debug
        Promise.reject(error)
    }
    
)
/*
service.interceptors.response.use(
    response=>{
        //todo.. 处理响应信息
        response
    },
    error=>{
        error
    }
)*/

export default service