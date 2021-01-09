importScripts('https://www.gstatic.com/firebasejs/7.6.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/7.6.0/firebase-messaging.js');

var firebaseConfig = {
        apiKey: "XXXXXXXXXX",
        authDomain: "xxx.firebaseapp.com",
        projectId: "xxx",
        storageBucket: "xxx.appspot.com",
        messagingSenderId: "XXXXXXXXXX",
        appId: "xxxxxxxxx"
    };

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();