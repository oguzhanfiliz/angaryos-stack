import { LoginComponent } from './pages/login/login.component';
//import { LinkPageComponent } from './pages/link-page/link-page.component';
import { PrivacyPoliticaComponent } from './pages/privacy-politica/privacy-politica.component'; 
import { LPPDComponent } from './pages/lppd/lppd.component';  
import { MobileHomeComponent } from './pages/mobile-home/mobile-home.component';  
import { MobileHomeDetailComponent } from './pages/mobile-home-detail/mobile-home-detail.component';  
import { MobileContactComponent } from './pages/mobile-contact/mobile-contact.component';  
import { PagesModule } from './pages/pages.module';

import { MapComponent } from './pages/map/map.component';
import { PublicMapComponent } from './pages/public-map/public-map.component';

import { BaseHelper } from './pages/helpers/base';

export const routeRules = 
[
    {
        path: 'mobile-home',
        component: MobileHomeComponent
    },
    {
        path: 'mobile-home-detail/:id',
        component: MobileHomeDetailComponent
    },
    {
        path: 'login',
        component: LoginComponent
    },
    {
        path: 'privacy-politica',
        component: PrivacyPoliticaComponent
    },
    {
        path: 'lppd',
        component: LPPDComponent
    },
    {
        path: 'mobile-contact',
        component: MobileContactComponent
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
    { path: '', redirectTo: (window.innerWidth > 640 ? 'login' : 'mobile-home'), pathMatch: 'full' },
    { path: '**', redirectTo: (window.innerWidth > 640 ? 'login' : 'mobile-home') }
];

console.log(routeRules);