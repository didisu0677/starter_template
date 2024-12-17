<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require __DIR__.'/PHPExcel.php';

class Simpleexcel {

    private $data           = array();
    private $title          = array();
    private $thead          = array();
    private $head           = array();
    private $group_head     = array();
    private $image          = array();
    private $alias          = array();
    private $col_excel      = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
    private $flname         = '';

    private $list_data      = null;
    private $list_array     = array();
    private $jml_per_sheet  = array();

    function __construct($config=array()) {
        if(isset($config['data']) || isset($config['title']) || isset($config['header'])) {
            $this->data[]       = isset($config['data']) ? $config['data'] : array();
            $this->title[]      = isset($config['title']) ? $config['title'] : 'unknown';
            $this->thead[]      = isset($config['header']) ? $config['header'] : array();
            $this->image[]      = isset($config['image']) ? $config['image'] : '';
            $this->alias[]      = isset($config['alias']) ? $config['alias'] : '';
            $this->group_head[] = isset($config['group_header']) ? $config['group_header'] : array();
        } else {
            foreach($config as $conf) {
                $ldata[]        = isset($conf['data']) ? $conf['data'] : array();
                $ltitle[]       = isset($conf['title']) ? $conf['title'] : 'unknown';
                $lheader[]      = isset($conf['header']) ? $conf['header'] : array();
                $limage[]       = isset($conf['image']) ? $conf['image'] : '';
                $lalias[]       = isset($conf['alias']) ? $conf['alias'] : array();
                $lgroup_head[]  = isset($conf['group_header']) ? $conf['group_header'] : array();
            }
            if(isset($ldata)) {
                $this->data         = $ldata;
                $this->title        = $ltitle;
                $this->thead        = $lheader;
                $this->image        = $limage;
                $this->alias        = $lalias;
                $this->group_head   = $lgroup_head;
            }
        }
        // $col_excel      = $col_excel2 = $this->col_excel;
        // foreach($col_excel as $ce1) {
        //     foreach($col_excel2 as $ce2) {
        //         $col_excel[] = $ce1.$ce2;
        //     }
        // }
        $col_excel = [];
        foreach ($this->excelColumnRange('A', 'ZZZ') as $value) {
            $col_excel[] = $value;
        }
        $this->col_excel = $col_excel;
    }

    function excelColumnRange($lower, $upper) {
        ++$upper;
        for ($i = $lower; $i !== $upper; ++$i) {
            yield $i;
        }
    }
    
    function filename($filename='') {
        $this->flname = $filename;
    }

    function header($head = array()) {
        $this->head = $head;
    }

