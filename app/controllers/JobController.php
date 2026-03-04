<?php
/**
 * List & detail job — wajib login (selain /auth/*)
 */
class JobController {
    private Job $jobModel;

    public function __construct() {
        $this->jobModel = new Job();
    }

    public function index(): void {
        requireLogin();
        $jobs = $this->jobModel->all();
        $appliedJobIds = [];
        if (isLoggedIn() && currentRole() === 'user') {
            $appModel = new Application();
            foreach ($jobs as $j) {
                if ($appModel->hasApplied(currentUserId(), (int)$j['id'])) {
                    $appliedJobIds[] = (int)$j['id'];
                }
            }
        }
        render_view('user/jobs/index', ['jobs' => $jobs, 'appliedJobIds' => $appliedJobIds, 'pageTitle' => 'Lowongan']);
    }

    public function show(): void {
        requireLogin();
        $id = (int) ($_GET['id'] ?? 0);
        if ($id < 1) {
            redirect('/jobs');
        }
        $job = $this->jobModel->findById($id);
        if (!$job) {
            $_SESSION['flash_error'] = 'Lowongan tidak ditemukan.';
            redirect('/jobs');
        }
        $canApply = false;
        $alreadyApplied = false;
        if (isLoggedIn() && currentRole() === 'user') {
            $appModel = new Application();
            $alreadyApplied = $appModel->hasApplied(currentUserId(), $id);
            $canApply = !$alreadyApplied;
        }
        render_view('user/jobs/show', [
            'job' => $job,
            'canApply' => $canApply,
            'alreadyApplied' => $alreadyApplied,
            'pageTitle' => e($job['title']),
        ]);
    }
}
