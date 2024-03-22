<?php
function actionFilter0($matured = null, $maturityValue = null, $age = null,
                       $gender = null, //
                       $prevHajj = null, $badhalHajj = null, //
                       $showFamily = null, $showEmergencyContact = null, $showMahram = null, $showHajjs = null, //
                       $showReplacement = null, $showCaretaker = null, $showDeposits = null, $showJob = null, $showAge = null, //
                       $showBalance = null, $showBalanceDate = null, $showAtollIsland = null) {

  if (sizeof($_GET) == 0 || (sizeof($_GET) == 1 && isset($_GET['nav']))) {
    $showFamily = 1;
    $showMahram = 1;
    $showHajjs = 1;
    $showReplacement = 1;
    $showBalance = 1;
//      $showBalanceDate = 0;
  }

  $fullAmount = empty($fullAmount) ? Constants::FULL_AMOUNT : $fullAmount;
  $maturityValue = empty($startYear) ? Constants::MATURITY_VALUE : $maturityValue;

  $this->layout = '//layouts/column1';


  if (isset($_GET['chart']) && $_GET['chart'] == 1) {
  } elseif (isset($_GET['export']) && $_GET['export'] == 1) {

//      $criteria->order = "matured_date asc";
//      $dataProvider = new CActiveDataProvider('Members', [
//          'criteria' => $criteria,
//          'pagination' => false,
//          //'sort' => $sort
//      ]);
//
//      $data = $dataProvider->getData();
    $xlData = [];

    $user = Users::model()->findByPk(Yii::app()->user->id);

// Prepare Excel Export and pick up template
    Yii::import('ext.PHPExcel');
    include(Yii::app()->basePath . '/extensions/PHPExcel/IOFactory.php');
    $xlReader = PHPExcel_IOFactory::createReader('Excel2007');
    $xlFile = $xlReader->load(Yii::app()->basePath . "/../templates/member_filter_template");
    $xlDateFormat = 'dd-mm-yyyy hh:mm:ss'; // date formatting

//Start working on first sheet & set sheet title
    $xlFile->setActiveSheetIndex(0);
    $worksheet = $xlFile->getActiveSheet();
    $worksheet->setTitle('Filtered Member list');

//Insert Filter values
    $worksheet->setCellValue('F6', ($matured == 0 ? 'All' : ($matured == 1 ? 'Yes' : 'No')));
    $worksheet->setCellValue('F7', $maturityValue);
    $maturedFromTime = 0;// strtotime($maturedFrom);
    $cellMaturedFrom = PHPExcel_Shared_Date::FormattedPHPToExcel(
      date('Y', $maturedFromTime), date('m', $maturedFromTime), date('d', $maturedFromTime), date('H', $maturedFromTime), date('i', $maturedFromTime), date('s', $maturedFromTime));
    $worksheet->setCellValue('F8', $cellMaturedFrom);
    $worksheet->getStyle('F8')->getNumberFormat()->setFormatCode($xlDateFormat);
    $maturedByTime = 0;//strtotime($maturedBy);
    $cellMaturedBy = PHPExcel_Shared_Date::FormattedPHPToExcel(
      date('Y', $maturedByTime), date('m', $maturedByTime), date('d', $maturedByTime), date('H', $maturedByTime), date('i', $maturedByTime), date('s', $maturedByTime));
    $worksheet->setCellValue('F9', $cellMaturedBy);
    $worksheet->setCellValue('F11', $user->person->full_name_english);
    $worksheet->setCellValue('F12', 0);

    $worksheet->setCellValue('H6', (($age == 0) ? 'All' : ($age == 1 ? '60 and above' : 'Below 60')));
    $worksheet->setCellValue('H7', (($gender == 0) ? 'All' : ($gender == 1 ? 'Male' : 'Female')));
    $worksheet->setCellValue('H8', (($prevHajj == 0) ? 'All' : ($prevHajj == 1 ? 'Yes' : 'No')));
    $worksheet->setCellValue('H9', (($badhalHajj == 0) ? 'All' : ($badhalHajj == 1 ? 'Yes' : 'No')));
    $cellGeneratedAt = PHPExcel_Shared_Date::FormattedPHPToExcel(
      date('Y'), date('m'), date('d'), date('H'), date('i'), date('s'));
    $worksheet->setCellValue('H11', $cellGeneratedAt);
    $worksheet->getStyle('H11')->getNumberFormat()->setFormatCode($xlDateFormat);

// Enter data from row 15
    foreach ($xlData as $k => $rowData) {
      $row = $k + 15;
      $column = 0;

// row formatting
      $worksheet->duplicateStyle($worksheet->getStyle('A' . ($row % 2 == 0 ? '1' : '2')), 'A' . $row . ':AB' . $row);

      foreach ($rowData as $cell) {
        $value = $cell;

// column specific formatting
        switch ($column) {
          case 6:
          case 11:
          case 12: //thaana columns
            $worksheet->getStyleByColumnAndRow($column, $row)->applyFromArray([
              'font' => [
                'name' => 'Faruma']
            ]);
            break;

          case 23: // url columns
            $worksheet->getStyleByColumnAndRow($column, $row)->applyFromArray([
              'font' => [
                'underline' => true,
                'color' => ['rgb' => '0000FF']
              ]]);
            $worksheet->getCellByColumnAndRow($column, $row)->getHyperlink()->setUrl($cell);
            break;

          case 24: // monetary column
            $worksheet->getStyleByColumnAndRow($column, $row)->getNumberFormat()->setFormatCode('#,##0.00');
            break;

          case 25:
          case 26:
          case 27:
          case 28: // date column
            $cellTimeStamp = strtotime($cell);
            $value = PHPExcel_Shared_Date::FormattedPHPToExcel(
              date('Y', $cellTimeStamp), date('m', $cellTimeStamp), date('d', $cellTimeStamp), date('H', $cellTimeStamp), date('i', $cellTimeStamp), date('s', $cellTimeStamp));
            $worksheet->getStyleByColumnAndRow($column, $row)->getNumberFormat()->setFormatCode($xlDateFormat);
            break;

        }

// cell value
        $worksheet->SetCellValueByColumnAndRow($column, $row, $value);

        $column++;
      }
    }

// Set filter, Active Sheet & Active Cell
    $worksheet->setAutoFilter('A14:AB14');
    $worksheet->setSelectedCell('A1');
    $xlFile->setActiveSheetIndex(0);

// Prepare headers for browser to interpret as xl file
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename=Member_filter_' . date('Ymd-hi') . '.xlsx');
    header('Cache-Control: max-age=0');

// for IE 9
    header('Cache-Control: max-age=1');

// for IE over SSL
//      header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

// Wrap the file and send
    $objWriter = new PHP_Writer_Excel2007($xlFile);
    $objWriter->save('php://output');

    Yii::app()->end();


// for data display
  }


  $this->render('filter', [
    'dataProvider' => isset($dataProvider) ? $dataProvider : null,
    'chart' => isset($chart) ? $chart : null,
    'chartIslands' => isset($chartIslands) ? $chartIslands : null,
    'pieData' => isset($pieData) ? $pieData : null,
    'matured' => $matured,
    'maturityValue' => $maturityValue,
    'maturedFrom' => '',
    'maturedBy' => '',
    'gender' => $gender,
    'prevHajj' => $prevHajj,
    'badhalHajj' => $badhalHajj,
    'age' => $age,
    'showFamily' => $showFamily,
    'showEmergencyContact' => $showEmergencyContact,
    'showMahram' => $showMahram,
    'showHajjs' => $showHajjs,
    'showReplacement' => $showReplacement,
    'showCaretaker' => $showCaretaker,
    'showDeposits' => $showDeposits,
    'showJob' => $showJob,
    'showBalance' => $showBalance,
    'showBalanceDate' => $showBalanceDate,
    'showAge' => $showAge,
    'showAtollIsland' => $showAtollIsland,
    'columns' => isset($columns) ? $columns : null,
  ]);
}

