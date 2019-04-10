<?php
namespace  App\Http\Controllers\Api;

use App\Facades\UtilsFacade;
use App\Http\Controllers\Controller;
use App\Service\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * @var CartService
     */
    protected $cartService;

    /**
     * CartController constructor.
     * @param CartService $cartService
     */
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        return UtilsFacade::render($this->cartService->page($request->all(), $request->user()->id));
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
       if (strlen($msg = $this->cartService->validateRequest($request->all(), $request->user()->id))) {
           return UtilsFacade::render(null, 1, $msg);
       }

       return UtilsFacade::render($this->cartService->save($request->all(), $request->user()->id));
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function update(Request $request, $id)
    {
        return UtilsFacade::render($this->cartService->update($id, $request->get('number', 0), $request->user()->id));
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function destroy(Request $request, $id)
    {
        return UtilsFacade::render($this->cartService->destroy($id, $request->user()->id));
    }

}