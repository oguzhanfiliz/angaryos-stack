import { LoginComponent } from './pages/login/login.component';
import { LinkPageComponent } from './pages/link-page/link-page.component';
import { PagesModule } from './pages/pages.module';

import { MapComponent } from './pages/map/map.component';
import { PublicMapComponent } from './pages/public-map/public-map.component';

import { BaseHelper } from './pages/helpers/base';

export const routeRules = 
[
    {
        path: '',
        component: LinkPageComponent
    },
    {
        path: 'login',
        component: LoginComponent
    },
    {
        path: 'map',
        component: PublicMapComponent
    },
    {
        path: BaseHelper.angaryosUrlPath+'/map',
        component: MapComponent
    },
    {
        path: BaseHelper.angaryosUrlPath,
        loadChildren: () => import('./pages/pages.module').then(m => m.PagesModule),
    },  
    { path: '', redirectTo: BaseHelper.angaryosUrlPath, pathMatch: 'full' },
    { path: '**', redirectTo: BaseHelper.angaryosUrlPath }
];