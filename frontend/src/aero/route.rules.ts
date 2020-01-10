import { LoginComponent } from './pages/login/login.component';
import { PagesModule } from './pages/pages.module';

import { BaseHelper } from './pages/helpers/base';

export const routeRules = 
[
    {
        path: 'login',
        component: LoginComponent
    },
    {
        path: BaseHelper.angaryosUrlPath,
        loadChildren: () => import('./pages/pages.module').then(m => m.PagesModule),
    },  
    { path: '', redirectTo: BaseHelper.angaryosUrlPath, pathMatch: 'full' },
    { path: '**', redirectTo: BaseHelper.angaryosUrlPath }
];