<?php

namespace App\Http\Controllers\Admin\Report\Attendance;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ResponseAjax;

class PersonController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
    * Settings Controller.
    *
    * @return \Illuminate\Http\Response
    */
    public function personView()
    {
        $staffs = DB::table('staffs')->get();

        return view('admin.report.attendance.person', ['staffs' => $staffs]);
    }

    /**
    * Settings Controller.
    *
    * @return \Illuminate\Http\Response
    */

    public function person(Request $request)
    {

        ResponseAjax::set('LoginStatus', 1);
        ResponseAjax::set('SuccessStatus', 0);

        $data = $request->input('data');

        $_token     = $data['_token'];

        $date_start = $data['date_start'];
        $date_end   = $data['date_end'];
        $staff_id   = $data['staff_id'];

        /*
         * Start : Incoming data check
         * */

        $ErrorCount = 0;

        if (empty($date_start))
        {
            ResponseAjax::set('Message.text', 'Select Start Date');
            $ErrorCount++;
        }

        if (empty($date_end))
        {
            ResponseAjax::set('Message.text', 'Select End Date');
            $ErrorCount++;
        }

        if ($staff_id < 0)
        {
            ResponseAjax::set('Message.text', 'Select Staff');
            $ErrorCount++; 
        }
        /*
         * End : Incoming data check
         * */


        /*
         * Start : insert in database
         * */
        if($ErrorCount == 0)
        {
            $attendance_staffs = DB::table('attendance_staffs as a')
            ->leftJoin('attendance_rules_staff as r', 'a.rule_id', '=', 'r.id')
            ->where('staff_id', $staff_id)
            ->where('date', '>=',$date_start)
            ->where('date', '<=',$date_end)
            ->select('a.id', 'a.staff_id', 'a.rule_id', 'a.date', 'a.entry_time', 'a.out_time', 'a.attened', 'a.entry_status', 'a.entry_status_comment', 'a.entry_delay_or_advanced_duration', 'a.out_status', 'a.out_status_comment', 'a.out_delay_or_advanced_duration', 'a.work_duration', 'a.status', 'r.name as rule_name')
            ->get();

            if($attendance_staffs)
            {
                ResponseAjax::set('SuccessStatus', 1);
                ResponseAjax::set('ResponseData', $attendance_staffs);
                ResponseAjax::set('Message.text', '<h3 style="color:#fff">Successfully staffs attendance retrived</h3>');
            }
            else
            {
                ResponseAjax::set('Message.text', 'Error - Insert Data execution error (Error - 1)');
            }
        }
        /*
         * End : insert in database
         * */

        ResponseAjax::Response();
    }
}
