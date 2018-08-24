<template>
    <el-row class="panel m-w-1280">
        <el-col :span="24" class="panel-top">
            <el-col :span="20">
                <span class="p-l-20">合同管理系统</span>
            </el-col>
            <el-col :span="4" class="pos-rel">
                <el-dropdown @command="handleMenu" class="user-menu">
                    <span class="el-dropdown-link c-gra" style="cursor: default">
                        {{ username }}&nbsp;&nbsp;<i class="fa fa-user" aria-hidden="true"></i>
                    </span>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item command="changePwd">修改密码</el-dropdown-item>
                        <el-dropdown-item command="logout">退出</el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </el-col>
        </el-col>
        <el-col :span="24" class="panel-center">
            <aside class="w-180 ovf-hd" v-show="!showLeftMenu">
                <div class="h-50" v-for="item in secMenu">
                    <div class="w-100p h-50 p-l-40 left-menu pointer j-white" :class="{'j-blue': item.name == $route.name}" @click="routerChange(item)">
                        <i :class="item.icon"></i>
                        <span class="p-l-20 m-t-15">{{ item.title }}</span>
                    </div>
                </div>
            </aside>
            <section class="panel-c-c" :class="{'hide-leftMenu': hasChildMenu}">
                <div class="grid-content bg-purple-light">
                    <el-col :span="24">
                        <transition name="fade" mode="out-in" appear>
                            <router-view></router-view>
                        </transition>
                    </el-col>
                </div>
            </section>
        </el-col>
        <changePwd :dialogVisible.sync="showDialog"></changePwd>
    </el-row>

</template>

<script>
    import changePwd from '../components/changePwd.vue'
    import http from '../assets/js/http'

    export default {
        data() {
            return {
                showDialog: false,
                username: '',
                topMenu: [],
                childMenu: [],
                menuData: [],
                hasChildMenu: false,
                menu: null,
                module: null,
                logo_type: null,
                activeIndex: "1",
                secMenu: [
                    {
                        path: '/home/edit',
                        name: 'Edit',
                        icon: 'el-icon-document',
                        title: '合同录入'
                    },
                    {
                        path: '/home/result',
                        name: 'Result',
                        icon: 'el-icon-menu',
                        title: '数据报表'
                    },
                    {
                        path: '/home/detail?number=' + this.$store.state.number,
                        icon: 'el-icon-location',
                        name: 'Detail',
                        title: '合同详情'
                    }
                ]
            }
        },
        watch: {
            '$route': function () {
                this.getCurrent();
            }
        },
        mounted() {
            console.log(this.$store.state.number)
        },
        methods: {
            getCurrent() {
                if (this.$route.name === 'Detail') {
                    this.activeIndex = "3";
                }
            },
            logout() {
                this.$confirm('确认退出吗?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消'
                }).then(() => {
                    _g.openGlobalLoading()
                    let data = {
                        authkey: Lockr.get('authKey'),
                        sessionId: Lockr.get('sessionId')
                    }
                    this.apiPost('admin/base/logout', data).then((res) => {
                        _g.closeGlobalLoading()
                        this.handelResponse(res, (data) => {
                            Lockr.rm('menus')
                            Lockr.rm('authKey')
                            Lockr.rm('rememberKey')
                            Lockr.rm('authList')
                            Lockr.rm('userInfo')
                            Lockr.rm('sessionId')
                            Cookies.remove('rememberPwd')
                            _g.toastMsg('success', '退出成功')
                            setTimeout(() => {
                                router.replace('/')
                            }, 1500)
                        })
                    })
                }).catch(() => {

                })
            },
            handleMenu(val) {
                switch (val) {
                    case 'logout':
                        this.logout()
                        break
                    case 'changePwd':
                        this.changePwd()
                        break
                }
            },
            changePwd() {
                this.showDialog = true
            },
            getUsername() {
                this.username = Lockr.get('userInfo').username
            },
            routerChange(item) {
                if (item.name != this.$route.name) {
                     router.push(item.path)
                }
            },
        },
        created() {
            let authKey = Lockr.get('authKey');
            if (!authKey) {
                _g.toastMsg('warning', '您尚未登录');
                setTimeout(() => {
                    router.replace('/')
                }, 1500)
                return
            }
            this.getUsername();
        },
        computed: {
            routerShow() {
                return store.state.routerShow
            },
            showLeftMenu() {
                this.hasChildMenu = store.state.showLeftMenu;
                return store.state.showLeftMenu
            }

        },
        components: {
            changePwd
        },
        mixins: [http]
    }
</script>
<style>
    .j-white {
        color: #fff;
    }
    .j-blue {
        color: #20a0ff;
    }
    .fade-enter-active,
    .fade-leave-active {
        transition: opacity .5s
    }

    .fade-enter,
    .fade-leave-active {
        opacity: 0
    }

    .panel {
        position: absolute;
        top: 0px;
        bottom: 0px;
        width: 100%;
    }

    .panel-top {
        height: 60px;
        line-height: 60px;
        background: #1F2D3D;
        color: #c0ccda;
    }

    .panel-center {
        background: #324057;
        position: absolute;
        top: 60px;
        bottom: 0px;
        overflow: hidden;
    }

    .panel-c-c {
        background: #f1f2f7;
        position: absolute;
        right: 0px;
        top: 0px;
        bottom: 0px;
        left: 180px;
        overflow-y: scroll;
        padding: 20px;
    }

    .logout {
        background: url(../assets/images/logout_36.png);
        background-size: contain;
        width: 20px;
        height: 20px;
        float: left;
    }

    .logo {
        width: 150px;
        float: left;
        margin: 10px 10px 10px 18px;
    }

    .tip-logout {
        float: right;
        margin-right: 20px;
        padding-top: 5px;
        cursor: pointer;
    }

    .admin {
        color: #c0ccda;
        text-align: center;
    }

    .hide-leftMenu {
        left: 0px;
    }
</style>