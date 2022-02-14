<?php

return [
    'createAccount' => [
        'classes' => [
            'App\Http\Controllers\SchedulingController' => [
                'deleteSchedulingEmails'
            ],
            'App\Http\Controllers\MailgunController' => [
                'subscribeList'
            ],
            'App\Http\Controllers\MailChimpController' => [
                'subscribeList'
            ]
        ],
    ],
    'completePayment' => [
        'classes' => [
            'App\Http\Controllers\SchedulingController' => [
                'deleteAllSchedulingEmailsByEventStop'
            ],
            'App\Http\Controllers\MailgunController' => [
                'subscribeList',
                'sendEmailTemplate'
            ]
        ],
    ],
    'receivedSupport' => [
        'classes' => [
            'App\Http\Controllers\SchedulingController' => [
                'deleteSchedulingEmails'
            ],
            'App\Http\Controllers\MailgunController' => [
                'sendEmailTemplate'
            ]
        ],
    ],
    'createReceipt' => [
        'classes' => [
            'App\Http\Controllers\SchedulingController' => [
                'deleteSchedulingEmails'
            ],
            'App\Http\Controllers\MailgunController' => [
                'sendEmailTemplate'
            ]
        ],
    ],
    'newProject' => [
        'classes' => [
            'App\Http\Controllers\SchedulingController' => [
                'deleteSchedulingEmails'
            ],
            'App\Http\Controllers\MailgunController' => [
                'sendEmailTemplate'
            ]
        ],
    ],
    'newStageProject' => [
        'classes' => [
            'App\Http\Controllers\SchedulingController' => [
                'deleteSchedulingEmails',
                'saveSchedulingEmails'
            ]
        ],
    ],
    'newAssessment' => [
        'classes' => [
            'App\Http\Controllers\SchedulingController' => [
                'deleteSchedulingEmails'
            ],
            'App\Http\Controllers\MailgunController' => [
                'sendEmailTemplate'
            ]
        ],
    ],
    'startPayment' => [
        'classes' => [
            'App\Http\Controllers\SchedulingController' => [
                'deleteSchedulingEmails',
                'saveSchedulingEmails'
            ],
        ],
    ],
    'newLogin' => [
        'classes' => [
            'App\Http\Controllers\SchedulingController' => [
                'deleteSchedulingEmails',
                'saveSchedulingEmails'
            ],
        ],
    ],
    'newProjectsWeek' => [
        'classes' => [
            'App\Http\Controllers\SchedulingController' => [
                'deleteSchedulingEmails'
            ],
            'App\Http\Controllers\MailgunController' => [
                'sendEmailTemplate'
            ]
        ],
    ],
    'startProject' => [
        'classes' => [
            'App\Http\Controllers\SchedulingController' => [
                'deleteSchedulingEmails',
                'saveSchedulingEmails'
            ],
        ],
    ],
    'userSP' => [
        'classes' => [
            'App\Http\Controllers\SchedulingController' => [
                'deleteSchedulingEmails'
            ],
            'App\Http\Controllers\MailgunController' => [
                'subscribeList'
            ]
        ],
    ],
    'removeUserSP' => [
        'classes' => [
            'App\Http\Controllers\SchedulingController' => [
                'deleteSchedulingEmails'
            ],
            'App\Http\Controllers\MailgunController' => [
                'updateMailList'
            ]
        ],
    ],
    'updateUser' => [
        'classes' => [
            'App\Http\Controllers\SchedulingController' => [
                'deleteSchedulingEmails'
            ],
            'App\Http\Controllers\MailgunController' => [
                'updateMailList'
            ],
            'App\Http\Controllers\MailChimpController' => [
                'updateMemberSubscribeList'
            ]
        ],
    ],
    'startEvaluation' => [
        'classes' => [
            'App\Http\Controllers\SchedulingController' => [
                'deleteSchedulingEmails',
                'saveSchedulingEmails'
            ]
        ],
    ],
    'stopEvaluation' => [
        'classes' => [
            'App\Http\Controllers\SchedulingController' => [
                'deleteSchedulingEmails',
            ]
        ],
    ],
    'notifyUserFinishedProject' => [
        'classes' => [
            'App\Http\Controllers\SchedulingController' => [
                'deleteSchedulingEmails',
                'saveSchedulingEmails'
            ]
        ],
    ],
    'updateNotifyUserFinishedProject' => [
        'classes' => [
            'App\Http\Controllers\SchedulingController' => [
                'updateSchedulingEmails',
            ]
        ],
    ],
    'createOrder' => [
        'classes' => [
            'App\Http\Controllers\JadLogController' => [
                'createOrder',
            ]
        ],
    ],
    'deleteOrder' => [
        'classes' => [
            'App\Http\Controllers\JadLogController' => [
                'deleteOrder',
            ]
        ],
    ],
    'trackingOrder' => [
        'classes' => [
            'App\Http\Controllers\JadLogController' => [
                'trackingOrder',
            ]
        ],
    ],
    "sendAlert" => [
        "classes" => [
            "App\\Http\\Controllers\\TelegramController" => [
                "sendMessage"
            ]
        ]
    ]
];
