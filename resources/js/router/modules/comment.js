/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout';

const commentRoutes = {
  path: '/comment',
  component: Layout,
  redirect: '/comment/list',
  meta: {
    title: 'Comments',
    icon: 'book',
    permissions: ['view menu comments'],
  },
  children: [
    {
      path: 'list',
      component: () => import('@/views/comments/List'),
      name: 'CommentsList',
      meta: { title: 'Comments', noCache: true },
    },
  ],
};

export default commentRoutes;
