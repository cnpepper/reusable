//根目录加载封装过的axios类
import request from '@/request.js'

// 使用 laravel passport 
export function axUserLogin(email,password){
    return request({
        url:'/user_login',
        method:'post',
        data:{
            email,
            password
        }
    })
}