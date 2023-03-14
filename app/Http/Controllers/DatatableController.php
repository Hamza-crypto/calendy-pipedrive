<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DatatableController extends Controller
{

    public function tasks(Request $request)
    {

        $totalData = Task::filters($request->all())->count();

        $totalFiltered = $totalData;

        $start = $request->length == -1 ? 0 : $request->start;
        $limit = $request->length == -1 ? $totalData : $request->length;


        $dbColumns = [
            0 => "id",
            5 => "left_location",
            6 => "date_paid",
            7 => "invoice_amount",
            8 => "created_at",
        ];

        $orderColumnIndex = $request->input('order.0.column');

        $orderDbColumn = $dbColumns[$orderColumnIndex];
        $orderDirection = $request->input('order.0.dir');


        if (empty($request->input('search.value'))) {
            $tasks = Task::filters($request->all());

            $tasks = $tasks->orderBy($orderDbColumn, $orderDirection);

            $tasks = $tasks->offset($start)->limit($limit)->get();

        } else {
            $search = $request->input('search.value');
            $tasks = Task::filters($request->all());
            $tasks = $tasks->where(function ($q1) use ($search) {
                $q1->where('email', 'LIKE', "%$search%")
                    ->orWhere('phone', 'LIKE', "%$search%");

            })->get();

            $totalFiltered = count($tasks);
            $tasks = $tasks->skip($start)->take($limit);
        }

//
        $data = [];

        foreach ($tasks as &$task) {
            $task->created_at2 = Carbon::parse($task->created_at)->diffForHumans();

            $task->type = gettype($task->created_at);
            $data[] = $task;
        }


        $extraInfo = [
            'total_orders_count' => $totalFiltered
        ];


        $data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalFiltered,
            'data' => $data,
            'extra_info' => $extraInfo
        ];

        return response()->json($data);


    }


}
