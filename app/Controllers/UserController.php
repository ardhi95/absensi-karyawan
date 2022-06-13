<?php

namespace App\Controllers;

use App\Models\AttendanceModel;
use App\Models\JobModel;
use App\Models\UserModel;
use CodeIgniter\Config\Services;

class UserController extends BaseController
{
    protected $userModel, $jobModel, $attendanceModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->jobModel = new JobModel();
        $this->attendanceModel = new AttendanceModel();
        if (session()->get('level') != "employee") {
            echo 'Access denied';
            exit;
        }
    }

    public function index()
    {
        $id = session()->get('id');

        $isLoggedIn = $this->attendanceModel
            ->where(['user_id' => $id])
            ->where('DATE(created_at)', date('Y-m-d'))
            ->first();

        $data = [
            'isLoggedIn' => $isLoggedIn
        ];

        echo view('layouts/pages/User/index', $data);
    }

    public function profile()
    {
        helper(['form']);
        $id = session()->get('id');
        $pointAllJobs = $this->jobModel
            ->where(['user_id' => $id])
            ->where(['is_completed' => 1])
            ->selectSum('point')
            ->first();

        $data = [
            'user' => $this->userModel->where(['userId' => $id])->first(),
            'point' => $pointAllJobs
        ];
        return view('layouts/pages/User/profile/index', $data);
    }

    public function absent()
    {
        echo view('layouts/pages/User/absent/index');
    }

    public function report()
    {
        $id = session()->get('id');
        $detail = $this->jobModel
            ->join('users', 'users.userId = jobs.user_id')
            ->where(['user_id' => $id])
            ->first();
        $data = [
            'job' => $detail,
        ];
        echo view('layouts/pages/User/report/index', $data);
    }

    public function completeReport($id)
    {
        $id_user = session()->get('id');
        $data = [
            'jobId' => $id,
            'user_id' => $id_user,
            'is_completed' => 1,
            'updated_at' => 'Y-m-d H:i:s',
        ];

        $this->jobModel->replace($data);

        session()->setFlashData('index', 'Success Completed your job!');
        return redirect()->to("/logout");
    }

    public function task()
    {
        $jobModel = new JobModel();
        $userId = session()->get('id');
        $job = $jobModel->where('user_id', $userId)->findAll();
        $data = [
            'page' => 'job',
            'job' => $job
        ];

        echo view('layouts/pages/User/task/index', $data);
    }

    public function TaskDetail($id)
    {
        $data = [
            'job' => $this->jobModel->where(['jobId' => $id])->first(),
        ];
        echo view('layouts/pages/User/task/detail', $data);
    }
}
