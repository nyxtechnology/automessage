<?php

namespace App\Http\Controllers;

use App\Events\RegisteredNewUser;
use App\Events\WebhookReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Routing\Controller as BaseController;

class ReceiverController extends Controller
{
  public function handleWebhook(Request $request)
  {
    $eventsMap = [
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
            'params' => $request->json('metadata'),
            'event' => $request->json('event')
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
            'params' => $request->json('metadata'),
            'event' => $request->json('event')
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
            'params' => $request->json('metadata'),
            'event' => $request->json('event')
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
            'params' => $request->json('metadata'),
            'event' => $request->json('event')
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
            'params' => $request->json('metadata'),
            'event' => $request->json('event')
        ],
        'newStageProject' => [
            'classes' => [
                'App\Http\Controllers\SchedulingController' => [
                    'deleteSchedulingEmails',
                    'saveSchedulingEmails'
                ]
            ],
            'params' => $request->json('metadata'),
            'event' => $request->json('event')
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
            'params' => $request->json('metadata'),
            'event' => $request->json('event')
        ],
        'startPayment' => [
            'classes' => [
                'App\Http\Controllers\SchedulingController' => [
                    'deleteSchedulingEmails',
                    'saveSchedulingEmails'
                ],
            ],
            'params' => $request->json('metadata'),
            'event' => $request->json('event')
        ],
        'newLogin' => [
            'classes' => [
                'App\Http\Controllers\SchedulingController' => [
                    'deleteSchedulingEmails',
                    'saveSchedulingEmails'
                ],
            ],
            'params' => $request->json('metadata'),
            'event' => $request->json('event')
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
            'params' => $request->json('metadata'),
            'event' => $request->json('event')
        ],
        'startProject' => [
            'classes' => [
                'App\Http\Controllers\SchedulingController' => [
                    'deleteSchedulingEmails',
                    'saveSchedulingEmails'
                ],
            ],
            'params' => $request->json('metadata'),
            'event' => $request->json('event')
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
            'params' => $request->json('metadata'),
            'event' => $request->json('event')
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
            'params' => $request->json('metadata'),
            'event' => $request->json('event')
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
            'params' => $request->json('metadata'),
            'event' => $request->json('event')
        ],
        'startEvaluation' => [
            'classes' => [
                'App\Http\Controllers\SchedulingController' => [
                    'deleteSchedulingEmails',
                    'saveSchedulingEmails'
                ]
            ],
            'params' => $request->json('metadata'),
            'event' => $request->json('event')
        ],
        'stopEvaluation' => [
            'classes' => [
                'App\Http\Controllers\SchedulingController' => [
                    'deleteSchedulingEmails',
                ]
            ],
            'params' => $request->json('metadata'),
            'event' => $request->json('event')
        ],
        'notifyUserFinishedProject' => [
            'classes' => [
                'App\Http\Controllers\SchedulingController' => [
                    'deleteSchedulingEmails',
                    'saveSchedulingEmails'
                ]
            ],
            'params' => $request->json('metadata'),
            'event' => $request->json('event')
        ],
        'updateNotifyUserFinishedProject' => [
            'classes' => [
                'App\Http\Controllers\SchedulingController' => [
                    'updateSchedulingEmails',
                ]
            ],
            'params' => $request->json('metadata'),
            'event' => $request->json('event')
        ],
        'createOrder' => [
            'classes' => [
                'App\Http\Controllers\JadLogController' => [
                    'createOrder',
                ]
            ],
            'params' => $request->json('metadata'),
            'event' => $request->json('event'),
            'webhook' => $request->json('webhook'),
        ],
        'deleteOrder' => [
            'classes' => [
                'App\Http\Controllers\JadLogController' => [
                    'deleteOrder',
                ]
            ],
            'params' => $request->json('metadata'),
            'event' => $request->json('event'),
            'webhook' => $request->json('webhook'),
        ],
        'trackingOrder' => [
            'classes' => [
                'App\Http\Controllers\JadLogController' => [
                    'trackingOrder',
                ]
            ],
            'params' => $request->json('metadata'),
            'event' => $request->json('event'),
            'webhook' => $request->json('webhook'),
        ]
    ];
    $settings = $eventsMap[$request->json('event')];
    $this->generateLog('Recebido', $settings['params']['to'], $request->json('event'), $request->json('metadata'));
    event(new WebhookReceived($settings));
    return response()->json(['message' => 'NYX - Automessage API', 'status' => 'OK']);
  }
}
