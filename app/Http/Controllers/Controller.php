<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function requestDatatables(array $params = array())
    {
        $datas = [];

        $data['draw'] = $params['draw'];

        if (isset($params['order']) && !empty($params['order'])) {
            $data['orderable'] = [];
            foreach ($params['order'] as $key => $order) {
                if ($params['columns'][$order['column']]['orderable'] == 'true') {
                    $data['orderable'][$params['columns'][$order['column']]['name']] = [
                        'column' => $params['columns'][$order['column']]['name'],
                        'dir'    => $order['dir']
                    ];
                }
            }
        } else {
            $data['orderable'] = [];
        }

        if (isset($params['columns']) && !empty($params['columns'])) {
            $data['searchable'] = [];
            foreach ($params['columns'] as $key => $column) {
                if ($column['searchable'] == 'true') {
                    $data['searchable'][] = $column['name'];
                }
            }
        } else {
            $data['searchable'] = [];
        }


        $data['search'] = ($params['search']['value']) ? $params['search']['value'] : '';

        $data['start']  = ($params['start']) ? $params['start'] : 0;
        $data['length'] = ($params['length']) ? $params['length'] : 0;

        return $data;
    }

    public function returnJson($data = [], $status = 200, $success = true, $message = null)
    {
        return response()->json([
            'status' => $status,
            'success' => $success,
            'data' => $data,
            'message' => $message
        ], $status)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
    }

    public function decToFraction($float)
    {
        $denom = 0;
        $whole = floor($float);
        $decimal = $float - $whole;
        $leastCommonDenom = 48; // 16 * 3;
        $denominators = array(2, 3, 4, 8, 16, 24, 48);
        $roundedDecimal = round($decimal * $leastCommonDenom) / $leastCommonDenom;
        if ($roundedDecimal == 0)
            return $whole;
        if ($roundedDecimal == 1)
            return $whole + 1;
        foreach ($denominators as $d) {
            if ($roundedDecimal * $d == floor($roundedDecimal * $d)) {
                $denom = $d;
                break;
            }
        }
        return ($whole == 0 ? '' : $whole) . " " . ($roundedDecimal * $denom) . "/" . $denom;
    }
}
