const mutations = {
  showLeftMenu(state, status) {
    state.showLeftMenu = status
  },
  showLoading(state, status) {
    state.globalLoading = status
  },
  setNumber(state, number) {
    state.number = number
  },
  setRules(state, rules) {
    state.rules = rules
  },
  setUsers(state, users) {
    state.users = users
  }
}

export default mutations
