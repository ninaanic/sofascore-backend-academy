<body>
<table>
    <tr>
        <td>Datum</td>
        <td><?= htmlspecialchars($event['start_date']); ?></td>
    </tr>
    <tr>
        <td><?= htmlspecialchars($event['home_team_id']); ?></td>
        <td><?= htmlspecialchars($event['home_score']); ?></td>
    </tr>
    <tr>
        <td><?= htmlspecialchars($event['away_team_id']); ?></td>
        <td><?= htmlspecialchars($event['away_score']); ?></td>
    </tr>
</table>
</body>
