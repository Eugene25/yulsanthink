import { Component } from '@angular/core';

import * as firebase from 'firebase';

export interface Tile {
  color: string;
  cols: number;
  rows: number;
  text: string;
}

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  
  panelOpenState = false;
  user:any;

  getUserState() {
    firebase.auth().onAuthStateChanged(user => {
      //console.log(user);
      this.user= user;
     });
  }

  ngOnInit() {
    this.getUserState();
  }

}
