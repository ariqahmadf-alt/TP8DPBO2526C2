<?php
require_once __DIR__ . '/Template.php';

class DepartmentView
{
    public function index(array $rows): string
    {
        $e = 'Template::e';
        $html  = '<a class="btn btn-primary my-3" href="index.php?page=departments&action=create">Add New</a>';
        $html .= '<table class="table table-hover align-middle"><thead><tr>'
            . '<th>ID</th><th>CODE</th><th>NAME</th><th>LECTURERS</th><th>ACTIONS</th>'
            . '</tr></thead><tbody>';
        foreach ($rows as $r) {
            $html .= '<tr>'
                . '<th>' . $e($r['id']) . '</th>'
                . '<td>' . $e($r['code']) . '</td>'
                . '<td>' . $e($r['name']) . '</td>'
                . '<td>' . $e($r['lecturer_count']) . '</td>'
                . '<td>' . Template::rowActions('departments', (int) $r['id'], 'Hapus program studi ini? Dosen terkait tidak ikut terhapus.') . '</td>'
                . '</tr>';
        }
        if (!$rows) {
            $html .= '<tr><td colspan="5" class="text-center text-muted">Belum ada program studi.</td></tr>';
        }
        return $html . '</tbody></table>';
    }

    public function form(array $data, array $errors, ?int $id): string
    {
        $fields = '';
        if ($id !== null) {
            $fields .= '<input type="hidden" name="id" value="' . $id . '">';
        }
        $fields .= Template::input('CODE', 'code', $data['code'], $errors, 'text', 'maxlength="10" placeholder="contoh: IK"')
            . Template::input('NAME', 'name', $data['name'], $errors, 'text', 'maxlength="100"');

        return $id === null
            ? Template::formCard('Create Department', 'bg-primary', 'departments', 'store', $fields)
            : Template::formCard('Update Department', 'bg-warning', 'departments', 'update', $fields);
    }
}
