<?php
namespace App\Helpers\Excel;

use App\Models\Employee;
use App\Models\Workplace;
use Log;



class PaymentExcel
{

    protected $employee;

    protected $workplace;

    function __construct(Employee $employee, Workplace $workplace, $date)
    {
        $this->employee = $employee;
        $this->workplace = $workplace;
        $this->date = $date;
    }

    /**
     * Excelオブジェクト取得
     */
    public function getExcelFile($data, $salaries, $paymentData)
    {
        $excel = $this->getExcelTemplate();
        //ヘッダ欄の作成
        $this->setHeaders($excel, $this->employee);
        $this->setUnitData($excel, $salaries);
        $this->setBody($excel, $paymentData['supply'], $paymentData['deduction']);
        return $excel;
    }

    /**
     * エクセルのテンプレート取得
     * @return \PHPExcel
     * @throws \PHPExcel_Reader_Exception
     */
    protected function getExcelTemplate()
    {
        $template = base_path("resources/excel/paymentTemplate.xls");
        $reader = \PHPExcel_IOFactory::createReader("Excel5");
        $excel = $reader->load($template);
        return $excel;
    }

    /**
     * ヘッダの出力
     */
    protected function setHeaders(\PHPExcel $excel, Employee $employee)
    {
        $sheet = $excel->setActiveSheetIndex(0);

        $sheet->setCellValue("B1", $this->date);
        $sheet->setCellValue("T1", $employee->name);
        $sheet->setCellValue("T2", $this->workplace->name);

    }

    protected function setUnitData(\PHPExcel $excel, $salaries)
    {

        $sheet = $excel->setActiveSheetIndex(0);

        foreach ($salaries as $idx => $salary) {

            $sheet->setCellValue("B" . ($idx + 4), '時給' . ($idx + 1) . '(' . $salary['start_time'] . ' 〜)');
            $sheet->setCellValue("H" . ($idx + 4), number_format($salary['hourly_pay']) . '円');
            $sheet->setCellValue("N" . ($idx + 4), floor($salary['time'] / 60) . '時間' . ($salary['time'] % 60) . '分');
            $sheet->setCellValue("T" . ($idx + 4), number_format($salary['price']) . '円');
        }
    }


    /**
     * ボディの出力
     */
    protected function setBody(\PHPExcel $excel, $supply, $deduction)
    {
        $sheet = $excel->setActiveSheetIndex(0);
        //支給
        $sheet->setCellValue("D10", $supply->base_salary!=0?$supply->base_salary:'');
        $sheet->setCellValue("H9", $supply->free_name_1);
        $sheet->setCellValue("H10", $supply->free_value_1!=0?$supply->free_value_1:'');
        $sheet->setCellValue("L9", $supply->free_name_2);
        $sheet->setCellValue("L10", $supply->free_value_2!=0?$supply->free_value_2:'');
        $sheet->setCellValue("P9", $supply->free_name_3);
        $sheet->setCellValue("P10", $supply->free_value_3!=0?$supply->free_value_3:'');
        $sheet->setCellValue("T9", $supply->free_name_4);
        $sheet->setCellValue("T10", $supply->free_value_4!=0?$supply->free_value_4:'');
        $sheet->setCellValue("D11", $supply->free_name_5);
        $sheet->setCellValue("D12", $supply->free_value_5!=0?$supply->free_value_5:'');
        $sheet->setCellValue("H12", $supply->over_cost!=0?$supply->over_cost:'');
        $sheet->setCellValue("L12", $supply->traffic_cost!=0?$supply->traffic_cost:'');
        $sheet->setCellValue("P12", $supply->unemployment_cost!=0?$supply->unemployment_cost:'');
        $sheet->setCellValue("T12", $supply->total!=0?$supply->total:'');

        //控除
        $sheet->setCellValue("D14", $deduction->health_insurance!=0?$deduction->health_insurance:'');
        $sheet->setCellValue("H14", $deduction->care_insurance!=0?$deduction->care_insurance:'');
        $sheet->setCellValue("L14", $deduction->welfare_pension!=0?$deduction->welfare_pension:'');
        $sheet->setCellValue("P14", $deduction->employment_insurance!=0?$deduction->employment_insurance:'');
        $sheet->setCellValue("T14", $deduction->social_insurance!=0?$deduction->social_insurance:'');
        $sheet->setCellValue("D16", $deduction->income_tax!=0?$deduction->income_tax:'');
        $sheet->setCellValue("H16", $deduction->inhabitant_tax!=0?$deduction->inhabitant_tax:'');
        $sheet->setCellValue("L15", $deduction->free_name_1);
        $sheet->setCellValue("L16", $deduction->free_value_1!=0?$deduction->free_value_1:'');
        $sheet->setCellValue("P15", $deduction->free_name_2);
        $sheet->setCellValue("P16", $deduction->free_value_2!=0?$deduction->free_value_2:'');
        $sheet->setCellValue("T16", $deduction->total!=0?$deduction->total:'');

        if (empty($supply->total)) {
            $supply->total = 0;
        }
        if (empty($deduction->total)) {
            $deduction->total = 0;
        }
        $payment = number_format((string)(str_replace(",","",$supply->total) - str_replace(",","",$deduction->total)));
        $sheet->setCellValue("P17", $payment);


    }

}