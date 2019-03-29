<?php

namespace App\Admin\Controllers;

use App\Models\Customer;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UsersController extends Controller
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
            ->description('用户列表')
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
            ->description('用户详情')
            ->body($this->detail($id));
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Customer);

        $grid->id('Id');
        $grid->email('邮箱');
        $grid->mobile('手机号码');
        $grid->nick('昵称');
        $grid->created_at('创建时间');
        $grid->updated_at('更新时间');
        // 不在页面显示 `新建` 按钮，因为我们不需要在后台新建用户
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            // 不在每一行后面展示删除按钮
            $actions->disableDelete();
            $actions->disableEdit();
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
        $show = new Show(Customer::findOrFail($id));

        $show->id('Id');
        $show->email('邮箱');
        $show->mobile('手机号码');
        $show->nick('昵称');
        $show->secret('密码')->as(function ($content) {
            return "********";
        });
        $show->openid('Openid');
        $show->created_at('创建时间');
        $show->updated_at('更新时间');

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
            });;

        return $show;
    }

}
