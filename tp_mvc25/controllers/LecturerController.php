<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Lecturer.php';
require_once __DIR__ . '/../models/Department.php';
require_once __DIR__ . '/../views/LecturerView.php';

class LecturerController extends BaseController
{
    private Lecturer $lecturers;
    private Department $departments;
    private LecturerView $view;

    public function __construct()
    {
        $this->lecturers   = new Lecturer();
        $this->departments = new Department();
        $this->view        = new LecturerView();
    }

    public function index(): void
    {
        $this->render('Lecturers', 'lecturers', $this->view->index($this->lecturers->getAll()));
    }

    public function create(): void
    {
        $blank = ['name' => '', 'nidn' => '', 'phone' => '', 'join_date' => '', 'department_id' => ''];
        $this->showForm('Create Lecturer', $blank, [], null);
    }

    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect('lecturers');
        }
        $data   = $this->readInput();
        $errors = $this->validate($data);

        if (!$errors) {
            try {
                $this->lecturers->create($this->normalize($data));
                $this->flash('success', 'Dosen berhasil ditambahkan.');
                $this->redirect('lecturers');
            } catch (mysqli_sql_exception $ex) {
                $errors = $this->translateError($ex);
            }
        }
        $this->showForm('Create Lecturer', $data, $errors, null);
    }

    public function edit(): void
    {
        $id  = (int) ($_GET['id'] ?? 0);
        $row = $this->lecturers->getById($id);
        if (!$row) {
            $this->flash('danger', 'Data dosen tidak ditemukan.');
            $this->redirect('lecturers');
        }
        $this->showForm('Update Lecturer', $row, [], $id);
    }

    public function update(): void
    {
        if (!$this->isPost()) {
            $this->redirect('lecturers');
        }
        $id = (int) $this->post('id');
        if (!$this->lecturers->getById($id)) {
            $this->flash('danger', 'Data dosen tidak ditemukan.');
            $this->redirect('lecturers');
        }
        $data   = $this->readInput();
        $errors = $this->validate($data);

        if (!$errors) {
            try {
                $this->lecturers->update($id, $this->normalize($data));
                $this->flash('success', 'Data dosen berhasil diperbarui.');
                $this->redirect('lecturers');
            } catch (mysqli_sql_exception $ex) {
                $errors = $this->translateError($ex);
            }
        }
        $this->showForm('Update Lecturer', $data, $errors, $id);
    }

    public function delete(): void
    {
        if ($this->isPost()) {
            $this->lecturers->delete((int) $this->post('id'));
            $this->flash('success', 'Dosen berhasil dihapus. Mata kuliah yang diampu kini tanpa dosen pengampu.');
        }
        $this->redirect('lecturers');
    }

    // ---------- helper privat ----------

    private function showForm(string $title, array $data, array $errors, ?int $id): void
    {
        $content = $this->view->form($data, $this->departments->getOptions(), $errors, $id);
        $this->render($title, 'lecturers', $content);
    }

    private function readInput(): array
    {
        return [
            'name'          => $this->post('name'),
            'nidn'          => $this->post('nidn'),
            'phone'         => $this->post('phone'),
            'join_date'     => $this->post('join_date'),
            'department_id' => $this->post('department_id'),
        ];
    }

    private function validate(array $d): array
    {
        $errors = [];
        if (!preg_match('/^.{1,100}$/su', $d['name'])) {
            $errors['name'] = 'Nama wajib diisi (maksimal 100 karakter).';
        }
        if (!preg_match('/^\d{10}$/', $d['nidn'])) {
            $errors['nidn'] = 'NIDN harus terdiri dari 10 digit angka.';
        }
        if (!preg_match('/^[0-9+\-\s]{8,20}$/', $d['phone'])) {
            $errors['phone'] = 'Nomor telepon tidak valid (8-20 karakter angka, +, atau -).';
        }
        $date = DateTime::createFromFormat('Y-m-d', $d['join_date']);
        if (!$date || $date->format('Y-m-d') !== $d['join_date']) {
            $errors['join_date'] = 'Tanggal bergabung tidak valid.';
        }
        if ($d['department_id'] !== '' && !$this->departments->getById((int) $d['department_id'])) {
            $errors['department_id'] = 'Program studi tidak ditemukan.';
        }
        return $errors;
    }

    private function normalize(array $d): array
    {
        $d['department_id'] = $this->nullableInt($d['department_id']);
        return $d;
    }

    private function translateError(mysqli_sql_exception $ex): array
    {
        if ($ex->getCode() === 1062) {
            return ['nidn' => 'NIDN sudah terdaftar.'];
        }
        throw $ex;
    }
}
