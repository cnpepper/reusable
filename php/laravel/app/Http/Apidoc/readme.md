FORMAT: 1A

# APIDoc

# User [/user]
用户模块

## 用户登录 [POST /user/login]
用户使用账号密码登录获取token信息。

+ Request (application/json)
    + Body

            {
                "email": "foo",
                "password": "1234567"
            }

+ Response 0 (application/json)
    + Body

            {
                "code": 0,
                "message": "ok",
                "result": ""
            }

# Test [/test]
测试模块

## 测试接口 [POST /test/query]
用来测试一些小功能。

+ Response 0 (application/json)
    + Body

            {
                "code": 0,
                "message": "ok",
                "result": ""
            }