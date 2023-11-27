<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SettingRequest;
use App\Interfaces\SettingInterface;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    use Message;

    protected $settingRepository;

    public function __construct(SettingInterface $settingRepository)
    {
        $this->middleware('permission:setting read', ['only' => ['index']]);
        $this->middleware('permission:setting edit', ['only' => ['update', 'edit']]);
        $this->settingRepository = $settingRepository;
    }

    public function index()
    {
        return $this->sendResponse(['setting' => $this->settingRepository->getSetting()], 'Data exited successfully');
    }

    public function edit($id)
    {
        return $this->sendResponse(['setting' => $this->settingRepository->getSettingById($id)], 'Data exited successfully');
    }

    public function update(SettingRequest $request, $id)
    {
        $data = $request->only(['close','facebook','phone','wats_app','online_payment','order_amount','best_prices_limits','cut_off_time']);
        $this->settingRepository->updateSetting($id, $data);
        return $this->sendResponse([], 'Data exited successfully');
    }

}
