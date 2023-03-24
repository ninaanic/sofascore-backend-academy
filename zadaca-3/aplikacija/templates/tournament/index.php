<table>
    <?php foreach ($tournaments as $tournament): ?>
        <tr>
            <td><?= htmlspecialchars($tournament['name']); ?></td>
            <td><a href="/?page=tournament&amp;slug=<?= htmlspecialchars($tournament['slug']); ?>">Detalji</a></td>
        </tr>
    <?php endforeach; ?>
</table>
