<?php

return [

    'credentials' => base_path(
        env(
            'FIREBASE_CREDENTIALS',
            'storage/app/firebase/firebase-credentials.json'
        )
    ),

];
