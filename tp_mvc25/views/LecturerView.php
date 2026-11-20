<?php
require_once __DIR__ . '/Template.php';

class LecturerView
{
    public function index(array $rows): string
    {
        $e = 'Template::e';
        $html  = '<a class="btn btn-primary my-3" href="index.php?page=lecturers&action=create">Add New</a>';
        $html .= '<table class="table table-hover align-middle"><thead><tr>'
            . '<th>ID</th><th>NAME</th><th>NIDN</th><th>PHONE</th><th>JOIN DATE</th><th>DEPARTMENT</th><th>COURSES</th><th>ACTIONS</th>'
            . '</tr></thead><tbody>';
        foreach ($rows as $r) {
            $html .= '<tr>'
                . '<th>' . $e($r['id']) . '</th>'
                . '<td>' . $e($r['name']) . '</td>'
                . '<td>' . $e($r['nidn']) . '</td>'
                . '<td>' . $e($r['phone']) . '</td>'
                . '<td>' . $e($r['join_date']) . '</td>'
                . '<td>' . ($r['department_name'] !== null ? $e($r['department_name']) : '<span class="text-muted">-</span>') . '</td>'
                . '<td>' . $e($r['course_count']) . '</td>'
                . '<td>' . Template::rowActions('lecturers', (int) $r['id'], 'Hapus dosen ini?') . '</td>'
                . '</tr>';
        }
        if (!$rows) {
            $html .= '<tr><td colspan="8" class="text-center text-muted">Belum ada data dosen.</td></tr>';
        }
        return $html . '</tbody></table>';
    }

    /** Form untuk create (id null) maupun edit. */
    public function form(array $data, array $departments, array $errors, ?int $id): string
    {
        $options = [];
        foreach ($departments as $d) {
            $options[$d['id']] = $d['code'] . ' - ' . $d['name'];
        }
        $fields = '';
        if ($id !== null) {
            $fields .= '<input type="hidden" name="id" value="' . $id . '">';
        }
        $fields .= Template::input('NAME', 'name', $data['name'], $errors, 'text', 'maxlength="100"')
            . Template::input('NIDN', 'nidn', $data['nidn'], $errors, 'text', 'maxlength="10" placeholder="10 digit angka"')
            . Template::input('PHONE', 'phone', $data['phone'], $errors, 'text', 'maxlength="20"')
            . Template::input('JOIN DATE', 'join_date', $data['join_date'], $errors, 'date')
            . Template::select('DEPARTMENT', 'department_id', $options, $data['department_id'], $errors, '-- Pilih program studi --');

        return $id === null
            ? Template::formCard('Create Lecturer', 'bg-primary', 'lecturers', 'store', $fields)
            : Template::formCard('Update Lecturer', 'bg-warning', 'lecturers', 'update', $fields);
    }
}
