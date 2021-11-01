/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout';

const comicRoutes = {
  path: '/comic',
  component: Layout,
  redirect: '/comic/list',
  meta: {
    title: 'Comics',
    icon: 'book',
    permissions: ['view menu comics'],
  },
  children: [
    {
      path: 'list',
      component: () => import('@/views/comics/List'),
      name: 'ComicsList',
      meta: { title: 'Comics', noCache: true },
    },
    {
      path: 'create',
      component: () => import('@/views/comics/Create'),
      name: 'CreateComic',
      meta: { title: 'Create Comic' },
    },
    {
      path: 'edit/:id(\\d+)',
      component: () => import('@/views/comics/Edit'),
      name: 'EditComic',
      meta: { title: 'Edit Comic', noCache: true },
      hidden: true,
    },
  ],
};

export default comicRoutes;
