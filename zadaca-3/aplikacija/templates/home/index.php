<table>
    <?php foreach ($sports as $sport): ?>
        <tr>
            <td><?= htmlspecialchars($sport['name']); ?></td>
            <td><a href="/?page=sport&amp;slug=<?= htmlspecialchars($sport['slug']); ?>">Detalji</a></td>
        </tr>
    <?php endforeach; ?>
</table>
