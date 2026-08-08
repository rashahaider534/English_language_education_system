importScripts(
    'https://www.gstatic.com/firebasejs/11.0.2/firebase-app-compat.js'
);

importScripts(
    'https://www.gstatic.com/firebasejs/11.0.2/firebase-messaging-compat.js'
);

firebase.initializeApp({
    apiKey: "AIzaSyBA7Hq0EI7RoU9uXTitfGBZFHdDQmzihX8",
  authDomain: "fluentproject-8103c.firebaseapp.com",
  projectId: "fluentproject-8103c",
  storageBucket: "fluentproject-8103c.firebasestorage.app",
  messagingSenderId: "9173555919",
  appId: "1:9173555919:web:3941ae25e4646fbe00f5ba",
});

const messaging = firebase.messaging();
