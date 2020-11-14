import { Component } from '@angular/core';
import { Platform } from '@ionic/angular';
import { StatusBar } from '@ionic-native/status-bar/ngx';
import { SplashScreen } from '@ionic-native/splash-screen/ngx';

@Component({
  selector: 'app-tabs',
  templateUrl: 'tabs.page.html',
  styleUrls: ['tabs.page.scss']
})
export class TabsPage {

  constructor(
    private platform: Platform,
    private statusBar: StatusBar,    
    private splashScreen: SplashScreen,
  ) 
  {
    this.initializeApp();
  }

  initializeApp() 
  {
    this.platform.ready().then(() => 
    {
      this.statusBar.styleDefault(); 
      this.splashScreen.hide();
    });
  }

}
