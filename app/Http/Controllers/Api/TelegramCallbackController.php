<?php

namespace App\Http\Controllers\Api;

use App\Enum\StateType;
use App\Http\Controllers\Controller;
use App\Models\TelegramState;
use App\Services\Telegram\TelegramBotApiContract;
use App\Services\TelegramStateService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramCallbackController extends Controller
{
    public function callback(Request $request, TelegramBotApiContract $service): void
    {
        $data = $request->all();

        $state = TelegramState::query()->orderBy('id', 'desc')->first();

        if($state && $state->is_active && isset($data['callback_query'])) {
            $state->update([
                'is_active' => false,
            ]);
        }

        if(isset($data['message'])) {
            Log::info($data['message']);
            $method = $data['message']['text'];

            if($state && $state->is_active) {

                if($state->state == StateType::NAME) {
                    TelegramStateService::saveName($method, $state);
                    $text = "Введите должность";
                    $service->sendMessage($text);
                    return;

                } elseif ($state->state == StateType::POSITION) {
                    TelegramStateService::savePosition($method, $state);
                    $text = "Введите номер телефона. \n Формат ввода: \n +79999999999";
                    $service->sendMessage($text);
                    return;

                } elseif ($state->state == StateType::PHONE) {
                    try {
                        $request->validate([
                            'message.text' => 'regex:/^((\+7)+([0-9]){10})$/'
                        ]);
                    } catch (\Throwable $e) {
                        $text = "Номер телефона должен соответствовать формату: \n +79999999999";
                        $service->sendMessage($text);
                        return;
                    }

                    TelegramStateService::savePhone($method, $state);
                    $text = "Введите дату рождения";
                    $service->sendMessage($text);
                    return;

                } elseif ($state->state == StateType::BIRTHDAY) {
                    try {
                        $dateAfter = now()->subYears(100);
                        $dateBefore = now()->subYears(18);

                        $request->validate([
                            'message.text' => "date|after:$dateAfter|before:$dateBefore"
                        ]);
                    } catch (\Throwable $e) {
                        $service->sendMessage($e->getMessage());
                        return;
                    }
                    TelegramStateService::saveBirthday($method, $state);

                    $button = [
                        'inline_keyboard' => [
                            [
                                [
                                    'text' => 'Сохранить',
                                    'callback_data' => "save"
                                ],
                            ],
                            [
                                [
                                    'text' => 'Отменить',
                                    'callback_data' => "close"
                                ],
                            ]
                        ]
                    ];
                    $service->sendMessage((string)view('users.add_worker', $state->data), json_encode($button));
                }
                return;
            }

            switch ($method) {
                case '/start':
                    $text = 'Выберите действия:';
                    $buttons = [
                        'inline_keyboard' => [
                            [
                                [
                                    'text' => 'Добавить сотрудника',
                                    'callback_data' => 'store'
                                ],
                                [
                                    'text' => 'Список сотрудников',
                                    'callback_data' => 'workers'
                                ],
                            ],
                            [
                                [
                                    'text' => 'Список уволенных',
                                    'callback_data' => 'firedWorkers'
                                ],
                                [
                                    'text' => 'Выгрузить в excel',
                                    'callback_data' => 'exportToExcel'
                                ],
                            ]
                        ]
                    ];
                    $service->sendMessage($text, json_encode($buttons));
                    break;

                default:
                    $text = "Для начала работы введите /start";
                    $service->sendMessage($text);
            }
        }

        if(isset($data['callback_query'])) {
            Log::info($data['callback_query']);
            $array = $data['callback_query']['data'];
            $method = explode('.', $array);

            switch ($method[0]) {
                case 'store':
                    $text = 'Введите ФИО';
                    $button = [
                        'inline_keyboard' => [
                            [
                                [
                                    'text' => 'Прервать добавление сотрудника',
                                    'callback_data' => "close"
                                ],
                            ]
                        ]
                    ];
                    $state = TelegramState::query()->create();
                    if($state) {
                        $service->sendMessage($text, json_encode($button));
                    }
                    break;

                case 'workers':
                    $text = "<strong>Список работающих сотрудников: \n</strong>";
                    $users = UserService::getWorkers();

                    if ($users->isEmpty()) {
                        $text = 'Сотрудников нет';
                        $service->sendMessage($text);
                    }

                    $service->sendMessage($text);
                    foreach ($users as $user) {
                        $button = [
                            'inline_keyboard' => [
                                [
                                    [
                                        'text' => 'Уволить',
                                        'callback_data' => "destroy.$user->id"
                                    ],
                                ]
                            ]
                        ];
                        $data = [
                            'user' => $user
                        ];
                        $service->sendMessage((string)view('users.workers', $data), json_encode($button));
                    }
                    break;

                case 'firedWorkers':
                    $text = "<strong>Список уволенных сотрудников: \n</strong>";
                    $users = UserService::getFiredWorkers();

                    if ($users->isEmpty()) {
                        $text = 'Уволенных сотрудников нет';
                        $service->sendMessage($text);
                    }
                    $service->sendMessage($text);

                    foreach ($users as $user) {
                        $button = [
                            'inline_keyboard' => [
                                [
                                    [
                                        'text' => 'Вернуть на работу',
                                        'callback_data' => "restore.$user->id"
                                    ],
                                ]
                            ]
                        ];
                        $data = [
                            'user' => $user
                        ];
                        $service->sendMessage((string)view('users.fired_workers', $data), json_encode($button));
                    }
                    break;

                case 'exportToExcel':
                    UserService::export();
                    $service->sendDocument('users.xls');
                    break;

                case 'destroy':
                    $userId = $method[1];
                    if (UserService::destroy($userId)) {
                        $service->sendMessage('Успешно уволен.');
                    }
                    break;

                case 'restore':
                    $userId = $method[1];
                    if (UserService::restore($userId)) {
                        $service->sendMessage('Успешно принят.');
                    }
                    break;

                case 'save':
                    $data = $state->data;
                    if (UserService::store($data)) {
                        $service->sendMessage('Успешно сохранен.');
                    }
                    break;

                case 'close':
                    $service->sendMessage('Добавление сотрудника отменено.');
                    break;
            }
        }
    }
}
