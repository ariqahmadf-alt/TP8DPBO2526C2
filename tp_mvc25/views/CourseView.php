<?php
require_once __DIR__ . '/Template.php';

class CourseView
{
    public function index(array $rows): string
    {
        $e = 'Template::e';
        $html  = '<a class="btn btn-primary my-3" href="index.php?page=courses&action=create">Add New</a>';
        $html .= '<table class="table table-hover align-middle"><thead><tr>'
            . '<th>ID</th><th>CODE</th><th>NAME</th><th>CREDITS</th><th>SEMESTER</th><th>LECTURER</th><th>ACTIONS</th>'
            . '</tr></thead><tbody>';
        foreach ($rows as $r) {
            $html .= '<tr>'
                . '<th>' . $e($r['id']) . '</th>'
                . '<td>' . $e($r['code']) . '</td>'
                . '<td>' . $e($r['name']) . '</td>'
                . '<td>' . $e($r['credits']) . '</td>'
                . '<td>' . $e($r['semester']) . '</td>'
                . '<td>' . ($r['lecturer_name'] !== null ? $e($r['lecturer_name']) : '<span class="text-muted">-</span>') . '</td>'
                . '<td>' . Template::rowActions('courses', (int) $r['id'], 'Hapus mata kuliah ini?') . '</td>'
                . '</tr>';
        }
        if (!$rows) {
            $html .= '<tr><td colspan="7" class="text-center text-muted">Belum ada mata kuliah.</td></tr>';
        }
        return $html . '</tbody></table>';
    }

    public function form(array $data, array $lecturers, array $errors, ?int $id): string
    {
        $options = [];
        foreach ($lecturers as $l) {
            $options[$l['id']] = $l['name'];
        }
        $fields = '';
        if ($id !== null) {
            $fields .= '<input type="hidden" name="id" value="' . $id . '">';
        }
        $fields .= Template::input('CODE', 'code', $data['code'], $errors, 'text', 'maxlength="10" placeholder="contoh: IK201"')
            . Template::input('NAME', 'name', $data['name'], $errors, 'text', 'maxlength="100"')
            . Template::input('CREDITS (SKS)', 'credits', $data['credits'], $errors, 'number', 'min="1" max="6"')
            . Template::input('SEMESTER', 'semester', $data['semester'], $errors, 'number', 'min="1" max="8"')
            . Template::select('LECTURER', 'lecturer_id', $options, $data['lecturer_id'], $errors, '-- Pilih dosen pengampu --');

        return $id === null
            ? Template::formCard('Create Course', 'bg-primary', 'courses', 'store', $fields)
            : Template::formCard('Update Course', 'bg-warning', 'courses', 'update', $fields);
    }
}
