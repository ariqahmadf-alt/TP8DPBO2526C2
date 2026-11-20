<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Lecturer.php';
require_once __DIR__ . '/../views/CourseView.php';

class CourseController extends BaseController
{
    private Course $courses;
    private Lecturer $lecturers;
    private CourseView $view;

    public function __construct()
    {
        $this->courses   = new Course();
        $this->lecturers = new Lecturer();
        $this->view      = new CourseView();
    }

    public function index(): void
    {
        $this->render('Courses', 'courses', $this->view->index($this->courses->getAll()));
    }

    public function create(): void
    {
        $blank = ['code' => '', 'name' => '', 'credits' => '', 'semester' => '', 'lecturer_id' => ''];
        $this->showForm('Create Course', $blank, [], null);
    }

    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect('courses');
        }
        $data   = $this->readInput();
        $errors = $this->validate($data);

        if (!$errors) {
            try {
                $this->courses->create($this->normalize($data));
                $this->flash('success', 'Mata kuliah berhasil ditambahkan.');
                $this->redirect('courses');
            } catch (mysqli_sql_exception $ex) {
                $errors = $this->translateError($ex);
            }
        }
        $this->showForm('Create Course', $data, $errors, null);
    }

    public function edit(): void
    {
        $id  = (int) ($_GET['id'] ?? 0);
        $row = $this->courses->getById($id);
        if (!$row) {
            $this->flash('danger', 'Mata kuliah tidak ditemukan.');
            $this->redirect('courses');
        }
        $this->showForm('Update Course', $row, [], $id);
    }

    public function update(): void
    {
        if (!$this->isPost()) {
            $this->redirect('courses');
        }
        $id = (int) $this->post('id');
        if (!$this->courses->getById($id)) {
            $this->flash('danger', 'Mata kuliah tidak ditemukan.');
            $this->redirect('courses');
        }
        $data   = $this->readInput();
        $errors = $this->validate($data);

        if (!$errors) {
            try {
                $this->courses->update($id, $this->normalize($data));
                $this->flash('success', 'Mata kuliah berhasil diperbarui.');
                $this->redirect('courses');
            } catch (mysqli_sql_exception $ex) {
                $errors = $this->translateError($ex);
            }
        }
        $this->showForm('Update Course', $data, $errors, $id);
    }

    public function delete(): void
    {
        if ($this->isPost()) {
            $this->courses->delete((int) $this->post('id'));
            $this->flash('success', 'Mata kuliah berhasil dihapus.');
        }
        $this->redirect('courses');
    }

    // ---------- helper privat ----------

    private function showForm(string $title, array $data, array $errors, ?int $id): void
    {
        $content = $this->view->form($data, $this->lecturers->getOptions(), $errors, $id);
        $this->render($title, 'courses', $content);
    }

    private function readInput(): array
    {
        return [
            'code'        => strtoupper($this->post('code')),
            'name'        => $this->post('name'),
            'credits'     => $this->post('credits'),
            'semester'    => $this->post('semester'),
            'lecturer_id' => $this->post('lecturer_id'),
        ];
    }

    private function validate(array $d): array
    {
        $errors = [];
        if (!preg_match('/^[A-Z0-9]{3,10}$/', $d['code'])) {
            $errors['code'] = 'Kode terdiri dari 3-10 huruf/angka.';
        }
        if (!preg_match('/^.{1,100}$/su', $d['name'])) {
            $errors['name'] = 'Nama wajib diisi (maksimal 100 karakter).';
        }
        if (!ctype_digit($d['credits']) || (int) $d['credits'] < 1 || (int) $d['credits'] > 6) {
            $errors['credits'] = 'SKS harus berupa angka 1-6.';
        }
        if (!ctype_digit($d['semester']) || (int) $d['semester'] < 1 || (int) $d['semester'] > 8) {
            $errors['semester'] = 'Semester harus berupa angka 1-8.';
        }
        if ($d['lecturer_id'] !== '' && !$this->lecturers->getById((int) $d['lecturer_id'])) {
            $errors['lecturer_id'] = 'Dosen tidak ditemukan.';
        }
        return $errors;
    }

    private function normalize(array $d): array
    {
        $d['credits']     = (int) $d['credits'];
        $d['semester']    = (int) $d['semester'];
        $d['lecturer_id'] = $this->nullableInt($d['lecturer_id']);
        return $d;
    }

    private function translateError(mysqli_sql_exception $ex): array
    {
        if ($ex->getCode() === 1062) {
            return ['code' => 'Kode mata kuliah sudah dipakai.'];
        }
        throw $ex;
    }
}
