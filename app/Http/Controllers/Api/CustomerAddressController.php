<?php
namespace App\Http\Controllers\Api;

use App\Facades\UtilsFacade;
use App\Http\Controllers\Controller;
use App\Service\CustomerAddressService;
use Illuminate\Http\Request;

class CustomerAddressController extends Controller
{
    /**
     * @var CustomerAddressService
     */
    protected $customerAddressService;

    /**
     * CustomerAddressController constructor.
     * @param CustomerAddressService $addressService
     */
    public function __construct(CustomerAddressService $addressService)
    {
        $this->customerAddressService = $addressService;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        return UtilsFacade::render($this->customerAddressService->page($request->all(), $request->user()->id));
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function show(Request $request, $id)
    {
        return UtilsFacade::render($this->customerAddressService->detail($id, $request->user()->id));
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        if (strlen($msg = $this->customerAddressService->validateSave($request->all(), $request->user()->id))) {
                return UtilsFacade::render(null, 2, $msg);
        }

        return UtilsFacade::render($this->customerAddressService->save($request->all(), $request->user()->id));
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        return UtilsFacade::render($this->customerAddressService->update($request->all(), $id, $request->user()->id));
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function destroy(Request $request, $id)
    {
        return UtilsFacade::render($this->customerAddressService->destroy($id, $request->user()->id));
    }
}