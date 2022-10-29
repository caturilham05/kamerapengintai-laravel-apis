<?php

namespace App\Helper;

use App\Helper\Helper as HelperHelper;
use App\Models\WarehouseOrder;

class Helper
{
    public static function arrayFirst(array $arr)
    {
        foreach ($arr as $key => $unused) {
            return $key;
        }
        return NULL;
    }

    public static function invoice($code = '', $table_db = '', $check_type = false, $single_code = 0, $delimiter = '/')
    {
        $condition = $check_type ? '%' . $code . '%' : 1;
        $dt_invoice_all = WarehouseOrder::select('id', 'invoice')->where('invoice', 'like', $condition)->orderBy('id', 'DESC')->limit(1)->get()->toArray();

        if (empty($dt_invoice_all)) return false;
        foreach ($dt_invoice_all as $key => $value) {
            $invoice[$value['id']] = $value['invoice'];
        }

        $dt_invoice = '';
        $month      = array(
            '01' => 'I',
            '02' => 'II',
            '03' => 'III',
            '04' => 'IV',
            '05' => 'V',
            '06' => 'VI',
            '07' => 'VII',
            '08' => 'VIII',
            '09' => 'IX',
            '10' => 'X',
            '11' => 'XI',
            '12' => 'XII',
        );
        $date_year = date('y');
        $out_month = $month[date('m')];

        foreach ($invoice as $key => $value) {
            $invoice_explode = explode($delimiter, $value);
            $invoice_explode_new[$key] = $invoice_explode;
        }

        $group_arr = array();
        if (!empty($single_code)) {
            foreach ($invoice_explode_new as $key2 => $value2) {
                if (@$value2[1] == $date_year && @$value2[2] == $out_month) {
                    $group_arr[$key2] = $value2[3];
                    arsort($group_arr);
                }
            }
        } else {
            foreach ($invoice_explode_new as $key2 => $value2) {
                if (@$value2[2] == $date_year && @$value2[3] == $out_month) {
                    $group_arr[$key2] = $value2[4];
                    arsort($group_arr);
                }
            }
            if (is_array(@$invoice_explode_new[Helper::arrayFirst($group_arr)])) {
                $dt_invoice = implode($delimiter, $invoice_explode_new[Helper::arrayFirst($group_arr)]);
            }
        }
        if (empty($dt_invoice)) {
            $last_inv = str_pad('1', 6, '0', STR_PAD_LEFT);
        } else {
            $inv      = explode($delimiter, $dt_invoice);
            $delimiter_with_year = '%' . $delimiter . $date_year . '%';
            $invoice_date_year = WarehouseOrder::select('invoice')->where('invoice', 'like', $delimiter_with_year)->orderBy('id', 'DESC')->limit(1)->get()->toArray();
            if (empty($invoice_date_year)) {
                $last_inv = str_pad('1', 6, '0', STR_PAD_LEFT);
            } else {
                $inv_p        = !empty($single_code) ? intval($inv[3] + 1) : intval($inv[4] + 1);
                $last_inv     = str_pad($inv_p, 6, '0', STR_PAD_LEFT);
                $invoice_month_exists = WarehouseOrder::select('invoice')->where('invoice', 'like', '%/' . $out_month . '/%')->orderBy('id', 'DESC')->limit(1)->get()->toArray();
                $last_inv = empty($invoice_month_exists) ? str_pad('1', 6, '0', STR_PAD_LEFT) : $last_inv;
            }
            $last_inv = (empty($dt_invoice)) ? '000001' : (($last_inv == 1000000) ? '000001' : (($last_inv != 1000000) ? $last_inv : ''));
            $out      = $code . $date_year . $delimiter . $out_month . $delimiter . $last_inv;

            return $out;
        }
    }
}
