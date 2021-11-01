/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout';

const authorRoutes = {
  path: '/author',
  component: Layout,
  redirect: '/author/list',
  meta: {
    title: 'Authors',
    icon: 'user',
    permissions: ['view menu administrator'],
  },
  children: [
    {
      path: 'list',
      component: () => import('@/views/authors/List'),
      name: 'AuthorList',
      meta: { title: 'Author', noCache: true },
    },
    {
      path: 'create',
      component: () => import('@/views/authors/Create'),
      name: 'Author',
      meta: { title: 'Add Author', noCache: true },
    },
    {
      path: 'edit/:id(\\d+)',
      component: () => import('@/views/authors/Edit'),
      name: 'EditAuthor',
      meta: { title: 'Edit Author', noCache: true },
      hidden: true,
    },
  ],
};

export default authorRoutes;
