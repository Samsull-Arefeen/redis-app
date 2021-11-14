<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redis;

class PersonController extends Controller {

    public function migrate() {
        set_time_limit(0);

        $import_val = $this->_csvToArray("files/test-data.csv");
        $counter = 0;

        foreach ($import_val as $row_data) {
            DB::insert('insert into persons (id, email, name, birthday, birthday_year, birthday_month, phone, ip, country) values (?, ?, ?, ?, ?, ?, ?, ?, ?)', $row_data);
            $counter++;
        }

        echo "Total " . $counter . " records added.";
    }

    private function _csvToArray($filename = '') {
        if (!file_exists($filename) || !is_readable($filename)) {
            return "Couldn't find/read the file";
        }

        $file_data = trim(file_get_contents($filename));
        $rows = array_filter(explode(PHP_EOL, $file_data));

        $data = [];

        foreach ($rows as $key => $val) {
            // Ignore the column name entry.
            if (($key != 0) && !empty($val)) {
                $details = explode(",", $val);
                $data[$key][] = !empty($details[0]) ? $details[0] : "";
                $data[$key][] = !empty($details[1]) ? $details[1] : "";
                $data[$key][] = !empty($details[2]) ? $details[2] : "";
                $data[$key][] = !empty($details[3]) ? date("Y-m-d H:i:s", strtotime($details[3])) : "";
                $data[$key][] = !empty($details[3]) ? (int) date("Y", strtotime($details[3])) : "";
                $data[$key][] = !empty($details[3]) ? (int) date("m", strtotime($details[3])) : "";
                $data[$key][] = !empty($details[4]) ? $details[4] : "";
                $data[$key][] = !empty($details[5]) ? $details[5] : "";
                $data[$key][] = !empty($details[6]) ? $details[6] : "";
            }
        }

        return $data;
    }

    public function list(Request $request) {
        $year = !empty($request->input('year')) ? (int) $request->input('year') : false;
        $month = !empty($request->input('month')) ? (int) $request->input('month') : false;
        $page = !empty($request->input('page')) ? (int) $request->input('page') : 1;

        $where = [];
        $cache_key = "all_records";

        if (!empty($year)) {
            $where[] = ['birthday_year', '=', $year];
            $cache_key .= "_" . $year;
        }
        if (!empty($month)) {
            $where[] = ['birthday_month', '=', $month];
            $cache_key .= "_" . $month;
        }

        $filtered = ($year || $month) ? true : false;
        $limit = !empty($request->input('limit')) ? $request->input('limit') : 20;

        $redis = Redis::connection();

        if (!$filtered) {
            $all_records = DB::table('persons')->paginate($limit);
            $paginate_record = json_decode(json_encode($all_records));
            $result = $paginate_record->data;

            $from = $paginate_record->from;
            $to = $paginate_record->to;
            $total = $paginate_record->total;
            $next_page_url = $paginate_record->next_page_url;
            $prev_page_url = $paginate_record->prev_page_url;
        } else {
            // First check if the filtered result exist
            $records_exists = $redis->exists($cache_key);
            if ($records_exists) {
                $data = json_decode(Redis::get($cache_key));
            } else {
                $all_filtered_records = DB::table('persons')->where($where)->get();

                $paginate_record = json_decode(json_encode($all_filtered_records));
                $temp_val = json_encode($paginate_record);

                $redis->flushDB(); // As per requirement, if new filter requested -- remove all previous keys
                $redis->set($cache_key, $temp_val, 'EX', 60);
                $data = $paginate_record;
            }

            // Custom Pagination calculation, as page number can't be a part of redis cache-key.
            $from = ($page == 1) ? $page : ((($page - 1) * $limit) + 1);
            $total = count($data);

            if ($page == 1) {
                $to = $limit;
            } else {
                if ($total < ($page * $limit)) {
                    $to = $total;
                    $slice_limit = false;
                } else {
                    $to = ($page * $limit);
                }
            }

            if ($from > $total) {
                return View::make('persons',
                                [
                                    'result' => [],
                                    'year' => $year ? $year : "",
                                    'month' => $month ? $month : "",
                                    'next_page_url' => false,
                                    'prev_page_url' => false,
                                    'limit' => $limit
                                ]
                );
            }

            $prev_page_url = ($page > 1) ? url('/persons?page=' . ((int) $page - 1)) : false;
            $next_page_url = ($to < $total) ? url('/persons?page=' . ((int) $page + 1)) : false;

            $result = isset($slice_limit) ? array_slice($data, $from - 1) : array_slice($data, $from - 1, $limit);
        }

        return View::make('persons',
                        [
                            'result' => $result,
                            'from' => $from,
                            'to' => $to,
                            'total' => $total,
                            'next_page_url' => $next_page_url,
                            'prev_page_url' => $prev_page_url,
                            'year' => $year ? $year : "",
                            'month' => $month ? $month : "",
                            'limit' => $limit
                        ]
        );
    }

}
