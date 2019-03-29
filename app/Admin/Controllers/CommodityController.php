<?php

namespace App\Admin\Controllers;

use App\Models\Commodity;
use App\Http\Controllers\Controller;
use App\Models\CommodityAttribute;
use App\Models\CommodityCategory;
use App\Models\CommoditySku;
use Encore\Admin\Admin;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use http\Client\Curl\User;
use Illuminate\Validation\Rule;

class CommodityController extends Controller
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
            ->description('商品列表')
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
            ->description('编辑商品')
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
            ->description('新增商品')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Commodity);

        $grid->id('Id')->sortable();
        $grid->column('category.name', '商品类别')->sortable();
        $grid->name('商品名称');
        $grid->image('主图')->display(function ($content) {
            if ($content) {
                $url = env('APP_URL') . '/app/public/admin/' . $content;
                return '<img src="' . $url . '" style="max-width:100px" alt="" srcset="">';
            }
        });
        $grid->on_sale('上架否')->display(function ($content) {
            return $content ? '是' : '否';
        });
        $grid->rating('评分')->display(function ($content) {
            return $content . '分';
        })->sortable();
        $grid->sold_count('销售量')->display(function ($content) {
            return number_format((float)$content, 0, '', ',');
        })->sortable();
        $grid->review_count('评论数')->display(function ($content) {
            return number_format((float)$content, 0, '', ',');
        })->sortable();
        $grid->price('价格')->display(function ($content) {
            return number_format((float)$content, 0, '', ',');
        })->sortable();
        $grid->created_at('创建时间');
        $grid->updated_at('更新时间');

        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->like('name', '商品名称');
            $filter->equal('category_id', '商品类别')->select('/admin/api/commodity_category?filterCommodity=true');
            $filter->equal('on_sale', '上架否')->radio([
                0    => '否',
                1    => '是',
            ]);
            $filter->lt('rating', '最低评分');
            $filter->lt('rating', '最高评分');
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
        $form = new Form(new Commodity);

        //商品类别
        $firstCategory = CommodityCategory::query()->where('parent_id', 0)->get();
        $firstCategoryArr = [];
        foreach ($firstCategory as $item) {
            $firstCategoryArr[$item->id] = $item->name;
        }

        if ($id) {
            $commodity = Commodity::query()->findOrFail($id);
            $form->select(
                'category_id_first', '商品一级类别')
                ->options($firstCategoryArr)
                ->default($commodity->category->parent->parent_id)
                ->load('category_id_second', '/admin/api/commodity_category');
            $form->select('category_id_second', '商品二级类别')
                ->default($commodity->category->parent_id)
                ->load('category_id', '/admin/api/commodity_category');
            $form->select('category_id', '商品三级类别')->rules([
                'required',
                Rule::exists((new CommodityCategory())->getTable(), 'id')->where('level', 3)->whereNull('deleted_at')
            ]);
        } else {
            $form->select(
                'category_id_first', '商品一级类别')
                ->options($firstCategoryArr)
                ->load('category_id_second', '/admin/api/commodity_category');
            $form->select('category_id_second', '商品二级类别')->load('category_id', '/admin/api/commodity_category');
            $form->select('category_id', '商品三级类别')->rules([
                'required',
                Rule::exists((new CommodityCategory())->getTable(), 'id')->where('level', 3)->whereNull('deleted_at')
            ]);
        }
        //执行js，
        Admin::script(<<<SCRIPT
            // 回填数据需要
            $('select[name="category_id_first"]').trigger('change');
            $('select[name="category_id_second"]').trigger('change');
SCRIPT
        );


        $form->ignore(['category_id_first', 'category_id_second']);

        //商品名
        $form->text('name', '商品名')->rules([
            'max:120'
        ]);
        $form->editor('description', '商品详情');
        $form->image('image', '商品首图')->removable();
        // 图册
//        $form->multipleImage('images', '商品图册')->removable()->rules([
//            'max:9'
//        ]);
        //是否上架
        $form->switch('on_sale', '上架否')->states([
            'on' => ['value' => 1, 'text' => '是', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => '否', 'color' => 'danger'],
        ])->default(true);
        //评分
        $form->decimal('rating', '评分')->default(5.0)->rules([
            'max:5.0',
            'min:0.0'
        ]);
        //销售数量
        $form->number('sold_count', '销售数量')->default(0)->rules([
            'integer'
        ]);
        // 直接添加一对多的关联模型
        $form->hasMany('skus', '单品列表', function (Form\NestedForm $form) {
            $form->text('sku_name', '单品名称')->rules('required');
            $form->text('sku_description', '单品描述')->rules('required');
            $form->image('sku_image', '单品图片')->removable();
            $form->currency('sku_price', '价格')->symbol('￥')->default(0)->rules('required');
            $form->number('sku_stock', '剩余库存')->rules('required|integer|min:0');
        });

        $form->tools(function (Form\Tools $tools) {
            // 去掉`查看`按钮
            $tools->disableView();
        });

        $form->footer(function ($footer) {
            // 去掉`查看`checkbox
            $footer->disableViewCheck();
        });


        // 定义事件回调，当模型即将保存时会触发这个回调
        $form->saving(function (Form $form) {
            $form->model()->price = collect($form->input('skus'))->where(Form::REMOVE_FLAG_NAME, '=', 0)->min('price') ?: 0;
        });

        return $form;
    }
}
