<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Self_;

class Order extends Model
{
    use SoftDeletes;

    const PAY_STATUS_UN = 'unpay';
    const PAY_STATUS_OK = 'paid';

    const REFUND_STATUS_PENDING = 'unapplied';
    const REFUND_STATUS_APPLIED = 'applied';
    const REFUND_STATUS_PROCESSING = 'processing';
    const REFUND_STATUS_SUCCESS = 'success';
    const REFUND_STATUS_FAILED = 'failed';

    const SHIP_STATUS_PENDING = 'undelivered';
    const SHIP_STATUS_DELIVERED = 'delivered';
    const SHIP_STATUS_RECEIVED = 'received';

    public static $refundStatusMap = [
        self::REFUND_STATUS_PENDING    => '未退款',
        self::REFUND_STATUS_APPLIED    => '已申请退款',
        self::REFUND_STATUS_PROCESSING => '退款中',
        self::REFUND_STATUS_SUCCESS    => '退款成功',
        self::REFUND_STATUS_FAILED     => '退款失败',
    ];

    public static $shipStatusMap = [
        self::SHIP_STATUS_PENDING   => '未发货',
        self::SHIP_STATUS_DELIVERED => '已发货',
        self::SHIP_STATUS_RECEIVED  => '已收货',
    ];

    /**
     * @var string
     */
    protected $table = 'orders';

    /**
     * @var array
     */
    protected $fillable = [
        'order_no',
        'customer_id',
        'address',
        'total_amount',
        'remark',
        'paid_at',
        'payment_method',
        'payment_no',
        'refund_status',
        'refund_no',
        'closed',
        'reviewed',
        'ship_status',
        'ship_data',
        'extra'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'address' => 'array',
        'ship_data' => 'array',
        'extra' => 'array',
        'closed' => 'boolean',
        'reviewed' => 'boolean',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'paid_at'
    ];

    /**
     *
     */
    protected static function boot()
    {
        parent::boot();
        // 监听模型创建事件，在写入数据库之前触发
        static::creating(function ($model) {
            // 如果模型的 no 字段为空
            if (!$model->order_no) {
                // 调用 findAvailableNo 生成订单流水号
                $model->order_no = static::findAvailableNo();
            }
        });
    }

    /**
     * @return mixed|string
     */
    public function getStatusAttribute()
    {

        if ($this->closed) {
            return [
                'key' => 'closed',
                'value' => '已关闭'
            ];
        } else if (!$this->reviewed && $this->ship_status === self::SHIP_STATUS_RECEIVED) {
            return [
                'key' => "need_review",
                'value' => '待评价'
            ];
        } else if (is_null($this->paid_at)) {
            return [
                'key' => self::PAY_STATUS_UN,
                'value' => '未支付'
            ];
        }else if($this->refund_status != self::REFUND_STATUS_PENDING) {
            return [
                'key' => $this->refund_status,
                'value' => self::$refundStatusMap[$this->refund_status]
            ];
        } else {
            return [
                'key' => $this->ship_status,
                'value' => self::$shipStatusMap[$this->ship_status]
            ];
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /**
     * @return bool|string
     * @throws \Exception
     */
    public static function findAvailableNo()
    {
        // 订单流水号前缀
        $prefix = 'OR' . date('YmdHis');
        for ($i = 0; $i < 10; $i++) {
            // 随机生成 6 位的数字
            $no = $prefix.str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            // 判断是否已经存在
            if (!static::query()->where('order_no', $no)->exists()) {
                return $no;
            }
        }
        Log::warning('订单号生成失败！');
        throw new \Exception('订单号生成失败！');
    }
}
