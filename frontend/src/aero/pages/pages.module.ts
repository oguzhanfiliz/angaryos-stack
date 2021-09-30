import { Injector, NgModule } from '@angular/core';
import { NgbModule, NgbDropdownConfig } from '@ng-bootstrap/ng-bootstrap';
import { GridsterModule } from 'angular-gridster2';
import { createCustomElement } from '@angular/elements';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { DragDropModule } from '@angular/cdk/drag-drop';
import { RouterModule } from '@angular/router';
import { InnerHtmlTransformerDirective } from './helpers/inner.html.transformer';
import { PagesRoutingModule } from './pages-routing.module';
import { PagesComponent } from './pages.component';
import { LinkPageComponent } from './link-page/link-page.component';
import { DashboardComponent } from './dashboard/dashboard.component';
import { RedirectComponent } from './redirect/redirect.component';
import { PrivacyPoliticaComponent } from './privacy-politica/privacy-politica.component';
import { LPPDComponent } from './lppd/lppd.component';
import { MobileHomeComponent } from './mobile-home/mobile-home.component';
import { MobileHomeDetailComponent } from './mobile-home-detail/mobile-home-detail.component';
import { MobileContactComponent } from './mobile-contact/mobile-contact.component';
import { ShortcutsComponent } from './shortcuts/shortcuts.component';
import { NotFoundComponent } from './not-found/not-found.component';
import { ListComponent } from './list/list.component';
import { SearchComponent } from './search/search.component';
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
import { PhoneElementComponent } from './helpers/custom-elements/form-element/phone-element/phone-element.component';
import { MoneyElementComponent } from './helpers/custom-elements/form-element/money-element/money-element.component';
import { RichTextElementComponent } from './helpers/custom-elements/form-element/rich-text-element/rich-text-element.component';
import { DataTableElementComponent } from './helpers/custom-elements/data-table-element/data-table-element.component';
import { DetailFilterElementComponent } from './helpers/custom-elements/data-table-element/detail-filter-element/detail-filter-element.component';
import { ColumnArrayElementComponent } from './helpers/custom-elements/column-array-element/column-array-element.component';
import { ColumnArrayFormElementComponent } from './helpers/custom-elements/column-array-form-element/column-array-form-element.component';
import { GeoPreviewElementComponent } from './helpers/custom-elements/geo-preview-element/geo-preview-element.component';
import { JsonViewerElementComponent } from './helpers/custom-elements/jsonviewer-element/jsonviewer-element.component';
import { RelationColumnElementComponent } from './helpers/custom-elements/relation-column-element/relation-column-element.component';
import { BooleanFastChangeElementComponent } from './helpers/custom-elements/boolean-fastchange-element/boolean-fastchange-element.component';
import { MobileBottomMenuElementComponent } from './helpers/custom-elements/mobile-bottom-menu-element/mobile-bottom-menu-element.component';
import { MobileAppBarElementComponent } from './helpers/custom-elements/mobile-app-bar-element/mobile-app-bar-element.component';
import { Camera } from '@ionic-native/camera/ngx'; 
import { Geolocation } from '@ionic-native/geolocation/ngx';
import { File } from '@ionic-native/file/ngx';
import { Device } from '@ionic-native/device/ngx';
import { NFC, Ndef } from '@ionic-native/nfc/ngx';

@NgModule({
  declarations: 
  [ 
    InnerHtmlTransformerDirective,
    PagesComponent,
    DashboardComponent,
    LinkPageComponent,
    RedirectComponent,
    ListComponent,
    SearchComponent,
    ArchiveComponent,
    DeletedComponent,
    ShowComponent,
    FormComponent,
    AuthWizardComponent,
    DataEntegratorComponent,

    LPPDComponent,
    PrivacyPoliticaComponent,
    NotFoundComponent,
    MobileHomeComponent,
    MobileHomeDetailComponent,
    MobileContactComponent,
    ShortcutsComponent,

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
    
    PhoneElementComponent,
    MoneyElementComponent,
    RichTextElementComponent,
    
    JsonViewerElementComponent,
    
    RelationColumnElementComponent,
    
    BooleanFastChangeElementComponent,
    
    MobileBottomMenuElementComponent,
    MobileAppBarElementComponent,
    
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
    FormsModule,
    NgbModule,
    GridsterModule 
  ],
  providers: 
  [ 
    NgbDropdownConfig, 
    Camera,
    Geolocation,
    File,
    Device,
    NFC,
    Ndef
  ], 
  bootstrap: [PagesComponent],
  entryComponents: 
  [
    
  ],
  exports: 
  [
    ShowComponent,
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
    
    PhoneElementComponent,
    MoneyElementComponent,
    RichTextElementComponent,
    
    JsonViewerElementComponent,
    
    RelationColumnElementComponent,
    
    BooleanFastChangeElementComponent,

    DataTableElementComponent,
    DetailFilterElementComponent,

    ColumnArrayElementComponent,
    ColumnArrayFormElementComponent,

    GeoPreviewElementComponent,
    
    MobileBottomMenuElementComponent,
    MobileAppBarElementComponent,
  ]
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
    
    'code-editor-element': CodeEditorElementComponent,
    
    'phone-element': PhoneElementComponent,
    'money-element': MoneyElementComponent,
    'rich-text-element': RichTextElementComponent,
    
    'jsonviewer-element': JsonViewerElementComponent,
    
    'relation-column-element': RelationColumnElementComponent,
    
    'boolean-fastchange-element': BooleanFastChangeElementComponent,
    
    'data-table-element': DataTableElementComponent,
    'detail-filter-element': DetailFilterElementComponent,
    'column-array-element': ColumnArrayElementComponent,
    'column-array-form-element': ColumnArrayFormElementComponent,
    'geo-preview-element': GeoPreviewElementComponent,
    
    'in-form-element': FormComponent,
    'in-show-element': ShowComponent,
    
    'shortcuts': ShortcutsComponent,
    
    'mobile-app-bar-element': MobileAppBarElementComponent,
    'mobile-bottom-menu-element': MobileBottomMenuElementComponent
  };

  constructor(private injector: Injector) { }

  ngDoBootstrap()
  {
    var keys = Object.keys(this.customElementList);
    for(var i = 0; i < keys.length; i++)
      customElements.define(keys[i], createCustomElement(this.customElementList[keys[i]], {injector: this.injector}));
  }
}