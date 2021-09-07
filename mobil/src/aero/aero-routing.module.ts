import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { routeRules } from './route.rules';
const routes: Routes = routeRules;

@NgModule({
  imports: [RouterModule.forRoot(routes, {useHash: true})],
  exports: [RouterModule]
})
export class AeroRoutingModule { }
