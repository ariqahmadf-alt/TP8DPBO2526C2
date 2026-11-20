<?php
/**
 * Kerangka halaman bersama (navbar, flash message, footer) dan
 * helper kecil untuk membuat elemen form.
 */
class Template
{
    /** Escape output agar aman dari XSS. */
    public static function e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    public static function page(string $title, string $active, string $content, ?array $flash = null): void
    {
        $menu = [
            'lecturers'   => 'Home',
            'departments' => 'Departments',
            'courses'     => 'Courses',
        ];
        ?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <title><?= self::e($title) ?> | Campus Data</title>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="index.php">Campus Data</a>
      <ul class="navbar-nav">
        <?php foreach ($menu as $key => $label): ?>
          <li class="nav-item">
            <a class="nav-link <?= $key === $active ? 'active' : '' ?>" href="index.php?page=<?= $key ?>"><?= $label ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </nav>

  <div class="container my-4">
    <?php if ($flash): ?>
      <div class="alert alert-<?= self::e($flash['type']) ?> alert-dismissible fade show" role="alert">
        <?= self::e($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
    <?= $content ?>
  </div>

  <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>
        <?php
    }

    /** Input teks/angka/tanggal dengan pesan galat per kolom. */
    public static function input(string $label, string $name, $value, array $errors, string $type = 'text', string $attrs = ''): string
    {
        $invalid = isset($errors[$name]) ? ' is-invalid' : '';
        $html  = '<div class="mb-3">';
        $html .= '<label class="form-label" for="' . $name . '">' . self::e($label) . '</label>';
        $html .= '<input type="' . $type . '" id="' . $name . '" name="' . $name . '" class="form-control' . $invalid . '" value="' . self::e($value) . '" ' . $attrs . '>';
        if ($invalid) {
            $html .= '<div class="invalid-feedback">' . self::e($errors[$name]) . '</div>';
        }
        return $html . '</div>';
    }

    /** Dropdown; $options berbentuk [nilai => label]. */
    public static function select(string $label, string $name, array $options, $selected, array $errors, string $placeholder): string
    {
        $invalid = isset($errors[$name]) ? ' is-invalid' : '';
        $html  = '<div class="mb-3">';
        $html .= '<label class="form-label" for="' . $name . '">' . self::e($label) . '</label>';
        $html .= '<select id="' . $name . '" name="' . $name . '" class="form-select' . $invalid . '">';
        $html .= '<option value="">' . self::e($placeholder) . '</option>';
        foreach ($options as $value => $text) {
            $sel = ((string) $value === (string) $selected) ? ' selected' : '';
            $html .= '<option value="' . self::e($value) . '"' . $sel . '>' . self::e($text) . '</option>';
        }
        $html .= '</select>';
        if ($invalid) {
            $html .= '<div class="invalid-feedback">' . self::e($errors[$name]) . '</div>';
        }
        return $html . '</div>';
    }

    /** Tombol Edit dan Delete untuk satu baris tabel. Delete memakai POST. */
    public static function rowActions(string $page, int $id, string $confirm): string
    {
        return '<a class="btn btn-success btn-sm" href="index.php?page=' . $page . '&action=edit&id=' . $id . '">Edit</a> '
            . '<form method="post" action="index.php?page=' . $page . '&action=delete" class="d-inline" '
            . 'onsubmit="return confirm(\'' . self::e($confirm) . '\')">'
            . '<input type="hidden" name="id" value="' . $id . '">'
            . '<button type="submit" class="btn btn-danger btn-sm">Delete</button></form>';
    }

    /** Kartu form generik untuk halaman Create/Update. */
    public static function formCard(string $heading, string $headerClass, string $page, string $action, string $fields): string
    {
        return '<div class="col-lg-6 m-auto">'
            . '<form method="post" action="index.php?page=' . $page . '&action=' . $action . '" novalidate>'
            . '<div class="card"><div class="card-header ' . $headerClass . '"><h1 class="text-white text-center h3 mb-0">' . self::e($heading) . '</h1></div>'
            . '<div class="card-body">' . $fields
            . '<button class="btn btn-success" type="submit">Submit</button> '
            . '<a class="btn btn-info" href="index.php?page=' . $page . '">Cancel</a>'
            . '</div></div></form></div>';
    }
}
