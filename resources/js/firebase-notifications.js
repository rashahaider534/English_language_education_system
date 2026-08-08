import { getToken } from 'firebase/messaging';
import { messaging } from './firebase';

console.log('Firebase notifications JS loaded');

export async function registerFirebaseToken() {

    try {

        const permission = await Notification.requestPermission();

        if (permission !== 'granted') {
            return;
        }

        const token = await getToken(messaging, {
            vapidKey: 'BFjqH9gcH1avIgfS9VTV14XLPM98IwY_lmAK-UpXwXKJJ-yNxQRCckMa-HiupcMGnYH_je5BAIGKoorwzob93Zc',
        });
console.log('FCM TOKEN:', token);
        if (!token) {
            return;
        }

        console.log('FCM Token:', token);

        await fetch('/firebase/token', {
            method: 'POST',

            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content'),
            },

            body: JSON.stringify({
                token: token,
                device_type: 'web',
                device_name: navigator.userAgent,
            }),
        });

    } catch (error) {
        console.error(error);
    }
}

registerFirebaseToken();
