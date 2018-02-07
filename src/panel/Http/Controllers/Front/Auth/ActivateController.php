<?php

namespace InetStudio\AdminPanel\Http\Controllers\Front\Auth;

use App\User;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use InetStudio\AdminPanel\Events\Auth\ActivatedEvent;
use InetStudio\AdminPanel\Services\Front\Auth\UsersActivationsService;
use InetStudio\Meta\Contracts\Services\Front\MetaServiceContract as FrontMetaServiceContract;

class ActivateController extends Controller
{
    /**
     * Активируем аккаунт.
     *
     * @param UsersActivationsService $usersActivationsService
     * @param string $token
     * @return View
     */
    public function activate(UsersActivationsService $usersActivationsService, string $token = ''): View
    {
        $seoService = app()->make(FrontMetaServiceContract::class);

        $activation = $usersActivationsService->getActivationByToken($token);

        if ($activation !== null) {
            $user = User::find($activation->user_id);
            $user->activated = 1;
            $user->save();

            $usersActivationsService->deleteActivation($token);

            event(new ActivatedEvent($user));

            $activation = [
                'success' => true,
                'message' => trans('admin::activation.activationSuccess'),
            ];
        } else {
            $activation = [
                'success' => false,
                'message' => trans('admin::activation.activationFail'),
            ];
        }

        return view('admin::front.auth.activate', [
            'SEO' => $seoService->getAllTags(null),
            'activation' => $activation,
        ]);
    }
}