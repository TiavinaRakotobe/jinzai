<?php

namespace AppBundle\Tools;

/**
 * Class: Export en Excel
 */
class CExportExcelAll
{

    /**
     * Action: création excel
     *
     * @param string $file
     * @param string $tHead
     * @param string $tDatas
     * @param int    $withMerge
     *
     * @return boolean
     */
    public static function createExcelOutput($file, $tHead, $tDatas, $withMerge = 0)
    {
        $keys = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O',
            'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC',
            'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO',
            'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'
        );

        $oExcel = new \PHPExcel();

        array_unshift($tDatas, $tHead);

        $index = 1;

        $styleArray = array(
            'font' => array('bold' => true),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'borders' => array(
                'top' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
                'left' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => \PHPExcel_Style_Border::BORDER_THIN)
            ),
            'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '49B5DF'))
        );


        for ($i = 0; $i < count($tHead); $i++) {
            $oExcel->getActiveSheet()->getStyle($keys[$i] . '1')->applyFromArray($styleArray);
        }


        for ($row = 0; $row < count($tDatas); $row++) {
            $col = 0;
            foreach ($tDatas[$row] as $data) {
                $oExcel->setActiveSheetIndex(0)
                    ->setCellValue($keys[$col] . $index, utf8_encode($data));

                $oExcel->getActiveSheet()->getColumnDimension($keys[$col])->setWidth(16);

                $col++;
            }
            $index++;
        }

        // activer la première feuille
        $oExcel->setActiveSheetIndex(0);
        // Enregistré au format 2007 excel
        $oWriter = \PHPExcel_IOFactory::createWriter($oExcel, 'Excel2007');
        $oWriter->save($file);

        return false;
    }

    /**
     * Action: initialise les headers classe excel
     *
     * @return boolean
     */
    public static function initHeader()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', true);
        ini_set('display_startup_errors', true);
        date_default_timezone_set('Europe/London');
        define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

        return false;
    }

}
