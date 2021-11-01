/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout';

const authorRoutes = {
  path: '/genre',
  component: Layout,
  redirect: '/genre/list',
  meta: {
    title: 'Genres',
    icon: 'comic',
    permissions: ['view menu genres'],
  },
  children: [
    {
      path: 'list',
      component: () => import('@/views/genres/List'),
      name: 'Genre',
      meta: { title: 'Genre', noCache: true },
    },
    {
      path: 'create',
      component: () => import('@/views/genres/Create'),
      name: 'AddGenre',
      meta: { title: 'Add Genre', noCache: true },
    },
    {
      path: 'edit/:id(\\d+)',
      component: () => import('@/views/genres/Edit'),
      name: 'EditGenre',
      meta: { title: 'editGenre', noCache: true, permissions: ['manage article'] },
      hidden: true,
    },
  ],
};

export default authorRoutes;
