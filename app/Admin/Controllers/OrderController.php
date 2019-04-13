<?php

namespace App\Admin\Controllers;

use App\Models\Commodity;
use App\Models\CommoditySku;
use App\Models\Customer;
use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Encore\Admin\Admin;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('列表')
            ->description('订单列表')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('详情')
            ->description('订单详情')
            ->body($this->detail($id));
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Order);

        $grid->order_no('订单号');
        $grid->customer_id('下单人')->display(function ($customer_id) {
            return Customer::query()->find($customer_id)->value('mobile');
        });
        $grid->address('收货人信息')->display(function ($address) {
            return '<span style="font-weight: bold">收件人：</span>' . $address['recipient']['name'] . '<br /><span style="font-weight: bold">收件人手机号：</span>' . $address['recipient']['phone'] . '<br /><span style="font-weight: bold">收货地址：</span>' . $address['address'];
        })->style('max-width:200px;word-break:break-all;');;
        $grid->total_amount('订单金额');
        $grid->paid_at('支付时间');
        $grid->payment_method('支付方式')->display(function ($method) {
            $returnStr = "";
            switch ($method) {
                case 'cash':
                    $returnStr = "现金";
                    break;
                default:
                    $returnStr = $method;
                    break;
            }
            return $returnStr;
        });

        $grid->refund_status('退款状态')->display(function ($status) {
            return Order::$refundStatusMap[$status];
        });
        $grid->closed('取消订单否')->display(function ($val) {
            return $val ? '已取消' : '未取消';
        });
        $grid->ship_status('物流状态')->display(function ($status) {
            return Order::$shipStatusMap[$status];
        });
        $grid->created_at('创建时间');


        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->where(function ($query) {  //过滤下单人
                $query->whereHas('customer', function ($query) {
                    $query->where('mobile', 'like', "%{$this->input}%");
                });

            }, '下单人手机号')->mobile();

            $filter->where(function ($query) { //过滤支付否
                if ($this->input) {
                    $query->whereNotNull('paid_at');
                } else {
                    $query->whereNull('paid_at');
                }
            }, '支付否')->radio([
                0 => '未支付',
                1 => '已支付',
            ]);
            //过滤退款状态
            $filter->equal('refund_status', '退款状态')->radio(Order::$refundStatusMap);

            //过滤物流状态
            $filter->equal('ship_status', '物流状态')->radio(Order::$shipStatusMap);

            //过滤下单时间
            $filter->column(1 / 2, function ($filter) {
                $filter->between('created_at', '下单时间')->datetime();
            }, '下单区间');
        });

        $grid->actions(function ($actions) {
            // 不在每一行后面展示删除按钮
            $actions->disableDelete();
            $actions->disableEdit();
            if ($actions->row->status['key'] === Order::SHIP_STATUS_PENDING) {    //待发货
                $actions->append('<span style="color: #3c8dbc;cursor:pointer " title="发货" id="ship" url="' . url('/admin/api/orders', ['id' => $actions->getKey()]) . '"><i class="fa fa-share"></i></span>');

                Admin::script(<<<SCRIPT
                    document.getElementById('ship').addEventListener('click', function() {
                        var url =  this.getAttribute('url');
                        $.ajax({
                            type: 'PUT',
                            url: url,
                            data: {
                                status: 'ship'
                            },
                            dataType: 'json',
                            success: function(res) {
                                if (!res.status) {
                                    location.reload();
                                } 
                            },
                        });
                    });
SCRIPT
                );
            }

            if ($actions->row->status['key'] === Order::REFUND_STATUS_APPLIED) {
                $actions->append('<span style="color: #3c8dbc;cursor:pointer " title="允许退款" id="agree-refund" url="' . url('/admin/api/orders', ['id' => $actions->getKey()]) . '"><i class="fa fa-check"></i></span>');
                $actions->append('<span style="color: #3c8dbc;cursor:pointer " title="拒绝退款" id="refuse-refund" url="' . url('/admin/api/orders', ['id' => $actions->getKey()]) . '"><i class="fa fa-times"></i></span>');

                Admin::script(<<<SCRIPT
                    document.getElementById('agree-refund').addEventListener('click', function() {
                        var url =  this.getAttribute('url');
                        $.ajax({
                            type: 'PUT',
                            url: url,
                            data: {
                                status: 'agree'
                            },
                            dataType: 'json',
                            success: function(res) {
                                if (!res.status) {
                                    location.reload();
                                } 
                            },
                        });
                      
                    });
                    
                    document.getElementById('refuse-refund').addEventListener('click', function() {
                        var url =  this.getAttribute('url');
                        $.ajax({
                            type: 'PUT',
                            url: url,
                            data: {
                                status: 'refuse'
                            },
                            dataType: 'json',
                            success: function(res) {
                                if (!res.status) {
                                    location.reload();
                                } 
                            },
                        });
                      
                    });
SCRIPT
                );
            }
        });


        $grid->tools(function ($tools) {
            // 禁用批量删除按钮
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });


        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Order::findOrFail($id));

        $show->order_no('订单编号');

        $show->customer_id('下单人')->as(function ($customer_id) {
            return Customer::query()->find($customer_id)->value('mobile');
        });
        $show->address('收货人信息')->as(function ($address) {
            return '收件人：' . $address['recipient']['name'] . '&nbsp;收件人手机号：' . $address['recipient']['phone'] . '&nbsp;收货地址：' . $address['address'];
        });
        $show->total_amount('订单金额');
        $show->remark('订单备注');

        if (is_null($show->getModel()->getAttribute('paid_at'))) {
            $show->paid_at('支付否')->as(function ($val) {
                return "未支付";
            });
        } else {
            $show->paid_at('支付时间');
            $show->payment_method('支付方式')->as(function ($method) {
                $returnStr = "";
                switch ($method) {
                    case 'cash':
                        $returnStr = "现金";
                        break;
                    default:
                        $returnStr = $method;
                        break;
                }
                return $returnStr;
            });
            $show->payment_no('支付平台订单号');
        }

        $show->refund_status('退款状态')->as(function ($status) {
            return Order::$refundStatusMap[$status];
        });
        $show->refund_no('退款单号');
        $show->closed('取消订单否')->as(function ($val) {
            return $val ? '已取消' : '未取消';
        });
        $show->ship_status('物流状态')->as(function ($status) {
            return Order::$shipStatusMap[$status];
        });
        $show->created_at('创建时间');
        $show->updated_at('更新时间');

        $show->panel()->tools(function ($tools) {
            $tools->disableEdit();
            $tools->disableDelete();
        });

        $show->remark('商品')->unescape()->as(function ($val) use ($show) {
            // table
            $headers = ['商品名', '购买类型', '图片', '数量', '价格', '评分', '评论'];
            $orderItems = $show->getModel()->items;
            $commodityIds = $orderItems->pluck('commodity_id');
            $commodities = Commodity::query()->whereIn('id', $commodityIds)->get();
            $commoditySkuIds = $orderItems->pluck('commodity_sku_id');
            $commoditySkus = CommoditySku::query()->whereIn('id', $commoditySkuIds)->get();
            $rows = [];
            foreach ($orderItems as $key => $item) {
                foreach ($commodities as $commodity) {
                    if ($item->commodity_id == $commodity->id) {
                        $rows[$key][] = $commodity->name;
                        break;
                    }
                }

                foreach ($commoditySkus as $commoditySku) {
                    if ($item->commodity_sku_id == $commoditySku->id) {
                        $rows[$key][] = $commoditySku->sku_name;
                        $rows[$key][] = "<img style='width: 100%' src='$commoditySku->sku_image'>";
                        break;
                    }
                }
                $rows[$key][] = $item->number;
                $rows[$key][] = $item->price;
                $rows[$key][] = $item->rating;
                $rows[$key][] = $item->review;
            }

            $table = new Table($headers, $rows);

            return $table->render();
        });


        return $show;
    }


}
