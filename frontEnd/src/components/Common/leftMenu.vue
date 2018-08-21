<template>
	<div>  
		<div v-for="item in menuData">
				<template v-if="item.menu == menu">
					<div class="w-100p h-50 p-l-40 left-menu pointer c-blue" @click="routerChange(item)">{{item.title}}</div>
				</template>
				<template v-else>
					<div class="w-100p h-50 p-l-40 left-menu pointer c-gra" @click="routerChange(item)">
						{{item.title}}
					</div>
				</template>
		</div>
	</div>
</template>

<script>
export default {
  props: ['menuData', 'menu'],
  data() {
    return {
    }
  },
  methods: {
    routerChange(item) 	{
      // 与当前页面路由相等则刷新页面
      if (item.url != this.$route.path) {
        router.push(item.url)
      } else {
          if (this.$route.query.name) {
              router.replace({ name: this.$route.query.name })
          }
      }
    }
  }
}
</script>