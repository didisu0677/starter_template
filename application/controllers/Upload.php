<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload extends BE_Controller {
    function __construct(){
        parent::__construct();
    }

    function file($rename_file='rename') {
        $temp_folder = md5(user('id').post('name'));
        if(!is_dir(FCPATH . 'assets/uploads/temp/'.$temp_folder)){
            $oldmask = umask(0);
            mkdir(FCPATH . 'assets/uploads/temp/'.$temp_folder,0777);
            umask($oldmask);
        }
        $files = glob(FCPATH . "assets/uploads/temp/".$temp_folder."/{,.}*", GLOB_BRACE);
        foreach($files as $file){
            // if(is_file($file)) @unlink($file);
        }

        $file_allowed = setting('fileupload_mimes') ? str_replace(',', '|', setting('fileupload_mimes')) : ALLOWED_FILE_UPLOAD;

        $config['upload_path'] = FCPATH . 'assets/uploads/temp/'.$temp_folder;
        $config['allowed_types'] = $file_allowed;
        $config['max_size']   = file_upload_max_size();
        $config['overwrite']  = 'TRUE';

        $this->load->library('upload');
        $this->upload->initialize($config);
        $this->upload->do_upload('document');
        $upload = $this->upload->data();
        if ($upload) {
            $uploaded = '';
            if(file_exists($upload['full_path'])) {
                $path           = $upload['file_path'];
                $ext            = $upload['file_ext'];
                $full_path      = $upload['full_path'];
                $proses         = true;

                if(strtolower($ext) == '.zip') {
                    if(file_exists($full_path)) {
                        $zip            = new ZipArchive;
                        if ($zip->open($full_path) === TRUE) {
                            $_temp      = FCPATH . 'assets/uploads/temp/check_zip_'.rand();
                            if(!is_dir($_temp)){
                                $oldmask = umask(0);
                                mkdir($_temp,0777);
                                umask($oldmask);
                            }

                            $zip->extractTo($_temp);
                            $zip->close();
                            $fl_allowed     = explode('|', ALLOWED_FILE_UPLOAD);
                            $fl_allowed[]   = 'sql';
                            foreach(c_scandir($_temp) as $f) {
                                $f_ext = explode('.', $f);
                                if(!in_array(strtolower($f_ext[count($f_ext) - 1]), $fl_allowed)) {
                                    $proses = false;
                                }
                            }
                            delete_dir($_temp);
                        }
                    }
                }
                if($proses) {
                    if($rename_file == 'rename') {
                        $neworig        = post('name') . $ext;
                        rename($upload['full_path'], $path . $neworig);
                        $uploaded       = "assets/uploads/temp/$temp_folder/$neworig";
                    } elseif($rename_file == 'datetime') {
                        $neworig        = uniqid() . '_' . date('Y_m_d_H_i_s') . $ext;
                        rename($upload['full_path'], $path . $neworig);
                        $uploaded       = "assets/uploads/temp/$temp_folder/$neworig";
                    } else {
                        $uploaded       = $upload['full_path'];
                    }
                    // cek apabila string terakhirnya tidak memiliki extention berarti file gagal diupload
                    $b = substr($uploaded, -7);
                    if(strpos($b, '.') === false || !file_exists($uploaded)) {
                        $uploaded = '';
                    }
                }
            }

            echo $uploaded;
        }
    }

    public function image($image_width=0,$image_height=0,$tipe=''){
        $temp_folder = md5(user('id').post('name'));
        if(!is_dir(FCPATH . 'assets/uploads/temp/'.$temp_folder)){
            $oldmask = umask(0);
        	mkdir(FCPATH . 'assets/uploads/temp/'.$temp_folder,0777);
            umask($oldmask);
        }
        $files = glob(FCPATH . "assets/uploads/temp/".$temp_folder."/{,.}*", GLOB_BRACE);
        foreach($files as $file){
            if(is_file($file)) @unlink($file);
        }

        $config['upload_path'] = FCPATH . 'assets/uploads/temp/'.$temp_folder;
        $config['allowed_types'] = ALLOWED_FILE_UPLOAD;
        $config['max_size']   = file_upload_max_size();
        $config['overwrite']  = 'TRUE';

        $this->load->library('upload');
        $this->upload->initialize($config);
        $this->upload->do_upload('image');
        $upload = $this->upload->data();
        if ($upload) {
            $path           = $upload['file_path'];
            $ext            = $upload['file_ext'];
            $neworig        = str_replace('[','_',str_replace(']','',post('name'))) . $ext;
            $neworigjpg     = str_replace('[','_',str_replace(']','',post('name'))) . '.jpg';
            rename($upload['full_path'], $path . $neworig);
            $uploaded       = "assets/uploads/temp/$temp_folder/$neworig";
            $uploadedjpg    = "assets/uploads/temp/$temp_folder/$neworigjpg";

            if($tipe == '') {
                if($image_width && $image_height) {
                    $clr_ext = strtolower(str_replace('.','',$ext));
                    if($clr_ext == 'gif' || $clr_ext == 'png' || $clr_ext == 'jpg' || $clr_ext == 'jpeg' || $clr_ext == 'bmp') {
                        $config['image_library']    = 'gd2';
                        $config['source_image']     = $uploaded;
                        $config['maintain_ratio']   = TRUE;
                        $config['width']            = $image_width;
                        $config['height']           = $image_height;

                        $this->load->library('image_lib', $config);
                        $this->image_lib->resize();
                    }
                }
                $oldmask = umask(0);
                chmod(FCPATH . $uploaded,0777);
                umask($oldmask);
                echo $uploaded;
            } else {
                $targ_w     = $image_width;
                $targ_h     = $image_height;
                $ext        = pathinfo($uploaded, PATHINFO_EXTENSION);

                $rgb_r      = 255;
                $rgb_g      = 255;
                $rgb_b      = 255;

                $size       = getimagesize($uploaded);
                $width      = $size[0];
                $height     = $size[1];
                $mime       = $size['mime'];
                $x = 0;
                $y = 0;
                if($width != $image_width || $height != $image_height) {
                    if($width > $height) {
                        $tg_w  = $targ_w;
                        $tmp_h = $targ_w / $width;
                        $tg_h  = $tmp_h * $height;
                    } else {
                        $tg_h  = $targ_h;
                        $tmp_w = $targ_h / $height;
                        $tg_w  = $tmp_w * $width;
                    }

                    if($mime == 'image/jpeg')
                        $img_r  = imagecreatefromjpeg($uploaded);
                    else if($mime == 'image/png')
                        $img_r  = imagecreatefrompng($uploaded);
                    else if($mime == 'image/gif')
                        $img_r  = imagecreatefromgif($uploaded);
                    else if($mime == 'image/wbmp')
                        $img_r  = imagecreatefromwbmp($uploaded);

                    $dst_r  = ImageCreateTrueColor( $tg_w, $tg_h );

                    $white  = imagecolorallocate($dst_r, $rgb_r, $rgb_g, $rgb_b);
                    imagefill($dst_r, 0, 0, $white);
                    imagecopyresampled($dst_r,$img_r,0,0,$x,$y,$tg_w,$tg_h,$width,$height);

                    imagejpeg($dst_r,$uploadedjpg,95);

                    $width      = $tg_w;
                    $height     = $tg_h;
                    if($width > $height) {
                        $tmp_width     = $width / $targ_w;
                        $t_height      = $height;
                        $height        = $targ_h * $tmp_width;
                        $y             = -(($height - $t_height) / 2);
                        $x             = 0;
                    }else{
                        $tmp_height    = $height / $targ_h;
                        $t_width       = $width;
                        $width         = $targ_w * $tmp_height;
                        $x             = -(($width - $t_width) / 2);
                    }

                    $img_r  = imagecreatefromjpeg($uploadedjpg);

                    $dst_r  = ImageCreateTrueColor( $targ_w, $targ_h );
                    $white  = imagecolorallocate($dst_r, $rgb_r, $rgb_g, $rgb_b);
                    imagefilledrectangle($dst_r, 0, 0, $targ_w, $targ_h, $white);
                    $wm_w = imagesx($img_r);
                    $wm_h = imagesy($img_r);
                    imagealphablending($img_r, false);
                    imagesavealpha($img_r, true);
                    imagecopy($dst_r, $img_r, ($targ_w - $wm_w) / 2 , ($targ_h - $wm_h) /2, 0, 0, $targ_w, $targ_h);
                    $white = imagecolorallocate($dst_r, $rgb_r, $rgb_g, $rgb_b);
                    if($wm_w > $wm_h) {
                        $y_wm   = ($targ_h - $wm_h) /2;
                        $h_2    = $targ_h - $y_wm;
                        $y_wm  += $h_2;
                        imagefilledrectangle($dst_r, 0, $y_wm, $targ_w, $h_2, $white);
                    } else {
                        $x_wm   = ($targ_w - $wm_w) /2;
                        $w_2    = $targ_w - $x_wm;
                        $x_wm  += $w_2;
                        imagefilledrectangle($dst_r, $x_wm, 0, $w_2, $targ_h, $white);
                    }
                    imagejpeg($dst_r,$uploaded,95);
                }
                $oldmask = umask(0);
                chmod(FCPATH . $uploaded,0777);
                umask($oldmask);
                echo $uploaded;
            }
        } else {
            echo "assets/images/nopohoto.png";
        }
    }
}