    function export() {
        $objPHPExcel    = new PHPExcel();
        $objPHPExcel->getProperties()
        ->setCreator("Bayu Ramadhan")
        ->setTitle($this->title[0]);
        for($z=0; $z < count($this->title); $z++) {
            if($z > 0) $objPHPExcel->createSheet();
            $objset = $objPHPExcel->setActiveSheetIndex($z);
            $objget = $objPHPExcel->getActiveSheet();
            $objget->setTitle(str_replace('template_import_','',$this->title[$z]));

            $thead = $this->thead[$z];
            if(count($thead) == 0) {
                if(count($this->data[$z]) > 0) {
                    foreach($this->data[$z][0] as $key => $value) {
                        $thead[] = $key.'>>'.$key;
                    }
                }
            } else {
                $thead = array();
                foreach($this->thead[$z] as $key => $value) {
                    $thead[] = $key.'>>'.$value;
                }
            }
            $i = $ii = 1;
            foreach($this->head as $kh => $vh) {
                $val = $vh ? $vh : '-';
                $objset->setCellValue('A'.$i, $kh);
                $objPHPExcel->getActiveSheet()->getStyle("A".$i)->getFont()->setBold( true );
                $objPHPExcel->getActiveSheet()->mergeCells("A".$i.":B".$i);
                $objset->setCellValue('C'.$i, $val);
                $i++;
            }

            if(count($this->head) > 0) $i++;
            if(isset($this->col_excel[count($thead)-1])) {
                $objPHPExcel->getActiveSheet()->getStyle("A".$i.":".$this->col_excel[count($thead)-1].$i)->applyFromArray(
                    array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                                'color' => array('rgb' => '777777')
                            )
                        )
                    )
                );
                $objPHPExcel->getActiveSheet()->getStyle("A".$i.":".$this->col_excel[count($thead)-1].$i)->getFont()->setBold( true );
                $objPHPExcel->getActiveSheet()
                    ->getStyle("A".$i.":".$this->col_excel[count($thead)-1].$i)
                    ->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('EEEEEE');
            }
            if(count($this->group_head[$z]) == 0) {
                foreach($thead as $k => $a) {
                    $content   = explode('>>', $a);
                    $h_ttl     = str_replace(array('-b','-d','-c','-p','-s'), '', $content[1] );
                    $objset->setCellValue($this->col_excel[$k].$i, strtoupper($h_ttl));
                }
                $i++;
            } else {
                $list_grouped   = array();
                foreach ($this->group_head[$z] as $gh => $a_gh) {
                    if(is_array($a_gh)) {
                        $first_key  = 0;
                        $last_key   = 0;
                        $w = 0;
                        foreach ($this->thead[$z] as $th => $v_th) {
                            if($th == $a_gh[0]) $first_key = $w;
                            if($th == $a_gh[count($a_gh)-1]) $last_key = $w;
                            $w++;
                        }
                        if($first_key != $last_key) {
                            $objset->setCellValue($this->col_excel[$first_key].$i, $gh);
                            $objPHPExcel->getActiveSheet()->getStyle($this->col_excel[$first_key].$i)->getFont()->setBold( true );
                            $objPHPExcel->getActiveSheet()->mergeCells($this->col_excel[$first_key].$i.':'.$this->col_excel[$last_key].$i);
                            $objPHPExcel->getActiveSheet()->getStyle($this->col_excel[$first_key].$i.':'.$this->col_excel[$last_key].$i)->applyFromArray(array(
                                    'alignment' => array(
                                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                    )
                                ));
                            foreach($a_gh as $agh) {
                                $list_grouped[] = $agh;
                            }
                        }
                    }
                }
                $i++;

                $objPHPExcel->getActiveSheet()->getStyle("A".$i.":".$this->col_excel[count($thead)-1].$i)->applyFromArray(
                    array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                                'color' => array('rgb' => '777777')
                            )
                        )
                    )
                );
                $objPHPExcel->getActiveSheet()->getStyle("A".$i.":".$this->col_excel[count($thead)-1].$i)->getFont()->setBold( true );
                $objPHPExcel->getActiveSheet()
                    ->getStyle("A".$i.":".$this->col_excel[count($thead)-1].$i)
                    ->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('EEEEEE');

                $w = 0;
                foreach ($this->thead[$z] as $th => $v_th) {
                    $merge = true;
                    foreach($list_grouped as $lg) {
                        if($th == $lg) $merge = false;
                    }
                    $v_th = str_replace(array('-b','-d','-c','-p'), '', $v_th );
                    if($merge) {
                        $n = $i - 1;
                        $objPHPExcel->getActiveSheet()->mergeCells($this->col_excel[$w].$n.':'.$this->col_excel[$w].$i);
                        $objset->setCellValue($this->col_excel[$w].$n, strtoupper($v_th));
                    } else {
                        $objset->setCellValue($this->col_excel[$w].$i, strtoupper($v_th));
                    }
                    $w++;
                }
                $i++;
            }
            $ii = $i - 1;
            foreach($this->data[$z] as $r) {
                $objPHPExcel->getActiveSheet()->getStyle("A".$i.":".$this->col_excel[count($thead)-1].$i)->applyFromArray(
                    array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                                'color' => array('rgb' => '777777')
                            )
                        )
                    )
                );
                foreach($thead as $k => $a) {
                    $content = explode('>>', $a);
                    $konten = isset($this->alias[$z][$content[0]][$r[$content[0]]]) ? $this->alias[$z][$content[0]][$r[$content[0]]] : $r[$content[0]];
                    if(strpos($thead[$k],'>>-d') !== false || strpos($thead[$k], '>>-b') || strpos($thead[$k], '>>-s') !== false || strpos($thead[$k], '>>-c') !== false || strpos($thead[$k], '>>-p') !== false) {
                        if(strpos($thead[$k], '>>-b') !== false) {
                            $objset->setCellValue($this->col_excel[$k].$i, $konten);
                            $objPHPExcel->getActiveSheet()->getStyle($this->col_excel[$k].$i)->getFont()->setBold( true );
                        } 

                        if(strpos($thead[$k], '-d') !== false) {
                            $c = $konten;
                            if(strlen($c) == 8) {
                                $c = substr($c, 0, 4).'-'.substr($c, 4, 2).'-'.substr($c, 6, 2);
                            }
                            if($c == '0000-00-00' || $c == '0000-00-00 00:00:00' || !$c) {
                                $objset->setCellValue($this->col_excel[$k].$i, '');
                            } else {
                                $objset->setCellValue($this->col_excel[$k].$i, PHPExcel_Shared_Date::PHPToExcel($c));
                                if (strpos($c, ':') !== false) {
                                    $formatCode = 'dd/mm/yyyy h:mm';
                                } else {
                                    $formatCode = 'dd/mm/yyyy';
                                }
                                $objPHPExcel->getActiveSheet()->getStyle($this->col_excel[$k].$i)->getNumberFormat()->setFormatCode($formatCode);
                            }
                        } elseif(strpos($thead[$k], '-s') !== false) {
                            $objset->setCellValueExplicit($this->col_excel[$k].$i, str_replace(array('-b','-c','-p','-s'), '', $konten),PHPExcel_Cell_DataType::TYPE_STRING);
                            $objPHPExcel->getActiveSheet()->getStyle($this->col_excel[$k].$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        } elseif(strpos($thead[$k], '-c') !== false) {
                            $objset->setCellValue($this->col_excel[$k].$i, str_replace(array('-b','-c','-p','-s'), '', $konten));
                            $objPHPExcel->getActiveSheet()->getStyle($this->col_excel[$k].$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY);
                        } elseif(strpos($thead[$k], '-p') !== false) {
                            $objset->setCellValue($this->col_excel[$k].$i, str_replace(array('-b','-c','-p','-s'), '', $konten));
                            $objPHPExcel->getActiveSheet()->getStyle($this->col_excel[$k].$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                        }
                        if(strpos($konten,'-b') !== false) {
                            $objPHPExcel->getActiveSheet()->getStyle($this->col_excel[$k].$i)->getFont()->setBold( true );
                        }
                    } else {
                        if(substr($konten, 0, 2) == '-m') {
                            $merge_start = $merge_end = str_replace('-m', '', $konten);
                            if($this->col_excel[$k+1] == $merge_end) {
                                $merge_start = $this->col_excel[$k];
                            } elseif($this->col_excel[$k-1] == $merge_start) {
                                $merge_end = $this->col_excel[$k];
                            }
                            $objPHPExcel->getActiveSheet()->mergeCells($merge_start.$i.":".$merge_end.$i);
                            $objset->setCellValue($this->col_excel[$k].$i, '');
                        } else {
                            if(strpos($konten,'-s') !== false) {
                                $objset->setCellValueExplicit($this->col_excel[$k].$i, str_replace(array('-b','-c','-p','-s'), '', $konten),PHPExcel_Cell_DataType::TYPE_STRING);
                            } else {
                                $objset->setCellValue($this->col_excel[$k].$i, str_replace(array('-b','-c','-p','-s'), '', $konten));
                            }
                            if(strpos($konten,'-b') !== false) {
                                $objPHPExcel->getActiveSheet()->getStyle($this->col_excel[$k].$i)->getFont()->setBold( true );
                            }
                            if(strpos($konten,'-c') !== false && is_numeric($konten)) {
                                $objPHPExcel->getActiveSheet()->getStyle($this->col_excel[$k].$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY);
                            } elseif(strpos($konten,'-s') !== false && is_float($konten)) {
                                $objPHPExcel->getActiveSheet()->getStyle($this->col_excel[$k].$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                            } elseif(strpos($konten,'-p') !== false && is_float($konten)) {
                                $objPHPExcel->getActiveSheet()->getStyle($this->col_excel[$k].$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                            }
                        }                        
                    }
                }            
                $i++;
                if(isset($this->image[$z]) && $this->image[$z]) {
                    $e_image    = explode('.',$this->image[$z]);
                    $ext        = $e_image[count($e_image)-1];
                    $bg         = false;
                    if(strtolower($ext) == 'png') {
                        $src        = imagecreatefrompng($this->image[$z]);
                        $width      = imagesx($src);
                        $height     = imagesy($src);
                        
                        $bg = imagecreatetruecolor($width, $height);
                        $white = imagecolorallocate($bg, 255, 255, 255);
                        imagefill($bg, 0, 0, $white);
                        
                        imagecopyresampled(
                            $bg, $src,
                            0, 0, 0, 0,
                            $width, $height,
                            $width, $height);
                    } elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg') {
                        $bg         = imagecreatefromjpeg($this->image[$z]);
                    } elseif(strtolower($ext) == 'gif') {
                        $bg         = imagecreatefromgif($this->image[$z]);
                    } elseif(strtolower($ext) == 'bmp') {
                        $bg         = imagecreatefrombmp($this->image[$z]);
                    }
                    if($bg) {
                        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                        $objDrawing->setName('Sample image');
                        $objDrawing->setDescription('Sample image');
                        $objDrawing->setImageResource($bg);
                        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
                        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
                        $objDrawing->setHeight(150);
                        $objDrawing->setCoordinates($this->col_excel[count($thead) + 1].$ii);
                        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                    }
                }
            }

            for($j = 0; $j < count($thead); $j++) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($this->col_excel[$j])
                    ->setAutoSize(true);                
            }
        }
        $filename = $this->flname ? $this->flname : $this->title[0];
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle(str_replace('template_import_','',$this->title[0]));
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        $objWriter->save("php://output");
        exit();
    }

    function read($file) {
        try {
            $objPHPExcel = PHPExcel_IOFactory::load($file);
        } catch(Exception $e) {
            die('Error loading file :' . $e->getMessage());
        }
        $sheet_count    = $objPHPExcel->getSheetCount();
        $worksheet      = array();
        $numRows        = array();
        for($i = 0; $i < $sheet_count; $i++) {
            $worksheet[$i]      = $objPHPExcel->getSheet($i)->toArray(null,true,true,true);
            $numRows[$i]        = count($worksheet[$i]);
        }
        $this->list_data        = $worksheet;
        $this->jml_per_sheet    = $numRows;
        return $numRows;
    }

    function define_column($array) {
        $this->list_array = $array;
    }

    function parsing($i,$j) {
        $data   = array();
        $row    = $this->list_data[$i][$j];
        foreach($this->list_array as $k => $a) {
            if(isset($row[$this->col_excel[$k]])) {
                $data[$a] = $row[$this->col_excel[$k]] == null ? '' : $row[$this->col_excel[$k]];
            } else {
                $data[$a] = '';
            }
            $check_date = explode('/',$data[$a]);
            if(strlen($data[$a]) >= 8 && strlen($data[$a]) <= 10 && count($check_date) == 3) {
                $orig = $data[$a];
                $data[$a] = date('Y-m-d',strtotime(str_replace('/','-',$data[$a])));
                if($data[$a] == '1970-01-01') {
                    $data[$a] = date('Y-m-d',strtotime($orig));
                }
            }
        }
        return $data;
    }

}