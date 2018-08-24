const actions = {
  showLeftMenu ({ commit }, status) {
    commit('showLeftMenu', status)
  },
  showLoading ({ commit }, status) {
    commit('showLoading', status)
  },
  setNumber({ commit }, number) {
    commit('setNumber', number)
  },
  setRules({ commit }, rules) {
    commit('setRules', rules)
  },
  setUsers({ commit }, users) {
    commit('setUsers', users)
  }
}

export default actions
