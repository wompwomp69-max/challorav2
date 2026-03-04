<?php
/**
 * HR: lihat applicant, update status
 */
class HrApplicationController {
    private Job $jobModel;
    private Application $appModel;
    private User $userModel;

    public function __construct() {
        $this->jobModel = new Job();
        $this->appModel = new Application();
        $this->userModel = new User();
    }

    private function requireHr(): void {
        requireRole('hr');
    }

    public function index(): void {
        $this->requireHr();
        $jobId = (int) ($_GET['id'] ?? 0);
        if ($jobId < 1 || !$this->jobModel->isCreatedBy($jobId, currentUserId())) {
            $_SESSION['flash_error'] = 'Lowongan tidak ditemukan.';
            redirect('/hr/jobs');
        }
        $job = $this->jobModel->findById($jobId);
        $applicants = $this->appModel->getByJobId($jobId);
        $workExpByUser = [];
        foreach ($applicants as $a) {
            $workExpByUser[(int)$a['user_id']] = $this->userModel->getWorkExperiences((int)$a['user_id']);
        }
        render_view('hr/applications/index', ['job' => $job, 'applicants' => $applicants, 'workExpByUser' => $workExpByUser, 'pageTitle' => 'Pelamar - ' . e($job['title'])]);
    }

    public function updateStatus(): void {
        $this->requireHr();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/hr/jobs');
        }
        $appId = (int) ($_POST['application_id'] ?? 0);
        $status = trim($_POST['status'] ?? '');
        $app = $this->appModel->getApplicationForHrJob($appId, currentUserId());
        if (!$app) {
            $_SESSION['flash_error'] = 'Data lamaran tidak ditemukan.';
            redirect('/hr/jobs');
        }
        if ($this->appModel->updateStatus($appId, $status)) {
            $_SESSION['flash'] = 'Status lamaran diperbarui.';
        }
        redirect('/hr/jobs/applicants?id=' . $app['job_id']);
    }

    /** Daftar pelamar yang sudah diterima (untuk semua lowongan HR) */
    public function accepted(): void {
        $this->requireHr();
        $hrId = currentUserId();
        $perPage = (int) ($_GET['per_page'] ?? 20);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 20;
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $totalAccepted = $this->appModel->countAcceptedForHr($hrId);
        $totalPages = $totalAccepted > 0 ? (int) ceil($totalAccepted / $perPage) : 1;
        $page = min($page, $totalPages);
        $applicants = $this->appModel->getAcceptedApplicantsForHr($hrId, $page, $perPage);
        render_view('hr/applications/accepted', [
            'applicants' => $applicants,
            'pageTitle' => 'Pelamar Diterima',
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => $totalPages,
            'totalAccepted' => $totalAccepted,
        ]);
    }
}
