import { Injectable } from '@angular/core';
import {student} from './student';
import {parent} from './parent';
import {employee} from './employee';
import { Router } from '@angular/router';

import { AngularFirestore, AngularFirestoreCollection, AngularFirestoreDocument } from 'angularfire2/firestore';
import { Observable } from '@firebase/util';
import * as firebase from 'firebase';
import { AngularFireStorage } from 'angularfire2/storage';

@Injectable({
  providedIn: 'root'
})
export class ManageService {

  students : Observable<student[]>;

  payOption : number;

  constructor( public afs:AngularFirestore,
    private afStorage: AngularFireStorage,
    private router: Router) { 
    afs.firestore.settings({ timestampsInSnapshots: true });
  }

  uploadFile(fileName,id){
    console.log(id);
    return this.afStorage.storage.ref().child('workerContacts/'+id+'.png').put(fileName);
}

  getLoginState(){
    firebase.auth().onAuthStateChanged(user => {
     //console.log(user);
     return user;
    });
  }

  getStudents(option) {

    if(option=='-1'){
      return this.afs.collection<student>('studentList').valueChanges();
    }else {
      return this.afs.collection<student>('studentList').doc(option).valueChanges();
    }
  }

  getParent(index) {
    return this.afs.collection<student>('studentList').doc(index).collection<parent>('parent').valueChanges();
  }

  getEmployees(option) {
   if(option=='-1')return this.afs.collection<employee>('employeeList').valueChanges();
   
    else return this.afs.collection<employee>('employeeList').doc(option).valueChanges();
  }

  addNewEmployee(name,age,id,address,contractType,
    employeeNumber,uploadState:boolean,startTime,endTime,
    startDate,endDate,timePay,totalWage,dayPerWeek) {

    this.afs.collection<employee>('employeeList').doc(employeeNumber).set({
      'name' : name,
      'age' : age,
      'id' : id,
      'employeeNumber' : employeeNumber,
      'address' : address,
      'contractType' : contractType
    });

    this.afs.collection<employee>('employeeList').doc(employeeNumber).collection('contractInfo').doc(contractType).set({
      'contractUploadState' : uploadState,
      'startTime' : startTime,
      'endTime' : endTime,
      'startDate' : startDate,
      'endDate' : endDate,
      'timePay' : timePay,
      'totalWage' : totalWage,
      'dayPerWeek' : dayPerWeek
    });

    this.router.navigate(['/employeeInfo', ]);
  }

  addNewStudent(name :string ,age : number,grade :number,
    id : string ,monthFee : number,subject:string,
    address:string,admissionDate:string,studentNumber : number) {
      
      console.log(subject);

      this.afs.collection<student>('studentList').doc(studentNumber.toString()).set({
        'name' : name,
        'age' : age,
        'grade' : grade,
        'id' : id,
        'monthFee' : monthFee,
        'subject' : subject,
        'address' : address,
        'admissionDate' : admissionDate,
        'studentNumber' : studentNumber
  });
  }

  addNewParent(studentNumber:number,father:string,mother:string,payWay:string,credit:string,account:string) {

    if(payWay=="credit") {
      this.payOption = 0;
      this.afs.collection<student>('studentList').doc(studentNumber.toString()).collection<parent>('parent').doc(mother+father).set({
        'father' : father,
        'mother' : mother,
        'payWay' : this.payOption,
        'credit' : credit });
    }else if(payWay=="account") {
      this.payOption = 1;
      this.afs.collection<student>('studentList').doc(studentNumber.toString()).collection<parent>('parent').doc(mother+father).set({
        'father' : father,
        'mother' : mother,
        'payWay' : this.payOption,
        'account' : account}); }
    else {
      this.payOption =2;
      this.afs.collection<student>('studentList').doc(studentNumber.toString()).collection<parent>('parent').doc(mother+father).set({
        'father' : father,
        'mother' : mother,
        'payWay' : this.payOption });
    }

  }

}

