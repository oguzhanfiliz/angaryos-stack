import { BrowserModule } from '@angular/platform-browser';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';

import { Injector, NgModule } from '@angular/core';
import { createCustomElement } from '@angular/elements';

import { DragDropModule } from '@angular/cdk/drag-drop';

import { FormsModule } from '@angular/forms';

import { HttpClientModule } from '@angular/common/http';

import { RouterModule } from '@angular/router';

import { AeroRoutingModule } from './aero-routing.module';
import { AeroComponent } from './aero.component';

import { MapComponent } from './pages/map/map.component';
import { PublicMapComponent } from './pages/public-map/public-map.component';

import { FormElementComponent } from './pages/helpers/custom-elements/form-element/form-element.component';
import { TextElementComponent } from './pages/helpers/custom-elements/form-element/text-element/text-element.component';
import { StringElementComponent } from './pages/helpers/custom-elements/form-element/string-element/string-element.component';
import { PasswordElementComponent } from './pages/helpers/custom-elements/form-element/password-element/password-element.component';
import { NumberElementComponent } from './pages/helpers/custom-elements/form-element/number-element/number-element.component';
import { BooleanElementComponent } from './pages/helpers/custom-elements/form-element/boolean-element/boolean-element.component';
import { SelectElementComponent } from './pages/helpers/custom-elements/form-element/select-element/select-element.component';
import { MultiSelectElementComponent } from './pages/helpers/custom-elements/form-element/multi-select-element/multi-select-element.component';
import { FilesElementComponent } from './pages/helpers/custom-elements/form-element/files-element/files-element.component';
import { MultiSelectDragDropElementComponent } from './pages/helpers/custom-elements/form-element/multi-select-drag-drop-element/multi-select-drag-drop-element.component';
import { MapElementComponent } from './pages/helpers/custom-elements/form-element/map-element/map-element.component';
import { DateTimeElementComponent } from './pages/helpers/custom-elements/form-element/date-time-element/date-time-element.component';
import { DateElementComponent } from './pages/helpers/custom-elements/form-element/date-element/date-element.component';
import { TimeElementComponent } from './pages/helpers/custom-elements/form-element/time-element/time-element.component';
import { CodeEditorElementComponent } from './pages/helpers/custom-elements/form-element/code-editor-element/code-editor-element.component';

import { FullScreenMapElementComponent } from './pages/helpers/custom-elements/fullscreen-map-element/fullscreen-map-element.component';
import { ColumnArrayFormElementComponent } from './pages/helpers/custom-elements/column-array-form-element/column-array-form-element.component';
import { FormComponent } from './pages/form/form.component';

import { LoginComponent } from './pages/login/login.component';
import { LinkPageComponent } from './pages/link-page/link-page.component';

import { GuiTriggerHelper } from './pages/helpers/gui-trigger';
import { MessageHelper } from './pages/helpers/message';
import { SessionHelper } from './pages/helpers/session';
import { GeneralHelper } from './pages/helpers/general';
import { AeroThemeHelper } from './pages/helpers/aero.theme';

@NgModule({
  declarations: 
  [
    AeroComponent, 
    LinkPageComponent,
    LoginComponent,
    MapComponent,
    PublicMapComponent,
    FullScreenMapElementComponent,
    FormComponent,
    ColumnArrayFormElementComponent,
    
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
  ],
  imports: 
  [
    AeroRoutingModule,
    BrowserModule,
    BrowserAnimationsModule,
    HttpClientModule,
    FormsModule,
    DragDropModule
  ],
  providers: 
  [
    GuiTriggerHelper,
    MessageHelper,
    SessionHelper,
    GeneralHelper,
    AeroThemeHelper
  ],
  bootstrap: 
  [
    AeroComponent
  ],
  entryComponents: 
  [
    FullScreenMapElementComponent,
    FormComponent,
    ColumnArrayFormElementComponent,
    
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
  ],
})

export class AeroModule 
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
    
    'code-editor-element': CodeEditorElementComponent,
    
    'fullscreen-map-element': FullScreenMapElementComponent,
    'in-form-element': FormComponent,
    'column-array-form-lement': ColumnArrayFormElementComponent
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
