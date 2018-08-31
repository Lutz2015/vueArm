import Login from './pages/login.vue'

import Home from './pages/home.vue'

import Edit from './pages/edit.vue'
import Result from './pages/result.vue'
import Detail from './pages/detail.vue'
import Cate from './pages/Cate.vue'

/**
 * meta参数解析
 * hideLeft: 是否隐藏左侧菜单，单页菜单为true
 * module: 菜单所属模块
 * menu: 所属菜单，用于判断三级菜单是否显示高亮，如菜单列表、添加菜单、编辑菜单都是'menu'，用户列表、添加用户、编辑用户都是'user'，如此类推
 */
const routes = [
    {
        path: '/',
        component: Login,
        title: '登录',
        name: 'Login',
        redirect: '/login',
    },
    {
        path: '/home',
        component: Home,
        title: '首页',
        children: [
            {
                path: 'edit',
                component: Edit,
                name: 'Edit',
                title: '合同录入',
                meta: {
                    hideLeft: false,
                }
            },
            {
                path: 'result',
                component: Result,
                name: 'Result',
                title: '报表展示',
                meta: {
                    hideLeft: false,
                }
            },
            {
                path: 'detail',
                component: Detail,
                name: 'Detail',
                title: '合同详情',
                meta: {
                    hideLeft: false,
                }
            },
            {
                path: 'cate',
                component: Cate,
                name: 'Cate',
                title: '合同详情',
                meta: {
                    hideLeft: false,
                }
            }
        ]
    }
]
export default routes
