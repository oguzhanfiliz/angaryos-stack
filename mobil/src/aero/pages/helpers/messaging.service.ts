/*/*import { Injectable } from '@angular/core';
import { AngularFireMessaging } from '@angular/fire/messaging';
import { BehaviorSubject } from 'rxjs'
import { BaseHelper } from './base';

@Injectable()

export class MessagingService 
{
    currentMessage = new BehaviorSubject(null);
    constructor(private angularFireMessaging: AngularFireMessaging) 
    {
        this.angularFireMessaging.messages.subscribe(
            (_messaging) => {
                _messaging['onMessage'] = _messaging['onMessage'].bind(_messaging);
                _messaging['onTokenRefresh'] = _messaging['onTokenRefresh'].bind(_messaging);
            }
        )
    }

    requestPermission() 
    {
        this.angularFireMessaging.requestToken.subscribe(
            (token) => 
            {
                BaseHelper.firebaseToken = token;

                var tokenTimeOut = 1000 * 60 * 60 * 24 * 5;
                BaseHelper.writeToLocal("firebaseToken", token, tokenTimeOut)
            },
            (err) => 
            {
                console.error('Unable to get permission to notify.', err);
            }
        );
    }

    receiveMessage() 
    {
        this.angularFireMessaging.messages.subscribe(
            (payload) => 
            {
                console.log("new message received. ", payload);
                this.currentMessage.next(payload);
            })
    }
}*/*/
