<?php
namespace App\Helpers\Excel;

use App\Http\Services\TimecardService;
use App\Models\Timecard;
use App\Models\TimecardDetail;
use App\Models\Workplace;
use Carbon\Carbon;
use Log;

class TimeCardsExcel
{


    /**出勤簿エクセルを返す
     */
    public function getExcelFile($employee, $workplace, $timecard)
    {
        $excel = $this->getExcelTemplate();
        //ヘッダ欄の作成
        $this->setHeaders($excel, $employee, $workplace, $timecard->date);
        $this->setBody($excel, $timecard);
        return $excel;
    }

    /**
     * エクセルのテンプレート取得
     */
    protected function getExcelTemplate()
    {
        $template = base_path("resources/excel/template.xls");
        $reader = \PHPExcel_IOFactory::createReader("Excel5");
        $excel = $reader->load($template);
        return $excel;
    }

    /**
     * ヘッダの出力
     */
    protected function setHeaders(\PHPExcel $excel, $employee, $workplace, $date)
    {
        $sheet = $excel->setActiveSheetIndex(0);

        $carbon = Carbon::parse($date);

        $sheet->setCellValue("A1", "出勤簿（" . $carbon->format("Y") . "年" . $carbon->format("m") . "月）");
        $sheet->setCellValue("C4", $employee->name);
        $sheet->setCellValue("C3", $workplace->name);
    }


    /**
     * ボディの出力
     */
    protected function setBody(\PHPExcel $excel, $timecard)
    {
        $sheet = $excel->setActiveSheetIndex(0);

        //対象日付(Y-m-d)の月初と月末取得
        $startDay = Carbon::parse($timecard->date)->startOfMonth();
        $endDay = Carbon::parse($timecard->date)->endOfMonth();

        $rowIndex = 7; // 7行目からスタート
        $sum_rest_time = 0;
        $sum_work_time = 0;

        //1日ずつ処理していく
        for ($start=$startDay;
             $start <= $endDay->startOfDay();
             $start = $start->modify('+1 day')) {

            $timeCardInfo = TimecardService::getTimecardInfoDaily($start->format('Y-m-d'), $timecard->employee_uid);
            $this->setTimeCardRow($sheet, $rowIndex, $startDay, $timeCardInfo);
            //処理が終わったら行と日付を+1する。
            $rowIndex++;
            $sum_rest_time += array_get($timeCardInfo, "rest_time");
            $sum_work_time += array_get($timeCardInfo, "work_time");
        }
        //最後に合計値
        if (!empty($sum_rest_time)) {
            $disp_sum_rest_time = floor($sum_rest_time/60).'時間'.($sum_rest_time%60).'分';
        } else {
            $disp_sum_rest_time = "";
        }
        if (!empty($sum_work_time)) {
            $disp_sum_work_time = floor($sum_work_time/60).'時間'.($sum_work_time%60).'分';
        } else {
            $disp_sum_work_time = "";
        }

        //28日終わりの場合
        if ($rowIndex == 35) {
            $sheet->setCellValue("C" . 35, "");
            $sheet->setCellValue("D" . 35, "");
            $sheet->setCellValue("C" . 36, "");
            $sheet->setCellValue("D" . 36, "");
            $sheet->setCellValue("C" . 37, "");
            $sheet->setCellValue("D" . 37, "");
        }
        //29日終わりの場合
        if ($rowIndex == 36) {
            $sheet->setCellValue("C" . 36, "");
            $sheet->setCellValue("D" . 36, "");
            $sheet->setCellValue("C" . 37, "");
            $sheet->setCellValue("D" . 37, "");
        }
        //30日終わりの場合
        if ($rowIndex == 37) {
            $sheet->setCellValue("C" . 37, "");
            $sheet->setCellValue("D" . 37, "");
        }

        $rowIndex = 38;
        $sheet->setCellValue("E" . $rowIndex, $disp_sum_rest_time);
        $sheet->setCellValue("F" . $rowIndex, $disp_sum_work_time);

    }

    /**
     * ボディ向け出力フォーマット
     * @param \PHPExcel_Worksheet $sheet
     * @param $rowIndex
     * @param $date
     * @param array $timecardInfo
     */
    protected function setTimeCardRow(\PHPExcel_Worksheet $sheet, $rowIndex, Carbon $date, $timecardInfo = [])
    {
        $day = $this->jaShortDay($date);
        $sheet->setCellValue("A" . $rowIndex, $date->format("d"));
        $sheet->setCellValue("B" . $rowIndex, $day);
        $sheet->setCellValue("C" . $rowIndex, array_get($timecardInfo, "clockIn"));
        $sheet->setCellValue("D" . $rowIndex, array_get($timecardInfo, "clockOut"));

        if (!empty(array_get($timecardInfo, "rest_time"))) {
            $disp_rest_time = floor(array_get($timecardInfo, "rest_time") / 60) . '時間' . (array_get($timecardInfo, "rest_time") % 60) . '分';
        } else {
            $disp_rest_time = "";
        }
        $sheet->setCellValue("E" . $rowIndex, $disp_rest_time);

        if (!empty(array_get($timecardInfo, "work_time"))) {
            $disp_work_time = floor(array_get($timecardInfo, "work_time") / 60) . '時間' . (array_get($timecardInfo, "work_time") % 60) . '分';
        } else {
            $disp_work_time = "";
        }
        $sheet->setCellValue("F" . $rowIndex, $disp_work_time);
    }

    protected function jaShortDay(\DateTime $date)
    {
        static $text = ['日', '月', '火', '水', '木', '金', '土'];

        return "({$text[$date->format('w')]})";
    }


}