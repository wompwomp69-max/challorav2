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
        $achievementsByUser = [];
        foreach ($applicants as $a) {
            $uid = (int) $a['user_id'];
            $workExpByUser[$uid] = $this->userModel->getWorkExperiences($uid);
            $achievementsByUser[$uid] = $this->userModel->getAchievements($uid);
        }
        $openMailto = $_SESSION['open_mailto'] ?? null;
        if ($openMailto) {
            unset($_SESSION['open_mailto']);
        }
        render_view('hr/applications/index', ['job' => $job, 'applicants' => $applicants, 'workExpByUser' => $workExpByUser, 'achievementsByUser' => $achievementsByUser, 'pageTitle' => 'Pelamar - ' . e($job['title']), 'openMailto' => $openMailto]);
    }

    public function updateStatus(): void {
        $this->requireHr();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/hr/jobs');
        }
        $appId = (int) ($_POST['application_id'] ?? 0);
        $status = trim($_POST['status'] ?? '');
        $openMailto = !empty($_POST['open_mailto']);
        $app = $this->appModel->getApplicationForHrJob($appId, currentUserId());
        if (!$app) {
            $_SESSION['flash_error'] = 'Data lamaran tidak ditemukan.';
            redirect('/hr/jobs');
        }
        if ($this->appModel->updateStatus($appId, $status)) {
            $_SESSION['flash'] = 'Status lamaran diperbarui.';
            if ($openMailto && in_array($status, ['accepted', 'rejected'], true)) {
                $user = $this->userModel->findById((int) $app['user_id']);
                $job = $this->jobModel->findById((int) $app['job_id']);
                $name = $user['name'] ?? 'Pelamar';
                $email = $user['email'] ?? '';
                $jobTitle = $job['title'] ?? 'lowongan';
                $mail = new MailService();
                if ($mail->isEnabled() && $email && $mail->sendApplicationResult($email, $name, $jobTitle, $status)) {
                    $_SESSION['flash'] = 'Status lamaran diperbarui. Email otomatis telah dikirim ke pelamar.';
                } else {
                    $subject = rawurlencode('Hasil Lamaran: ' . $jobTitle . ' - ' . ($status === 'accepted' ? 'Diterima' : 'Tidak Diterima'));
                    $body = $status === 'accepted'
                        ? rawurlencode("Yth. {$name},\n\nSelamat! Anda dinyatakan LULUS seleksi untuk posisi {$jobTitle}.\n\nSilakan hubungi kami untuk langkah selanjutnya.\n\nTerima kasih.")
                        : rawurlencode("Yth. {$name},\n\nTerima kasih telah melamar untuk posisi {$jobTitle}.\n\nMohon maaf, setelah proses seleksi Anda belum dapat kami terima untuk posisi ini.\n\nTetap semangat dan terima kasih.");
                    $_SESSION['open_mailto'] = "mailto:{$email}?subject={$subject}&body={$body}";
                }
            }
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
