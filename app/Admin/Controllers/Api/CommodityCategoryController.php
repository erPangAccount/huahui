<?php
namespace App\Admin\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommodityCategory;
use Illuminate\Http\Request;

class CommodityCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = CommodityCategory::query()->selectRaw("id, name as text");
        if ($request->get('filterCategory', '')) {
            $query->where('parent_id', '=', 0);
        } else if ($request->get('filterCommodity', '')) {
            $query->where('level', '=', 3);
        } else {
            $query->where('parent_id', '=', $request->get('q', ''));
        }

        return $query->get();
    }
}