<?php
    $baseUrl = base_url('/');
    $themeModel = new \App\Models\ThemeModel();
    $themes = $themeModel->getAll();

    use App\Libraries\Dialog;

    $htmlEdit = $form->renderPlainDialog('formEdit');
    $htmlNew = $form->renderDialog('New theme', 'formNew', "{$baseUrl}/insert");
    $htmlDelete = Dialog::renderDelete('Delete theme', 'CONFIRM DELETE');
?>

<h1>Themes</h1>

<table>
    <thead>
        <tr>
            <th>Theme Name</th>
            <th>Image</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($themes as $theme): ?>
            <tr>
                <td><?= $theme->theme_name ?></td>
                <td><img src="<?= $theme->url_image ?>" alt="<?= $theme->theme_name ?>"></td>
                <td>
                    <a href="<?= $baseUrl ?>/theme/edit/<?= $theme->theme_id ?>" class="button">Edit</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
