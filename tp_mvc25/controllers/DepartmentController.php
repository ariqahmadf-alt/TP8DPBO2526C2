<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Department.php';
require_once __DIR__ . '/../views/DepartmentView.php';

class DepartmentController extends BaseController
{
    private Department $departments;
    private DepartmentView $view;

    public function __construct()
    {
        $this->departments = new Department();
        $this->view        = new DepartmentView();
    }

    public function index(): void
    {
        $this->render('Departments', 'departments', $this->view->index($this->departments->getAll()));
    }

    public function create(): void
    {
        $this->showForm('Create Department', ['code' => '', 'name' => ''], [], null);
    }

    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect('departments');
        }
        $data   = $this->readInput();
        $errors = $this->validate($data);

        if (!$errors) {
            try {
                $this->departments->create($data);
                $this->flash('success', 'Program studi berhasil ditambahkan.');
                $this->redirect('departments');
            } catch (mysqli_sql_exception $ex) {
                $errors = $this->translateError($ex);
            }
        }
        $this->showForm('Create Department', $data, $errors, null);
    }

    public function edit(): void
    {
        $id  = (int) ($_GET['id'] ?? 0);
        $row = $this->departments->getById($id);
        if (!$row) {
            $this->flash('danger', 'Program studi tidak ditemukan.');
            $this->redirect('departments');
        }
        $this->showForm('Update Department', $row, [], $id);
    }

    public function update(): void
    {
        if (!$this->isPost()) {
            $this->redirect('departments');
        }
        $id = (int) $this->post('id');
        if (!$this->departments->getById($id)) {
            $this->flash('danger', 'Program studi tidak ditemukan.');
            $this->redirect('departments');
        }
        $data   = $this->readInput();
        $errors = $this->validate($data);

        if (!$errors) {
            try {
                $this->departments->update($id, $data);
                $this->flash('success', 'Program studi berhasil diperbarui.');
                $this->redirect('departments');
            } catch (mysqli_sql_exception $ex) {
                $errors = $this->translateError($ex);
            }
        }
        $this->showForm('Update Department', $data, $errors, $id);
    }

    public function delete(): void
    {
        if ($this->isPost()) {
            $this->departments->delete((int) $this->post('id'));
            $this->flash('success', 'Program studi berhasil dihapus. Dosen terkait tetap tersimpan tanpa program studi.');
        }
        $this->redirect('departments');
    }

    // ---------- helper privat ----------

    private function showForm(string $title, array $data, array $errors, ?int $id): void
    {
        $this->render($title, 'departments', $this->view->form($data, $errors, $id));
    }

    private function readInput(): array
    {
        return ['code' => strtoupper($this->post('code')), 'name' => $this->post('name')];
    }

    private function validate(array $d): array
    {
        $errors = [];
        if (!preg_match('/^[A-Z0-9]{2,10}$/', $d['code'])) {
            $errors['code'] = 'Kode terdiri dari 2-10 huruf/angka.';
        }
        if (!preg_match('/^.{1,100}$/su', $d['name'])) {
            $errors['name'] = 'Nama wajib diisi (maksimal 100 karakter).';
        }
        return $errors;
    }

    private function translateError(mysqli_sql_exception $ex): array
    {
        if ($ex->getCode() === 1062) {
            return ['code' => 'Kode program studi sudah dipakai.'];
        }
        throw $ex;
    }
}
