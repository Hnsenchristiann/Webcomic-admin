/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout';

const authorRoutes = {
  path: '/tag',
  component: Layout,
  redirect: '/tag/list',
  meta: {
    title: 'Tags',
    icon: 'comic',
    permissions: ['view menu tags'],
  },
  children: [
    {
      path: 'list',
      component: () => import('@/views/tags/List'),
      name: 'Tag',
      meta: { title: 'Tag', noCache: true },
    },
    {
      path: 'create',
      component: () => import('@/views/tags/Create'),
      name: 'AddTag',
      meta: { title: 'Add Tag', noCache: true },
    },
    {
      path: 'edit/:id(\\d+)',
      component: () => import('@/views/tags/Edit'),
      name: 'EditTag',
      meta: { title: 'Edit Tag', noCache: true, permissions: ['manage article'] },
      hidden: true,
    },
  ],
};

export default authorRoutes;
