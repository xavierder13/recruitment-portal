import Vue from 'vue';
import Router from 'vue-router';
import Home from './views/Home.vue';
import Login from './auth/Login.vue';
import Dashboard from './views/dashboard/Dashboard.vue';
import RecruitmentFrontendIndex from './views/recruitment_frontend/RecruitmentFrontendIndex.vue';
import JobVacanciesIndex from './views/recruitment/JobVacanciesIndex.vue';
import JobApplicantsIndex from './views/recruitment/JobApplicantsIndex.vue';
import JobApplicantsIndexNew from './views/recruitment/JobApplicantsIndexNew.vue';
import JobApplicantsView from './views/recruitment/JobApplicantsView.vue';
import UserIndex from './views/user/UserIndex.vue';
import UserCreate from './views/user/UserCreate.vue';
import UserProfile from './views/user/UserProfile.vue';
import PositionIndex from './views/position/PositionIndex.vue';
import BranchIndex from './views/branch/BranchIndex.vue';
import Permission from './views/permission/PermissionIndex.vue';
import Role from './views/role/RoleIndex.vue';
import ActivityLogs from './views/activity_logs/ActivityLogs.vue';
import PageNotFound from './404/PageNotFound.vue';
import Unauthorize from './401/Unauthorize.vue';

Vue.use(Router);

const routes = [
  {
    path: '/',
    name: 'home',
    component: Home,
    children: [
      {
        path: '/jobvacancies/index',
        name: 'jobvacancies.index',
        component: JobVacanciesIndex
      },
      // {
      //   path: '/jobapplicants/index',
      //   name: 'jobapplicants.index',
      //   component: JobApplicantsIndex
      // },
      {
        path: '/jobapplicants/index-new',
        name: 'jobapplicants.index_new',
        component: JobApplicantsIndexNew
      },
      {
        path: '/jobapplicants/view/:id',
        name: 'jobapplicants.view',
        component: JobApplicantsView
      },
      {
        path: '/user/index',
        name: 'user.index',
        component: UserIndex
      },
      {
        path: '/user/create',
        name: 'user.create',
        component: UserCreate
      },
      {
        path: '/user/profile',
        name: 'user.profile',
        component: UserProfile
      },
      {
        path: '/branch/index',
        name: 'branch.index',
        component: BranchIndex
      },
      {
        path: '/position/index',
        name: 'position.index',
        component: PositionIndex
      },
      {
        path: '/permission/index',
        name: 'permission.index',
        component: Permission
      },
      {
        path: '/role/index',
        name: 'role.index',
        component: Role
      },
      {
        path: '/activity_logs',
        name: 'activity_logs',
        component: ActivityLogs
      },
      {
        path: '/unauthorize',
        name: 'unauthorize',
        component: Unauthorize,
      }
    ],
    beforeEnter(to, from, next) {

      if (localStorage.getItem('access_token')) {
        next();
      }
      else {
        next('/job-vacancies');
      }
    }
  },
  {
    path: '/hradmin-login',
    name: 'login',
    component: Login,
    beforeEnter(to, from, next) {
      
      if (localStorage.getItem('access_token')) {
        next('/');
      }
      else {
        next();
      }
    }
  },
  {
    path: '*',
    name:'not.found',
    component: PageNotFound,
  },
  {
    path: '/job-vacancies',
    name:'job.vacancies',
    component: RecruitmentFrontendIndex,
  },
];

const router = new Router({
  routes: routes,
  // mode: 'history',
  hash: false,
});

export default router;