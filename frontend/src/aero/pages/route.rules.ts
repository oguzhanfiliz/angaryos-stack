import { PagesComponent } from './pages.component';

import { DashboardComponent } from './dashboard/dashboard.component';
import { LoginComponent } from './login/login.component';
import { ListComponent } from './list/list.component';
import { ArchiveComponent } from './archive/archive.component';
import { DeletedComponent } from './deleted/deleted.component';
import { FormComponent } from './form/form.component';
import { ShowComponent } from './show/show.component';
import { NotFoundComponent } from './not-found/not-found.component';
import { AuthWizardComponent } from './auth-wizard/auth-wizard.component';

export const routeRules = 
[{
    path: '',
    component: PagesComponent,
    children: 
    [
        {
            path: '',
            redirectTo: 'dashboard',
            pathMatch: 'full',
        },
        {
            path: 'dashboard',
            component: DashboardComponent,
        },          
        {
            path: 'table/:tableName',
            component: ListComponent 
        },
        {
            path: 'table/:tableName/create',
            component: FormComponent 
        },
        {
            path: 'table/:tableName/:recordId/edit',
            component: FormComponent 
        },
        {
            path: 'table/:tableName/:recordId/archive',
            component: ArchiveComponent 
        },
        {
            path: 'table/:tableName/deleted',
            component: DeletedComponent 
        },
        {
            path: 'table/:tableName/:recordId',
            component: ShowComponent 
        },
        {
            path: 'table/:tableName/:recordId/edit',
            component: FormComponent 
        },          
        {
            path: 'authWizard/:tableName',
            component: AuthWizardComponent 
        },
        {
            path: '**',
            component: NotFoundComponent,
        }
    ]
}];