// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
import { getMessaging } from 'firebase/messaging';
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyBA7Hq0EI7RoU9uXTitfGBZFHdDQmzihX8",
  authDomain: "fluentproject-8103c.firebaseapp.com",
  projectId: "fluentproject-8103c",
  storageBucket: "fluentproject-8103c.firebasestorage.app",
  messagingSenderId: "9173555919",
  appId: "1:9173555919:web:3941ae25e4646fbe00f5ba",
  measurementId: "G-JZ4WCSWZQV"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
//const analytics = getAnalytics(app);
export const messaging = getMessaging(app);
