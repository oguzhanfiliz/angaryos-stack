import { Injector, NgModule } from '@angular/core';
import { createCustomElement } from '@angular/elements';

import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { DragDropModule } from '@angular/cdk/drag-drop';

import { RouterModule } from '@angular/router';

import { PagesRoutingModule } from './pages-routing.module';

import { PagesComponent } from './pages.component';

import { DashboardComponent } from './dashboard/dashboard.component';
import { NotFoundComponent } from './not-found/not-found.component';
import { ListComponent } from './list/list.component';
import { ArchiveComponent } from './archive/archive.component';
import { DeletedComponent } from './deleted/deleted.component';
import { ShowComponent } from './show/show.component';
import { FormComponent } from './form/form.component';
import { AuthWizardComponent } from './auth-wizard/auth-wizard.component';
import { DataEntegratorComponent } from './data-entegrator/data-entegrator.component';



import { FormElementComponent } from './helpers/custom-elements/form-element/form-element.component';
import { TextElementComponent } from './helpers/custom-elements/form-element/text-element/text-element.component';
import { StringElementComponent } from './helpers/custom-elements/form-element/string-element/string-element.component';
import { PasswordElementComponent } from './helpers/custom-elements/form-element/password-element/password-element.component';
import { NumberElementComponent } from './helpers/custom-elements/form-element/number-element/number-element.component';
import { BooleanElementComponent } from './helpers/custom-elements/form-element/boolean-element/boolean-element.component';
import { SelectElementComponent } from './helpers/custom-elements/form-element/select-element/select-element.component';
import { MultiSelectElementComponent } from './helpers/custom-elements/form-element/multi-select-element/multi-select-element.component';
import { FilesElementComponent } from './helpers/custom-elements/form-element/files-element/files-element.component';
import { MultiSelectDragDropElementComponent } from './helpers/custom-elements/form-element/multi-select-drag-drop-element/multi-select-drag-drop-element.component';
import { MapElementComponent } from './helpers/custom-elements/form-element/map-element/map-element.component';
import { DateTimeElementComponent } from './helpers/custom-elements/form-element/date-time-element/date-time-element.component';
import { DateElementComponent } from './helpers/custom-elements/form-element/date-element/date-element.component';
import { TimeElementComponent } from './helpers/custom-elements/form-element/time-element/time-element.component';
import { CodeEditorElementComponent } from './helpers/custom-elements/form-element/code-editor-element/code-editor-element.component';

import { DataTableElementComponent } from './helpers/custom-elements/data-table-element/data-table-element.component';
import { DetailFilterElementComponent } from './helpers/custom-elements/data-table-element/detail-filter-element/detail-filter-element.component';
import { ColumnArrayElementComponent } from './helpers/custom-elements/column-array-element/column-array-element.component';
import { ColumnArrayFormElementComponent } from './helpers/custom-elements/column-array-form-element/column-array-form-element.component';

import { GeoPreviewElementComponent } from './helpers/custom-elements/geo-preview-element/geo-preview-element.component';



@NgModule({
  declarations: 
  [ 
    PagesComponent,
    DashboardComponent,
    ListComponent,
    ArchiveComponent,
    DeletedComponent,
    ShowComponent,
    FormComponent,
    AuthWizardComponent,
    DataEntegratorComponent,

    NotFoundComponent,

    FormElementComponent,
    TextElementComponent,
    StringElementComponent,
    PasswordElementComponent,
    NumberElementComponent,
    BooleanElementComponent,
    SelectElementComponent,
    MultiSelectElementComponent,
    FilesElementComponent,
    MultiSelectDragDropElementComponent,
    MapElementComponent,
    DateTimeElementComponent,
    DateElementComponent,
    TimeElementComponent,
    CodeEditorElementComponent,

    DataTableElementComponent,
    DetailFilterElementComponent,

    ColumnArrayElementComponent,
    ColumnArrayFormElementComponent,

    GeoPreviewElementComponent
  ],
  imports: 
  [
    PagesRoutingModule,
    CommonModule,
    DragDropModule,
    FormsModule
  ],
  providers: [ ],
  bootstrap: [PagesComponent],
  entryComponents: 
  [
    FormComponent,
    FormElementComponent,
    TextElementComponent,
    StringElementComponent,
    PasswordElementComponent,
    NumberElementComponent,
    BooleanElementComponent,
    SelectElementComponent,
    MultiSelectElementComponent,
    FilesElementComponent,
    MultiSelectDragDropElementComponent,
    MapElementComponent,
    DateTimeElementComponent,
    DateElementComponent,
    TimeElementComponent,
    CodeEditorElementComponent,

    DataTableElementComponent,
    DetailFilterElementComponent,

    ColumnArrayElementComponent,
    ColumnArrayFormElementComponent,

    GeoPreviewElementComponent
  ],
})
export class PagesModule 
{
  customElementList =
  {
    'form-element': FormElementComponent,
    'text-element': TextElementComponent,
    'string-element': StringElementComponent,
    'password-element': PasswordElementComponent,
    'number-element': NumberElementComponent,
    'boolean-element': BooleanElementComponent,
    'select-element': SelectElementComponent,
    'multi-select-element': MultiSelectElementComponent,
    'files-element': FilesElementComponent,    
    'multi-select-drag-drop-element': MultiSelectDragDropElementComponent,
    'map-element': MapElementComponent,
    
    'date-time-element': DateTimeElementComponent,
    'date-element': DateElementComponent,
    'time-element': TimeElementComponent,
    
    'data-table-element': DataTableElementComponent,
    'detail-filter-element': DetailFilterElementComponent,
    'column-array-element': ColumnArrayElementComponent,
    'column-array-form-element': ColumnArrayFormElementComponent,
    'geo-preview-element': GeoPreviewElementComponent,

    'code-editor-element': CodeEditorElementComponent,

    'in-form-element': FormComponent
  };

  constructor(private injector: Injector) 
  {
    
  }

  ngDoBootstrap()
  {
    var keys = Object.keys(this.customElementList);
    for(var i = 0; i < keys.length; i++)
      customElements.define(keys[i], createCustomElement(this.customElementList[keys[i]], {injector: this.injector}));
  }
}