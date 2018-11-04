<template>
    <div>
        <el-row>
            <el-col :span="24">
                <el-menu class="el-menu-demo" mode="horizontal"
                    background-color="#545c64" text-color="#fff" active-text-color="#ffd04b" :router="is_router">
                    <el-menu-item index="/home">首页</el-menu-item>
                    <el-submenu index="2">
                        <template slot="title">工作台</template>
                        <el-submenu index="2-4">
                            <template slot="title">Mysql控制台</template>
                            <el-menu-item index="/check">审核SQL</el-menu-item>
                            <el-menu-item index="/select">查询数据</el-menu-item>
                            <el-menu-item index="/query">SQL管理</el-menu-item>
                        </el-submenu>
                    </el-submenu>
                    <el-menu-item index="3" disabled>消息中心</el-menu-item>
                    <el-submenu index="4">
                        <template slot="title">用户</template>
                        <el-menu-item index="/">登出</el-menu-item>
                        <el-menu-item index="/test">测试页面</el-menu-item>
                    </el-submenu>
                </el-menu>
            </el-col>
        </el-row>
        <el-row>
            <el-col :span="24">
                <router-view></router-view>
            </el-col>
        </el-row>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                is_router:true
            }
        }
    }
</script>

<style>

</style>