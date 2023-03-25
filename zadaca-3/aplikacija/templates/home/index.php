<table>
    <?php foreach ($sports as $sport): ?>
        <tr>
            <td><?= htmlspecialchars($sport['name']); ?></td>
            <td><a href="/sport/<?= htmlspecialchars($sport['slug']); ?>">Detalji</a></td>
        </tr>
    <?php endforeach; ?>
</table>
