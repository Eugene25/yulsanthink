import { Injectable } from '@angular/core';

@Injectable()
export class WindowService {

  public userID : any;

  get windowRef() {
    return window
  }

  userStatusRef(userID) {
    this.userID = userID;
  }

}