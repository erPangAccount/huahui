<?php

namespace App\Admin\Controllers;

use App\Models\CommodityCategory;
use App\Http\Controllers\Controller;
use Encore\Admin\Admin;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rule;

class CommodityCategoryController extends Controller
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
            ->description('商品类别列表')
            ->body($this->grid());
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('编辑')
            ->description('编辑商品类别')
            ->body($this->form($id)->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('新增')
            ->description('新增商品类别')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CommodityCategory);

        $grid->id('Id');
        $grid->parent_id('父级分类')->display(function ($content) {
            $categry = CommodityCategory::query()->find($content);
            if ($categry) {
                return $categry->name;
            }
        });

        $grid->name('分类名');
        $grid->created_at('创建时间');
        $grid->updated_at('更新时间');

        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->like('name', '类别名');
            $filter->equal('parent_id', '父级分类')->select('/admin/api/commodity_category?filterCategory=true');
        });

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableView();
        });
        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form($id = 0)
    {
        $form = new Form(new CommodityCategory);

        $firstCategory = CommodityCategory::query()->where('parent_id', 0)->get();
        $firstCategoryArr = [];
        foreach ($firstCategory as $item) {
            $firstCategoryArr[$item->id] = $item->name;
        }

        if ($id) {
            $category = CommodityCategory::query()->findOrFail($id);
            if ($category->parent()->exists() && $category->parent->parent()->exists()) {
                $form->select('parent_id_first', '一级分类')
                    ->options($firstCategoryArr)
                    ->default($category->parent->parent_id)
                    ->load('parent_id', '/admin/api/commodity_category');
            } else if ($category->parent()->exists() && !$category->parent->parent()->exists()) {
                $form->select('parent_id_first', '一级分类')
                    ->options($firstCategoryArr)
                    ->default($category->parent_id)
                    ->load('parent_id', '/admin/api/commodity_category');
            } else {
                $form->select('parent_id_first', '一级分类')
                    ->options($firstCategoryArr)
                    ->load('parent_id', '/admin/api/commodity_category');
            }
            $form->select('parent_id', '二级分类');
        } else {
            $form->select('parent_id_first', '一级分类')
                ->options($firstCategoryArr)
                ->load('parent_id', '/admin/api/commodity_category');

            $form->select('parent_id', '二级分类');
        }



        $form->text('name', '类别名')->rules([
            'required'
        ]);
        $form->ignore(['parent_id_first']);

        //执行js，
        Admin::script(<<<SCRIPT
            // 回填数据需要
            $('select[name="parent_id_first"]').trigger('change');
            // 添加二级分类需要
            $('form[class="form-horizontal"]').submit(function(e){
                if (!$('select[name="parent_id"]').val()) {
                    var firstValue = $('select[name="parent_id_first"]').val() || 0;
                    $('select[name="parent_id"]').html("<option value='" + firstValue + "' selected>二级分类</option>")

                }
            });

SCRIPT
        );

        $form->tools(function (Form\Tools $tools) {
            // 去掉`查看`按钮
            $tools->disableView();
        });

        $form->footer(function ($footer) {
            // 去掉`查看`checkbox
            $footer->disableViewCheck();

            // 去掉`继续编辑`checkbox
            $footer->disableEditingCheck();

//            // 去掉`继续创建`checkbox
//            $footer->disableCreatingCheck();
        });

        $form->saving(function (Form $form) {
            if ($form->model()->newQuery()->where('name', '=', $form->name)->where('parent_id', '=', $form->parent_id)->whereNull('deleted_at')->exists()) {
                $error = new MessageBag([
                    'title'   => '保存失败！',
                    'message' => '该商品分类已存在！',
                ]);

                return back()->with(compact('error'));
            }
        });

        return $form;
    }
}
