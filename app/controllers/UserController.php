<?php
/**
 * Candidate: profile edit, my applications
 */
class UserController {
    private User $userModel;
    private Application $appModel;

    public function __construct() {
        $this->userModel = new User();
        $this->appModel = new Application();
    }

    public function profile(): void {
        requireRole('user');
        $user = $this->userModel->findById(currentUserId());
        if (!$user) {
            redirect('/auth/logout');
        }
        unset($user['password']);
        $applications = $this->appModel->getByUserId(currentUserId());
        $workExperiences = $this->userModel->getWorkExperiences(currentUserId());
        render_view('user/settings/index', ['user' => $user, 'applications' => $applications, 'workExperiences' => $workExperiences, 'pageTitle' => 'Profil']);
    }

    public function profileEdit(): void {
        requireRole('user');
        $user = $this->userModel->findById(currentUserId());
        if (!$user) redirect('/auth/logout');
        unset($user['password']);
        $workExperiences = $this->userModel->getWorkExperiences(currentUserId());
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $fatherName = trim($_POST['father_name'] ?? '');
            $motherName = trim($_POST['mother_name'] ?? '');
            $maritalStatus = trim($_POST['marital_status'] ?? '');
            $educationLevel = trim($_POST['education_level'] ?? '');
            $graduationYear = trim($_POST['graduation_year'] ?? '');
            $educationMajor = trim($_POST['education_major'] ?? '');
            $educationUniversity = trim($_POST['education_university'] ?? '');
            $titles = $_POST['work_title'] ?? [];
            $companies = $_POST['work_company'] ?? [];
            $yearStarts = $_POST['work_year_start'] ?? [];
            $yearEnds = $_POST['work_year_end'] ?? [];
            $descriptions = $_POST['work_description'] ?? [];
            $workItems = [];
            foreach ($titles as $i => $t) {
                $workItems[] = [
                    'title' => $t ?? '',
                    'company_name' => $companies[$i] ?? '',
                    'year_start' => $yearStarts[$i] ?? '',
                    'year_end' => $yearEnds[$i] ?? '',
                    'description' => $descriptions[$i] ?? ''
                ];
            }
            if ($name === '') {
                $error = 'Nama wajib diisi.';
                $workExperiences = $workItems;
                $user = array_merge($user, [
                    'name' => $name, 'phone' => $phone, 'address' => $address,
                    'father_name' => $fatherName, 'mother_name' => $motherName, 'marital_status' => $maritalStatus,
                    'education_level' => $educationLevel, 'graduation_year' => $graduationYear,
                    'education_major' => $educationMajor, 'education_university' => $educationUniversity
                ]);
            } else {
                $this->userModel->update(currentUserId(), [
                    'name' => $name, 'phone' => $phone, 'address' => $address,
                    'father_name' => $fatherName, 'mother_name' => $motherName, 'marital_status' => $maritalStatus,
                    'education_level' => $educationLevel, 'graduation_year' => $graduationYear,
                    'education_major' => $educationMajor, 'education_university' => $educationUniversity
                ]);
                $this->userModel->setWorkExperiences(currentUserId(), $workItems);
                $_SESSION['flash'] = 'Profil berhasil diperbarui.';
                $_SESSION['user_name'] = $name;
                redirect('/user/settings');
            }
        }
        render_view('user/settings/edit', ['user' => $user, 'workExperiences' => $workExperiences, 'error' => $error, 'pageTitle' => 'Pengaturan Profil']);
    }
}
