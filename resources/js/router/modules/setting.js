/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout';

const settingRoutes = {
  path: '/setting',
  component: Layout,
  meta: {
    title: 'Settings',
  },
  children: [
    {
      path: 'list',
      component: () => import('@/views/settings/List'),
      name: 'SettingList',
      meta: { title: 'Settings', noCache: true },
    },
  ],
};

export default settingRoutes;
