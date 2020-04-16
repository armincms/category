Nova.booting((Vue, router, store) => {
  router.addRoutes([
    {
      name: 'category',
      path: '/category',
      component: require('./components/Tool'),
    },
  ])
})
