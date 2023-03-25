<table>
    <?php foreach ($events as $event): ?>
        <tr>
            <td><?= htmlspecialchars($event['start_date']); ?></td>
            <td><a href="/event/<?= htmlspecialchars($event['id']); ?>">Detalji</a></td>
        </tr>
    <?php endforeach; ?>
</table>