<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ExampleController extends Controller
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

//    /**
//     * Edit interface.
//     *
//     * @param mixed   $id
//     * @param Content $content
//     * @return Content
//     */
//    public function edit($id, Content $content)
//    {
//        return $content
//            ->header('编辑')
//            ->description('编辑用户')
//            ->body($this->form()->edit($id));
//    }

//    /**
//     * Create interface.
//     *
//     * @param Content $content
//     * @return Content
//     */
//    public function create(Content $content)
//    {
//        return $content
//            ->header('新增')
//            ->description('新增用户')
//            ->body($this->form());
//    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Customer());

        $grid->id('ID')->sortable();
        $grid->column('email', '邮箱');
        $grid->column('mobile', '手机号');
        $grid->column('nick', '昵称');
        $grid->column('created_at', '创建时间')->sortable();
        $grid->column('updated_at', '更新时间')->sortable();

        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->equal('email', '邮箱');
            $filter->equal('mobile', '手机号');
            $filter->like('nick', '昵称');
        });


        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableEdit();
        });
        $grid->disableCreateButton();
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

        $show->fields([
            'id' => 'ID',
            'email' => '邮箱',
            'mobile' => '手机号',
            'nick' => '昵称',
            'created_at' => '创建时间',
            'updated_at' => '更新时间'
        ]);

        return $show;
    }

//    /**
//     * Make a form builder.
//     *
//     * @return Form
//     */
//    protected function form()
//    {
//        $form = new Form(new Customer());
//
//        $form->display('id', 'ID');
//        $form->text('email', '邮箱');
//
//        $form->footer(function ($footer) {
//            // 去掉`查看`checkbox
//            $footer->disableViewCheck();
//
//            // 去掉`继续编辑`checkbox
//            $footer->disableEditingCheck();
//
//            // 去掉`继续创建`checkbox
//            $footer->disableCreatingCheck();
//        });
//
//        return $form;
//    }
}
