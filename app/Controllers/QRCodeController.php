<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\QRModel;
use Config\Services;
use App\Libraries\Ciqrcode;

use function bin2hex;
use function file_exists;
use function mkdir;

class QRCodeController extends BaseController
{

    public function __construct()
    {
        $this->qrModel = new QRModel();

        if (session()->get('level') != "admin") {
            echo 'Access denied';
            exit;
        }
    }

    public function index() {
        $qrs = $this->qrModel->findAll();
        $data = [
            'page' => 'QR',
            'qr' => $qrs
        ];

        return view('layouts/pages/admin/qr/index', $data);
    }

    public function create()
    {
        helper(['form']);
        $data = [
            'page' => 'QR',
            'validation' => Services::validation(),
        ];

        echo view('layouts/pages/admin/qr/create', $data);
    }

    public function generate_qrcode($data)
    {
        /* Load QR Code Library */
        // $this->load->library('Ciqrcode');
        $ciqrcode = new Ciqrcode;

        /* Data */
        $hex_data   = bin2hex($data);
        $save_name  = $hex_data . '.png';

        /* QR Code File Directory Initialize */
        $dir = 'assets/media/qrcode/';
        if (! file_exists($dir)) {
            mkdir($dir, 0775, true);
        }

        /* QR Configuration  */
        $config['cacheable']    = true;
        $config['imagedir']     = $dir;
        $config['quality']      = true;
        $config['size']         = '1024';
        $config['black']        = [255, 255, 255];
        $config['white']        = [255, 255, 255];
        $ciqrcode->initialize($config);

        /* QR Data  */
        $params['data']     = $data;
        $params['level']    = 'L';
        $params['size']     = 10;
        $params['savename'] = FCPATH . $config['imagedir'] . $save_name;

        $ciqrcode->generate($params);

        /* Return Data */
        return [
            'content' => $data,
            'file'    => $dir . $save_name,
        ];
    }

    public function add_data()
    {
        /* Generate QR Code */
        $data = $this->request->getVar('content');
        $qr   = $this->generate_qrcode($data);

        /* Add Data */
        if ($this->qrModel->insert_data($qr)) {
            // $this->modal_feedback('success', 'Success', 'Add Data Success', 'OK');
            session()->setFlashdata('success_qr', 'Create QR Success.');
        } else {
            session()->setFlashdata('failed_qr', 'Create QR Failed.');
            // $this->modal_feedback('error', 'Error', 'Add Data Failed', 'Try again');
        }

        return redirect()->to(site_url('/qr'));
    }

    public function edit_data($id)
    {
        /* Old QR Data */
        $old_data = $this->qrModel->qrModel($id);
        $old_file = FCPATH . $old_data['file'];

        /* Generate New QR Code */
        $data = $this->input->post('content');
        $qr   = $this->generate_qrcode($data);

        /* Edit Data */
        if ($this->qrModel->qrModel($id, $old_file, $qr)) {
            $this->modal_feedback('success', 'Success', 'Edit Data Success', 'OK');
        } else {
            $this->modal_feedback('error', 'Error', 'Edit Data Failed', 'Try again');
        }

        return redirect()->to(site_url('/'));
    }

    public function remove_data($id)
    {
        /* Current QR Data */
        $qr_data = $this->qrModel->qrModel($id);
        $qr_file = $qr_data['file'];

        /* Delete Data */
        if ($this->qrModel->qrModel($id, $qr_file)) {
            $this->modal_feedback('success', 'Success', 'Delete Data Success', 'OK');
        } else {
            $this->modal_feedback('error', 'Error', 'Delete Data Failed', 'Try again');
        }

        return redirect()->to(site_url('/'));
    }

    protected function modal_feedback($type, $title, $desc, $button): void
    {
        $message = '
            <div id="modalFeedback" class="modal fade">
                <div class="modal-dialog modal-dialog-centered modal-confirm">
                    <div class="modal-content">
            
                        <div class="modal-header-' . $type . '">
                            <div class="icon-box">
                                <i class="material-icons">' . ($type == 'success' ? '&#xE876;' : '&#xE5CD;') . '</i>
                            </div>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        </div>
                        
                        <div class="modal-body text-center">
                            <h4>' . $title . '</h4>	
                            <p>' . $desc . '</p>
                            <button class="btn" data-dismiss="modal">' . $button . '</button>
                        </div>
                        
                    </div>
                </div>
            </div>  
        ';
        $this->session->set_flashdata('modal_message', $message);
    }
}
