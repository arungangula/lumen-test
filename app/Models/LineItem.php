<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class LineItem extends Model
{
    const ITEM_TYPE_PACKAGE             = 'PACKAGE';
    const ITEM_TYPE_PACKAGE_DISCOUNT    = 'PACKAGE_DISCOUNT';
    const ITEM_TYPE_COUPON_DISCOUNT     = 'COUPON_DISCOUNT';
    const ITEM_TYPE_SHIPPING_CHARGE_DISCOUNT     = 'SHIPPING_CHARGE_DISCOUNT';
    const ITEM_TYPE_PACKAGE_COMMISSION  = 'PACKAGE_COMMISSION';
    const ITEM_TYPE_PACKAGE_ADVANCE     = 'PACKAGE_ADVANCE';
    const ITEM_TYPE_PACKAGE_OUT_OF_STOCK     = 'OUT_OF_STOCK';
    const ITEM_TYPE_COMMISSION_ORDER    = 'COMMISSION_ORDER';
    const ITEM_TYPE_SERVICE_TAX         = 'SERVICE_TAX';
    const ITEM_TYPE_SWACHCH_TAX         = 'SWACHCH_TAX';
    const ITEM_TYPE_KRISHI_TAX          = 'KRISHI_TAX';
    const ITEM_TYPE_SHIPPING_CHARGE     = 'SHIPPING_CHARGE';
    const ITEM_TYPE_COD_CHARGE          = 'COD_CHARGE';
    const ITEM_TYPE_CGST                = 'CGST';
    const ITEM_TYPE_SGST                = 'SGST';
    const ITEM_TYPE_IGST                = 'IGST';
    const ITEM_ID_SHIPPING_CHARGE_DISCOUNT = 9999;

    protected $table = 'line_items';

    public static function TAXES() {
        return [
            self::ITEM_TYPE_SERVICE_TAX,
            self::ITEM_TYPE_SWACHCH_TAX,
            self::ITEM_TYPE_KRISHI_TAX,
            self::ITEM_TYPE_CGST,
            self::ITEM_TYPE_SGST,
            self::ITEM_TYPE_IGST,
        ];
    }

    public function order()
    {
      return $this->belongsTo('App\Models\Order','order_id');
    }

    public function package()
    {
      return $this->belongsTo('App\Models\Package','item_id');
    }

    public function getMetaData($key) {
        $data = json_decode($this->meta, true);
        return isset($data[$key]) ? $data[$key] : "";
    }
}
